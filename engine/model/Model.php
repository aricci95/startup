<?php

abstract class Model
{

    protected $db;
    protected $context;

    private $_queryBuilder;

    public function __construct(Db $db)
    {
        $this->db      = $db;
        $this->context = Context::getInstance();
    }

    public function query($table = null)
    {
        if (empty($table)) {
            $table = strtolower(get_class($this));
        }

        $this->_queryBuilder = new QueryBuilder($this->db, $table);

        return $this->_queryBuilder;
    }

    public function getBuilder()
    {
        $this->_queryBuilder;
    }

}
