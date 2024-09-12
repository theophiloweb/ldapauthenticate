<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

// Carrega o arquivo de idioma
/*
$lang = Factory::getLanguage();
$lang->load('mod_ldapauthenticate', __DIR__, 'pt-BR', true);
*/
$lang = Factory::getLanguage();
$extension = 'mod_ldapauthenticate';
$base_dir = JPATH_SITE;
$language_tag = $lang->getTag();
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);


// Registra o handler de tarefas
$app = Factory::getApplication();
$task = $app->input->getCmd('task');
$session = Factory::getSession();

// Lógica de login
if ($task == 'user.login') {
    require_once __DIR__ . '/src/ldapauthenticate.php';
    if ($response->status === 'success') {
        $app->enqueueMessage(Text::_('MOD_LDAPAUTHENTICATE_LOGIN_SUCCESS'), 'success');        
    } else {
        $app->enqueueMessage(Text::_('MOD_LDAPAUTHENTICATE_LOGIN_FAILED'), 'error');        
    }
}


// Lógica de logout
if ($task == 'user.logout') {
    $session->clear('ldap_user');
    $session->clear('user');
    $app->logout();
    $message = $lang->_('MOD_LDAPAUTHENTICATE_LOGOUT_SUCCESS');
    $app->enqueueMessage($message, 'success');
    //$app->enqueueMessage(Text::_('MOD_LDAPAUTHENTICATE_LOGOUT_SUCCESS'), 'success');
    $app->redirect(Route::_('index.php'), 302);
}


// Obtém a instância do usuário logado
$ldap_user = $session->get('ldap_user');
if ($ldap_user && !empty($ldap_user->username)) {
    $userId = UserHelper::getUserId($ldap_user->username);
    $user = Factory::getUser($userId);
    // Atualize os grupos do usuário
    if ($user->id) {
        $user->groups = UserHelper::getUserGroups($userId);
        $loggedIn = true;
    } else {
        $user = null;
        $loggedIn = false;
    }
} else {
    $user = null;
    $loggedIn = false;
}

// Caminho para o arquivo de layout do módulo
$layoutPath = ModuleHelper::getLayoutPath('mod_ldapauthenticate', 'default');

// Dados a serem passados para o layout
$layoutData = array(
    'user' => $user,
    'loggedIn' => $loggedIn,
    'params' => $params,
);

require $layoutPath;
?>
