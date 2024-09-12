<?php
// Certifique-se de que o Joomla Framework foi carregado
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Response\JsonResponse;

// Obtenha a sessão e limpe os dados do LDAP
$session = Factory::getSession();
$session->clear('ldap_user');

// Retorne uma resposta JSON de sucesso
echo new JsonResponse(['success' => true]);
exit();
