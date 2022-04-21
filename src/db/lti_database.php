<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../services/services.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

// HOST
//  HTTPS: puertos 80(redir)|443
//  HTTP : cualquier otro puerto
define("TOOL_HOST", (preg_match("/(80|443)/", $_SERVER['SERVER_PORT']) ? ('https://' . $_SERVER['HTTP_HOST']. '/lti13') : ($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'])));
// PROBLEMAS CON EL HTTPS (Fatal error: Uncaught IMSGlobal\LTI\LTI_Exception: State not found)
// FUNCIONA CON EDX
//define("TOOL_HOST", ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: $_SERVER['REQUEST_SCHEME']) . '://' . $_SERVER['HTTP_HOST']);
//define("TOOL_HOST", ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: 'https' . '://' . 'https://ailanto-dev.intecca.uned.es/lti/lti13/') );
// PROBLEMAS CON EL HTTP (aparece el contenido en blanco)
// FUNCIONA CON MOODLE
//define("TOOL_HOST", ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: 'https' . '://' . 'ailanto-dev.intecca.uned.es/lti13') );

// ID TOKEN
// Open ID Connect (OIDC)
// https://auth0.com/blog/id-token-access-token-what-is-the-difference/
define("TOOL_TOKEN", ($_REQUEST['id_token'] ? ($post_param = json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true)) : ($post_param = $_POST)) );

// PARAMETROS LTI
//  GET : $_REQUEST['']
//  POST: JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1])
//define("TOOL_PARAMS_ISS", ($_REQUEST['iss'] ? $_REQUEST['iss'] : explode('&', explode('%26', explode('iss%3D', $_SERVER['REQUEST_URI'])[1])[0])[0]) ); //$_POST['id_token'] $_REQUEST['state'] json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]))['aud']) //json_decode(JWT::urlsafeB64Decode(explode('.',$_REQUEST['id_token'])[1]), true)['iss']
define("TOOL_PARAMS_ISS", ($_REQUEST['iss'] ? $_REQUEST['iss'] : ($post_param['iss']?$post_param['iss']:explode('&', explode('iss=', $_REQUEST['redirect_uri'])[1])[0])) );
define("TOOL_PARAMS_LOGIN", ($_REQUEST['login_hint'] ? ($_REQUEST['login_hint']) : ($post_param["login_hint"]?$post_param["login_hint"]:"000000")) );
//define("TOOL_PARAMS_TARGET", ($_REQUEST['target_link_uri'] ? $_REQUEST['target_link_uri'] : explode('&', explode('target_link_uri=', $_SERVER['QUERY_STRING'])[1])[0]) ); //explode('%26', explode('target_link_uri%3D', $_SERVER['REQUEST_URI'])[0])[0] //json_decode(JWT::urlsafeB64Decode(explode('.',$_REQUEST['id_token'])[1]), true)['redirect_uri']
define("TOOL_PARAMS_TARGET", ($_REQUEST['target_link_uri'] ? $_REQUEST['target_link_uri'] : ($post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]?$post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]:($post_param["redirect_uri"]?$post_param["redirect_uri"]:explode('&', explode('target_link_uri=', $_REQUEST['redirect_uri'])[1])[0]))) );
//echo (TOOL_PARAMS_TARGET);
define("TOOL_PARAMS_LTI", ($_REQUEST['lti_message_hint'] ? $_REQUEST['lti_message_hint'] : ($post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"]?$post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"]:$post_param["lti_message_hint"]) ) );

// LLAMADA REDIRECCION
//  GET: construye la llamada LAUNCH
//  para la PLATAFORMA genérica del Servidor LTI
define("TOOL_REDIR", (preg_match("/\/publicacion\/[a-f,0-9]{24}/", TOOL_PARAMS_TARGET) ? (TOOL_HOST . "/launch.php". (preg_match("/(80|443)/", $_SERVER['SERVER_PORT']) ? "?iss=" . TOOL_PARAMS_ISS . "&target_link_uri=" . TOOL_PARAMS_TARGET:'')) : (TOOL_PARAMS_TARGET)) );

session_start();
use \IMSGlobal\LTI;

$_SESSION['iss'] = [];
$_SESSION['target_link_uri'] = [];
//use \Services\services;
use Services\Services;

