<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/lti_database.php';

use \IMSGlobal\LTI;

//$launch = LTI\LTI_Message_Launch::new(new Lti_Database())
//    ->validate();

?>
<link href="static/breakout.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Gugi" rel="stylesheet">

<div id="game-screen">
    <div style="position:absolute;width:1000px;margin-left:-500px;left:50%; display:block">
        <div id="scoreboard" style="position:absolute; right:0; width:200px; height:486px">
            <h2 style="margin-left:12px;">Scoreboard</h2>
            <table id="leadertable" style="margin-left:12px;">
            </table>
        </div>
        <canvas id="breakoutbg" width="800" height="500" style="position:absolute;left:0;border:0;">
        </canvas>
        <canvas id="breakout" width="800" height="500" style="position:absolute;left:0;">
        </canvas>
    </div>
</div>

<script type="text/javascript" src="static/breakout.js" charset="utf-8"></script>