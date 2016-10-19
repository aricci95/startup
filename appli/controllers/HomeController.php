<?php


class HomeController extends AppController
{
    public function render()
    {
        $this->view->setViewName('home/wHome');
        $this->view->setTitle('Bienvenue sur Startup Simulator !');
        $this->view->render();
    }
}