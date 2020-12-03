<?php
require_once __DIR__ . '/../../db/example_database.php';
?>
<ul>
    <li>Fancy LMS</li>
    <li>Users</li>
    <li>Courses</li>
    <!-- <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=http%3A%2F%2Flocalhost:9001&login_hint=12345&target_link_uri=http%3A%2F%2Flocalhost%2Fgame.php&lti_message_hint=12345'">Games 101</li> -->
    <!-- <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=<?php echo TOOL_HOST; ?>:9001&login_hint=123456&target_link_uri=<?php echo TOOL_HOST; ?>%2Fgame.php&lti_message_hint=123456'">Games 101</li> -->
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=http%3A%2F%2Flocalhost:9001&login_hint=123456&target_link_uri=<?php echo TOOL_HOST; ?>%2FFgame.php&lti_message_hint=123456'">Pruebas 101</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login_example.php?iss=http%3A%2F%2Flocalhost:9001&login_hint=123456&target_link_uri=<?php echo TOOL_HOST; ?>%2Fgame.php&lti_message_hint=123456'">Example 102</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login_econtent.php?iss=http%3A%2F%2Flocalhost:9001&login_hint=123456&lti_message_hint=123456'">Econtent 103</li>

    <li>Settings</li>
</ul>
<iframe id="frame" style="width:1200px; height:600px" >

</iframe>
<style>
ul {
    position:absolute;
    left:0;
    top:0;
    width:200px;
    bottom:0;
    background-color:darkslategray;
    color: white;
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-size: 28px;
    font-weight: bold;
    margin:0;
    list-style-type: none;
}
li {
    padding-top: 26px;

}
li.sub {
    padding-left:26px;
    font-size: 24px;
}
iframe {
    position: absolute;
    margin-left: 250px;
}
</style>