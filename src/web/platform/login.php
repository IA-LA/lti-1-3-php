<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../db/iss_target_lti_database.php';

// DONE obtener registro de PLATAFORMA AUTH usando servicio GET `iss` !!!!!!!!!
// se hace en iss_target_lti_database mediante Services en params

use \Firebase\JWT\JWT;
$message_jwt = [
    // Generaliza ISS
    /////////////////
    //"iss" => 'http://localhost:9001',
    //"iss" => '5fc3860a81740b0ef098a965',
    //"iss" => TOOL_ISS,
    "iss" => TOOL_PARAMS_ISS,
    //"iss" => "10020220328120650000000a",
    //"iss" => $_REQUEST['iss'],
    "aud" => ['d42df408-70f5-4b60-8274-6c98d3b9468d'],
    "sub" => '0ae836b9-7fc9-4060-006f-27b2066ac545',
    "exp" => time() + 600,
    "iat" => time(),
    "nonce" => uniqid("nonce"),
    "https://purl.imsglobal.org/spec/lti/claim/deployment_id" => '8c49a5fa-f955-405e-865f-3d7e959e809f',
    "https://purl.imsglobal.org/spec/lti/claim/message_type" => "LtiResourceLinkRequest",
    "https://purl.imsglobal.org/spec/lti/claim/version" => "1.3.0",
    // Generaliza LAUNCH URL y TARJET URI
    /////////////////////////////////////
    //"https://purl.imsglobal.org/spec/lti/claim/target_link_uri" => TOOL_HOST . "/game.php",
    //"https://purl.imsglobal.org/spec/lti/claim/target_link_uri" => TOOL_REDIR,
    //"https://purl.imsglobal.org/spec/lti/claim/target_link_uri" => TOOL_PARAMS_TARGET,
    //"https://purl.imsglobal.org/spec/lti/claim/target_link_uri" => $_REQUEST['target_link_uri'],
    //"https://purl.imsglobal.org/spec/lti/claim/target_link_uri" => TOOL_PARAMS_TARGET,
    "https://purl.imsglobal.org/spec/lti/claim/target_link_uri" => TOOL_PARAMS_TARGET,
    //"https://purl.imsglobal.org/spec/lti/claim/target_link_uri" => explode('?target_link_uri=', $_REQUEST['redirect_uri'])[1],
    "https://purl.imsglobal.org/spec/lti/claim/roles" => [
        "http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor"
    ],
    "https://purl.imsglobal.org/spec/lti/claim/resource_link" => [
        //"id" => "7b3c5109-b402-4eac-8f61-bdafa301cbb4",
        "id"=> TOOL_PARAMS_LTI, //"0123456",
        "title"=> "Resource_Link",
        "description"=> "Resource_Link en servidor LTI Tool.",
    ],
    "https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice" => [
        "context_memberships_url" => TOOL_HOST . "/platform/services/nrps/",
        "service_versions" => ["2.0"]
    ],
    "https://purl.imsglobal.org/spec/lti-ags/claim/endpoint" => [
        "scope" => [
          "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
          "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
          "https://purl.imsglobal.org/spec/lti-ags/scope/score"
        ],
        "lineitems" => TOOL_HOST . "/platform/services/ags/lineitems.php",
    ]
];
$database = new Iss_Target_Lti_Database();
// The platform then creates a JWT with all the LTI parameters as claims, signs the JWT
// using the platform private key, and sends the signed token as a form post to the
// resource link URL.
$jwt = JWT::encode(
    $message_jwt,
    file_get_contents(__DIR__ . '/../../db/platform.key'),
    'RS256',
    'fcec4f14-28a5-4697-87c3-e9ac361dada5'
);
?>

<form id="auto_submit" action="<?= explode('?iss=', ($_REQUEST['redirect_uri']))[0]; ?>" method="POST">
    <input type="hidden" name="id_token" value="<?= $jwt ?>" />
    <input type="hidden" name="state" value="<?= $_REQUEST['state']; ?>" />
    <input type="hidden" name="iss" value="<?= TOOL_PARAMS_ISS; ?>" />
    <input type="hidden" name="login_hint" value="<?= TOOL_PARAMS_LOGIN; ?>" />
    <input type="hidden" name="target_link_uri" value="<?= TOOL_PARAMS_TARGET; ?>" />
    <input type="hidden" name="lti_message_hint" value="<?= TOOL_PARAMS_LTI; ?>" />
</form>
<script>
    document.getElementById('auto_submit').submit();
</script>
