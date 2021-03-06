<?php

Class MessageService extends Service
{
    /**
     * Envoi un message via la messagerie
     *
     * @param  int $expediteur_id
     * @param  int $destinataire_id
     * @param  string $content
     * @return bool
     */
    public function send($expediteur_id, $destinataire_id, $content)
    {
        if (empty($expediteur_id) || empty($destinataire_id)) {
            $message = "<br/><br/>Valeurs en paramètres : <br/>";

            throw new Exception('Erreur lors de la sauvegarde du message, destinataire / expediteur manquant' . $message, ERROR_BEHAVIOR);
        }

        $content = nl2br(str_replace('\\', '', htmlspecialchars($content, ENT_QUOTES, 'utf-8')));

        $message_data = array(
            'content' => $content,
            'expediteur_id' => $expediteur_id,
            'destinataire_id' => $destinataire_id,
            'date' => date('Y-m-d H:i:s'),
            'state_id' => MESSAGE_STATUS_SENT,
            'mailbox_id' => 1,
        );

        if ($this->query('message')->insert($message_data)) {
            $destinataire = $this->query('user')->selectById($destinataire_id);
            $message      = $this->context->get('user_prenom').' vous a envoyé un nouveau message ! <a href="http://startup.fr/message/' . $this->context->get('user_id') . '">Cliquez ici</a> pour le lire.';

            return $this->get('mailer')->send($destinataire['user_mail'], 'Nouveau message sur PlanSki !', $message);
        }
    }

    /**
     * Poste un message sur le forum global
     *
     * @param  string $message
     * @param  int $authorId
     * @return bool
     */
    public function post($message)
    {
        if (empty($message)) {
            throw new Exception('message vide', ERROR_BEHAVIOR);
        }

        $message = str_replace('\\', '', htmlspecialchars($message, ENT_QUOTES, 'utf-8'));

        $message_data = array(
            'content' => $message,
            'user_id' => $this->context->get('user_id'),
            'date' => date('Y-m-d H:i:s'),
            'user_prenom' => $this->context->get('user_prenom'),
        );

        return $this->model->Forum->insert($message_data);
    }
}
