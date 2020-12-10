<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/lti_database.php';

//echo TOOL_HOST . "/launch.php" . "?target_link_uri=" . TOOL_REDIR;
//echo $_REQUEST['iss'];

use \IMSGlobal\LTI;

LTI\LTI_OIDC_Login::new(new Lti_Database())
    //->do_oidc_login_redirect(TOOL_HOST . "https://ailanto-dev.intecca.uned.es/lti/launch.php" . "?target_link_uri=" . TOOL_REDIR)
    //->do_oidc_login_redirect(TOOL_HOST . "/launch.php" . "?iss=" . TOOL_ISS . "&target_link_uri=" . TOOL_REDIR)
    ->do_oidc_login_redirect(TOOL_HOST . "/launch.php" . "?iss=" . TOOL_ISS, $_REQUEST)
    ->do_redirect();
?>