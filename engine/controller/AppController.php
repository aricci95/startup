<?php
abstract class AppController extends Controller
{

    protected $_authLevel = array(
        Auth::ROLE_ADMIN,
    );

    public function __construct()
    {
        parent::__construct();

        $this->_checkSession();
    }

    private function _getNotifications()
    {
        // Vérification des nouveaux messages
        $oldMessagesCount  = $this->context->get('new_messages');

        $this->context->set('new_messages', $this->model->message->countNewMessages($this->context->get('user_id')));

        $this->context->set('notification', $this->model->query('notification')->orderBy('notification_date DESC')->select());

        if ($oldMessagesCount < $this->context->get('new_messages')) {
            $this->get('growler')->send('Nouveau message !', GROWLER_INFO);
        }
    }

    protected function _refreshLastConnexion()
    {
        // Status
        if ($this->context->get('user_id')) {
            if ($this->context->get('user_last_connexion')) {
                $now      = time();
                $left     = $this->context->get('user_last_connexion');
                $timeLeft = $now - $left;

                if ($timeLeft == 0 || $timeLeft > (ONLINE_TIME_LIMIT - 300)) {
                    $this->model->User->updateLastConnexion($this->context->get('user_id'));
                }
            } else {
                $this->model->User->updateLastConnexion($this->context->get('user_id'));
            }
        }
    }

    // Vérifie la conformité de la session
    protected function _checkSession()
    {
        return true; // @TODO REMOVE THIS

        $socialAppsData = $this->context->get('userprofile');

        if (!empty($socialAppsData['email']) && $socialAppsData['verified']) {
            if (!$this->get('Facebook')->login()) {
                session_destroy();
                $this->redirect('subscribe');
            }
        }

        // Cas user en session
        if ($this->context->get('user_valid') && $this->context->get('user_id') && $this->context->get('user_mail')) {
            if ($this->context->get('user_valid') == 1) {
                if ($this->context->get('role_id') == Auth::ROLE_SUPER_ADMIN || empty($this->_authLevel) || in_array($this->context->get('role_id'), $this->_authLevel)) {
                    return true;
                } else {
                    // Utilisateur valide mais droits insuffisants
                    $this->get('growler')->error('Authentification requise.');

                    $this->redirect('home');
                    die;
                }
            } else {
                // Message non validé
                session_destroy();

                $this->get('growler')->error('Votre email n\'a pas été validé, vous devez cliquer sur le lien qui vous a été envoyé par email.');

                $this->redirect('subscribe');
            }
        } // Cas pas d'user en session, vérification des cookies
        elseif (!empty($_COOKIE['planskiEmail']) && !empty($_COOKIE['planskiPwd'])) {
            try {
                $logResult = $this->get('auth')->checkLogin($_COOKIE['planskiEmail'], $_COOKIE['planskiPwd']);
            } catch (Exception $e) {
                $this->get('growler')->error($e->getMessage());

                $this->redirect('subscribe');
            }

            return $logResult;
        } // Cas page accès sans autorisation
        elseif (empty($this->_authLevel)) {
            return true;
        } else {
            $this->get('growler')->error('Authentification requise.');

            $this->redirect('subscribe');
            die;
        }
    }
}
