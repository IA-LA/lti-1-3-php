<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/example_database.php';

use \IMSGlobal\LTI;

$launch = LTI\LTI_Message_Launch::new(new Example_Database())
    ->validate();

?>

<iframe id="frame" style="width:800px; height:400px"  onload="document.getElementById('frame').src='<?php echo TOOL_PARAM; ?>'">
</iframe>

<?php
    if ($launch->is_deep_link_launch()) {
?>
        <div id="config">
        </div>
<?php
    die;
    }
?>

Fatal error: Uncaught IMSGlobal\LTI\LTI_Exception: State not found in /srv/app/vendor/imsglobal/lti-1p3-tool/src/lti/LTI_Message_Launch.php:243 Stack trace: #0 /srv/app/vendor/imsglobal/lti-1p3-tool/src/lti/LTI_Message_Launch.php(81): IMSGlobal\LTI\LTI_Message_Launch->validate_state() #1 /srv/app/web/game.php(8): IMSGlobal\LTI\LTI_Message_Launch->validate() #2 {main} thrown in /srv/app/vendor/imsglobal/lti-1p3-tool/src/lti/LTI_Message_Launch.php on line 243
