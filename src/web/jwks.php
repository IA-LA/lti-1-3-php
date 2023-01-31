<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \IMSGlobal\LTI;

LTI\JWKS_Endpoint::new([
    'TRwtvqCcefOWuXU3-Dt4d26vCQExxh14vTO7_A375Pw' => file_get_contents(__DIR__ . '/../db/private.key')
])->output_jwks();

?>