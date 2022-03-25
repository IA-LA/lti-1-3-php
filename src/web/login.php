<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/lti_database.php';

use \IMSGlobal\LTI;

LTI\LTI_OIDC_Login::new(new Lti_Database(["iss" => $_REQUEST['iss'], "login_hint" => $_REQUEST['login_hint'], "target_link_uri" => $_REQUEST['target_link_uri'], "lti_message_hint" => $_REQUEST['lti_message_hint']]))
    //->do_oidc_login_redirect(TOOL_HOST . "/game.php")
    //->do_oidc_login_redirect(TOOL_HOST . "https://ailanto-dev.intecca.uned.es/lti/launch.php" . "?target_link_uri=" . TOOL_REDIR)
    //->do_oidc_login_redirect(TOOL_HOST . "/launch.php" . "?iss=" . TOOL_ISS . "&target_link_uri=" . TOOL_REDIR)
    //->do_oidc_login_redirect(TOOL_HOST . "/launch.php" . "?iss=" . $_REQUEST['iss'] . "&target_link_uri=" . $_REQUEST['target_link_uri'], $_REQUEST)
    // Actividades PHP o no PHP
    //->do_oidc_login_redirect(sizeof(explode('.php', $_REQUEST['target_link_uri']) ) > 1 ? ($_REQUEST['target_link_uri'] . "?iss=" . $_REQUEST['iss'] . "&target_link_uri=" . $_REQUEST['target_link_uri']) : (TOOL_HOST . "/launch.php" . "?iss=" . $_REQUEST['iss'] . "&target_link_uri=" . $_REQUEST['target_link_uri']), ["iss" => $_REQUEST['iss'], "login_hint" => $_REQUEST['login_hint'], "target_link_uri" => $_REQUEST['target_link_uri'], "lti_message_hint" => $_REQUEST['lti_message_hint']] )

    // Actividades ECONTENT alojadas en el Servidor o Externas alojadas en otro servidor o Plataforma:
        // eContent: utiliza un conteniedo .php y un iframe para presentarlo y manejar la llamda POST JWT LTI Claims (como la de platform/login.php)
        // Externas: se redireccionan directamente a su 'target_link_uri' de plataforma o actividad independiente.
    // Login en la Plataforma Consumidora de la Actividad LTI debidamente registrada 'target_link_uri'
    //->do_oidc_login_redirect(sizeof(explode('econtent.php', $_REQUEST['target_link_uri']) ) > 1 ? ($_REQUEST['target_link_uri'] . "?iss=" . $_REQUEST['iss'] . "&target_link_uri=" . $_REQUEST['target_link_uri']) : ("https://ailanto-dev.intecca.uned.es/lti13" . "/launch.php" . "?iss=" . $_REQUEST['iss'] . "&target_link_uri=" . $_REQUEST['target_link_uri']), ["iss" => $_REQUEST['iss'], "login_hint" => $_REQUEST['login_hint'], "target_link_uri" => $_REQUEST['target_link_uri'], "lti_message_hint" => $_REQUEST['lti_message_hint']] )
    // Login:
    //      - En una Plataforma Consumidora Externa de la Actividad LTI debidamente registradas ambas => redirecciona hacia 'target_link_uri'
    //      - En la Platforma Consumidora Interna de una Actividad eContent debidamente registradas ambas =>  launch.php (lanzador propio del servidor)
    ->do_oidc_login_redirect(sizeof(explode('econtent.php', $_REQUEST['target_link_uri']) ) > 1 ? ($_REQUEST['target_link_uri']) : ("https://ailanto-dev.intecca.uned.es/lti13" . "/launch.php")

    // Redirección hacia 'target_link_uri'
        // https://www.w3docs.com/snippets/php/how-to-redirect-a-web-page-with-php.html
    ->do_redirect();
?>