// Conectar con servicio READ
//  get_iss($iss);
// Ej.: http://192.168.0.31:9002/login.php?iss=5fd9e0b286cb7926b85375e5&login_hint=123456&target_link_uri=http://192.168.0.31:8000/uploads/publicacion/10020210506073929000000a/&lti_message_hint=123456
/////////////////////////////

try{

    // CLASE SERVICIOS
    // Conectar con servicios CRUD
    //  get_iss($iss);
    // Ej.: http://192.168.0.31:9002/login.php?iss=5fd9e0b286cb7926b85375e5&login_hint=123456&target_link_uri=http://192.168.0.31:8000/uploads/publicacion/10020210506073929000000a/&lti_message_hint=123456
    /////////////////////////////
    $serv = new Services($_REQUEST);
    $serv = Services::new($_REQUEST);

    // Contenido Issuer (Audience o Iss)
    $iss_GET = ['MAl' => 'MAl'];
    // Contenido Redirección (Target)
    $target_link_uri_GET = ['mAl' => 'mAl'];

    // Llamadas REST
    //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
    //  https://www.php.net/manual/en/context.http.php
    // Obtiene la configuración de las actividades con una llamada de lectura `GET`
    // al servidor de SERVICIOS
    ///////////////////////////
    $iss_GET =  $serv->service('read', 'Platform', 'id_actividad', TOOL_PARAMS_ISS, $_REQUEST);
    $target_link_uri_GET =  $serv->service('read', 'Lti', 'url_actividad', TOOL_PARAMS_TARGET, $_REQUEST);
    //echo "ISS";
    //print_r($iss_GET);
    //echo "TARGET";
    //print_r($target_link_uri_GET);

    // LLAMADA OK
    // Contenido Resultado de las llamadas existe
    //if(($json_obj['result'] === "ok")){
    if(($iss_GET['result'] === "ok") && ($target_link_uri_GET['result'] === "ok")){

        //echo '<p>' . 'SERVICIO OK: ' . $url;

        // Comprobar que ambas REDIRECTION URI son idénticas AND (TOOL_REDIR === $json_obj['data']['launch_parameters']['target_link_uri'])
        // print $url_get . ' ###### ' . TOOL_ISS . ' ###### ' . TOOL_REDIR . ' ###### ' . strpos($json_obj['data']['launch_parameters']['target_link_uri'], TOOL_REDIR) . ' READ ' . $json_obj['data']['launch_parameters']['target_link_uri'] . ' FIN ';
        //$target_link_uri_GET = (string) $json_obj['data']['launch_parameters']['target_link_uri'];
        // Comprueba que iss y target_link son idénticos a los registrados en la BBDD
        // TODO Comprobar que los hint son idénticos a los registrados en la BBDD AND (['login_hint']) AND (['lti_message_hint'])
        //echo $target_link_uri_GET . ' URLS === URLS ' . TOOL_PARAMS_TARGET;

        //if(!($target_link_uri_GET === TOOL_PARAMS_TARGET)){
        //    define("TOOL_PARAMS_TARGET", $target_link_uri_GET);
        //}
        //echo "<p>" . 'SERVICIO GET:';
        //print $json_obj['data']['launch_parameters']['iss'];
        //print "<p>" . 'ARRAY ISS:';

        // Parámetros
        //$iss_GET = [$json_obj['data']['launch_parameters']['iss'] => $json_obj['data']['credentials']];
        //$iss_GET = [$json_obj['data']['id_actividad'] => $json_obj['data']['credentials']];
        //var_dump($_SESSION['iss']);

        // Obtiene la configuración de los sitios con una llamada de lectura `GET`
        //echo "<p>" . '$_SESSION["iss"] 1:';
        //var_dump($_SESSION['iss'], $iss_GET);
        $_SESSION['iss'] = array_merge($_SESSION['iss'], [$iss_GET['data']['id_actividad'] => $iss_GET['data']['credentials']]);

        //echo "<p>" . '$_SESSION["target_link_uri"] 2:';
        //var_dump($_SESSION['target_link_uri'], $target_link_uri_GET);
        $_SESSION['target_link_uri'] = array_merge($_SESSION['target_link_uri'], [$target_link_uri_GET['data']['id_actividad'] => $target_link_uri_GET['data']]);

        // Obtiene la configuración de los sitios del directorio `/configs` y de fichero JSON
        //$reg_configs = array_diff(scandir(__DIR__ . '/configs'), array('..', '.', '.DS_Store'));
        //foreach ($reg_configs as $key => $reg_config) {
        //    $_SESSION['iss'] = array_merge($_SESSION['iss'], json_decode(file_get_contents(__DIR__ . "/configs/$reg_config"), true));
        //    print "<p>" . 'FICHERO:';
        //    var_dump(json_decode(file_get_contents(__DIR__ . "/configs/$reg_config"), true));
        //}
        // Salida OK Plataforma y Target Link URI encontrada
        //exit(0);

    }
    elseif ($iss_GET['result'] === "error"){
        //echo ' GET ERROR iss: ' . $iss_GET['result'];
        // Salida ERROR Plataforma no encontrada
        exit(0);
    }
    elseif ($target_link_uri_GET['result'] === "error"){
        //echo ' GET ERROR target: ' . $target_link_uri_GET['result'];
        // Salida ERROR Actividad no encontrada
        exit(0);
    }

}
catch(Exception $e){
    //echo ' GET ERROR exception: ' . $e->getMessage();
    // Salida Excepción URL
    exit(0);

}

