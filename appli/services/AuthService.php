<?php

class AuthService extends Service
{

    public function login($email, $pwd)
    {
        $email = trim($this->context->params['user_mail']);

        $logResult = $this->checkLogin($email, md5($this->context->params['user_pwd']));

        if ($logResult) {
            if ($this->context->getParam('savepwd') == 'on') {
                setcookie('startupEmail', $this->context->getParam('user_prenom'), time() + 365*24*3600, '/', null, false, true);
                setcookie('startupPwd', md5($this->context->getParam('user_pwd')), time() + 365*24*3600, '/', null, false, true);
            } else {
                setcookie('startupEmail', 0, time(), '/', false, true);
                setcookie('startupPwd', 0, time(), '/', false, true);
            }

            return true;
        }

        return false;
    }

    public function checkLogin($email, $pwd)
    {
        $user = $this->model->user->findByEmailPwd($email, $pwd);

        if (!empty($user['user_mail']) && !empty($user['user_id']) && strtolower($user['user_mail']) == strtolower($email) && $email != '') {
            if ($user['user_valid'] != 1) {
                throw new Exception("Email non validé");
            } elseif ($user['role_id'] > 0) {
                $this->model->user->updateLastConnexion();

                if (!empty($ville)) {
                    $user['ville_longitude_deg'] = $ville['ville_longitude_deg'];
                    $user['ville_latitude_deg'] = $ville['ville_latitude_deg'];
                }

                return $this->authenticateUser($user);
            }
        } else {
            throw new Exception("Mauvais email / mot de passe");
        }

        return false;
    }

    public function authenticateUser(array $user)
    {
        $this->context->set('user_id', (int) $user['user_id'])
                      ->set('user_prenom', $user['user_prenom'])
                      ->set('user_last_connexion', time())
                      ->set('user_mail', $user['user_mail'])
        return true;
    }

    public function sendPwd($email)
    {
        $user = $this->query('user')
                     ->single()
                     ->where(array('user_mail' => $email))
                     ->select(array('user_id', 'user_prenom', 'user_mail'));

        if (!empty($user['user_prenom'])) {
            $pwd_valid = $this->model->auth->resetPwd($user['user_id']);

            $message = 'Pour modifier ton mot de passe clique sur le lien suivant : <a href="http://www.startup.fr/lostpwd/new/' . $pwd_valid . '">modifier mon mot de passe</a>';

            return $this->get('mailer')->send($user['user_mail'], 'Modifcation du mot de passe startup', $message);
        } else {
            return false;
        }
    }

    public function disconnect()
    {
        setcookie('startupEmail', 0, time(), '/', false, true);
        setcookie('startupPwd', 0, time(), '/', false, true);

        $this->context->destroy();

        return true;
    }
}
