<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../db/iss_target_lti_database.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

use \IMSGlobal\LTI;
use \IMSGlobal\LTI\Cookie;
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
        ->validate();
        //->validate($_REQUEST);
        //->validate($post_param);

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

    // DIV cross-browser and fully responsive
    // https://www.nodejsauto.com/2020/08/iframe-where-src-what-is-blob.html
    // ALTERNATIVES
    // https://stackoverflow.com/questions/9245133/how-to-hide-iframe-src
    echo '
                <div id="divP"></div>' .

        // Inyección de publicación HTML
        //file_get_contents('https://ailanto-dev.intecca.uned.es/lti/publicacion/10220210903095251000000a/index.html') .

        '<!--',
        '<p>VARIABLES GET:</p>', $_SERVER['HTTP_ORIGIN'], $_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER['QUERY_STRING'],
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
        '-->';

    // https://purl.imsglobal.org/spec/lti/claim/message_type ==== LtiResourceLinkRequest
    echo '<!-- <hr/><br/><b>Resource Link Request Launch!</b> -->',
        '<script hidden>
                        // https://www.nodejsauto.com/2020/08/iframe-where-src-what-is-blob.html
                        // https://stackoverflow.com/questions/9245133/how-to-hide-iframe-src
                        var blobMe= URL["createObjectURL"](new Blob([""], {type: "text/html"}));
                        var elIframe = document["createElement"]("iframe");
                        elIframe["setAttribute"]("frameborder", "0");
                        elIframe["setAttribute"]("width", "100%");
                        elIframe["setAttribute"]("height", "500px");
                        elIframe["setAttribute"]("allowfullscreen", "true");
                        elIframe["setAttribute"]("webkitallowfullscreen", "true");
                        elIframe["setAttribute"]("mozallowfullscreen", "true");
                        elIframe["setAttribute"]("src", blobMe);
                        var idOne= "diffusion" + Date.now();
                        elIframe["setAttribute"]("id", idOne);
                        document.getElementById("htmlTest").appendChild(elIframe);
                        const iframeHere= "";
                        document["getElementById"](idOne)["contentWindow"]["document"].write("<script type=\'text/javascript\'>location.href = \'' . $activity_GET['data']['url_actividad'] . '\'\x3c/script>");
                    </script>';

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
