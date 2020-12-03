<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/example_database.php';

//echo TOOL_HOST . "/launch.php" . "?target_link_uri=" . TOOL_PARAM;

use \IMSGlobal\LTI;

LTI\LTI_OIDC_Login::new(new Example_Database())
    ->do_oidc_login_redirect(TOOL_HOST . "/game.php")
    ->do_redirect();
?>