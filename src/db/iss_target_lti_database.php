<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/params.php';
require_once __DIR__ . '/../services/services.php';

// VARIABLES DE SESION
session_start();

$_SESSION['iss'] = [];
$_SESSION['target_link_uri'] = [];

// LIBRERIA LTI
use \IMSGlobal\LTI;

// SERVICIOS
use Services\Services;

try{

    // CLASE SERVICIOS
    // Conectar con servicios CRUD
    //  get_iss($iss);
    // Ej.: http://192.168.0.31:9002/login.php?iss=5fd9e0b286cb7926b85375e5&login_hint=123456&target_link_uri=http://192.168.0.31:8000/uploads/publicacion/10020210506073929000000a/&lti_message_hint=123456
    /////////////////////////////
    $serv = new Services($_REQUEST);
    $serv = Services::new($_REQUEST);

    // VARIABLES DE SERVICIOS
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
        echo ("<h1>Error.</h1>" . TOOL_PARAMS_ISS);
        exit(0);
    }
    elseif ($target_link_uri_GET['result'] === "error"){
        //echo ' GET ERROR target: ' . $target_link_uri_GET['result'];
        // Salida ERROR Actividad no encontrada
        echo ("<h1>Error..</h1>" . TOOL_PARAMS_TARGET);
        exit(0);
    }

}
catch(Exception $e){
    //echo ' GET ERROR exception: ' . $e->getMessage();
    // Salida Excepción URL
    echo ("<h1>Error...</h1>");
    exit($e->getMessage());
    exit(0);

}

class Iss_Target_Lti_Database implements LTI\Database {

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