<?php

class Notification extends Model
{
    const ACTION_COMMENT = 1;

    public static $messages = array(
        self::ACTION_COMMENT => '<b>%s</b> a posté un commentaire.',
    );
}