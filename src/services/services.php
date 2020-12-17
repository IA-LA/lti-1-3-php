<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

class services
{

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

        $_SESSION['iss'] = [];
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(array $request = null) {
        return new services($request);
    }

    /**
     * Calculate the redirect location to return to based on an OIDC third party initiated login request.
     *
     * @param string        $url URL to redirect back to after the OIDC login. This URL must match exactly a URL white listed in the platform.
     * @param array|string  $request    An array of request parameters. If not set will default to $_REQUEST.
     *
     * @return String Returns a redirect object containing the fully formed OIDC login URL.
     */
    public function info($url, array $request = null) {

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

        return $url;
    }

    /**
     * Calculate the redirect location to return to based on an OIDC third party initiated login request.
     *
     * @param string        $url URL to redirect back to after the OIDC login. This URL must match exactly a URL white listed in the platform.
     * @param string        $rest GET, POST, PUT, DELETE, etc. Command to redirect back to after the OIDC login. This URL must match exactly a URL white listed in the platform.
     * @param array|string  $request    An array of request parameters. If not set will default to $_REQUEST.
     *
     * @return Redirect Returns a redirect object containing the fully formed OIDC login URL.
     */
    public function REST($url, $rest, array $request = null) {

        // Conectar con servicio READ
        //  get_iss($iss);
        /////////////////////////////

        // Llamadas REST
        //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
        //  https://www.php.net/manual/en/context.http.php
        // Obtiene la configuración de las actividades con una llamada de lectura `GET`
        ///////////////////
        $url = "http://10.201.54.31:49151/servicios/lti/lti13/read/" . TOOL_PARAMS_ISS;

        $opts = array('http' =>
            array(
                'method' => $rest,
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

        return $_SESSION['iss'];
    }
}