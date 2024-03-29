<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/iss_target_lti_database.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

use \IMSGlobal\LTI;

try {

    // REDIRECCION POST
    // JWT Claims decode
    // https://auth0.com/blog/id-token-access-token-what-is-the-difference/
    $post_param = json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true);

    // Valida el Lanzamiento
    // Lee los parámetros de la Redirección POST de la Plataforma
    $launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database()) //;
       ->validate();
    
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
    //header('Location: ' . TOOL_PARAMS_TARGET . "?id_token=" . $_REQUEST['id_token'], true, 302);
    //die;

    // CORS HEADER
    // https://ubiq.co/tech-blog/set-access-control-allow-origin-cors-headers-apache/
    //header('Access-Control-Allow-Headers: *', true, 200);

    // IFRAME FULL PAGE cross-browser and fully responsive
    //  https://stackoverflow.com/questions/17710039/full-page-iframe
    // ALTERNATIVES
    //  https://www.geeksforgeeks.org/alternative-to-iframes-in-html5/
    // TODO+NE Incidencia `$_REQUEST is not defined`
    // Creadas variables y parámetros para enviar al CLiente el JWT
    echo '<embed id="embedL" src="' . ($post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]) . '?id_token=' . $_REQUEST['id_token'] . '&state=' . $_REQUEST['state'] . '"
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
        height: 100%;">
            <script hidden>
                // VAriable1
                var $_REQUEST = new Array(); 
                $_REQUEST["id_token"] = "' . $_REQUEST['id_token'] . '"; 
                console.log("ID_TOKEN($_REQUEST): " + $_REQUEST["id_token"]);

                // VAriable2
                var jwt = {id_token : "' . $_REQUEST['id_token'] . '"};
                console.log("ID_TOKEN(jwt): " + jwt.id_token);
                
                // Params GET
                console.log(location.search);
            </script>
        </embed>' .
        '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>',
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
        '<br/><b>ROL: <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/roles'][0], '</a></b>
        -->'
      ;

?>

<?php
    if ($launch->is_resource_launch()) {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== LtiResourceLinkRequest
        echo '<hr/><br/><b>Resource Link Request Launch!</b>';
        //echo('<!--');
        //echo('<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>');
        //echo('ID_TOKEN:');
        //print_r($post_param);
        //echo('-->');
    } else if ($launch->is_deep_link_launch()) {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== LtiDeepLinkingRequest
        echo '<hr/><br/><b>Deep Linking Request Launch!</b>';
?>
        <div id="config">
            <br/>LAUNCH DEEP LINK:
            <?php
            echo $post_param['iss'], $post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"];
            ?>
        </div>
<?php
        die;
    } else {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== otros tipos
        echo '<hr/><br/><b>Unknown launch type</b>';
    }
}
catch (IMSGlobal\LTI\LTI_Exception $e){

    echo ("<h1>Error de validación de credenciales...</h1>");
    exit($e->getMessage());
}
?>
