<?php

class NotificationController extends AppController
{
    protected $_authLevel = array();

    public function renderRead()
    {
        $this->get('notification')->read($this->context->getParam('notificationId'));

        echo JSON_OK;
    }
}