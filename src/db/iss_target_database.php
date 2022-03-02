<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../services/services.php';

// PROBLEMAS CON EL HTTPS (Fatal error: Uncaught IMSGlobal\LTI\LTI_Exception: State not found)
// FUNCIONA CON EDX
define("TOOL_HOST", ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: $_SERVER['REQUEST_SCHEME']) . '://' . $_SERVER['HTTP_HOST']);
//define("TOOL_HOST", ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: 'https' . '://' . 'https://ailanto-dev.intecca.uned.es/lti/lti13/') );
// PROBLEMAS CON EL HTTP (aparece el contenido en blanco)
// FUNCIONA CON MOODLE
//define("TOOL_HOST", ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: 'https' . '://' . 'ailanto-dev.intecca.uned.es/lti13') );
define("TOOL_PARAMS_ISS", ($_REQUEST['iss'] ? $_REQUEST['iss'] : explode('&', explode('%26', explode('iss%3D', $_SERVER['REQUEST_URI'])[1])[0])[0]) ); //$_POST['id_token'] $_REQUEST['state'] json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]))['aud']) //json_decode(JWT::urlsafeB64Decode(explode('.',$_REQUEST['id_token'])[1]), true)['iss']
define("TOOL_PARAMS_LOGIN", $_REQUEST['login_hint'] );
//echo ($_SERVER['QUERY_STRING']);
define("TOOL_PARAMS_TARGET", ($_REQUEST['target_link_uri'] ? $_REQUEST['target_link_uri'] : explode('&', explode('target_link_uri=', $_SERVER['QUERY_STRING'])[1])[0]) ); //explode('%26', explode('target_link_uri%3D', $_SERVER['REQUEST_URI'])[0])[0] //json_decode(JWT::urlsafeB64Decode(explode('.',$_REQUEST['id_token'])[1]), true)['redirect_uri']
//echo (TOOL_PARAMS_TARGET);
define("TOOL_PARAMS_LTI", $_REQUEST['lti_message_hint'] );
define("TOOL_TOKEN", ($_REQUEST['id_token'] ? json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true)['iss'] : $_POST['id_token'] . $HTTP_POST_VARS) );

session_start();
use \IMSGlobal\LTI;

$_SESSION['iss'] = [];
use \services\services;

// Conectar con servicio READ
//  get_iss($iss);
// Ej.: http://192.168.0.31:9002/login.php?iss=5fd9e0b286cb7926b85375e5&login_hint=123456&target_link_uri=http://192.168.0.31:8000/uploads/publicacion/10020210506073929000000a/&lti_message_hint=123456
/////////////////////////////
$serv = new Services($_REQUEST);
$serv = services\Services::new($_REQUEST);

// Append the requested resource location to the URL
//$url.= $_SERVER['REQUEST_URI'];
//echo $_REQUEST['target_link_uri'];

// Contenido Registro
$iss_get = ['MAl' => 'MAl'];
// Contenido Redirección
$target_link_uri_get = ['mAl' => 'mAl'];

// Llamadas REST
//  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
//  https://www.php.net/manual/en/context.http.php
// Obtiene la configuración de las actividades con una llamada de lectura `GET`
// al servidor de SERVICIOS
///////////////////////////
$iss_get =  $serv.service('read', 'Platform', 'id_actividad', $_REQUEST['iss'], $_REQUEST);
$target_link_uri_get =  $serv.service('read', 'Lti', 'id_actividad', $_REQUEST['target_link_uri'], $_REQUEST);

