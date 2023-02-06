<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../db/iss_target_lti_database.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

use \IMSGlobal\LTI;
use \IMSGlobal\LTI\Cookie;

// SERVICIOS
use Services\Services;

try {

    // COMPROBACION problema ERROR 'Failed to fetch public key'
    //$w = stream_get_wrappers();
    //echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "\n";
    //echo 'http wrapper: ', in_array('http', $w) ? 'yes':'no', "\n";
    //echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "\n";
    //echo 'wrappers: ', var_export($w);

    // REDIRECCION POST
    // JWT Claims decode
    // https://auth0.com/blog/id-token-access-token-what-is-the-difference/
    $post_param = json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true);
    //print('<p>' . $_REQUEST['state'] . '</p>');
    //print_r($post_param);
    // Imprime variables de Servidor (HTTP_REFERER, REQUEST_URI, ...)
    //print_r($_SERVER);
    //die;

    ////$login = LTI\LTI_OIDC_Login::new(new Iss_Target_Lti_Database())
    /////    ->do_oidc_login_redirect(TOOL_REDIR)
    //////    ->do_js_redirect();

    //$cookie = new Cookie('lti1p3_' . $_REQUEST['state']);
    //$cookie->set_cookie('lti1p3_' . $_REQUEST['state'], $_REQUEST['state']);
    //print_r($cookie);

    // Valida el Lanzamiento
    // Lee los parámetros de la Redirección POST de la Plataforma
    //$launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database()) //;
    // Usa una Cookie pero falla al evitar el ERROR ´State not found´
    //$launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database(), null, $cookie);
    // Evita ERROR ´State not found´
    $launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database($_REQUEST))//;
    //$launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database($post_param))//;
        //->validate();
        ->validate($_REQUEST);
        //->validate($post_param);

    //LAUNCH TYPE:
    //  - LtiResourceLinkRequest
    //  - LtiDeepLinkingRequest
    //  - Otros tipos!!!
    if ($launch->is_resource_launch()) {

?>
        <!-- Contenido de JWT 1 RESOURCE LINK
        <p>Hola <b><?php echo $post_param["given_name"]; ?></b>, bienvenid@ al eContent ´<?php echo $post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["title"]; ?>´ del curso ´<?php echo $post_param["https://purl.imsglobal.org/spec/lti/claim/context"]["title"]; ?>´ como <?php echo explode('#', $post_param['https://purl.imsglobal.org/spec/lti/claim/roles'][0])[1]; ?>. </p>
        -->
<?php
        // CLASE SERVICIOS
        // Conectar con servicios CRUD
        //  get_iss($iss);
        // Ej.: http://192.168.0.31:9002/login.php?iss=5fd9e0b286cb7926b85375e5&login_hint=123456&target_link_uri=http://192.168.0.31:8000/uploads/publicacion/10020210506073929000000a/&lti_message_hint=123456
        /////////////////////////////
        $serv = new Services($_REQUEST);
        $serv = Services::new($_REQUEST);

        // Llamadas REST
        //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
        //  https://www.php.net/manual/en/context.http.php
        // Obtiene la configuración de las actividades con una llamada de lectura `GET`
        // al servidor de SERVICIOS
        ///////////////////////////
        /// Platform ('HTTP_ORIGIN' o 'HTTP_REFERER')
        $iss_GET =  $serv->service('read', 'Platform', 'id_actividad', $_SERVER['HTTP_ORIGIN'], $_REQUEST);
        /// Lti Activity ()
        $activity_GET = $serv->service('read', 'Lti', 'url_actividad', (string)$post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"], $_REQUEST);

        // LLAMADA OK
        // Contenido Resultado de las llamadas existe
        //if(($json_obj['result'] === "ok")){
        //if(($iss_GET['result'] === "ok") && ($activity_GET['result'] === "ok")) {
        // Difusión GICCU (dependiendo del perfil)
        // URL eContent (Extensión): https://www.intecca.uned.es/difusiongiccu/extension/61810f9c74d032d10c623378
        // URL eContent (Pre Grado): https://www.intecca.uned.es/giccu/salidaweb/5e46670337ebc61534f37c4a/5e46673e37ebc61534f37c4c/61810f9c74d032d10c623378/index.html
        // URL Trabajo GICCU       : https://www.intecca.uned.es/giccu/trabajos/61810f9c74d032d10c623378
        // Perfiles (https://purl.imsglobal.org/spec/lti/claim/roles):
        //      http://purl.imsglobal.org/vocab/lis/v2/membership#Learner
        //      http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor
        //      http://purl.imsglobal.org/vocab/lis/v2/system/person#Administrator
        //      http://purl.imsglobal.org/vocab/lis/v2/institution/person#Administrator
        //
        if(($iss_GET['result'] === "ok") && ($activity_GET['result'] === "ok")) {
            if(in_array("http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor", $post_param['https://purl.imsglobal.org/spec/lti/claim/roles'])) {
?>
                <!-- Contenido de JWT 1.1 RESOURCE LINK Instructor-->
                <p>Hola <b><?php echo $post_param["given_name"]; ?></b>, bienvenid@ al eContent ´<?php echo $post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["title"]; ?>´ del curso ´<?php echo $post_param["https://purl.imsglobal.org/spec/lti/claim/context"]["title"]; ?>´ como <?php echo explode('#', $post_param['https://purl.imsglobal.org/spec/lti/claim/roles'][0])[1]; ?>. </p>
                <!-- -->
<?php
                echo '               
                <p><b>Escoge una opción para acceder a la Actividad:</b></p>
                ';
                echo '
                <ul>
                    <li>               
                        <!-- VIEW -->
                        <!-- <form id="view" action="' . ($post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]) . '?id_token=' . $_REQUEST['id_token'] . '&state=' . $_REQUEST['state'] . '" method="POST"> -->
                        <!-- <form id="view" action="' . ($post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]) . '" method="GET"> -->
                        <form id="view" action="' . ($post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]) . '?id_token=' . $_REQUEST['id_token'] . '&state=' . $_REQUEST['state'] . '" method="GET">
                            <input type="hidden" name="id_token" value="' . $_REQUEST['id_token'] . '" />
                            <input type="hidden" name="state" value="' . $_REQUEST['state'] . '" />
                            <button type="submit" class="btn btn-success">Ver Actividad (' . $activity_GET["data"]["id_actividad"] . ')</button>
                        </form>
                    </li>
                ';
                echo '   
                    <li>         
                        <!-- EDIT  -->    
                        <!-- <form id="edit" action="https://www.intecca.uned.es/giccu/trabajos/' . explode('/', ($post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"]))[5] . '?id_token=' . $_REQUEST['id_token'] . '&state=' . $_REQUEST['state'] . '" method="POST"> -->
                        <!--  <form id="edit" action="https://www.intecca.uned.es/giccu/trabajos/628f467031e62395f35638b5" method="GET"> -->
                        <form id="edit" action="' . ((isset($activity_GET["data"]["trabajo_actividad"]) && !empty($activity_GET["data"]["trabajo_actividad"]) && ($activity_GET["data"]["trabajo_actividad"] !== '')) ? "https://www.intecca.uned.es/giccu/trabajos/" . $activity_GET["data"]["trabajo_actividad"] : "https://www.intecca.uned.es/giccu/") . '" method="GET">
                            <input type="hidden" name="id_token" value="' . $_REQUEST['id_token'] . '" />
                            <input type="hidden" name="state" value="' . $_REQUEST['state'] . '" />
                            <button type="submit" class="btn btn-Warning">' . ((isset($activity_GET["data"]["trabajo_actividad"]) && !empty($activity_GET["data"]["trabajo_actividad"]) && ($activity_GET["data"]["trabajo_actividad"] !== '')) ? "Editar Trabajo (" . $activity_GET["data"]["trabajo_actividad"] .")" : "Ir a GICCU") . '</button>
                        </form>
                    </li>
                </ul>';
                echo '                
                <!-- BOOTSTRAP  -->
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"/>
                <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
                <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

                <form role="form" method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                  <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email" value="' . htmlspecialchars($post_param["email"]) . '">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputUser" class="col-sm-2 col-form-label">User Name</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputUser" name="user" placeholder="Username" value="' . htmlspecialchars($post_param["name"]) . '">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Platform</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputPassword" name="password" placeholder="Password" value="' . htmlspecialchars($post_param["https://purl.imsglobal.org/spec/lti/claim/tool_platform"]["name"]) . '">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="offset-sm-2 col-sm-10">
                      <input type="submit" value="Ver eContent" name="submit" class="btn btn-primary"/>
                    </div>
                  </div>
                </form>
                ';

                // ERROR file_get_content()
                ///////////////////////////
                $w = stream_get_wrappers();
                echo 'openssl: ', extension_loaded('openssl') ? 'yes' : 'no', "\n";
                echo 'http wrapper: ', in_array('http', $w) ? 'yes' : 'no', "\n";
                echo 'https wrapper: ', in_array('https', $w) ? 'yes' : 'no', "\n";
                echo 'wrappers: ', var_export($w);

                //LAUNCH ID
                ///////////
                $launch_id = $launch->get_launch_id();
                echo '<br/><br/><b>LAUNCH ID:</b>' . json_encode($launch_id);
                //CAMBIAR SERVICIOS LINEITEMS
                /////////////////////////////
                /// NPRS
                print_r($post_param);
                // https://moodle.org/mod/forum/discuss.php?d=391538#p1606269
                $post_param["https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice"]["context_memberships_url"]='http://ailanto-dev.intecca.uned.es/mod/lti/services.php/2/lineitems/32/lineitem/scores?type_id=3';
                //$post_param["https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice"]["service_versions"]='2.0';
                print_r($post_param);
                /// AGS
                print_r($post_param);
                // https://moodle.org/mod/forum/discuss.php?d=391538#p1606269
                $post_param["https://purl.imsglobal.org/spec/lti-ags/claim/endpoint"]["lineitem"]='http://ailanto-dev.intecca.uned.es/mod/lti/services.php/2/lineitems/32/lineitem/scores?type_id=3';
                //$post_param["https://purl.imsglobal.org/spec/lti-ags/claim/endpoint"]["lineitems"]='http://ailanto-dev.intecca.uned.es/mod/lti/services.php/2/lineitems/scores?type_id=3';
                print_r($post_param);

                $launch = LTI\LTI_Message_Launch::from_cache($launch_id, new Iss_Target_Lti_Database($post_param));

                //SERVICES
                //////////
                // NPRS (Names and Role Provisioning Services)
                if (!$launch->has_nrps()) {
                    throw new Exception("Don't have names and roles!");
                }
                $nrps = $launch->get_nrps();
                echo '<br/><br/><b>NRPS:</b>' . json_encode($nrps);
                print_r($nrps);
                $members = $nrps->get_members();
                echo '<br/><br/><b>MEMBERS:</b>' . json_encode(($members ? $members : '[]'));
                print_r(($members ? $members : []));

                // AGS (Assignment and Grade Services)
                if (!$launch->has_ags()) {
                    throw new Exception("Don't have grades!");
                }
                $grades = $launch->get_ags();
                echo '<br/><br/><b>GRADES1:</b>' . json_encode($grades);
                print_r($grades);

                $score = LTI\LTI_Grade::new()
                    ->set_score_given(120)
                    ->set_score_maximum(100)
                    ->set_timestamp(date(DateTime::ISO8601))
                    ->set_activity_progress('Completed')
                    ->set_grading_progress('FullyGraded')
                    ->set_user_id($launch->get_launch_data()['sub']);
                $score_lineitem = LTI\LTI_Lineitem::new()
                    ->set_tag('score')
                    ->set_score_maximum(100)
                    ->set_label('Score')
                    ->set_resource_id(['9']);
                echo '<br/><br/><b>GRADES->PUT_GRADE()0</b>:';
                echo json_encode($grades->put_grade($score, $score_lineitem));

                $grade = LTI\LTI_Grade::new()
                    ->set_score_given(20)
                    ->set_score_maximum(100)
                    ->set_timestamp(date(DateTime::ISO8601))
                    ->set_activity_progress('Completed')
                    ->set_grading_progress('FullyGraded')
                    ->set_user_id($launch->get_launch_data()['sub']);
                echo '<br/><br/><b>GRADE</b>:' . json_encode($grade);
                //print_r($grade);

                echo '<br/><br/><b>GRADES->PUT_GRADE()1</b>:';
                echo json_encode($grades->put_grade($grade));
                print_r($grades);

                $score_lineitem = LTI\LTI_Lineitem::new()
                    ->set_tag('score')
                    ->set_score_maximum(100)
                    ->set_label('Score')
                    ->set_resource_id($launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);

                $scores = $grades->get_grades($score_lineitem);

                $time_lineitem = LTI\LTI_Lineitem::new()
                    ->set_tag('time')
                    ->set_score_maximum(999)
                    ->set_label('Time Taken')
                    ->set_resource_id('time'.$launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);
                $times = $grades->get_grades($time_lineitem);

                $members = $launch->get_nrps()->get_members();

                $scoreboard = [];

                foreach ($scores as $score) {
                    $result = ['score' => $score['resultScore']];
                    foreach ($times as $time) {
                        if ($time['userId'] === $score['userId']) {
                            $result['time'] = $time['resultScore'];
                            break;
                        }
                    }
                    foreach ($members as $member) {
                        if ($member['user_id'] === $score['userId']) {
                            $result['name'] = $member['name'];
                            break;
                        }
                    }
                    $scoreboard[] = $result;
                }
                echo json_encode($scoreboard);

                $grades = $launch->get_ags();
                echo '<br/><br/><b>GRADES2</b>:' . json_encode($grades);
                //print_r($grades);

                $lineitem = LTI\LTI_Lineitem::new()
                    ->set_id(2121)
                    ->set_tag(['grade1'])
                    ->set_score_maximum(100)
                    ->set_label('Grade');
                echo '<br/><br/><b>LINEITEM1</b>:' . json_encode($lineitem);
                print_r($lineitem);

                echo '<br/><br/><b>ENDPOINT</b>:';
                print_r($launch->get_launch_data()['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint']);

                echo '<br/><br/><b>GRADES->PUT_GRADE()2</b>:';
                echo json_encode($grades->put_grade($grade, $lineitem));
                //print_r($grades);

                $grades = $launch->get_ags();
                echo '<br/><br/><b>GRADES3</b>:' . json_encode($grades);
                //print_r($grades);

                $lineitem = LTI\LTI_Lineitem::new()
                    ->set_tag('grade2')
                    ->set_score_maximum(100)
                    ->set_label('Grade');
                echo '<br/><br/><b>LINEITEM2</b>:' . json_encode($lineitem);
                //print_r($lineitem);

                echo '<br/><br/><b>ENDPOINT</b>:';
                //print_r($launch->get_launch_data()['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint']);

                echo '<br/><br/><b>GRADES->PUT_GRADE()3</b>:';
                echo json_encode($grades->put_grade($grade, $lineitem));
                //print_r($grades);

                $grades = $launch->get_ags();
                echo '<br/><br/><b>GRADES4</b>:' . json_encode($grades);
                //print_r($grades);

                /*
                    $score = LTI\LTI_Grade::new()
                        ->set_score_given($_REQUEST['score'])
                        ->set_score_maximum(100)
                        ->set_timestamp(date(DateTime::ISO8601))
                        ->set_activity_progress('Completed')
                        ->set_grading_progress('FullyGraded')
                        ->set_user_id($launch->get_launch_data()['sub']);
                    $score_lineitem = LTI\LTI_Lineitem::new()
                        ->set_tag('score')
                        ->set_score_maximum(100)
                        ->set_label('Score')
                        ->set_resource_id($launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);
                    $grades->put_grade($score, $score_lineitem);


                    $time = LTI\LTI_Grade::new()
                        ->set_score_given($_REQUEST['time'])
                        ->set_score_maximum(999)
                        ->set_timestamp(date(DateTime::ISO8601))
                        ->set_activity_progress('Completed')
                        ->set_grading_progress('FullyGraded')
                        ->set_user_id($launch->get_launch_data()['sub']);
                    $time_lineitem = LTI\LTI_Lineitem::new()
                        ->set_tag('time')
                        ->set_score_maximum(999)
                        ->set_label('Time Taken')
                        ->set_resource_id('time'.$launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);
                    $grades->put_grade($time, $time_lineitem);

                    $ags = $launch->get_ags();

                    $score_lineitem = LTI\LTI_Lineitem::new()
                        ->set_tag('score')
                        ->set_score_maximum(100)
                        ->set_label('Score')
                        ->set_resource_id($launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);
                    $scores = $ags->get_grades($score_lineitem);

                    $time_lineitem = LTI\LTI_Lineitem::new()
                        ->set_tag('time')
                        ->set_score_maximum(999)
                        ->set_label('Time Taken')
                        ->set_resource_id('time'.$launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);
                    $times = $ags->get_grades($time_lineitem);

                    $members = $launch->get_nrps()->get_members();

                    $scoreboard = [];

                    foreach ($scores as $score) {
                        $result = ['score' => $score['resultScore']];
                        foreach ($times as $time) {
                            if ($time['userId'] === $score['userId']) {
                                $result['time'] = $time['resultScore'];
                                break;
                            }
                        }
                        foreach ($members as $member) {
                            if ($member['user_id'] === $score['userId']) {
                                $result['name'] = $member['name'];
                                break;
                            }
                        }
                        $scoreboard[] = $result;
                    }
                    echo json_encode($scoreboard);
                */
            }
            else {
                // DIV cross-browser and fully responsive
                // https://www.nodejsauto.com/2020/08/iframe-where-src-what-is-blob.html
                // ALTERNATIVES
                // https://stackoverflow.com/questions/9245133/how-to-hide-iframe-src
                echo '
                <div id="htmlTest"></div>' .

                    // Inyección de publicación HTML
                    //file_get_contents('https://ailanto-dev.intecca.uned.es/lti/publicacion/10220210903095251000000a/index.html') .

                    '<!--',
                '<p>VARIABLES GET:</p>', $_SERVER['HTTP_ORIGIN'], $_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER['QUERY_STRING'],
                '<p>VARIABLES POST:</p>', $_POST['state'], $_POST['id_token'],
                '<hr/>',
                '<br/><b>PLATFORM:</b> <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/tool_platform']['name'], '</a></b>',
                '<hr/>',
                '<br/><b>ISS: <a href="http://Hecho.que.Lti_Database.tome.este.parámetro.ISS.de.la.llamada.GET/POST">', $post_param['iss'], '</a></b>',
                '<br/><b>LOGIN_HINT: <a href="http://Hecho.que.Lti_Database.tome.este.parámetro.ISS.de.la.llamada.GET/POST">', "no disponible", '</a></b>',
                '<br/><b>TARGET_LINK_URI: <a href="http://Hecho.que.Lti_Database.tome.TARGET_LINK_URI.de.la.llamada.GET/POST">', $post_param['https://purl.imsglobal.org/spec/lti/claim/target_link_uri'], '</a></b>',
                '<br/><b>LTI_MESSAGE_HINT: <a href="http://Hecho.que.Lti_Database.tome.LTI_MESSAGE_HINT.de.la.llamada.GET/POST">', $post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"], '</a></b>',
                '<br/><b>TYPE: <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/message_type'], '</a></b>',
                '<br/><b>VERSION: <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/version'], '</a></b>',
                '<br/><b>USER: <a href="http://">', $post_param['name'], '</a></b>',
                '<br/><b>EMAIL: <a href="http://">', $post_param['email'], '</a></b>',
                '<br/><b>ROL: <a href="http://">', $post_param['https://purl.imsglobal.org/spec/lti/claim/roles'][0], '</a></b>',
                '-->';

                // https://purl.imsglobal.org/spec/lti/claim/message_type ==== LtiResourceLinkRequest
                echo '<!-- <hr/><br/><b>Resource Link Request Launch!</b> -->',
                    '<script hidden>
                    // https://www.nodejsauto.com/2020/08/iframe-where-src-what-is-blob.html
                    // https://stackoverflow.com/questions/9245133/how-to-hide-iframe-src
                    var blobMe= URL["createObjectURL"](new Blob([""], {type: "text/html"}));
                    var elIframe = document["createElement"]("iframe");
                    elIframe["setAttribute"]("frameborder", "0");
                    elIframe["setAttribute"]("width", "100%");
                    elIframe["setAttribute"]("height", "500px");
                    elIframe["setAttribute"]("allowfullscreen", "true");
                    elIframe["setAttribute"]("webkitallowfullscreen", "true");
                    elIframe["setAttribute"]("mozallowfullscreen", "true");
                    elIframe["setAttribute"]("src", blobMe);
                    var idOne= "diffusion" + Date.now();
                    elIframe["setAttribute"]("id", idOne);
                    document.getElementById("htmlTest").appendChild(elIframe);
                    const iframeHere= "";
                    document["getElementById"](idOne)["contentWindow"]["document"].write("<script type=\'text/javascript\'>location.href = \'' . $activity_GET['data']['url_actividad'] . '\'\x3c/script>");
                </script>';
            }
        }
        else
            echo '405 No permitido';

        // REDIRECCTION
        ///////////////
        // RELOCATION
        //header('X-Frame-Options: ' . 'SAMEORIGIN', true);
        //header('Location: ' . $post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"], true, 302);
        //die;

        // GET
        //print_r($_REQUEST);
        //print('<p>' . $_REQUEST['iss']);
        //print('<p>' . $_REQUEST['login_hint']);
        //print('<p>' . $_REQUEST['target_link_uri']);
        //print('<p>' . $_REQUEST['lti_message_hint']);
        //print('<p>' . $_REQUEST['id_token']);
        //print('<p>' . $_REQUEST['state']);
        // POST
        //print('<p>' . $post_param['iss'] . $post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"] . $post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["id"]);
        //print_r($post_param);
        //print('</p>');

        // REDIRECTION HEADER
        //header('Location: ' . TOOL_PARAMS_TARGET, true, 302);
        //die;

    } else if ($launch->is_deep_link_launch()) {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== LtiDeepLinkingRequest
        echo '<!-- <hr/><br/><b>Deep Linking Request Launch!</b> -->';
        $dl = $launch->get_deep_link();
        $resource = LTI\LTI_Deep_Link_Resource::new()
            ->set_url("https://my.tool/launch")
            ->set_custom_params(['my_param' => 'value'])
            ->set_title('My Resource');
        $dl->output_response_form([$resource]);
        $dl->get_response_jwt([$resource]);
?>
    <!--
        <div id="config">
            <br/>LAUNCH DEEP LINK:
            <?php
            echo $post_param['iss'], $post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"];
            ?>
        </div>
     -->

    <!-- Contenido de JWT 2 DEEP LINK
    <p>Hola <?php echo $post_param["given_name"]; ?>, bienvenid@ al eContent ´<?php echo $post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["title"]; ?>´ del curso ´<?php echo $post_param["https://purl.imsglobal.org/spec/lti/claim/context"]["title"]; ?>´ como <?php echo explode('#', $post_param['https://purl.imsglobal.org/spec/lti/claim/roles'][0])[1]; ?> </p>
    -->
<?php
        die;
    } else {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== otros tipos
        echo '<!-- <hr/><br/><b>Unknown launch type</b> -->';
    }
}
catch (IMSGlobal\LTI\LTI_Exception $e){

    echo ("<h1>Error de validación de credenciales....</h1>");
    exit($e->getMessage());
}
?>
