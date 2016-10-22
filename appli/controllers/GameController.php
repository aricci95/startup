<?php

class GameController extends AppController
{
    public function render()
    {
        $this->view->setViewName('game/wIndex')->render();
    }

    public function renderShow()
    {
        $employeeId = (int) $this->context->getParam('employeeId');

        $this->view->getJSONResponse('staff/wWindows');
    }
}