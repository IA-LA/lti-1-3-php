<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/lti_database.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
$post_param = json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true);

use \IMSGlobal\LTI;
//print_r($_REQUEST);
print('<p>' . $_REQUEST['iss']);
//print('<p>' . $_REQUEST['login_hint']);
print('<p>' . $_REQUEST['target_link_uri']);
//print('<p>' . $_REQUEST['lti_message_hint']);
//print('<p>' . $_REQUEST['id_token']);
print('<p>' . $_REQUEST['state']);
print('<p>');
print_r($post_param);
// TODO leer `target_link_uri` del servicio GET por la `iss` !!!!!!!!!
$launch = LTI\LTI_Message_Launch::new(new Lti_Database(["iss" => $_REQUEST['iss'], "login_hint" => $_REQUEST['login_hint'], "target_link_uri" => $_REQUEST['target_link_uri'], "lti_message_hint" => $_REQUEST['lti_message_hint']]))
    ->validate();

// REDIRECTION HEADER
//header('Location: ' . TOOL_PARAMS_TARGET, true, 302);
//die;

// IFRAME FULL PAGE cross-browser and fully responsive
//  https://stackoverflow.com/questions/17710039/full-page-iframe
echo '<embed id="frame" src="' . $_REQUEST['target_link_uri'] . '"   style="
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
    height: 100%;
  "/>' .
  '<p>VARIABLES GET:</p>', $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER['QUERY_STRING'],
  '<p>VARIABLES POST:</p>', $_POST['state'], $_POST['id_token'],
  '<hr/>',
  '<br/><b>PLATFORM:</b> <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/tool_platform']['name'], '</a></b>',
  '<hr/>',
  '<br/><b>ISS: <a href="http://Haz.que.Lti_Database.tome.este.parámetro.ISS.de.la.llamada.POST">', $post_param['iss'], '</a></b>',
  '<br/><b>TARGET_LINK_URI: <a href="http://Haz.que.Lti_Database.tome.TARGET_LINK_URI.llamada.POST">', $post_param['https://purl.imsglobal.org/spec/lti/claim/target_link_uri'], '</a></b>',
  '<br/><b>TYPE: <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/message_type'], '</a></b>',
  '<br/><b>VERSION: <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/version'], '</a></b>',
  '<br/><b>USER: <a href="http://">', $post_param['name'], '</a></b>',
  '<br/><b>EMAIL: <a href="http://">', $post_param['email'], '</a></b>',
  '<br/><b>ROL: <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/roles'][0], '</a></b>'
  ;

?>

<?php
    if ($launch->is_resource_launch()) {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== LtiResourceLinkRequest
        echo '<hr/><br/><b>Resource Link Request Launch!</b>';
    } else if ($launch->is_deep_link_launch()) {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== LtiDeepLinkingRequest
        echo '<hr/><br/><b>Deep Linking Request Launch!</b>';
?>
        <div id="config">
            <br/>LAUNCH DEEP LINK:
            <?php
            echo $_REQUEST['iss'], $_REQUEST['target_link_uri'];
            ?>
        </div>
<?php
        die;
    } else {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== otros tipos
        echo '<hr/><br/><b>Unknown launch type</b>';
    }
?>
