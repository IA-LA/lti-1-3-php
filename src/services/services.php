<?php

class Services
{

    private $request;
    private $rest;
    private $protocol;
    private $url;
    private $ruta;
    private $response;

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
        return new Services($request);
    }

    /**
     * Getter and Setters
     * @return mixed
     */
    public function get_rest() {
        return $this->rest;
    }

    public function set_rest($rest) {
        $this->rest = $rest;
        return $this;
    }

    public function get_protocol() {
        return $this->protocol;
    }

    public function set_protocol($protocol) {
        $this->protocol = $protocol;
        return $this;
    }

    public function get_url() {
        return $this->url;
    }

    public function set_url($url) {
        $this->url = $url;
        return $this;
    }

    public function get_ruta() {
        return $this->ruta;
    }

    public function set_ruta($ruta) {
        $this->ruta = $ruta;
        return $this;
    }

    public function get_response() {
        return $this->response;
    }

    public function set_response($response) {
        $this->response = $response;
        return $this;
    }

    /**
     * Calculate the verb to return to a REST request.
     *
     * @param string        $method CRUD GET, POST, PUT, DELETE, etc. Command to redirect back to after the OIDC login. This URL must match exactly a URL white listed in the platform.
     * @param string        $model COLLETION to get BBDD (Lti, Platform, Upload, etc.).
     * @param string        $id ATTRIBUTE to search on cllection.
     * @param array|string  $request    An array of request parameters. If not set will default to $_REQUEST.
     *
     * @return String Returns a redirect object containing the fully formed OIDC login URL.
     */
    public function verb($method, array $request = null) {

        // Conectar con verbo servicio method
        //  create: post_iss($iss);
        //  read: get_iss($iss);
        //  update: put_iss($iss);
        //  delete: delete_iss($iss);
        /////////////////////////////
        $this->rest = 'GET';
        switch($method) {
            case 'create':
                $this->rest = 'POST';
                break;
            case 'read':
                $this->rest = 'GET';
                break;
            case 'update':
                $this->rest = 'PUT';
                break;
            case 'delete':
                $this->rest = 'DELETE';
                break;
        }

        return $this->rest;
    }

    /**
     * Calculate the redirect protocol to return to a REST request.
     *
     * @param string        $url URL to redirect back to after the OIDC login. This URL must match exactly a URL white listed in the platform.
     * @param array|string  $request    An array of request parameters. If not set will default to $_REQUEST.
     *
     * @return String Returns a redirect object containing the fully formed OIDC login URL.
     */
    public function protocol(array $request = null) {

        // Información servidor SERVICIOS protocol.url.ruta
        //  https://www.php.net/manual/es/function.header.php
        ///////////////////////
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            $this->protocol = "https://";
        else
            $this->protocol = "http://";

        return $this->protocol;
    }

    /**
     * Calculate the redirect URL to return to a REST request.
     *
     * @param array|string  $request    An array of request parameters. If not set will default to $_REQUEST.
     *
     * @return String Returns a redirect object containing the fully formed OIDC login URL.
     */
    public function url(array $request = null) {

        // Append the host(domain name, ip) to the URL.
        if(strpos($_SERVER['HTTP_HOST'], '.intecca.uned.es') || strpos($_SERVER['HTTP_HOST'], '193.146.230.217')){
            // SERVIDOR SERVICIOS GENERAL
            $this->url = '10.201.54.31';
        }
        else
            // SERVIDOR SERVICIOS EN LOCAL
            $this->url = explode(':', $_SERVER['HTTP_HOST'])[0];

        return $this->url;
    }

    /**
     * Calculate the redirect ruta to return to a REST request.
     *
     * @param string        $method CRUD GET, POST, PUT, DELETE, etc. Command to redirect back to after the OIDC login. This URL must match exactly a URL white listed in the platform.
     * @param string        $model COLLETION to get BBDD (Lti, Platform, Upload, etc.).
     * @param string        $id ATTRIBUTE to search on cllection.
     * @param array|string  $request    An array of request parameters. If not set will default to $_REQUEST.
     *
     * @return String Returns a redirect object containing the fully formed OIDC login URL.
     */
    public function ruta($method, $model, $id, array $request = null) {
        $this->ruta = ":49151/servicios/lti/lti13/". $method ."/coleccion/" . $model . "/id_actividad/" . $id;
        return $this->ruta;
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


    /**
     * Calculate the redirect location to return to based on an OIDC third party initiated login request.
     *
     * @param string        $method CRUD GET, POST, PUT, DELETE, etc. Command to redirect back to after the OIDC login. This URL must match exactly a URL white listed in the platform.
     * @param string        $model COLLETION to get BBDD (Lti, Platform, Upload, etc.).
     * @param string        $id ATTRIBUTE to search on cllection.
     * @param array|string  $request An array of request parameters. If not set will default to $_REQUEST.
     *
     * @return array|json Returns a redirect object containing the information of the REST action.
     */
    public function service($method, $model, $id , array $request = null) {

        if ($request === null) {
            $request = $_REQUEST;
        }

        // Componentes Llamada REST
        $rest = $this->verb($method);
        // Componentes Llamada URI
        $protocol = $this->protocol($_REQUEST);
        $url = $this->url($_REQUEST);
        $ruta = $this->ruta($method, $model, $id);

        // Append the requested resource location to the URL
        //$url.= $_SERVER['REQUEST_URI'];
        //echo $_REQUEST['target_link_uri'];

        try{
            // Llamada REST
            //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
            //  https://www.php.net/manual/en/context.http.php
            // Obtiene la configuración de las actividades con una llamada de lectura `GET`
            ///////////////////
            $opts = array('http' =>
                array(
                    'method' => $rest,
                    'timeout' => '5',
                    'ignore_errors' => '1'
                )
            );
            $context = stream_context_create($opts);
            $stream = fopen($protocol . $url . $ruta, 'r', false, $context);

            //echo ' STREAM11: ' . $stream;
            // ERROR LA ABRIR EL FLUJO
            if(!$stream) {
                //echo ' STREAM12: ' . $stream;
                // Ethernet
                $stream = fopen($protocol . "192.168.0.31" . $ruta, 'r', false, $context);
                // ERROR LA ABRIR EL FLUJO
                if(!$stream) {
                    //echo ' STREAM ERROR 13: ' . $stream;
                    // USB L
                    $stream = fopen($protocol . "192.168.42.10" . $ruta, 'r', false, $context);
                    // ERROR LA ABRIR EL FLUJO
                    if(!$stream) {
                        //echo ' STREAM ERROR 14: ' . $stream;
                        // Wifi H
                        $stream = fopen($protocol . "192.168.43.130" . $ruta, 'r', false, $context);
                        // ERROR LA ABRIR EL FLUJO
                        if(!$stream) {
                            //echo ' STREAM ERROR 15: ' . $stream;
                            // URL Servicios no encontrada
                            return ['result' => 'error', 'data' => 'URL de Servicios no disponible'];
                            exit(0);
                        }
                    }
                }
            }

            // header information as well as meta data
            // about the stream
            //var_dump(stream_get_meta_data($stream));

            // actual data at $url
            //var_dump(stream_get_contents($stream));

            // Result json
            //  https://www.php.net/manual/es/function.json-decode.php
            $json_obj = json_decode(stream_get_contents($stream), true, 5);
            //var_dump($json_obj);
            //echo $json_obj['result'];
            //echo $json_obj->{'data'}->{'usuario'}->{'email'};

            // LLAMADA OK
            // Contenido Resultado de la llamada
            //if(($json_obj['result'] === "ok") && ($json_obj['data']['launch_parameters']['target_link_uri'] === TOOL_PARAMS_TARGET)){
            if(($json_obj['result'] === "ok")){

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
                //$iss_get = [$json_obj['data']['id_actividad'] => $json_obj['data']['credentials']];
                //var_dump($_SESSION['iss']);
                return ['result' => 'ok', 'data' => $json_obj['data']];
                exit(0);
            }
            elseif ($json_obj['result'] === "error"){
                //echo ' STREAM ERROR 31: ' . $json_obj['result'];
                // Salida ERROR Actividad no encontrada
                return ['result' => 'error', 'data' => 'Credenciales inexistentes'];
                exit(0);
            }
            fclose($stream);

        }
        catch(Exception $e){
            //echo ' STREAM ERROR 21: ' . $stream;
            // Salida Excepción URL
            return ['result' => 'error', 'data' => 'Fallo al abrir el flujo'];
            exit(0);
        }
    }
    
}