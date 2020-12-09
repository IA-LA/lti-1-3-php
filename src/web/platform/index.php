<?php
require_once __DIR__ . '/../../db/lti_database.php';
?>
<ul>
    <li>Fancy LMS</li>
    <li>Users</li>
    <li>Courses</li>
    <!-- <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=http%3A%2F%2Flocalhost:9001&login_hint=12345&target_link_uri=http%3A%2F%2Flocalhost%2Fgame.php&lti_message_hint=12345'">Games 101</li> -->
    <!-- <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=<?php echo TOOL_HOST; ?>:9001&login_hint=123456&target_link_uri=<?php echo TOOL_HOST; ?>%2Fgame.php&lti_message_hint=123456'">Games 101</li> -->
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=http://localhost:9001&login_hint=12345&target_link_uri=<?php echo TOOL_HOST; ?>/game.php&lti_message_hint=12345'">Games 101</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=http%3A%2F%2Flocalhost:9001&login_hint=123456&target_link_uri=<?php echo TOOL_HOST; ?>%2FPlantilla Azul_5e0df19c0c2e74489066b43f%2Findex_default.html&lti_message_hint=123456'">Pruebas 100</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=5fc3860a81740b0ef098a965&login_hint=123456&target_link_uri=http://10.201.54.31:9002%2FPlantilla Azul_5e0df19c0c2e74489066b43f%2Findex_default.html&lti_message_hint=123456'">BBDD 101</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=5fc3860a81740b0ef098a966&login_hint=123456&target_link_uri=https://www.uned.es&lti_message_hint=123456'">BBDD 102</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=5fc3860a81740b0ef098a967&login_hint=123456&target_link_uri=https://www.bing.es&lti_message_hint=123456'">BBDD 103</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login_default.php?iss=http%3A%2F%2Flocalhost:9001&login_hint=123456&target_link_uri=<?php echo TOOL_HOST; ?>%2Fgame.php&lti_message_hint=123456'">Example 101</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login_econtent.php?iss=http%3A%2F%2Flocalhost:9001&login_hint=123456&lti_message_hint=123456'">Econtent 101</li>

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