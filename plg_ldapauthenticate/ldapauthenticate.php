<?php
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Authentication\AuthenticationResponse;

class plgAuthenticationLdapauthenticate extends CMSPlugin
{
    public function onUserAuthenticate($credentials, $options, &$response)
    {
        if (Factory::getApplication()->isClient('site')) {
            $username = $credentials['username'];
            $password = $credentials['password'];

            $response->username = $username;
            $response->fullname = '';
            $response->status = 'failure';
            $response->error_message = '';

            $userInfo = $this->authenticateWithLdap($username, $password);

            if ($userInfo) {
                $response->status = 'success';
                $response->username = $userInfo['uid'];
                $response->email = $userInfo['mail'];
                $response->fullname = $userInfo['cn'];
                $response->error_message = '';

                $this->syncUserWithJoomla($userInfo);

                return true;
            }
        }

        $response->error_message = 'Invalid credentials';
        return false;
    }

    protected function authenticateWithLdap($username, $password)
    {
        $ldap_server = $this->params->get('ldap_server');
        $ldap_port = $this->params->get('ldap_port');
        $ldap_admin_username = $this->params->get('ldap_admin_username');
        $ldap_admin_password = $this->params->get('ldap_admin_password');
        $ldap_base_dn = $this->params->get('ldap_base_dn');
        $ldap_uid_field = $this->params->get('ldap_uid_field');
        $ldap_auth_method = $this->params->get('ldap_auth_method');
        $ldap_fullname_field = $this->params->get('ldap_fullname_field');
        $ldap_username_field = $this->params->get('ldap_username_field');
        $ldap_email_field = $this->params->get('ldap_email_field');

        if (!extension_loaded('ldap')) {
            die("LDAP extension is not loaded.");
        }

        $ldap_url = "ldap://$ldap_server:$ldap_port";
        if ($ldap_auth_method === 'ssl') {
            $ldap_url = "ldaps://$ldap_server:$ldap_port";
        }

        $ldapconn = ldap_connect($ldap_url);

        if (!$ldapconn) {
            return false; // Could not connect to LDAP server
        }

        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

        if ($ldap_auth_method === 'starttls') {
            if (!ldap_start_tls($ldapconn)) {
                ldap_close($ldapconn);
                return false; // Failed to start TLS connection
            }
        }

        $ldapbind = ldap_bind($ldapconn, $ldap_admin_username, $ldap_admin_password);

        if (!$ldapbind) {
            ldap_close($ldapconn);
            return false; // LDAP admin bind failed
        }

        $search_filter = "($ldap_uid_field={$username})";
        $result = ldap_search($ldapconn, $ldap_base_dn, $search_filter);
        $entries = ldap_get_entries($ldapconn, $result);

        if ($entries['count'] == 0) {
            ldap_close($ldapconn);
            return false; // User not found in LDAP
        }

        $user_dn = $entries[0]['dn'];
        $ldapbind = ldap_bind($ldapconn, $user_dn, $password);

        if (!$ldapbind) {
            ldap_close($ldapconn);
            return false; // User authentication failed
        }

        $userInfo = [
            'cn' => $entries[0][$ldap_fullname_field][0],
            'uid' => $entries[0][$ldap_username_field][0],
            'mail' => $entries[0][$ldap_email_field][0]
        ];

        ldap_close($ldapconn);

        return $userInfo;
    }

    protected function syncUserWithJoomla($userInfo)
    {
        // Obtenha o ID do usuário pelo nome de usuário (username)
        $userId = UserHelper::getUserId($userInfo['uid']);
        $user = Factory::getUser($userId);
        
        // Capture os grupos do usuário a partir dos parâmetros do plugin
        $userGroups = $this->params->get('ldap_user_groups', [2, 10]); // Padrão para 2 (Registered) e 10 (LDAP)
        
       
        if ($user->id == 0) {
            // Se o usuário não existir, crie um novo usuário
            $randomPassword = UserHelper::genRandomPassword(); // Gera uma senha aleatória   
            $data = array(
                'name' => $userInfo['cn'], // Nome completo do usuário no LDAP
                'username' => $userInfo['uid'],
                'email' => $userInfo['mail'], // Email do usuário no LDAP
                'password' => $randomPassword,
                'password2' => $randomPassword,
                'password_clear' => $randomPassword,
                'groups' => $userGroups // Grupos do parâmetro
            );

            // Associa os dados do usuário ao objeto JUser
            if (!$user->bind($data)) {
                Factory::getApplication()->enqueueMessage('Erro ao vincular dados: ' . $user->getError(), 'error');
                return false;
            }

            // Salva o usuário no banco de dados
            if (!$user->save()) {
                Factory::getApplication()->enqueueMessage('Erro ao salvar o usuário: ' . $user->getError(), 'error');
                return false;
            }
        } else {
            // Se o usuário já existir, atualize seus grupos
            $user->groups = $userGroups; // Grupos do parâmetro

            if (!$user->save(true)) {
                Factory::getApplication()->enqueueMessage('Erro ao atualizar os grupos do usuário: ' . $user->getError(), 'error');
                return false;
            }
        }

        return true;
    }
}
