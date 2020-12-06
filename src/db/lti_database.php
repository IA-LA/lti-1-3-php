<?php
require_once __DIR__ . '/../vendor/autoload.php';
define("TOOL_HOST", ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: $_SERVER['REQUEST_SCHEME']) . '://' . $_SERVER['HTTP_HOST']);
define("TOOL_PARAM", ($_REQUEST['target_link_uri'] ? $_REQUEST['target_link_uri'] : $_REQUEST['iss']) );
session_start();
use \IMSGlobal\LTI;

$_SESSION['iss'] = [];
// TODO Conectar con servicio READ
// Información servidor
///////////////////////
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $url = "https://";
else
    $url = "http://";
// Append the host(domain name, ip) to the URL.
$url.= $_SERVER['HTTP_HOST'];

// Append the requested resource location to the URL
$url.= $_SERVER['REQUEST_URI'];

echo $_REQUEST['target_link_uri'];
// Llamadas REST
//  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
//  https://www.php.net/manual/en/context.http.php
///////////////////
$url = "http://10.201.54.31:49151/servicios/lti/lti13/read/5fc3860a81740b0ef098a965";

$opts = array('http' =>
    array(
        'method' => 'GET',
        'max_redirects' => '0',
        'ignore_errors' => '1'
    )
);

$context = stream_context_create($opts);
$stream = fopen($url, 'r', false, $context);

// header information as well as meta data
// about the stream
var_dump(stream_get_meta_data($stream));

// actual data at $url
//var_dump(stream_get_contents($stream));
// Resultado
$json_obj = json_decode(stream_get_contents($stream), true);
var_dump($json_obj);
echo $json_obj['result'];
//echo $json_obj->{'data'}->{'usuario'}->{'email'};
if($json_obj['result'] === "ok"){
    // Registro
    print 'OKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK:';
    echo $json_obj['data']['launch_parameters']['iss'];
    $_SESSION['iss'] = array_merge($_SESSION['iss'], [$json_obj['data']['launch_parameters']['iss'] => $json_obj['data']['credentials']]);
    echo $_SESSION['iss'];
}
fclose($stream);

// Obtiene la configuración de los sitios del directorio `/configs` y de fichero JSON
$reg_configs = array_diff(scandir(__DIR__ . '/configs'), array('..', '.', '.DS_Store'));
foreach ($reg_configs as $key => $reg_config) {
    $_SESSION['iss'] = array_merge($_SESSION['iss'], json_decode(file_get_contents(__DIR__ . "/configs/$reg_config"), true));
    echo json_decode(file_get_contents(__DIR__ . "/configs/$reg_config"), true);
}
class Example_Database implements LTI\Database {
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

    // Obtiene la cave privada de cada sitio `$iss`
    private function
    private_key($iss) {
        return file_get_contents(__DIR__ . $_SESSION['iss'][$iss]['private_key_file']);
    }
}
?>