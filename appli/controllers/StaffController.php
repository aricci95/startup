<?php

class StaffController extends AppController
{
    public function render()
    {
        $this->view->getJSONResponse('staff/wIndex');
    }

    public function renderShow()
    {
        $employeeId = (int) $this->context->getParam('employeeId');

        $this->view->getJSONResponse('staff/wWindows');
    }
}