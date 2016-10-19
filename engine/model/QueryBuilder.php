<?php

class QueryBuilder
{
    protected $db;

    public $sql;
    public $stmt;
    public $table;
    public $begin;
    public $offset;
    public $where = array();
    public $orderBy = array();
    public $single = false;
    public $join = array();
    public $leftJoin = array();
    public $between = array();
    public $lowerThan = array();

    public function __construct(Db $db,$table)
    {
        $this->db = $db;
        $this->table($table);
    }

    public function getQuery()
    {
        return nl2br($this->sql);
    }

    private function _cleanIndex($results)
    {
        foreach ($results as $key => $objects) {
            foreach ($objects as $index => $object) {
                if (is_int($index)) {
                    unset($results[$key][$index]);
                }
            }
        }

        return $results;
    }

    public function table($table)
    {
        $this->table = trim(strtolower($table));

        return $this;
    }

    private function _addWhereClause()
    {
        if (!empty($this->where)) {
            $this->sql .= ' WHERE TRUE ';

            foreach ($this->where as $key => $value) {
                if (strpos($key, '.')) {
                    $explode = explode('.', $key);
                    $index = $explode[1];
                } else {
                    $index = $key;
                }

                if (strpos($index, '!') === 0) {
                    $key = str_replace('!', '', $key);
                    $index = str_replace('!', '', $index);

                    $this->sql .= " AND $key != :$index
                    ";
                } else if (strpos($index, '%') === 0) {
                    $key = str_replace('%', '', $key);
                    $index = str_replace('%', '', $index);

                    $this->sql .= " AND $key LIKE :$index
                    ";
                } else {
                    $this->sql .= " AND $key = :$index
                    ";
                }
            }
        }
    }

    private function _bindWhereValues()
    {
        if (!empty($this->where)) {
            foreach ($this->where as $key => $value) {
                if (strpos($key, '.')) {
                    $explode = explode('.', $key);
                    $index = $explode[1];
                } else {
                    $index = $key;
                }

                if (strpos($index, '%') === 0) {
                    $this->stmt->bindValue(str_replace('%', '', $index), '%' . $value . '%');
                } else if (strpos($index, '!') === 0) {
                    $this->stmt->bindValue(str_replace('!', '', $index), $value);
                } else {
                    $this->stmt->bindValue($index, $value);
                }
            }
        }
    }