// Obtiene la configuración de los sitios con una llamada de lectura `GET`
//echo "<p>" . '$_SESSION["iss"] 1:';
//var_dump($_SESSION['iss'], $iss_GET);
$_SESSION['iss'] = array_merge($_SESSION['iss'], $iss_GET);
//echo "<p>" . '$_SESSION["iss"] 2:';
//var_dump($_SESSION['iss'], $iss_GET);

class Lti_Database implements LTI\Database {

    private $request;

    /**
     * Constructor
     *
     * @param array  $request   Request information.
     */
    function __construct(array $request = null) {

        if ($request === null) {
            $request = $_REQUEST;
        }
        $this->request = $request;

        // CONSTANTES
        //["iss" => $_REQUEST['iss'], "login_hint" => $_REQUEST['login_hint'], "target_link_uri" => $_REQUEST['target_link_uri'], "lti_message_hint" => $_REQUEST['lti_message_hint']]
        define("TOOL_PARAMS_ISS", $this->request['iss'] );
        //define("TOOL_ISS", $this->request['iss'] );
        define("TOOL_PARAMS_LOGIN", $this->request['login_hint'] );
        define("TOOL_PARAMS_TARGET", $this->request['target_link_uri'] );
        //define("TOOL_REDIR", $this->request['target_link_uri'] );
        define("TOOL_PARAMS_LTI", $this->request['lti_message_hint'] );

    }

    // TODO obtener `iss` del registro de PLATAFORMA usando servicio GET !!!!!!!!!
    // Comparar con registro $_POST de PLATAFORMA OAUTH !!!!!!!!!
    private function find_issuer($iss) {

    }

    // TODO obtener`target_link_uri` del registro de Actividad LTI usando servicio GET !!!!!!!!!
    // Comparar con parámetro $_GET de LOGIN !!!!!!!!!
    private function find_target($target_link_uri) {

    }

    public function find_registration_by_issuer($iss) {
        if (empty($_SESSION['iss']) || empty($_SESSION['iss'][$iss])) {
            return false;
        }
        return LTI\LTI_Registration::new()
            ->set_auth_login_url($_SESSION['iss'][$iss]['auth_login_url'])
            ->set_auth_token_url($_SESSION['iss'][$iss]['auth_token_url'])
            ->set_auth_server($_SESSION['iss'][$iss]['auth_server']) //No aparece en la llamada a GAME
            ->set_client_id($_SESSION['iss'][$iss]['client_id'])
            ->set_key_set_url($_SESSION['iss'][$iss]['key_set_url'])
            ->set_kid($_SESSION['iss'][$iss]['kid'])
            ->set_issuer($iss)
            ->set_tool_private_key($this->private_key($iss));
    }

    public function find_deployment($iss, $deployment_id) {
        if (!in_array($deployment_id, $_SESSION['iss'][$iss]['deployment'])) {
            return false;
        }
        return LTI\LTI_Deployment::new()
            ->set_deployment_id($deployment_id);
    }

    // Obtiene la cave privada de cada sitio issue `$iss`
    private function
    private_key($iss) {
        return file_get_contents(__DIR__ . $_SESSION['iss'][$iss]['private_key_file']);
    }
}
?>