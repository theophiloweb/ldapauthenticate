# Joomla 5 LDAP Authentication Module & Plugin

[![Joomla Version](https://img.shields.io/badge/Joomla-5.0-orange)](https://www.joomla.org/)
[![PHP Version](https://img.shields.io/badge/PHP-8.2-blue)](https://www.php.net/releases/8.2/)
[![License](https://img.shields.io/badge/License-GNU%20GPL%20v2.0-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-Connect-blue)](https://www.linkedin.com/in/teophilo-silva-dev)
[![Email](https://img.shields.io/badge/Email-Contact-red)](mailto:teophilo@gmail.com)

Este repositório contém um módulo e plugin personalizados para Joomla 5 que implementam autenticação de usuários via LDAP. Este projeto é ideal para administradores de sistemas que utilizam Joomla como CMS e necessitam integrar a autenticação com um servidor LDAP.

## 📋 Funcionalidades

- **Autenticação LDAP Segura:** Autentica os usuários com segurança em um servidor LDAP utilizando métodos de autenticação como SSL e StartTLS.
- **Sincronização Automática de Usuários:** Cria automaticamente um usuário Joomla se ele não existir no banco de dados e sincroniza os grupos de usuários de acordo com os parâmetros de configuração.
- **Gestão de Sessões Seguras:** Gera e gerencia sessões de usuários de forma segura, tanto na autenticação quanto na desconexão.
- **Feedbacks para o Usuário:** Exibe mensagens claras de sucesso e erro durante o login e logout.

## 📦 Instalação

### Requisitos

- Joomla 5.x
- PHP 8.2 ou superior
- Servidor LDAP configurado

### Passo a Passo

1. **Baixar o Repositório:**
   ```bash
   git clone https://github.com/seuusuario/joomla-ldap-authentication.git

2. **Instalar o Módulo e o Plugin:

- No backend do Joomla, vá para **Extensões > Gerenciar > Instalar**.
- Carregue os arquivos ZIP do módulo e do plugin.

3. **Configurar o Plugin LDAP:

- Vá para **Extensões > Plugins** e procure por **Authentication - LDAP Authenticate**.
- Configure os parâmetros necessários como servidor LDAP, porta, e método de autenticação.

4. **Publicar o Plugin e o Módulo:

- Assegure-se de que o plugin e o módulo estão publicados.

## 🔧 Configuração

A configuração do plugin requer informações específicas do servidor LDAP:

- **Servidor LDAP (ldap_server):** Endereço do servidor LDAP.
- **Porta LDAP (ldap_port):** Porta utilizada para conexão (389 para LDAP e 636 para LDAPS).
- **Usuário Admin (ldap_admin_username):** Nome de usuário do administrador LDAP para bind.
- **Senha Admin (ldap_admin_password):** Senha do usuário administrador para bind.
- **Base DN (ldap_base_dn):** Distinguished Name (DN) base para busca de usuários.
- **Campo de UID (ldap_uid_field):** Campo do LDAP que corresponde ao nome de usuário.
- **Método de Autenticação (ldap_auth_method):** Método de autenticação: simples, SSL ou StartTLS.

## 🚀 Uso

Após a configuração, o módulo e o plugin LDAP estarão prontos para uso. O fluxo de autenticação é o seguinte:

1. **Usuário Submete o Formulário de Login:**
   - O módulo captura as credenciais do usuário.
   - O plugin é acionado para autenticar o usuário no servidor LDAP.

2. **Autenticação e Sincronização:**
   - Se o usuário for autenticado com sucesso no LDAP, ele é sincronizado no Joomla.
   - Se o usuário não existir no Joomla, ele é criado automaticamente.

3. **Login Bem-Sucedido:**
   - O usuário é redirecionado para a página principal com uma mensagem de sucesso.

4. **Logout:**
   - Limpa as informações de sessão e redireciona o usuário.

## 📝 Contato

- **Email:** [teophilo@gmail.com](mailto:teophilo@gmail.com)
- **LinkedIn:** [Teophilo Silva](https://www.linkedin.com/in/teophilo-silva-dev)

## 📄 Licença

Este projeto é licenciado sob a licença GNU General Public License v2.0. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

Feito com ❤️ por [Teophilo Silva](https://www.linkedin.com/in/teophilo-silva-dev)
