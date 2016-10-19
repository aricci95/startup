<?php
class AuthController extends AppController
{

    protected $_authLevel = array();

    public function renderLogin()
    {
        if (!empty($this->context->params['user_mail']) && !empty($this->context->params['user_pwd'])) {
            try {
                $authentResult = $this->get('auth')->login($this->context->params['user_mail'], $this->context->params['user_pwd']);

                if ($authentResult) {
                    if ($this->context->get('role_id') == User::TYPE_OWNER) {
                        $this->redirect('location');
                    } else {
                        $this->redirect('crew');
                    }
                }
            } catch (Exception $e) {
                Log::err($e->getMessage());

                $this->get('growler')->error($e->getMessage());

                $this->redirect('subscribe');
            }
        }

        $this->get('growler')->error('Mauvais email / mot de passe.');

        $this->redirect('subscribe');
    }

    public function renderDisconnect()
    {
        if ($this->get('auth')->disconnect()) {
            $this->redirect('subscribe');
        } else {
             $this->get('growler')->error();
        }
    }
}
