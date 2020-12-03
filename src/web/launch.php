<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/example_database.php';

use \IMSGlobal\LTI;

$launch = LTI\LTI_Message_Launch::new(new Example_Database())
    ->validate();

?>

<iframe id="frame" src="<?php echo TOOL_PARAM; ?>" style="width:800px; height:400px"  onload="document.getElementById('frame').src=''">

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
