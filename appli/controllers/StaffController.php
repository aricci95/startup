<?php

class StaffController extends AppController
{
    public function render()
    {
        $this->view->setTitle('Staff')->setViewName('staff/wIndex')->render();
    }

    public function renderShow()
    {
        $employeeId = (int) $this->context->getParam('employeeId');

        $this->view->getJSONResponse('staff/wWindows');
    }
}