<?php

abstract class Controller
{

    public $view;
    public $model;
    public $container;
    public $context;

    protected $_JS = array();

    public function __construct()
    {
        $this->context   = Context::getInstance();
        $this->model     = Model_Manager::getInstance();
        $this->container = Service_Container::getInstance();

        $this->view = new AppView($this->container, $this->context);

        $this->view->page   = (!empty($_GET['page'])) ? strtolower($_GET['page']) : 'user';
        $this->view->action = (!empty($_GET['action'])) ? strtolower($_GET['page']) : 'index';

        if (!empty($this->_JS)) {
            $this->addJSLibraries();
        }

        $this->context->buildParams();
    }

    private function _setContext()
    {
        $init_vars = array(
            'user_id' => null,
            'user_prenom' => null,
            'user_email' => null,
            'user_photo_url' => 'unknowUser.jpg',
        );

        $this->context = array_merge($_SESSION, $init_vars);
    }

    public function get($service)
    {
        return $this->container->get($service);
    }

    public function isAjax()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getView()
    {
        return $this->view;
    }

    public function addJSLibraries()
    {
        foreach ($this->_JS as $library) {
            $this->view->addJS($library);
        }
    }

    public function redirect($page = 'user', $params = null, $action = '')
    {
        if (!empty($this->get('growler')->hasMessage())) {
            $this->get('growler')->record();
        }

        $url = "/$page";
        if (!empty($action)) {
            $url .= '/'.$action;
        }
        if (is_array($params) && count($params > 0)) {
            foreach ($params as $key => $val) {
                $url .= '/'.$val;
            }
        }
        header("Location: $url");
        die();
    }
}