// LLAMADA OK
// Contenido Resultado de las llamadas existe
//if(($json_obj['result'] === "ok")){
if(($iss_get['result'] === "ok") && ($target_link_uri_get['result'] === "ok")){

    //echo '<p>' . 'SERVICIO OK: ' . $url;

    // Comprobar que ambas REDIRECTION URI son idénticas AND (TOOL_REDIR === $json_obj['data']['launch_parameters']['target_link_uri'])
    // print $url_get . ' ###### ' . TOOL_ISS . ' ###### ' . TOOL_REDIR . ' ###### ' . strpos($json_obj['data']['launch_parameters']['target_link_uri'], TOOL_REDIR) . ' READ ' . $json_obj['data']['launch_parameters']['target_link_uri'] . ' FIN ';
    //$target_link_uri_get = (string) $json_obj['data']['launch_parameters']['target_link_uri'];
    // Comprueba que iss y target_link son idénticos a los registrados en la BBDD
    // TODO Comprobar que los hint son idénticos a los registrados en la BBDD AND (['login_hint']) AND (['lti_message_hint'])
    //echo $target_link_uri_get . ' URLS === URLS ' . TOOL_PARAMS_TARGET;

    //if(!($target_link_uri_get === TOOL_PARAMS_TARGET)){
    //    define("TOOL_PARAMS_TARGET", $target_link_uri_get);
    //}
    //echo "<p>" . 'SERVICIO GET:';
    //print $json_obj['data']['launch_parameters']['iss'];
    //print "<p>" . 'ARRAY ISS:';

    // Parámetros
    //$iss_get = [$json_obj['data']['launch_parameters']['iss'] => $json_obj['data']['credentials']];
    //$iss_get = [$json_obj['data']['id_actividad'] => $json_obj['data']['credentials']];
    //var_dump($_SESSION['iss']);
}
elseif ($iss_get['result'] === "error"){
    //echo ' STREAM ERROR 31: ' . $json_obj['result'];
    // Salida ERROR Plataforma no encontrada
    exit(0);
}
elseif ($target_link_uri_get['result'] === "error"){
    //echo ' STREAM ERROR 31: ' . $json_obj['result'];
    // Salida ERROR Actividad no encontrada
    exit(0);
}

// Obtiene la configuración de los sitios con una llamada de lectura `GET`
//echo "<p>" . '$_SESSION["iss"] 1:';
//var_dump($_SESSION['iss'], $iss_get);
$_SESSION['iss'] = array_merge($_SESSION['iss'], [$iss_get['data']['id_actividad'] => $iss_get['data']['credentials']]);
//echo "<p>" . '$_SESSION["iss"] 2:';
//var_dump($_SESSION['iss'], $iss_get);

$_SESSION['iss'] = array_merge($_SESSION['iss'], [$target_link_uri_get['data']['id_actividad'] => $target_link_uri_get['data']]);

// Obtiene la configuración de los sitios del directorio `/configs` y de fichero JSON
//$reg_configs = array_diff(scandir(__DIR__ . '/configs'), array('..', '.', '.DS_Store'));
//foreach ($reg_configs as $key => $reg_config) {
    //    $_SESSION['iss'] = array_merge($_SESSION['iss'], json_decode(file_get_contents(__DIR__ . "/configs/$reg_config"), true));
    //    print "<p>" . 'FICHERO:';
    //    var_dump(json_decode(file_get_contents(__DIR__ . "/configs/$reg_config"), true));
//}


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

    // TODO obtener registro de SERVIDOR LTI usando servicio GET `iss` !!!!!!!!!
    // Comparar con registro $_POST de PLATAFORMA OAUTH !!!!!!!!!
    private function find_issuer($iss) {

    }

    public function find_registration_by_issuer($iss) {
        // Sesión aún no existe
        if (empty($_SESSION['iss']) || empty($_SESSION['iss'][$iss])) {
            return false;
        }
        // Llamada servicio BBDD(iss existe)
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

    public function find_registration_by_target($target_link_uri) {
        // Llamada servicio BBDD(iss existe) para ver si target_link_uri está registrado
        if (empty($_SESSION['target_link_uri']) || empty($_SESSION['iss'][$target_link_uri])) {
            return false;
        }
        else{
            // Llamada servicio BBDD(target_link_uri existe)
            return LTI\LTI_Registration::new()
                ->set_auth_login_url($_SESSION['iss'][$target_link_uri]['url_actividad'])
                ->set_auth_token_url($_SESSION['iss'][$target_link_uri]['url_actividad'])
                ->set_auth_server($_SESSION['iss'][$target_link_uri]['url_actividad']) //No aparece en la llamada a GAME
                ->set_client_id($_SESSION['iss'][$target_link_uri]['id_actividad'])
                ->set_key_set_url($_SESSION['iss'][$target_link_uri]['url_actividad'])
                ->set_kid($_SESSION['iss'][$target_link_uri]['id_actividad'])
                ->set_issuer($target_link_uri)
                ->set_tool_private_key($this->private_key($target_link_uri));}
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