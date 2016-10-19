<?php

class NotificationController extends AppController
{
    protected $_authLevel = array(
        Auth::ROLE_SKI,
        Auth::ROLE_OWNER,
        Auth::ROLE_ADMIN,
    );

    public function renderRead()
    {
        $this->get('notification')->read($this->context->getParam('notificationId'));

        echo JSON_OK;
    }
}