<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/lti_database.php';

//echo TOOL_HOST . "/launch.php" . "?target_link_uri=" . TOOL_REDIR;
//echo $_REQUEST['iss'], $_REQUEST['target_link_uri'];

use \IMSGlobal\LTI;

LTI\LTI_OIDC_Login::new(new Lti_Database())
    //->do_oidc_login_redirect(TOOL_HOST . "https://ailanto-dev.intecca.uned.es/lti/launch.php" . "?target_link_uri=" . TOOL_REDIR)
    //->do_oidc_login_redirect(TOOL_HOST . "/launch.php" . "?iss=" . TOOL_ISS . "&target_link_uri=" . TOOL_REDIR)
    //->do_oidc_login_redirect(TOOL_HOST . "/launch.php" . "?iss=" . $_REQUEST['iss'] . "&target_link_uri=" . $_REQUEST['target_link_uri'], $_REQUEST)
        // Actividades PHP o no PHP
    ->do_oidc_login_redirect(sizeof(explode('.php', $_REQUEST['target_link_uri']) ) > 1 ? $_REQUEST['target_link_uri'] . "?param=NO" : TOOL_HOST . "/launch.php" . "?iss=" . $_REQUEST['iss'] . "&target_link_uri=" . $_REQUEST['target_link_uri'], $_REQUEST)
    ->do_redirect();
?>