    public function select(array $attributes = array())
    {
        $attributes_string = empty($attributes) ? '*' : implode(',
            ', $attributes);

        $this->sql = '
            SELECT
                ' . $attributes_string . '
            FROM
                ' . $this->table . '
            ';

        if (!empty($this->join)) {
            $index = 0;

            foreach (array_reverse($this->join) as $key => $value) {
                if (is_int($key)) {
                    $this->sql .= ' JOIN ' . $value . ' ON (' . $this->table . '.' . $this->table . '_id = ' . $value . '.' . $value . '_id)
                    ';
                } else {
                    $arrayKeys = array_reverse(array_keys($this->join));

                    $prevTable = empty($arrayKeys[$index - 1]) ? $this->table : $arrayKeys[$index - 1];

                    $this->sql .= ' JOIN ' . $key . ' ON (' . $prevTable . '.' . $value . ' = ' . $key . '.' . $value . ')
                    ';

                    $index++;
                }
            }
        }

        if (!empty($this->leftJoin)) {
            $index = 0;

            foreach (array_reverse($this->leftJoin) as $key => $value) {
                if (is_int($key)) {
                    $this->sql .= ' LEFT JOIN ' . $value . ' ON (' . $this->table . '.' . $this->table . '_id = ' . $value . '.' . $value . '_id)
                    ';
                } else {
                    $arrayKeys = array_keys($this->leftJoin);

                    if (empty($arrayKeys[$index - 1])) {
                        if (empty($this->join)) {
                            $prevTable = $this->table;
                        } else {
                            $keys = array_keys($this->join); // @TODO PEUT ETRE ENLEVER LE REVERSE
                            $prevTable = $keys[0];
                        }
                    } else {
                        $prevTable = $arrayKeys[$index - 1];
                    }

                    $this->sql .= ' LEFT JOIN ' . $key . ' ON (' . $prevTable . '.' . $value . ' = ' . $key . '.' . $value . ')
                    ';

                    $index++;
                }
            }
        }

        $this->_addWhereClause();

        if (!empty($this->between)) {
            foreach ($this->between as $key => $value) {
                $this->sql .= ' AND ' . $key . ' BETWEEN :' . $key . '_begin AND :'  . $key . '_end
                ';
            }
        }

        if (!empty($this->lowerThan)) {
            foreach ($this->lowerThan as $key => $value) {
                $this->sql .= " AND $key < :$key
                ";
            }
        }

        if (!empty($this->orderBy)) {
            $this->sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if (!empty($this->begin) && !empty($this->offset)) {
            $this->sql .= ' LIMIT :begin, :offset ';
        }

        $this->stmt = $this->db->prepare($this->sql);

        $this->_bindWhereValues();

        if (!empty($this->lowerThan)) {
            foreach ($this->lowerThan as $key => $value) {
                $this->stmt->bindValue($key, $value);
            }
        }

        if (!empty($this->between)) {
            foreach ($this->between as $key => $value) {
                $this->stmt->bindValue($key . '_begin', $value['begin']);
                $this->stmt->bindValue($key . '_end', $value['end']);
            }
        }

        if (!empty($this->begin) && !empty($this->offset)) {
            $this->stmt->bindValue('begin', $this->begin, PDO::PARAM_INT);
            $this->stmt->bindValue('offset', $this->offset, PDO::PARAM_INT);
        }

        $results =  $this->_cleanIndex($this->db->executeStmt($this->stmt)->fetchAll());

        return ($this->single && !empty($results)) ? $results[0] : $results;
    }

    public function selectById($id, array $attributes = array())
    {
        return $this->single()->where(array($this->table . '.' . $this->table . '_id' => $id))->select($attributes);
    }

    public function update(array $attributes = array())
    {
        $this->sql = 'UPDATE ' . $this->table . ' SET ';

        foreach ($attributes as $key => $value) {
            if (!is_int($key)) {
                $update[] = $key . ' = ' . ':' . $key;
            }
        }

        $this->sql .= implode($update, ', ');

        $this->_addWhereClause();

        $this->stmt = $this->db->prepare($this->sql);

        foreach ($attributes as $key => $value) {
            if (!is_int($key)) {
                $this->stmt->bindValue(':' . $key, $value);
            }
        }

        $this->_bindWhereValues();

        $this->sql = str_replace(', WHERE', ' WHERE', $this->sql);

        return $this->db->executeStmt($this->stmt);
    }

    public function updateById($id, array $attributes = array())
    {
        $this->sql = 'UPDATE ' . $this->table . ' SET ';

        foreach ($attributes as $key => $value) {
            if (!is_int($key)) {
                $update[] = $key . ' = ' . ':' . $key;
            }
        }

        $this->sql .= implode($update, ', ');

        $this->sql .= ' WHERE ' . $this->table . '_id = :id;';

        $this->stmt = $this->db->prepare($this->sql);

        foreach ($attributes as $key => $value) {
            if (!is_int($key)) {
                $this->stmt->bindValue(':' . $key, $value);
            }
        }

        $this->stmt->bindValue('id', $id, PDO::PARAM_INT);

        return $this->db->executeStmt($this->stmt);
    }

    public function insert(array $values = array())
    {
        $this->sql = 'INSERT INTO ' . $this->table . ' (' . implode(', ', array_keys($values)) . ')  VALUES (';

        $valuesToBind = array();
        foreach ($values as $key => $value) {
            $valuesToBind[] = ':' . $key;
        }

        $this->sql .= implode(', ', $valuesToBind) .' );';

        $this->stmt = $this->db->prepare($this->sql);

        foreach ($values as $key => $value) {
            if (is_int($value)) {
                $this->stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else if ($value == '') {
                $this->stmt->bindValue($key, null);
            } else {
                $this->stmt->bindValue($key, $value);
            }
        }

        $this->db->executeStmt($this->stmt);

        return $this->db->lastInsertId();
    }

    public function delete()
    {
        if (empty($this->where)) {
            throw new Exception('Delete query must have a where clause.', 1);
        }

        $this->sql = 'DELETE FROM ' . $this->table . ' ';

        $this->_addWhereClause();

        $this->stmt = $this->db->prepare($this->sql);

        $this->_bindWhereValues();

        return $this->db->executeStmt($this->stmt);
    }

    public function where(array $params = array())
    {
        $this->where = array_merge($params, $this->where);

        return $this;
    }

    public function between(array $params = array())
    {
        $this->between = array_merge($params, $this->between);

        return $this;
    }

    public function lowerThan($params)
    {
        $this->lowerThan = array_merge($params, $this->lowerThan);

        return $this;
    }

    public function single($value = true)
    {
        $this->single = $value;

        return $this;
    }

    public function join($params)
    {
        if (!is_array($params)) {
            $params = array($params);
        }

        $this->join = array_merge($params, $this->join);

        return $this;
    }

    public function leftJoin($params)
    {
        if (!is_array($params)) {
            $params = array($params);
        }

        $this->leftJoin = array_merge($params, $this->leftJoin);

        return $this;
    }

    public function orderBy($params)
    {
        if (!is_array($params)) {
            $params = array($params);
        }

        $this->orderBy = array_merge($params, $this->orderBy);

        return $this;
    }

    public function limit($begin, $offset)
    {
        $this->begin = $begin;
        $this->offset = $offset;

        return $this;
    }
}