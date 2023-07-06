<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../db/iss_target_lti_database.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

use \IMSGlobal\LTI;
use \IMSGlobal\LTI\Cookie;

// SERVICIOS
use Services\Services;

try {

    // COMPROBACION problema ERROR 'Failed to fetch public key'
    //$w = stream_get_wrappers();
    //echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "\n";
    //echo 'http wrapper: ', in_array('http', $w) ? 'yes':'no', "\n";
    //echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "\n";
    //echo 'wrappers: ', var_export($w);

    // REDIRECCION POST
    // JWT Claims decode
    // https://auth0.com/blog/id-token-access-token-what-is-the-difference/
    $post_param = json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true);
    //print_r($post_param);
    //print('<p>' . $_REQUEST['state']);
    //$post_parm['state'] = $_REQUEST['state'];
    //die;

    ////$login = LTI\LTI_OIDC_Login::new(new Iss_Target_Lti_Database())
    /////    ->do_oidc_login_redirect(TOOL_REDIR)
    //////    ->do_js_redirect();

    //$cookie = new Cookie('lti1p3_' . $_REQUEST['state']);
    //$cookie->set_cookie('lti1p3_' . $_REQUEST['state'], $_REQUEST['state']);
    //print_r($cookie);

    // Valida el Lanzamiento
    // Lee los parámetros de la Redirección POST de la Plataforma
    //$launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database()) //;
    // Usa una Cookie pero falla al evitar el ERROR ´State not found´
    //$launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database(), null, $cookie);
    $launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database())//;
    // Intenta evitar ERROR ´State not found´ en NAVEGACION PRIVADA
    //$launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database($_REQUEST))//;
    //$launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database($post_param))//;
    //->validate();
    ->validate($_REQUEST);
    //->validate($post_param);

    // CLASE SERVICIOS
    // Conectar con servicios CRUD
    //  get_iss($iss);
    // Ej.: http://192.168.0.31:9002/login.php?iss=5fd9e0b286cb7926b85375e5&login_hint=123456&target_link_uri=http://192.168.0.31:8000/uploads/publicacion/10020210506073929000000a/&lti_message_hint=123456
    /////////////////////////////
    $serv = new Services($_REQUEST);
    $serv = Services::new($_REQUEST);

    // Llamadas REST
    //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
    //  https://www.php.net/manual/en/context.http.php
    // Obtiene la configuración de las actividades con una llamada de lectura `GET`
    // al servidor de SERVICIOS
    ///////////////////////////
    /// Platform ('HTTP_ORIGIN' o 'HTTP_REFERER')
    $iss_GET =  $serv->service('read', 'Platform', 'id_actividad', $_SERVER['HTTP_ORIGIN'], $_REQUEST);
    /// Lti Activity ()
    $activity_GET = $serv->service('read', 'Lti', 'url_actividad', (string)$post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"], $_REQUEST);

    // LLAMADA OK
    // Contenido Resultado de las llamadas existe
    //if(($json_obj['result'] === "ok")){
    //if(($iss_GET['result'] === "ok") && ($activity_GET['result'] === "ok")) {
    // Difusión GICCU (dependiendo del perfil)
    // URL eContent (Extensión): https://www.intecca.uned.es/difusiongiccu/extension/61810f9c74d032d10c623378
    // URL eContent (Pre Grado): https://www.intecca.uned.es/giccu/salidaweb/5e46670337ebc61534f37c4a/5e46673e37ebc61534f37c4c/61810f9c74d032d10c623378/index.html
    // URL Trabajo GICCU       : https://www.intecca.uned.es/giccu/trabajos/61810f9c74d032d10c623378
    // Perfiles (https://purl.imsglobal.org/spec/lti/claim/roles):
    //      http://purl.imsglobal.org/vocab/lis/v2/membership#Learner
    //      http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor
    //      http://purl.imsglobal.org/vocab/lis/v2/system/person#Administrator
    //      http://purl.imsglobal.org/vocab/lis/v2/institution/person#Administrator
    //
    if(($iss_GET['result'] === "ok") && ($activity_GET['result'] === "ok")) {

        // RELOCATION HEADER
        //header('X-Frame-Options: ' . 'SAMEORIGIN', true);
        //header('Location: ' . $post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"], true, 302);
        //die;

        // GET
        //print_r($_REQUEST);
        //print('<p>' . $_REQUEST['iss']);
        //print('<p>' . $_REQUEST['login_hint']);
        //print('<p>' . $_REQUEST['target_link_uri']);
        //print('<p>' . $_REQUEST['lti_message_hint']);
        //print('<p>' . $_REQUEST['id_token']);
        //print('<p>' . $_REQUEST['state']);
        // POST
        //print('<p>' . $post_param['iss'] . $post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"] . $post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"]);
        //print_r($post_param);
        //print('</p>');

        // REDIRECTION HEADER
        //header('Location: ' . TOOL_PARAMS_TARGET, true, 302);
        //die;

            ///////////////////////////////////////////////
            /// ACCESS TOKEN (INICIO)
            /// NRPS

            // Build up JWT to exchange for an auth token
            $client_id = $post_param['aud'];
            $jwt_claim = [
                "iss" => '$client_id',
                "sub" => $client_id,
                //"aud" => 'http://ailanto-dev.intecca.uned.es/mod/lti/auth.php',
                "aud" => $iss_GET['data']['credentials']['auth_login_url'],
                "iat" => time() - 5,
                "exp" => time() + 60,
                "jti" => 'lti-service-token_' . hash('sha256', random_bytes(64))
            ];

            //
            // ERROR: { "error" : "invalid_request" }
            // ERROR: { "error" : "invalid_client" }
            // ERROR: { "error" : ""kid" empty, unable to lookup correct key" }
            //$headers = [
            //    'kid: ff25d970a021ff7cdad1',
            //];
            $kid=[];
            $kid[0]='ff25d970a021ff7cdad1';
            // Sign the JWT with our private key (given by the platform on registration) and the JWKS json kid
            $jwt = JWT::encode($jwt_claim, file_get_contents(__DIR__ . '/../../db/private.key'), 'RS256', json_decode(file_get_contents(__DIR__ . '/../jwks.json'), true)['kid']);
            //$jwt = JWT::encode($jwt_claim, file_get_contents(__DIR__ . '/../../db/tool.key'), 'RS256');

            // Build auth token request headers
            $auth_request = [
                'grant_type' => 'client_credentials',
                'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                'client_assertion' => $jwt,
                'scope' => 'https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly',
                //'scope' => implode(' ', ["https://purl.imsglobal.org/spec/lti-ags/scope/lineitem", "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly", "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly", "https://purl.imsglobal.org/spec/lti-ags/scope/score"])
            ];

            // Make request to get auth token
            $ch = curl_init();
            //curl_setopt($ch, CURLOPT_URL, 'http://ailanto-dev.intecca.uned.es/mod/lti/token.php');
            curl_setopt($ch, CURLOPT_URL, $iss_GET['data']['credentials']['auth_token_url']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($auth_request));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $resp = curl_exec($ch);
            $token_data = json_decode($resp, true);
            curl_close ($ch);

            //echo "<br/><br/><b>(NRPS) ACCESS TOKEN: </b>";
            //print_r($ch);
            //print_r($resp);
            //print_r($token_data);
            //echo($token_data['access_token']);

            /// NRPS
            /// ACCESS TOKEN    (FIN)
            ///////////////////////////////////////////////

        ///////////////////////////////////////////////
        /// TOKENS (INICIO)
        /// AGS
        ///
            ///////////////////////////////////////////////
            /// ACCESS TOKEN (INICIO)
            /// AGS

            // Build up JWT to exchange for an auth token
            $client_id = $post_param['aud'];
            $jwt_claim = [
                "iss" => '$client_id',
                "sub" => $client_id,
                //"aud" => 'http://ailanto-dev.intecca.uned.es/mod/lti/auth.php',
                "aud" => $iss_GET['data']['credentials']['auth_login_url'],
                "iat" => time() - 5,
                "exp" => time() + 60,
                "jti" => 'lti-service-token_' . hash('sha256', random_bytes(64))
            ];

            //
            // ERROR:
            // ERROR: HTTP/1.1 400 No handler found for /2/lineitems/32/lineitem/scores application/json
            // ERROR: HTTP/1.1 400 Incorrect score received
            // ERROR: {"status":401,"reason":"Unauthorized","request":{"method":"POST","url":"\/mod\/lti\/services.php\/2\/lineitems\/32\/lineitem\/scores?type_id=3","accept":"application\/json","contentType":"application\/vnd.ims.lis.v1.score+json"}}}
            //$headers = [
            //    'kid: ff25d970a021ff7cdad1',
            //];
            $kid=[];
            $kid[0]='ff25d970a021ff7cdad1';
            // Sign the JWT with our private key (given by the platform on registration) and the JWKS json kid
            $jwt = JWT::encode($jwt_claim, file_get_contents(__DIR__ . '/../../db/private.key'), 'RS256', json_decode(file_get_contents(__DIR__ . '/../jwks.json'), true)['kid']);
            //$jwt = JWT::encode($jwt_claim, file_get_contents(__DIR__ . '/../../db/tool.key'), 'RS256');

            // Build auth token request headers
            $auth_request = [
                'grant_type' => 'client_credentials',
                'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                'client_assertion' => $jwt,
                //'scope' => 'https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly',
                'scope' => implode(' ', ["https://purl.imsglobal.org/spec/lti-ags/scope/lineitem", "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly", "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly", "https://purl.imsglobal.org/spec/lti-ags/scope/score"])
            ];

            // Make request to get auth token
            $ch = curl_init();
            //curl_setopt($ch, CURLOPT_URL, 'http://ailanto-dev.intecca.uned.es/mod/lti/token.php');
            curl_setopt($ch, CURLOPT_URL, $iss_GET['data']['credentials']['auth_token_url']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($auth_request));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $resp_ags = curl_exec($ch);
            $token_data_ags = json_decode($resp_ags, true);
            curl_close ($ch);

            //echo "<br/><br/><b>(AGS) ACCESS TOKEN: </b>";
            //print_r($ch);
            //print_r($resp_ags);
            //print_r($token_data_ags);
            //echo($token_data_ags['access_token']);

            /// AGS
            /// ACCESS TOKEN    (FIN)
            ///////////////////////////////////////////////

            ///////////////////////////////////////////////
            /// REFRESH TOKEN (INICIO)
            /// AGS

            // Build up JWT to exchange for an auth token
            $client_id = $post_param['aud'];
            $jwt_claim = [
                "iss" => '$client_id',
                "sub" => $client_id,
                //"aud" => 'http://ailanto-dev.intecca.uned.es/mod/lti/auth.php',
                "aud" => $iss_GET['data']['credentials']['auth_login_url'],
                "iat" => time() - 5,
                "exp" => time() + 60,
                "jti" => 'lti-service-token_' . hash('sha256', random_bytes(64))
            ];

            //
            // ERROR:
            // ERROR: HTTP/1.1 400 No handler found for /2/lineitems/32/lineitem/scores application/json
            // ERROR: HTTP/1.1 400 Incorrect score received
            // ERROR: {"status":401,"reason":"Unauthorized","request":{"method":"POST","url":"\/mod\/lti\/services.php\/2\/lineitems\/32\/lineitem\/scores?type_id=3","accept":"application\/json","contentType":"application\/vnd.ims.lis.v1.score+json"}}}
            //$headers = [
            //    'kid: ff25d970a021ff7cdad1',
            //];
            // $kid=[];
            // $kid[0]='ff25d970a021ff7cdad1';
            // Sign the JWT with our private key (given by the platform on registration) and the JWKS json kid
            $jwt = JWT::encode($jwt_claim, file_get_contents(__DIR__ . '/../../db/private.key'), 'RS256', json_decode(file_get_contents(__DIR__ . '/../jwks.json'), true)['kid']);
            //$jwt = JWT::encode($jwt_claim, file_get_contents(__DIR__ . '/../../db/tool.key'), 'RS256');

            // Build auth token request headers
            $auth_request = [
                'grant_type' => 'authorization_code',
                'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                'client_assertion' => $jwt,
                //'scope' => 'https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly',
                'scope' => implode(' ', ["https://purl.imsglobal.org/spec/lti-ags/scope/lineitem", "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly", "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly", "https://purl.imsglobal.org/spec/lti-ags/scope/score"])
            ];

            // Make request to get refresh auth token
            $ch = curl_init();
            //curl_setopt($ch, CURLOPT_URL, 'http://ailanto-dev.intecca.uned.es/mod/lti/token.php');
            curl_setopt($ch, CURLOPT_URL, $iss_GET['data']['credentials']['auth_token_url']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($auth_request));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $resp_ags = curl_exec($ch);
            $refresh_token_data_ags = json_decode($resp_ags, true);
            curl_close ($ch);

            echo "<br/><br/><b>(AGS) authorization_code ACCESS TOKEN: </b>";
            //print_r($ch);
            //print_r($resp_ags);
            print_r($refresh_token_data_ags);
            //echo($token_data_ags['access_token']);

            /// AGS
            /// REFRESH TOKEN    (FIN)
            ///////////////////////////////////////////////
            ///

        ///
        /// AGS
        /// TOKENS    (FIN)
        ///////////////////////////////////////////////
        ///
    }
    else
        echo '405 No permitido';

    // IFRAME FULL PAGE cross-browser and fully responsive
    //  https://stackoverflow.com/questions/17710039/full-page-iframe
    // ALTERNATIVES
    //  https://www.geeksforgeeks.org/alternative-to-iframes-in-html5/
    // TODO+NE Incidencia `$_REQUEST is not defined`
    // Creadas variables y parámetros para enviar al CLiente el JWT
    $authTokenData='\'var $_REQUEST = {"id_token\":\"' . $_REQUEST['id_token'] . '\"};\'';
    $authTokenData='\'var $_REQUEST = {
                        "id_token\":\"' . $_REQUEST['id_token'] . '\",
                        "id_token\":\"' . $resp . '\",
                        "id_token\":\"' . $resp_ags . '\"
                    };\'';
    $authTokenData='{
                    "id_token": "' . $_REQUEST['id_token'] . '",
                    "auth_token_nrps": ' . $resp . ',
                    "auth_token_ags": ' . $resp_ags . '
                  }';
    $authTokenScript='function loadToken() {
                            var iframe = document.getElementById(\'embedE\');
                            var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
                            var scriptSource = ' . $authTokenData . '
                                  };
                            var script = iframeDocument.createElement(\'script\');
                            script.setAttribute(\'id\',\'data\');
                            script.setAttribute(\'type\',\'application/json\');
                            var source = iframeDocument.createTextNode(scriptSource);
                            script.appendChild(source);
                            iframeDocument.body.appendChild(script);
                        }';
    echo '<script
>
            // Same-Origin Policy (SOP)
            // Cross-Origin Resource Sharing (CORS)
            document.domain = "uned.es";
            
            function loadToken() {
                /* 
                    https://javascript.info/cross-window-communication
                 */
                //Si no hay tokens generados
                if(document.getElementById("data") === null ){
                    var iframe = document.getElementById("embedE");
                    //var iframeDocument = iframe.contentDocument;// || iframe.contentWindow.document;
                    //var innerDoc = (iframe.contentDocument);// ? iframe.contentDocument : iframe.contentWindow.document;
                    //var scriptSource = ' . '$authTokenData' . ';
                    //var scriptSource = JSON.stringify(' . '$authTokenData' . ');
                    var scriptSource = "var $_REQUEST = " + JSON.stringify(' . $authTokenData . ');
                    var script = document.createElement("script");
                    script.setAttribute("id","data");
                    script.setAttribute("type","application/json");
                    var source = document.createTextNode(scriptSource);
                    script.appendChild(source);
                    //document.write(JSON.stringify(script));
                    // var innerDoc = iframe.contentDocument || iframe.contentWindow.document;
                    // var innerDoc = iframe.contentDocument;
                    // var body = innerDoc.getElementsByTagName("body");
                    //$(\'#embedE\').contents().find(\'body\').html(\'Hey, I have changed content of <body>! Yay!!!\');
                    // body.appendChild(script);
                    iframe.appendChild(script);
                 }
                // document.write(JSON.parse(document.getElementById("data").text)["id_token"]);
                //document.write("iFrame: " + iframe.document);
                //document.write("frames: " + window.frames["embedE"]);
                //document.write("body: " + iframe.getElementsByTagName("body"));
                //document.write("<innerHTML>: " + iframe.getElementsByTagName("document").innerHTML);
                //document.write("<var $_REQUEST>: " + $_REQUEST["id_token"]);
            }                
        </script>
        <!-- 
        CORS: estrategia JONP
        https://www.ionos.es/digitalguide/paginas-web/desarrollo-web/jsonp/#c210966
        <script type="text/javascript" src="https://agora.uned.es/mod/lti/services.php/16/lineitems?type_id=10"/> 
        --> 
        <!-- https://stackoverflow.com/questions/1763508/passing-arrays-as-url-parameter -->
        <embed id="embedE" src="' . ($post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]) . '?id_token=' . $_REQUEST['id_token'] . '&auth_token_nrps=' . urlencode(json_encode($token_data)) . '&auth_token_ags=' . urlencode(json_encode($token_data_ags)) . '"
            style="
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            width: 100%;
            border: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            z-index: 999999;
            height: 100%;"
            onload=\'setTimeout(function () {
                                    loadToken();
                                }, 5000);\'/>
            <!--
            <iframe id="frame" src="' . $post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"] . '"
            allowfullscreen="true" allowpaymentrequest="true"
            style="
            position: fixed;
            top: 0px;
            bottom: 0px;
            right: 0px;
            width: 100%;
            border: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            z-index: 999999;
            height: 100%;"></iframe>
            -->
             <script>    
              embedE.onload = function() {
                // we can get the reference to the inner window
                let iframeWindow = embedE.contentWindow; // OK
                try {
                  // ...but not to the document inside it
                  let doc = embedE.contentDocument; // ERROR
                } catch(e) {
                  alert(e); // Security Error (another origin)
                }
            
                // also we can not READ the URL of the page in iframe
                try {
                  // Can not read URL from the Location object
                  let href = embedE.contentWindow.location.href; // ERROR
                } catch(e) {
                  //alert(e); // Security Error
                }
            
                // ...we can WRITE into location (and thus load something else into the iframe)!
                //embedE.contentWindow.location = \'/\'; // OK
            
                //embedE.onload = null; // clear the handler, not to run it after the location change
                embedE.onload=setTimeout(function () {
                                    loadToken();
                                }, 5000);
              };
        </script>' .
            '<!--',
            '<p>VARIABLES GET:</p>', $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER['QUERY_STRING'],
            '<p>VARIABLES POST:</p>', $_POST['state'], $_POST['id_token'],
            '<hr/>',
            '<br/><b>PLATFORM:</b> <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/tool_platform']['name'], '</a></b>',
            '<hr/>',
            '<br/><b>ISS: <a href="http://Hecho.que.Lti_Database.tome.este.parámetro.ISS.de.la.llamada.GET/POST">', $post_param['iss'], '</a></b>',
            '<br/><b>LOGIN_HINT: <a href="http://Hecho.que.Lti_Database.tome.este.parámetro.ISS.de.la.llamada.GET/POST">', "no disponible", '</a></b>',
            '<br/><b>TARGET_LINK_URI: <a href="http://Hecho.que.Lti_Database.tome.TARGET_LINK_URI.de.la.llamada.GET/POST">', $post_param['https://purl.imsglobal.org/spec/lti/claim/target_link_uri'], '</a></b>',
            '<br/><b>LTI_MESSAGE_HINT: <a href="http://Hecho.que.Lti_Database.tome.LTI_MESSAGE_HINT.de.la.llamada.GET/POST">', $post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"], '</a></b>',
            '<br/><b>TYPE: <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/message_type'], '</a></b>',
            '<br/><b>VERSION: <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/version'], '</a></b>',
            '<br/><b>USER: <a href="http://">', $post_param['name'], '</a></b>',
            '<br/><b>EMAIL: <a href="http://">', $post_param['email'], '</a></b>',
            '<br/><b>ROL: <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/roles'][0], '</a></b>',
            '-->'
        ;

    ?>
    <!-- Contenido
    <p>Hola <?php echo $post_param["given_name"]; ?>, bienvenid@ al eContent ´<?php echo $post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["title"]; ?>´ del curso ´<?php echo $post_param["https://purl.imsglobal.org/spec/lti/claim/context"]["title"]; ?>´ como <?php echo explode('#', $post_param['https://purl.imsglobal.org/spec/lti/claim/roles'][0])[1]; ?> </p>
    -->
    <?php
    if ($launch->is_resource_launch()) {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== LtiResourceLinkRequest
        echo '<!-- <hr/><br/><b>Resource Link Request Launch!</b> -->';
        echo('<!--');
        echo('<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>');
        echo('ID_TOKEN:');
        print_r($post_param);
        echo('-->');
    } else if ($launch->is_deep_link_launch()) {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== LtiDeepLinkingRequest
        echo '<!-- <hr/><br/><b>Deep Linking Request Launch!</b> -->';
        ?>
        <!--
        <div id="config">
            <br/>LAUNCH DEEP LINK:
            <?php
        echo $post_param['iss'], $post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"];
        ?>
        </div>
     -->
        <?php
        die;
    } else {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== otros tipos
        echo '<!-- <hr/><br/><b>Unknown launch type</b> -->';
    }
}
catch (IMSGlobal\LTI\LTI_Exception $e){

    echo ("<h1>Error de validación de credenciales....</h1>");
    exit($e->getMessage());
}
?>