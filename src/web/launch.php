<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/lti_database.php';

use \IMSGlobal\LTI;

$launch = LTI\LTI_Message_Launch::new(new Lti_Database())
    ->validate();

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
  "></iframe>', $_SERVER['REQUEST_URI'];

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
