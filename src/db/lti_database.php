<?php
require_once __DIR__ . '/../vendor/autoload.php';

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
define("TOOL_TOKEN", ($_REQUEST['id_token'] ? ($post_param = json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true)) : ($post_param = $_POST['id_token'] . $HTTP_POST_VARS)) );

// PARAMETROS LTI
//  GET : $_REQUEST['']
//  POST: JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1])
//define("TOOL_PARAMS_ISS", ($_REQUEST['iss'] ? $_REQUEST['iss'] : explode('&', explode('%26', explode('iss%3D', $_SERVER['REQUEST_URI'])[1])[0])[0]) ); //$_POST['id_token'] $_REQUEST['state'] json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]))['aud']) //json_decode(JWT::urlsafeB64Decode(explode('.',$_REQUEST['id_token'])[1]), true)['iss']
define("TOOL_PARAMS_ISS", ($_REQUEST['iss'] ? $_REQUEST['iss'] : ($post_param = json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true))['iss']) );
define("TOOL_PARAMS_LOGIN", ($_REQUEST['login_hint'] ? ($_REQUEST['login_hint']) : ("000000")) );
//define("TOOL_PARAMS_TARGET", ($_REQUEST['target_link_uri'] ? $_REQUEST['target_link_uri'] : explode('&', explode('target_link_uri=', $_SERVER['QUERY_STRING'])[1])[0]) ); //explode('%26', explode('target_link_uri%3D', $_SERVER['REQUEST_URI'])[0])[0] //json_decode(JWT::urlsafeB64Decode(explode('.',$_REQUEST['id_token'])[1]), true)['redirect_uri']
define("TOOL_PARAMS_TARGET", ($_REQUEST['target_link_uri'] ? $_REQUEST['target_link_uri'] : ($post_param = json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true))["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]) );
//echo (TOOL_PARAMS_TARGET);
define("TOOL_PARAMS_LTI", ($_REQUEST['lti_message_hint'] ? $_REQUEST['lti_message_hint'] : ($post_param = json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true))["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"]) );

session_start();
use \IMSGlobal\LTI;

$_SESSION['iss'] = [];

// Conectar con servicio READ
//  get_iss($iss);
// Ej.: http://192.168.0.31:9002/login.php?iss=5fd9e0b286cb7926b85375e5&login_hint=123456&target_link_uri=http://192.168.0.31:8000/uploads/publicacion/10020210506073929000000a/&lti_message_hint=123456
/////////////////////////////

try{
    // Información servidor
    //  https://www.php.net/manual/es/function.header.php
    ///////////////////////
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        $url = "https://";
    else
        $url = "http://";

    // Append the host(domain name, ip) to the URL.
    if(strpos($_SERVER['HTTP_HOST'], '.intecca.uned.es') || strpos($_SERVER['HTTP_HOST'], '193.146.230.217')){
        // SERVIDOR SERVICIOS GENERAL
        $url .= '10.201.54.31';
    }
    else{
        //error_reporting(E_ERROR | E_PARSE);

        // Comprueba que respondan las URL locales
        // https://www.geeksforgeeks.org/how-to-send-http-response-code-in-php/
        // Initialize a variable into domain name
        $domains = [
            'localHwifi'=>'192.168.43.130',
            //'localHusb'=>'192.168.42.0',
            //'localLwifi'=>'192.168.42.0',
            'localLusb'=>'192.168.42.10',
            'local_ethernet'=>'192.168.0.31',
            // SERVIDOR SERVICIOS LOCAL
            'local'=>explode(':', $_SERVER['HTTP_HOST'])[0],
        ];

        // Function to get HTTP response code
        function get_http_response_code($domain) {
            $headers = get_headers($domain);
            return substr($headers[0], 9, 3);
        }
        foreach ($domains as $key => $domain) {
            // Function call
            $get_http_response_code = get_http_response_code($url . $domain);

            // Display the HTTP response code
            //echo $get_http_response_code;

            // Check HTTP response code is 200 or not
            if ($get_http_response_code == 200){
                //echo "<br>HTTP request successfully " . $key;
                // SERVIDOR SERVICIOS LOCAL
                $url .= $domain;

                // https://stackoverflow.com/questions/18012290/how-to-break-out-of-a-foreach-once-a-condition-is-met
                break;
            }
            else
                //echo "<br>HTTP request not successfully! " . $key;
                $url = $url;
        }

        // SERVIDOR SERVICIOS LOCAL
        //$url .= explode(':', $_SERVER['HTTP_HOST'])[0];
    }

    // Append the requested resource location to the URL
    //$url.= $_SERVER['REQUEST_URI'];
    //echo $_REQUEST['target_link_uri'];

    // Llamadas REST
    //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
    //  https://www.php.net/manual/en/context.http.php
    // Obtiene la configuración de las actividades con una llamada de lectura `GET`
    // al servidor de SERVICIOS
    ///////////////////////////
    $url_get = $url . ":49151/servicios/lti/lti13/read/coleccion/Lti/id_actividad/" . TOOL_PARAMS_ISS;

    // CONTEXT Options
    /**
    $opts = array('http' =>
        array(
            'method' => 'HEAD',
            'timeout' => '5',
            'ignore_errors' => '1'
        )
    );
     *
     * GET_HEADERS()
     *
    if(strpos(get_headers("http://10.201.54.31:49151/servicios/json/RUTAS.json", 0, stream_context_create($opts))[0], 'OK')){
        $url_get = "http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/Lti/id_actividad/" . TOOL_PARAMS_ISS;
        echo 'PARSE11: ' . parse_url("http://10.201.54.31:49151/servicios/json/RUTAS.json")['port'];
    }
    elseif (strpos(get_headers("http://192.168.0.31:49151/servicios/json/RUTAS.json", 0, stream_context_create($opts))[0], 'OK')) {
        $url_get = "http://192.168.0.31:49151/servicios/lti/lti13/read/coleccion/Lti/id_actividad/" . TOOL_PARAMS_ISS;
        echo 'PARSE12: ' . parse_url('http://192.168.0.31:49151/servicios/lti/lti13/read/coleccion/Lti/id_actividad/');
    }
    elseif (strpos(get_headers("http://127.0.0.1:49151/servicios/json/RUTAS.json", 0, stream_context_create($opts))[0], 'OK')) {
        $url_get = "http://127.0.0.1:49151/servicios/lti/lti13/read/coleccion/Lti/id_actividad/" . TOOL_PARAMS_ISS;
        echo 'PARSE13: ' . parse_url('http://127.0.0.1:49151/servicios/lti/lti13/read/coleccion/Lti/id_actividad/');
    }
     *
     * FILE_EXISTS()
     *
    $context = stream_context_create($opts);
    if (file_exists($url_get)){
    $stream = fopen($url_get, 'r', false, $context);
    }
    elseif (file_exists("http://192.168.0.31:8000/index.php")){

    $url_get= "http://192.168.42.10:49151/servicios/lti/lti13/read/coleccion/Lti/id_actividad/" . TOOL_PARAMS_ISS;
    $stream = fopen($url_get, 'r', false, $context);
    }
    elseif (file_exists("http://192.168.42.185:8000/index.php")){

    $url_get= "http://192.168.42.10:49151/servicios/lti/lti13/read/coleccion/Lti/id_actividad/" . TOOL_PARAMS_ISS;
    $stream = fopen($url_get, 'r', false, $context);
    }
    elseif (file_exists("http://192.168.42.10:8000/index.php")){

    $url_get= "http://192.168.42.10:49151/servicios/lti/lti13/read/coleccion/Lti/id_actividad/" . TOOL_PARAMS_ISS;
    $stream = fopen($url_get, 'r', false, $context);
    }
    elseif (file_exists("http://192.168.43.130:8000/index.php")){

    $url_get = "http://192.168.43.130:49151/servicios/lti/lti13/read/coleccion/Lti/id_actividad/" . TOOL_PARAMS_ISS;
    $stream = fopen($url_get, 'r', false, $context);
    }
    else {

    // Salida URL no encontrada
    echo 'Salida URL no encontrada';
    exit(0);
    }
     **/

    // GET URL
    // CONTEXT Options
    $opts = array('http' =>
        array(
            'method' => 'GET',
            'timeout' => '5',
            'ignore_errors' => '1'
        )
    );

    // Contenido Issuer
    $iss_GET = ['MAl' => 'MAl'];
    // Contenido Redirección
    $target_link_uri_GET = '';

    // GET URL
    $context = stream_context_create($opts);
    $stream = fopen($url_get, 'r', false, $context);

    // header information as well as meta data
    // about the stream
    //var_dump(stream_get_meta_data($stream));

    // actual data at $url_get
    //var_dump(stream_get_contents($stream));

    // Resultado
    //  https://www.php.net/manual/es/function.json-decode.php
    $json_obj = json_decode(stream_get_contents($stream), true, 5);
    //echo ' STREAM CONTENT 11: '. $url_get;
    //print_r($json_obj['data']);
    //var_dump($json_obj);
    //echo $json_obj['result'];
    //echo $json_obj->{'data'}->{'usuario'}->{'email'};

}
catch(Exception $e){
    //echo ' STREAM ERROR 21: ' . $stream;
    // Salida Excepción URL
    exit(0);

}

// LLAMADA OK
// Contenido Resultado de la llamada
// Doble comprobación según el estándar LTI (para evitar redirecciones arbitrarias):
//  - Issuer de Plataforma con credenciales dadas de alta (READ ok)
//  - Target con URL registrada como Actividad LTI
if(($json_obj['result'] === "ok") && ($json_obj['data']['url_actividad'] === TOOL_PARAMS_TARGET)){
//if(($json_obj['result'] === "ok")){

    //echo '<p>' . 'SERVICIO OK: ' . $url_get;

    // Comprobar que ambas REDIRECTION URI son idénticas AND (TOOL_REDIR === $json_obj['data']['launch_parameters']['target_link_uri'])
    // print $url_get . ' ###### ' . TOOL_ISS . ' ###### ' . TOOL_REDIR . ' ###### ' . strpos($json_obj['data']['launch_parameters']['target_link_uri'], TOOL_REDIR) . ' READ ' . $json_obj['data']['launch_parameters']['target_link_uri'] . ' FIN ';
    $target_link_uri_GET = (string) $json_obj['data']['url_actividad'];
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
    $iss_GET = [$json_obj['data']['id_actividad'] => $json_obj['data']['credentials']];
    //$iss_GET = [$json_obj['data']['id_actividad'] => $json_obj['data']['credentials']];
    //var_dump($_SESSION['iss']);
}
elseif ($json_obj['result'] === "error"){
    //echo ' STREAM ERROR 31: ' . $json_obj['result'];
    // Salida ERROR Actividad no encontrada
    exit(0);
}
fclose($stream);

// Obtiene la configuración de los sitios con una llamada de lectura `GET`
//echo "<p>" . '$_SESSION["iss"] 1:';
//var_dump($_SESSION['iss'], $iss_GET);
$_SESSION['iss'] = array_merge($_SESSION['iss'], $iss_GET);
//echo "<p>" . '$_SESSION["iss"] 2:';
//var_dump($_SESSION['iss'], $iss_GET);

// Obtiene la configuración de los sitios del directorio `/configs` y de fichero JSON
$reg_configs = array_diff(scandir(__DIR__ . '/configs'), array('..', '.', '.DS_Store'));
foreach ($reg_configs as $key => $reg_config) {
    //    $_SESSION['iss'] = array_merge($_SESSION['iss'], json_decode(file_get_contents(__DIR__ . "/configs/$reg_config"), true));
    //    print "<p>" . 'FICHERO:';
    //    var_dump(json_decode(file_get_contents(__DIR__ . "/configs/$reg_config"), true));
}


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