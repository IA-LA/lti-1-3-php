<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/example_database.php';

use \IMSGlobal\LTI;

$launch = LTI\LTI_Message_Launch::new(new Example_Database())
    ->validate();

echo '<iframe id="frame" src="' . TOOL_PARAM . '"   style="
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
  "></iframe>';

?>

<?php
    if ($launch->is_deep_link_launch()) {
?>
        <div id="config">
        </div>
<?php
    die;
    }
?>
