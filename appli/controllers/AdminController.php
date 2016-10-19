<?php

class AdminController extends AppController
{

    protected $_authLevel = array(
        Auth::ROLE_ADMIN,
    );

    public function render()
    {
        $this->view->setTitle('Administration');
        $this->view->setViewName('admin/wAdmin');
        $this->view->render();
    }

    public function renderSwitch()
    {
        $this->view->users = $this->model->query('user')
                                  ->where(array('!user_id' => $this->context->get('user_id'), 'user_valid' => 1))
                                  ->orderBy(array('user_prenom', 'user_nom'))
                                  ->select();

        $this->view->action = 'setSwitch';
        $this->view->setTitle('User switch');
        $this->view->setViewName('admin/wUsers');
        $this->view->render();
    }

    public function renderSetSwitch()
    {
        if (!empty($this->context->params['user_id'])) {

            $user = $this->model->query('user')->selectById($this->context->params['user_id']);

            if (!empty($user)) {
                if ($user['user_valid'] != 1) {
                    $this->get('growler')->send('Utilistateur non validé.', GROWLER_ERR);
                } else {
                    $this->context->set('user_id', $user['user_id'])
                                  ->set('user_prenom', $user['user_prenom'])
                                  ->set('role_id', $user['role_id'])
                                  ->set('user_photo_url', $user['user_photo_url'])
                                  ->set('user_valid', $user['user_valid'])
                                  ->set('user_mail', $user['user_mail'])
                                  ->set('user_gender', $user['user_gender'])
                                  ->set('forum_notification', $user['forum_notification']);

                    $this->get('growler')->send('Vous avez changé votre utilisateur courant.');

                    $this->redirect('user');
                }
            }
        } else {
            $this->get('growler')->error();
        }
        $this->render();
    }

    public function renderDeleteUser()
    {
        $this->view->users = $this->model->query('user')
                                  ->where(array('!user_id' => $this->context->get('user_id'), 'user_valid' => 1))
                                  ->orderBy(array('user_prenom', 'user_nom'))
                                  ->select();

        $this->view->action = 'removeUser';
        $this->view->setTitle('Supprimer un utilisateur');
        $this->view->setViewName('admin/wUsers');
        $this->view->render();
    }

    public function renderRemoveUser()
    {
        if (!empty($this->context->params['user_id']) && $this->get('user')->delete($this->context->params['user_id'])) {
            $this->get('growler')->send('Utilisateur supprimé.');
        } else {
            $this->get('growler')->error();
        }

        $this->render();
    }

    public function renderMessage()
    {
        $this->view->setTitle('Message à tous les users');
        $this->view->setViewName('admin/wMessage');
        $this->view->render();
    }

    public function renderMessageSubmit()
    {
        if (!empty($this->context->params['content'])) {
            $from    = $this->context->get('user_id');
            $users   = $this->model->query('user')
                            ->where(array('!user_id' => $this->context->get('user_id'), 'user_valid' => 1))
                            ->select();

            $sentMessages = 0;
            foreach ($users as $user) {
                if ($this->get('message')->send($from, $user['user_id'], $this->context->params['content'])) {
                    $sentMessages++;
                }
            }

            if ($sentMessages > 0) {
                $this->get('growler')->send($sentMessages.' Emails envoyés.', GROWLER_OK);
            } else {
                $this->get('growler')->error();
            }
        } else {
            $this->view->growlerError('Le message vide.');
        }

        $this->render();
    }
}
