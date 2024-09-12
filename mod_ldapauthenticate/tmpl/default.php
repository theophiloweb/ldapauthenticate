<?php defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

global $response; // Declara a variável como global
$session = Factory::getSession();
$response = $session->get('ldap_user'); // Recupera o $response da sessão

// Verifica se o usuário está logado
$user = Factory::getUser();
$loggedIn = !$user->guest;
?>

<div id="loginModal" class="modal">
    <div class="modal-content">
        <?php if ($loggedIn): ?>
            <p><strong>Seu Login:</strong> <?php echo htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Seu Email:</strong> <?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Seu Nome:</strong> <?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?></p>
            <h3>Grupos do Usuário:</h3>
            <ul>
                <?php foreach ($user->groups as $groupId): ?>
                    <li><?php echo htmlspecialchars($groupId, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php if ($params->get('show_logout_button')): ?>
                <form action="<?php echo Route::_('index.php'); ?>" method="post" id="logout-form" class="form-inline">
                    <input type="hidden" name="option" value="com_users">
                    <input type="hidden" name="task" value="user.logout">
                    <input type="hidden" name="return" value="<?php echo base64_encode(JUri::base()); ?>">
                    <?php echo HTMLHelper::_('form.token'); ?>
                    <button type="submit" name="Submit" class="login login-btn ldap-logout-button btn btn-secondary">Sair</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <form action="<?php echo Route::_('index.php'); ?>" method="post" class="ldap-login-form">
                <?php echo HTMLHelper::_('form.token'); ?>
                <input type="hidden" name="option" value="mod_ldapauthenticate">
                <input type="hidden" name="task" value="user.login">
                <input type="hidden" name="return" value="<?php echo base64_encode($params->get('login_redirect_url', JUri::base())); ?>">
                <div class="ldap-login-form-group">
                    <input type="text" name="username" id="username" class="ldap-login-input" placeholder="Usuário" required>
                </div>
                <div class="ldap-login-form-group">
                    <input type="password" name="password" id="password" class="ldap-login-input" placeholder="Senha" required>
                </div>
                <button type="submit" class="login login-btn ldap-login-button">Autenticar</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<button id="loginBtn" class="login-btn btn" style="background-color: <?php echo $loggedIn ? 'green' : 'blue'; ?>;">
    <?php echo $loggedIn ? 'Sair' : 'Entrar'; ?>
</button>



<style>
/* Estilos para o modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 400px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.ldap-login-form {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.ldap-login-form-group {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-bottom: 10px;
    width: 100%;
}

.ldap-login-input {
    background-color: #f5f5f5;
    border: none;
    border-radius: 5px;
    box-shadow: inset 0px 0px 5px rgba(0, 0, 0, 0.1);
    color: #333;
    font-size: 14px;
    padding: 10px;
    text-align: center;
    transition: all 0.3s ease;
    width: 100%;
}

.ldap-login-input:focus {
    background-color: #fff;
    box-shadow: inset 0px 0px 5px rgba(0, 0, 0, 0.1), 0px 0px 10px rgba(0, 0, 0, 0.3);
}

.login-btn {
    border: none;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
    color: #fff;
    cursor: pointer;
    font-size: 14px;
    padding: 10px 20px;
    text-align: center;
    text-transform: uppercase;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.login {
    background-color: blue;
}

.logout {
    background-color: green;
}


/*
.ldap-login-button, .ldap-logout-button, .login-btn {
    background-color: #333;
    border: none;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
    color: #fff;
    cursor: pointer;
    font-size: 14px;
    padding: 10px 20px;
    text-align: center;
    text-transform: uppercase;
    transition: all 0.3s ease;
    margin-top: 10px;
}
*/

.ldap-login-button:hover, .ldap-logout-button:hover, .login-btn:hover {
    background-color: #555;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('loginModal');
    var btn = document.getElementById('loginBtn');

    btn.onclick = function() {
        modal.style.display = 'flex';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
});
</script>

