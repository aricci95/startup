<?php

class User extends Model
{
    const RIDE_SKI       = 1;
    const RIDE_SNOWBOARD = 2;

    const TYPE_SKI   = 1;
    const TYPE_OWNER = 2;

    public static $rides = array(
        self::RIDE_SKI       => 'ski',
        self::RIDE_SNOWBOARD => 'snowboard',
    );

    public static $attributes = array(
        'primary' => array(
            'user.user_id',
            'user_prenom',
            'role_id',
            'user_mail',
            'FLOOR((DATEDIFF( CURDATE(), (user_birth))/365)) AS age',
            'UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion',
            'user_photo_url',
        ),
        'profile' => array(
            'user_ride',
            'user_level',
        ),
        'details' => array(
            'user_smoke',
            'user_description',
            'user_profession',
            'user_cuisine',
            'user_vehicule',
            'user_hygiene',
            'user_fun',
            'user_cash',
            '(SELECT LEFT(SUM(rate) / count(*), 1) FROM vote WHERE vote.key_id = user.user_id AND type_id = 1) AS user_rate',
        ),
        'auth' => array(
            'user_valid',
        ),
        'optional' => array(
            'user_birth',
            'user_nom',
            'user_gender',
            'user_poids',
            'user_taille',
            'user_adresse',
        ),
    );

    public static $evals = array(
        'fun' => 'fun',
        'cuisine' => 'cuisine',
        'hygiène' => 'hygiene',
        'dépenses' => 'cash',
    );

    public function updateLastConnexion($userId = null)
    {
        if (empty($userId)) {
            $userId = $this->context->get('user_id');
        }

        $sql = 'UPDATE user SET user_last_connexion = NOW() WHERE user_id = :user_id';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);

        $this->db->executeStmt($stmt);

        return true;
    }

    public function updateUserData(array $data = array())
    {
        $sql = 'UPDATE user_data SET ';

        foreach ($data as $key => $value) {
            $sql .= $key . ' = :'. $key;
        }

        $sql .= ' WHERE user_id = :user_id;';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('user_id', $this->context->get('user_id'), PDO::PARAM_INT);

        foreach ($data as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        return $this->db->executeStmt($stmt);
    }

    public function getSearch($criterias, $offset = 0)
    {
        $queryBuilder = $this->query('user_data')
                             ->Join(array('user' => 'user_id'))
                             ->leftJoin(array('city' => 'ville_id'));

        if (!empty($criterias['search_name'])) {
            $queryBuilder->where(array('%user_prenom' => $criterias['search_name']));
        }

        if (!empty($criterias['search_gender'])) {
            $queryBuilder->where(array('user_gender' => $criterias['search_gender']));
        }

        if (!empty($criterias['search_age'])) {
            $queryBuilder->lowerThan(array('search_age' => 'FLOOR((DATEDIFF( CURDATE(), (user_birth))/365))'));
        }

        if (!empty($criterias['search_distance'])) {
            $longitude = $this->context->get('ville_longitude_deg');
            $latitude = $this->context->get('ville_latitude_deg');

            $ratio = COEF_DISTANCE * $criterias['search_distance'];

            $queryBuilder->between(array(
                'ville_longitude_deg' => array(
                    'begin' => ($longitude - $ratio),
                    'end' => ($longitude + $ratio)
                ),
                'ville_latitude_deg' => array(
                    'begin' => ($latitude - $ratio),
                    'end' => ($latitude + $ratio)
                ),
            ));
        }

        $queryBuilder->orderBy(array('user_last_connexion DESC'));

        $queryBuilder->limit($offset * NB_SEARCH_RESULTS, NB_SEARCH_RESULTS);

        $fields = array_merge(self::$attributes['primary'], self::$attributes['profile'], self::$attributes['details'], City::$attributes['primary']);

        return $queryBuilder->select($fields);
    }

    public function getUserByIdDetails($userId, $type = User::TYPE_SKI)
    {
        if ($type == User::TYPE_OWNER) {
            return $this->query('user')
                    ->single()
                    ->leftJoin(array('city' => 'ville_id'))
                    ->where(array('user.user_id' => $userId))
                    ->select();
        } else {
            return $this->query('user_data')
                        ->single()
                        ->join(array('user' => 'user_id'))
                        ->leftJoin(array('city' => 'ville_id'))
                        ->where(array('user.user_id' => $userId))
                        ->select(array_merge(
                            self::$attributes['primary'],
                            self::$attributes['profile'],
                            self::$attributes['details'],
                            self::$attributes['optional']
                        ));
        }
    }

    public function deleteById($id)
    {
        $sql = "DELETE FROM user WHERE user_id = :id;
                DELETE FROM message WHERE destinataire_id = :id OR expediteur_id = :id;
                DELETE FROM chat WHERE `from` = :id OR `to` = :id;
            ";
        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('id', $id, PDO::PARAM_INT);

        return $this->db->executeStmt($stmt);
    }

    public function setValid($code)
    {
        $sql = 'UPDATE
                    user
                SET
                    user_valid = 1
                WHERE
                    user_valid = :code';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':code', $code, PDO::PARAM_STR);

        return $this->db->executeStmt($stmt);
    }

    public function isUsedEmail($email)
    {
        $sql = 'SELECT user_id
                FROM   user
                WHERE  user_mail = :email';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('email', $email, PDO::PARAM_STR);

        return $this->db->executeStmt($stmt)->fetch();
    }

    public function createUser($items)
    {
        $items['user_valid'] = uniqid();
        $items['user_subscribe_date'] = 'NOW()';

        $userId = $this->query()->insert($items);

        if ($userId && $items['role_id'] == User::TYPE_SKI) {
            $this->query('user_data')->insert(array('user_id' => $userId));
        }

        return $items['user_valid'];
    }

    public function findByEmailPwd($email, $pwd)
    {
        return $this->query('user')
            ->leftJoin(array('city' => 'ville_id'))
            ->single()
            ->where(array(
                'user_mail' => strtolower($email),
                'user_pwd' => $pwd,
                ))
            ->select(
                array_merge(
                    self::$attributes['primary'],
                    self::$attributes['auth'],
                    City::$attributes['primary']
                )
            );
    }

    public function findByEmail($email)
    {
        $sql = '
                SELECT
                    user_id,
                    user_pwd,
                    user_prenom,
                    role_id,
                    user_photo_url,
                    FLOOR((DATEDIFF( CURDATE(), (user_birth))/365)) AS age,
                    user_gender,
                    user_valid,
                    ville_nom_reel,
                    user_mail,
                    user.ville_id as ville_id
                FROM user
                LEFT JOIN city ON (user.ville_id = city.ville_id)
                WHERE LOWER(user_mail) = LOWER(:email)
            ;';

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue('email', $email);

            return $this->db->executeStmt($stmt)->fetch();
    }

}
