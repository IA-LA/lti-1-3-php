<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/lti_database.php';

use \IMSGlobal\LTI;

LTI\JWKS_Endpoint::new([
    'TRwtvqCcefOWuXU3-Dt4d26vCQExxh14vTO7_A375Pw' => file_get_contents(__DIR__ . '/../db/imsglobal_tool_private.key')
])->output_jwks();

?>