<?php

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
define("TOOL_PARAMS_ISS", ($_REQUEST['iss'] ? explode('%26', $_REQUEST['iss'])[0] : ($post_param['iss']?$post_param['iss']:explode('&', explode('iss=', $_REQUEST['redirect_uri'])[1])[0])) );
define("TOOL_PARAMS_LOGIN", ($_REQUEST['login_hint'] ? ($_REQUEST['login_hint']) : ($post_param["login_hint"]?$post_param["login_hint"]:"000000")) );
define("TOOL_PARAMS_TARGET", ($_REQUEST['target_link_uri'] ? $_REQUEST['target_link_uri'] : ($post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]?$post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]:($post_param["redirect_uri"]?$post_param["redirect_uri"]:explode('&', explode('target_link_uri=', $_REQUEST['redirect_uri'])[1])[0]))) );
define("TOOL_PARAMS_LTI", ($_REQUEST['lti_message_hint'] ? $_REQUEST['lti_message_hint'] : ($post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"]?$post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"]:$post_param["lti_message_hint"]) ) );

// LLAMADA REDIRECCION
//  GET: construye la llamada a LAUNCH
//      PLATAFORMA real que emebebe eContent del Generador de Contenidos publicados (/publicacion/) en el Servidor LTI
//      PLATAFORMA simulada del Servidor LTI (00000000000000000000000[a-f,0-9]) apuntando a eContent subidos por Upload
//  GET: redirecciona la llamada a TARGET_LINK_URI
//      PLATAFORMAS reales y URLs externas
//define("TOOL_REDIR", (preg_match("/\/publicacion\/[a-f,0-9]{24}/", TOOL_PARAMS_TARGET) ? (TOOL_HOST . "/launch.php". "?iss=" . TOOL_PARAMS_ISS . "&target_link_uri=" . TOOL_PARAMS_TARGET ) : (TOOL_PARAMS_TARGET)) );
//define("TOOL_REDIR", (preg_match("/00000000000000000000000[a-f,0-9]{1}/", TOOL_PARAMS_ISS) ? (TOOL_HOST . "/launch.php". "?iss=" . TOOL_PARAMS_ISS . "&target_link_uri=" . TOOL_PARAMS_TARGET ) : (TOOL_PARAMS_TARGET)) );
//define("TOOL_REDIR", (preg_match("/00000000000000000000000[a-f,0-9]{1}/", TOOL_PARAMS_ISS) ? (TOOL_HOST . "/launch.php". "?iss=" . TOOL_PARAMS_ISS . "&target_link_uri=" . TOOL_PARAMS_TARGET ) : (preg_match("/\/publicacion\/[a-f,0-9]{24}/", TOOL_PARAMS_TARGET) ? (TOOL_HOST . "/launch.php") : (TOOL_PARAMS_TARGET))) );
define("TOOL_REDIR", (preg_match("/00000000000000000000000[a-f,0-9]{1}/", TOOL_PARAMS_ISS) ? (TOOL_HOST . "/lms/publish.php". "?iss=" . TOOL_PARAMS_ISS . "&target_link_uri=" . TOOL_PARAMS_TARGET ) : (preg_match("/\/publicacion\/[a-f,0-9]{24}/", TOOL_PARAMS_TARGET) ? (TOOL_HOST . "/lms/publish.php") : (TOOL_PARAMS_TARGET))) );

?>