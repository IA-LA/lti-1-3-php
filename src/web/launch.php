<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/lti_database.php';
define("TOOL_PARAMS_ISS", $_REQUEST['iss'] );
define("TOOL_PARAMS_LOGIN", $_REQUEST['login_hint'] );
define("TOOL_PARAMS_TARGET", $_REQUEST['target_link_uri'] );
define("TOOL_PARAMS_LTI", $_REQUEST['lti_message_hint'] );

use \IMSGlobal\LTI;
print($_REQUEST['iss'] . $_REQUEST['login_hint'] . $_REQUEST['target_link_uri'] . _REQUEST['lti_message_hint']);
$launch = LTI\LTI_Message_Launch::new(new Lti_Database(["iss" => TOOL_PARAMS_ISS, "login_hint" => $_REQUEST['login_hint'], "target_link_uri" => TOOL_PARAMS_TARGET, "lti_message_hint" => $_REQUEST['lti_message_hint']]))
    ->validate();

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
$post_param = json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true);
// IFRAME FULL PAGE cross-browser and fully responsive
//  https://stackoverflow.com/questions/17710039/full-page-iframe
echo '<iframe id="frame" src="' . $_REQUEST['target_link_uri'] . '"   style="
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
  "></iframe> <p>VARIABLES GET:</p>', $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER['QUERY_STRING'],
  '<p>VARIABLES POST:</p>', $_POST['state'], $_POST['id_token'], $post_param['iss'], $post_param['https://purl.imsglobal.org/spec/lti/claim/target_link_uri'];

?>

<?php
    if ($launch->is_deep_link_launch()) {
?>
        <div id="config">
            <?php
                echo $_REQUEST['iss'], $_REQUEST['target_link_uri'];
            ?>
        </div>
<?php
    die;
    }
?>
