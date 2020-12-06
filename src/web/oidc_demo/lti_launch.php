<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../db/lti_database.php';

use \IMSGlobal\LTI;
$launch = LTI\LTI_Message_Launch::new(new Example_Database())
    ->validate();
?>
<h2>Success</h2>