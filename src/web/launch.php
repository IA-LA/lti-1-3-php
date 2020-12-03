<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/example_database.php';

use \IMSGlobal\LTI;

$launch = LTI\LTI_Message_Launch::new(new Example_Database())
    ->validate();

echo '<iframe id="frame" src="https://www.bing.es" onload="document.getElementById(\'frame\').src=\'\'"><\/iframe>'

?>

<!-- <iframe id="frame" src="https://www.bing.es" onload="document.getElementById('frame').src=''"></iframe> -->

<?php
    if ($launch->is_deep_link_launch()) {
?>
        <div id="config">
        </div>
<?php
    die;
    }
?>
