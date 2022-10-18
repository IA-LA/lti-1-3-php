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
define("TOOL_PARAMS_ISS", ($_REQUEST['iss'] ? $_REQUEST['iss'] : ($post_param['iss']?$post_param['iss']:explode('&', explode('iss=', $_REQUEST['redirect_uri'])[1])[0])) );
define("TOOL_PARAMS_LOGIN", ($_REQUEST['login_hint'] ? ($_REQUEST['login_hint']) : ($post_param["login_hint"]?$post_param["login_hint"]:"000000")) );
define("TOOL_PARAMS_TARGET", ($_REQUEST['target_link_uri'] ? $_REQUEST['target_link_uri'] : ($post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]?$post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]:($post_param["redirect_uri"]?$post_param["redirect_uri"]:explode('&', explode('target_link_uri=', $_REQUEST['redirect_uri'])[1])[0]))) );
define("TOOL_PARAMS_LTI", ($_REQUEST['lti_message_hint'] ? $_REQUEST['lti_message_hint'] : ($post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"]?$post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"]:$post_param["lti_message_hint"]) ) );

// LLAMADA REDIRECCION
//  GET: construye la llamada a LAUNCH/PUBLISH
//      PLATAFORMA real que emebebe eContent del Generador de Contenidos alojados y publicados (publicacion/) en el Servidor LTI
//      PLATAFORMA simulada del Servidor LTI (00000000000000000000000[a-f,0-9]) apuntando a eContent subidos por Upload
//  GET: redirecciona la llamada a TARGET_LINK_URI
//      PLATAFORMAS reales y URLs externas no alojadas en el Servidor LTI
//define("TOOL_REDIR", (preg_match("/\/publicacion\/[a-f,0-9]{24}/", TOOL_PARAMS_TARGET) ? (TOOL_HOST . "/launch.php". "?iss=" . TOOL_PARAMS_ISS . "&target_link_uri=" . TOOL_PARAMS_TARGET ) : (TOOL_PARAMS_TARGET)) );
//define("TOOL_REDIR", (preg_match("/00000000000000000000000[a-f,0-9]{1}/", TOOL_PARAMS_ISS) ? (TOOL_HOST . "/launch.php". "?iss=" . TOOL_PARAMS_ISS . "&target_link_uri=" . TOOL_PARAMS_TARGET ) : (TOOL_PARAMS_TARGET)) );
//define("TOOL_REDIR", (preg_match("/00000000000000000000000[a-f,0-9]{1}/", TOOL_PARAMS_ISS) ? (TOOL_HOST . "/launch.php". "?iss=" . TOOL_PARAMS_ISS . "&target_link_uri=" . TOOL_PARAMS_TARGET ) : (preg_match("/\/publicacion\/[a-f,0-9]{24}/", TOOL_PARAMS_TARGET) ? (TOOL_HOST . "/launch.php") : (TOOL_PARAMS_TARGET))) );
define("TOOL_REDIR",
    // Tareas publicadas en abierto en Plataformas simuladas
    (preg_match("/00000000000000000000000[a,c-f,0-9]{1}/", TOOL_PARAMS_ISS)
    ? (TOOL_HOST . "/launch.php" . "?iss=" . TOOL_PARAMS_ISS . "&target_link_uri=" . TOOL_PARAMS_TARGET)
        // Tareas publicadas en abierto
    : (preg_match("/\/publicacion\/[a-f,0-9]{24}/", TOOL_PARAMS_TARGET)
        // Tareas publicadas en abierto en Ágora
        ? (preg_match("/https:\/\/agora\.uned\.es/", TOOL_PARAMS_ISS)
            // Tareas publicadas en abierto por DEMO
            ? (preg_match("/\/publicacion\/101/", TOOL_PARAMS_TARGET)
                ? (TOOL_HOST . "/lms/giccu/diffusion.php")
                    // Actividades publicadas en abierto como administrador de H5P
                : (preg_match("/\/publicacion\/10020220629094/", TOOL_PARAMS_TARGET)
                    ? (TOOL_HOST . "/lms/publish.php")
                    // Tareas publicadas en abierto por CTU
                    : (preg_match("/\/publicacion\/102/", TOOL_PARAMS_TARGET)
                            ? (TOOL_HOST . "/launch.php")
                            // Tareas publicadas por Resto
                            : (TOOL_PARAMS_TARGET)
                            )
                        )
                )
            // Tareas publicadas en abierto en Plataforma Local
            : (preg_match("/:\/\/ailanto-dev\.intecca\.uned\.es/", TOOL_PARAMS_ISS)
                // Tareas publicadas en abierto por DEMO
                ? (preg_match("/\/publicacion\/100/", TOOL_PARAMS_TARGET)
                    ? (TOOL_HOST . "/lms/giccu/diffusion.php")
                    // Tareas publicadas en abierto como administrador
                    : (preg_match("/\/publicacion\/101/", TOOL_PARAMS_TARGET)
                        ? (TOOL_HOST . "/lms/publish.php")
                        // Tareas publicadas en abierto por CTU
                        : (preg_match("/\/publicacion\/102/", TOOL_PARAMS_TARGET)
                            ? (TOOL_PARAMS_TARGET)
                            // Tareas publicadas en abierto por Resto
                            : (TOOL_PARAMS_TARGET)
                            )
                        )
                )
                // Tareas publicadas en abierto en REsto Plataformas
                : (TOOL_PARAMS_TARGET)
            )
        )
        // Tareas publicadas en cerrado
        : (TOOL_PARAMS_TARGET)
        )
    )
);

?>