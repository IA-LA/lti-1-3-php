<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

define("TOOL_HOST", ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: $_SERVER['REQUEST_SCHEME']) . '://' . $_SERVER['HTTP_HOST']);
define("TOOL_PARAMS_ISS", ($_REQUEST['iss'] ? $_REQUEST['iss'] : explode('&', explode('%26', explode('iss%3D', $_SERVER['REQUEST_URI'])[1])[0])[0]) ); //$_POST['id_token'] $_REQUEST['state'] json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]))['aud']) //json_decode(JWT::urlsafeB64Decode(explode('.',$_REQUEST['id_token'])[1]), true)['iss']
define("TOOL_PARAMS_LOGIN", $_REQUEST['login_hint'] );
define("TOOL_PARAMS_TARGET", ($_REQUEST['target_link_uri'] ? $_REQUEST['target_link_uri'] : explode('lti_message_hint', explode('target_link_uri=', $_SERVER['QUERY_STRING'])[1])[0]) ); //explode('%26', explode('target_link_uri%3D', $_SERVER['REQUEST_URI'])[0])[0] //json_decode(JWT::urlsafeB64Decode(explode('.',$_REQUEST['id_token'])[1]), true)['redirect_uri']
define("TOOL_PARAMS_LTI", $_REQUEST['lti_message_hint'] );
define("TOOL_TOKEN", ($_REQUEST['id_token'] ? json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true)['iss'] : $_POST['id_token'] . $HTTP_POST_VARS) );

session_start();
use \IMSGlobal\LTI;

$_SESSION['iss'] = [];

// Conectar con servicio READ
//  get_iss($iss);
/////////////////////////////

// Información servidor
//  https://www.php.net/manual/es/function.header.php
///////////////////////
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $url = "https://";
else
    $url = "http://";
// Append the host(domain name, ip) to the URL.
$url.= $_SERVER['HTTP_HOST'];

// Append the requested resource location to the URL
$url.= $_SERVER['REQUEST_URI'];
//echo $_REQUEST['target_link_uri'];

// Llamadas REST
//  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
//  https://www.php.net/manual/en/context.http.php
// Obtiene la configuración de las actividades con una llamada de lectura `GET`
///////////////////
$url = "http://10.201.54.31:49151/servicios/lti/lti13/read/" . TOOL_PARAMS_ISS;

$opts = array('http' =>
    array(
        'method' => 'GET',
        'timeout' => '5',
        'ignore_errors' => '1'
    )
);

$context = stream_context_create($opts);
$stream = fopen($url, 'r', false, $context);

// header information as well as meta data
// about the stream
//var_dump(stream_get_meta_data($stream));

// actual data at $url
//var_dump(stream_get_contents($stream));

// Resultado
//  https://www.php.net/manual/es/function.json-decode.php
$json_obj = json_decode(stream_get_contents($stream), true, 5);
//var_dump($json_obj);
//echo $json_obj['result'];
//echo $json_obj->{'data'}->{'usuario'}->{'email'};

// Contenido Registro
$iss_get = ['MAl' => 'MAl'];
// TODO Comprobar que los hint son idénticos AND (['login_hint']) AND (['lti_message_hint'])
// Comprobar que ambas REDIRECTION URI son idénticas AND (TOOL_REDIR === $json_obj['data']['launch_parameters']['target_link_uri'])
// print $url . ' ###### ' . TOOL_ISS . ' ###### ' . TOOL_REDIR . ' ###### ' . strpos($json_obj['data']['launch_parameters']['target_link_uri'], TOOL_REDIR) . ' READ ' . $json_obj['data']['launch_parameters']['target_link_uri'] . ' FIN ';
$GET_target_link_uri = (string) $json_obj['data']['launch_parameters']['target_link_uri'];
if(($json_obj['result'] === "ok") && ($GET_target_link_uri === TOOL_PARAMS_TARGET) ){
    //echo "<p>" . 'SERVICIO GET:';
    //print $json_obj['data']['launch_parameters']['iss'];
    //print "<p>" . 'ARRAY ISS:';
    // Parámetros
    $iss_get = [$json_obj['data']['launch_parameters']['iss'] => $json_obj['data']['credentials']];
    //$iss_get = [$json_obj['data']['id_actividad'] => $json_obj['data']['credentials']];
    //var_dump($_SESSION['iss']);
}
fclose($stream);

// Obtiene la configuración de los sitios con una llamada de lectura `GET`
//echo "<p>" . '$_SESSION["iss"] 1:';
//var_dump($_SESSION['iss'], $iss_get);
$_SESSION['iss'] = array_merge($_SESSION['iss'], $iss_get);
//echo "<p>" . '$_SESSION["iss"] 2:';
//var_dump($_SESSION['iss'], $iss_get);

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

    // TODO obtener registro de SERVIDOR LTI usando servicio GET `iss` !!!!!!!!!
    // Comparar con registro $_POST de PLATAFORMA AUTH !!!!!!!!!
    private function find_issuer($iss) {

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