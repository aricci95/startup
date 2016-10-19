<?php

class MessageController extends AppController
{

    private function _getDestinataire($parentMessages, $userId)
    {
        if (empty($parentMessages) && $userId > 0) {
            return $this->model->User->getUserByIdDetails($userId);
        }
        // Si rajout de message de soit même
        if ($parentMessages[0]['expediteur_id'] == $this->context->get('user_id')) {
            return $this->model->User->getUserByIdDetails($parentMessages[0]['destinataire_id']);
        } // Si réponse
        else {
            return $this->model->User->getUserByIdDetails($parentMessages[0]['expediteur_id']);
        }

        // Si nouveau message, on récupère les infos du destinataire
        if (isset($this->context->params['destinataire_id'])) {
            return $this->model->User->getUserByIdDetails($this->context->params['destinataire_id']);
        } elseif (isset($this->context->params['user_id'])) {
            return $this->model->User->getUserByIdDetails($this->context->params['user_id']);
        }
    }

    private function _checkMessages($parentMessages, $destinataireId)
    {
        $contextUserId = $this->context->get('user_id');

        // Si nouvelle conversation
        if (empty($parentMessages)) {
            return true;
        }

        if ($parentMessages[0]['destinataire_id'] != $contextUserId && $parentMessages[0]['expediteur_id'] != $contextUserId) {
            Log::hack('tentative d\'accès a une conversation étrangère.');
            return false;
        }

        foreach ($parentMessages as $key => $value) {
            $parentMessages[$key]['content'] = Tools::toSmiles($value['content']);

            // Si nouveau message, alors on le mets en état lu
            if ($value['state_id'] == MESSAGE_STATUS_SENT && $value['expediteur_id'] != $contextUserId) {
                $this->model->message->updateMessageState($value['message_id'], MESSAGE_STATUS_READ);

                if ($this->context->get('new_messages') > 0) {
                    $this->context->set('new_messages', $this->context->get('new_messages') - 1);
                }
            }
        }

        return true;
    }

    public function render()
    {
        $this->view->addJS(JS_SCROLL_REFRESH);

        if (empty($this->context->params['value'])) {
            $this->get('growler')->error('Message introuvable');

            $this->redirect('mailbox');
        }

        $userId = $this->context->params['value'];

        $parentMessages = $this->model->message->getConversation($userId);


        if ($this->_checkMessages($parentMessages, $userId)) {
            $this->view->parentMessages  = $parentMessages;
            $this->view->destinataire = $this->_getDestinataire($parentMessages, $userId);

            $this->view->setTitle(ucfirst($this->view->destinataire['user_prenom']));
            $this->view->setViewName('message/wMain');
            $this->view->render();
        } else {
            $this->get('growler')->error('Message introuvable');

            $this->redirect('mailbox');
        }
    }

    public function renderSubmit()
    {
        if (!empty($this->context->params['last_content']) && !empty($this->context->params['content']) && $this->context->params['last_content'] === $this->context->params['content']) {
            $this->render();
            return;
        }

        if (empty($this->context->params['destinataire_id'])) {
            Log::err('destinataire vide');
            $this->get('growler')->error();

            $this->redirect('mailbox');
        }

        $from = $this->context->get('user_id');
        $to   = $this->context->params['destinataire_id'];

        if (empty($this->context->params['content'])) {
            $this->get('growler')->send('Message vide.', GROWLER_INFO);
            $this->render();

            return;
        }


        if ($this->get('message')->send($from, $to, $this->context->params['content'])) {
            $message = $this->context->get('user_prenom') . ' vous a envoyé un nouveau message ! <a href="http://startup.fr/message/' . $this->context->get('user_id') . '">Cliquez ici</a> pour le lire.';

            $this->get('growler')->send('Message envoyé.');

            $this->redirect('message', array($this->context->params['value']));
        } else {
            Log::err('impossible d\'enregistrer le message.');
            $this->get('growler')->error('Le message n\'a pas pu être envoyé, merci de réessayer.');

            $this->redirect('mailbox');
        }

        return;
    }

    public function renderMore()
    {
        if (empty($this->context->params['value']) || empty($this->context->params['option'])) {
            return;
        }

        $offset = $this->context->params['value'];
        $userId = $this->context->params['option'];

        // On récupère les information du message
        $parentMessages = $this->model->message->getConversation($userId, $offset);

        if (count($parentMessages) > 0) {
            $this->_checkMessages($parentMessages, $userId);
            $this->view->parentMessages  = $parentMessages;
            $this->view->destinataire = $this->_getDestinataire($parentMessages, $userId);
            $this->view->offset = $offset++;
            $this->view->getJSONResponse('message/wItems');
        } else {
            return;
        }
    }
}
