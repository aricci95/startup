<?php

class LostpwdController extends AppController
{
    protected $_authLevel = array();

    public function render()
    {
        if (!empty($this->context->params['value'])) {
            $this->view->setViewName('lostPwd/wNewPwd');
            $this->view->setTitle('Modification du mot de passe');
            $this->view->pwd_valid = $this->context->params['value'];
        } else {
            $this->view->setViewName('lostPwd/wLostPwd');
            $this->view->setTitle('Récupération des identifiants');
        }

        $this->view->render();
    }

    public function renderNew()
    {
        if (empty($this->context->params['value']) && empty($this->context->params['pwd_valid'])) {
            $this->get('growler')->error('Le champs est vide');
            $this->render();
        } else {
            $this->view->setViewName('lostPwd/wNewPwd');
            $this->view->setTitle('Modification du mot de passe');
            $this->view->pwd_valid = empty($this->context->params['pwd_valid']) ? $this->context->params['value'] : $this->context->params['pwd_valid'];
            $this->view->render();
        }
    }

    public function renderSubmit()
    {
        if (!empty($this->context->params['user_mail'])) {
            if ($this->get('auth')->sendPwd($this->context->params['user_mail'])) {
                $this->get('growler')->send('Vos identifiants ont étés envoyés par mail.');

                $this->redirect('subscribe');
            } else {
                $this->get('growler')->error('Email introuvable.');
                $this->render();
            }
        } else {
            $this->get('growler')->error('Le champs est vide');
            $this->render();
        }
    }

    public function renderSubmitNew()
    {
        if (empty($this->context->params['pwd_valid'])
         || empty($this->context->params['user_pwd'])
         || empty($this->context->params['pwd_confirm'])
         || $this->context->params['user_pwd'] != $this->context->params['pwd_confirm']) {
            $this->get('growler')->error('Les deux champs doivent être identiques.');

            $this->renderNew();
        } else {
            if ($this->model->auth->updatePwd($this->context->params['user_pwd'], $this->context->params['pwd_valid'])) {
                $this->get('growler')->send('Votre mot de passe a été modifié.');

                $this->redirect('subscribe');
            } else {
                $this->get('growler')->error();

                $this->render();
            }
        }
    }
}
