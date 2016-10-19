<?php

class Context
{

    private static $_instance = null;

    private $_sessionData = array();

    public  $params = array();

    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        $init_vars = array(
            'user_id' => 0,
            'role_id' => 0,
            'user_photo_url' => 'unknowUser.jpg',
            'new_messages' => 0,
        );

        $this->_sessionData = array_merge($init_vars, $_SESSION);
    }

    public function buildParams()
    {
        unset($_GET['page']);
        unset($_GET['action']);
        unset($_POST['x']);
        unset($_POST['y']);

        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                if (!is_array($value)) {
                    $value = trim($value);
                }

                $this->params[$key] = $value;
            }
        }

        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                if (!is_array($value)) {
                    $value = trim($value);
                }
                $this->params[$key] = $value;
            }
        }

        return $this->params;
    }

    public function getParam($param)
    {
        if (isset($this->params[$param])) {
            return $this->params[$param];
        } else {
            return null;
        }
    }

    public function set($key, $value)
    {
        $this->_sessionData[$key] = $_SESSION[$key] = $value;

        return $this;
    }

    public function get($key)
    {
        if (!isset($this->_sessionData[$key])) {
            $this->set($key, null);
        }

        return $this->_sessionData[$key];
    }

    public function delete($key)
    {
        if (isset($this->_sessionData[$key])) {
            unset($this->_sessionData[$key]);
        }

        return true;
    }

    public function destroy()
    {
        $this->_sessionData = $_SESSION = array();

        session_destroy();

        return true;
    }

}
