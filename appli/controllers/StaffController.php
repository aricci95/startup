<?php

class StaffController extends AppController
{
    public function render()
    {
        $this->view->setTitle('Staff')->setViewName('staff/wIndex')->render();
    }
}