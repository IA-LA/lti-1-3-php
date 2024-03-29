<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../../../../db/iss_target_lti_database.php';

use \IMSGlobal\LTI;
echo json_encode([
    "id" => TOOL_HOST . "/platform/nrps.php",
    "members" => [
        [
            "status" => "Active",
            "context_id" => "2923-abc",
            "name" => "Trudie Senaida",
            "given_name" => "Trudie",
            "family_name" => "Senaida",
            "user_id" => "0ae836b9-7fc9-4060-006f-27b2066ac545",
            "roles" => [
                "Instructor"
            ],
            "message" => []
        ],
        [
            "status" => "Active",
            "context_id" => "2923-abc",
            "name" => "Marget Elke",
            "given_name" => "Marget",
            "family_name" => "Elke",
            "user_id" => "4d0b3941-83f5-47fe-bd8a-66b39aa0651d",
            "roles" => [
                "Instructor"
            ],
            "message" => []
        ]
    ]

]);
?>