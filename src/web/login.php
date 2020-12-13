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
    ->do_oidc_login_redirect(sizeof(explode('.php', $_REQUEST['target_link_uri']) ) > 1 ? ($_REQUEST['target_link_uri'] . "?iss=" . $_REQUEST['iss']) : (TOOL_HOST . "/launch.php" . "?iss=" . $_REQUEST['iss'] . "&target_link_uri=" . $_REQUEST['target_link_uri']), ["iss" => $_REQUEST['iss'], "login_hint" => $_REQUEST['login_hint'], "target_link_uri" => $_REQUEST['target_link_uri'], "lti_message_hint" => $_REQUEST['lti_message_hint']] )
    ->do_redirect();
?>