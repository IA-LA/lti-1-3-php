<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../../../../db/iss_target_lti_database.php';

use \IMSGlobal\LTI;
echo json_encode([
    [
        "id" => TOOL_HOST . "/platform/services/ags.php?tag=time",
        "scoreMaximum" => 999,
        "label" => "Time",
        "tag" => "time",
        "resourceId" => "time7b3c5109-b402-4eac-8f61-bdafa301cbb4"
    ],
    [
        "id" => TOOL_HOST . "/platform/services/ags.php?tag=score",
        "scoreMaximum" => 108,
        "label" => "Score",
        "tag" => "score",
        "resourceId" => "7b3c5109-b402-4eac-8f61-bdafa301cbb4"
    ]
]);
?>