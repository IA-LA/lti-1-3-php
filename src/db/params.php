<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

// HOST
//  HTTPS: puertos 80(redir)|443
//  HTTP : cualquier otro puerto
define("TOOL_HOST", (preg_match("/(80|443)/", $_SERVER['SERVER_PORT']) ? ('https://' . $_SERVER['HTTP_HOST']. '/lti13') : ($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'])));

// ID TOKEN
// Open ID Connect (OIDC)
// https://auth0.com/blog/id-token-access-token-what-is-the-difference/
define("TOOL_TOKEN", ($_REQUEST['id_token'] ? ($post_param = json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true)) : ($post_param = $_POST)) );

// PARAMETROS LTI
//  GET : $_REQUEST['']
//  POST: JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1])
define("TOOL_PARAMS_ISS", ($_REQUEST['iss'] ? $_REQUEST['iss'] : ($post_param['iss']?$post_param['iss']:explode('&', explode('iss=', $_REQUEST['redirect_uri'])[1])[0])) );
define("TOOL_PARAMS_LOGIN", ($_REQUEST['login_hint'] ? ($_REQUEST['login_hint']) : ($post_param["login_hint"]?$post_param["login_hint"]:"000000")) );
define("TOOL_PARAMS_TARGET", ($_REQUEST['target_link_uri'] ? $_REQUEST['target_link_uri'] : ($post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]?$post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]:($post_param["redirect_uri"]?$post_param["redirect_uri"]:explode('&', explode('target_link_uri=', $_REQUEST['redirect_uri'])[1])[0]))) );
define("TOOL_PARAMS_LTI", ($_REQUEST['lti_message_hint'] ? $_REQUEST['lti_message_hint'] : ($post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"]?$post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"]:$post_param["lti_message_hint"]) ) );

// LLAMADA REDIRECCION
//  GET: construye la llamada LAUNCH
//  para la PLATAFORMA genérica del Servidor LTI
define("TOOL_REDIR", (preg_match("/\/publicacion\/[a-f,0-9]{24}/", TOOL_PARAMS_TARGET) ? (TOOL_HOST . "/launch.php". (preg_match("/(80|443)/", $_SERVER['SERVER_PORT']) ? "?iss=" . TOOL_PARAMS_ISS . "&target_link_uri=" . TOOL_PARAMS_TARGET : '') ) : (TOOL_PARAMS_TARGET)) );

?>