<?php
require_once __DIR__ . '/../../db/lti_database.php';
?>
<ul>
    <li>Fancy LMS <p>(<?php echo TOOL_HOST; ?>)</p></li>
    <li>Users</li>
    <li>Courses</li>
    <!-- <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=http%3A%2F%2Flocalhost:9001&login_hint=12345&target_link_uri=http%3A%2F%2Flocalhost%2Fgame.php&lti_message_hint=12345'">Games 101</li> -->
    <!-- <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=<?php echo TOOL_HOST; ?>:9001&login_hint=123456&target_link_uri=<?php echo TOOL_HOST; ?>%2Fgame.php&lti_message_hint=123456'">Games 101</li> -->
    <!-- <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=game&login_hint=12345&target_link_uri=<?php echo TOOL_HOST; ?>/game.php&lti_message_hint=12345'">Game</li> -->
    <!-- <li class="sub" onclick="document.getElementById('frame').src='http://10.201.54.31:9002/login_econtent.php?iss=http%3A%2F%2Flocalhost:9002&login_hint=123456&target_link_uri=http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43f/index.php&lti_message_hint=123456'">eContent</li> -->
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/game.php'">Game</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/Plantilla%20Azul_5e0df19c0c2e74489066b43g/index.html'">eContent</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=5fc3860a81740b0ef098a968&login_hint=123456&target_link_uri=http://10.201.54.31:9002/game.php&lti_message_hint=123456'">Game BD</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=5fc3860a81740b0ef098a983&login_hint=123456&target_link_uri=http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43f/index_default.html&lti_message_hint=123456'">eContent BD</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=5fc3860a81740b0ef098a966&login_hint=123456&target_link_uri=https://www.uned.es&lti_message_hint=123456'">UNED BD</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=5fc3860a81740b0ef098a967&login_hint=123456&target_link_uri=https://www.bing.es&lti_message_hint=123456'">Bing BD</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=5fc3860a81740b0ef098a971&login_hint=123456&target_link_uri=<?php echo TOOL_HOST; ?>%2Fgame.php&lti_message_hint=123456'">Game BD-PHP</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login.php?iss=5fc3860a81740b0ef098a972&login_hint=123456&target_link_uri=<?php echo TOOL_HOST; ?>/Plantilla Azul_5e0df19c0c2e74489066b43f/index.php&lti_message_hint=123456'">eContent BD-PHP</li>
    <!--
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login_default.php?iss=http%3A%2F%2Flocalhost:9001&login_hint=123456&target_link_uri=<?php echo TOOL_HOST; ?>%2Fgame.php&lti_message_hint=123456'">Example 1</li>
    <li class="sub" onclick="document.getElementById('frame').src='<?php echo TOOL_HOST; ?>/login_econtent.php?iss=http%3A%2F%2Flocalhost:9001&login_hint=123456&target_link_uri=<?php echo TOOL_HOST; ?>%2FPlantilla Azul_5e0df19c0c2e74489066b43f/index.php&lti_message_hint=123456'">Econtent 1</li>
    -->

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
    p {
        font-size: 12px;
        font-weight: normal;
    }
</style>
