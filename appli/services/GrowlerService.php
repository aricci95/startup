<?php

class GrowlerService extends Service
{
    public $messages = array();

    public function __construct()
    {
        parent::__construct();

        $this->messages = $this->context->get('messages');

        $this->context->set('messages', array());
    }

    public function hasMessage()
    {
        return (count($this->messages) > 0);
    }

    public function record()
    {
        $this->context->set('messages', $this->messages);

        $this->messages = array();

        return $this;
    }

    public function send($message, $type = GROWLER_OK, $title = '')
    {
        $this->messages[] = array(
            'content' => $message,
            'type' => $type,
            'title' => $title,
        );

        return $this;
    }

    public function error($content = MESSAGE_400)
    {
        $this->send($content, GROWLER_ERR);

        return $this;
    }

    public function getMessages()
    {
        if (!is_array($this->messages)) {
            return false;
        }

        $generatedMessages = array();

        foreach ($this->messages as $message) {
            $generatedMessages[] = $this->generate($message);
        }

        return $generatedMessages;
    }

    public function generate(array $message = array())
    {
        $script_message = "<script>
            $(function(){
                $.gritter.add({
            ";

        if (!empty($message['title'])) {
            $script_message .= "title: '$title',";
        }

        $script_message .= "
                    text:  '" . $message['content'] . "',
                    class_name : 'gritter-". $message['type'] ."'
                });
            });
            </script>";

        return $script_message;
    }
}
