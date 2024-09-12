<?php
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Authentication\AuthenticationResponse;
use Joomla\CMS\User\UserHelper;

PluginHelper::importPlugin('authentication', 'ldapauthenticate');

$app = Factory::getApplication();
$input = $app->input;

$credentials = array();
$credentials['username'] = $input->getString('username', '');
$credentials['password'] = $input->getString('password', '');

$options = array();
$options['remember'] = $input->getBool('remember', false);

global $response; // Declara a variável como global
$response = new AuthenticationResponse();

$results = $app->triggerEvent('onUserAuthenticate', array($credentials, $options, &$response));

if ($response->status === 'success') {    
    $session = Factory::getSession();
    $session->set('ldap_user', $response);

    // Obtenha o ID do usuário e carregue a instância do usuário
    $userId = UserHelper::getUserId($response->username);
    $user = Factory::getUser($userId);

    // Sincronize os grupos do usuário
    $user->groups = UserHelper::getUserGroups($userId);
    
    // Atualize a sessão com as informações do usuário
    $session->set('user', $user);

    // Redirecione para a página desejada após o login
    $app->redirect(Route::_('index.php'), 302);
} else {
    $app->enqueueMessage($response->error_message ?: 'Falha na autenticação LDAP', 'error');
    $app->redirect(Route::_('index.php'), 302);
    //$app->redirect(Route::_('index.php?option=com_users&view=login'), 302);
}
?>
