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
    print('<p>' . $_REQUEST['state'] . '</p>');
    print_r($post_param);
    print_r($_SERVER);
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

?>
    <!-- Contenido de JWT 1
    <p>Hola <?php echo $post_param["given_name"]; ?>, bienvenid@ al eContent ´<?php echo $post_param["https://purl.imsglobal.org/spec/lti/claim/resource_link"]["title"]; ?>´ del curso ´<?php echo $post_param["https://purl.imsglobal.org/spec/lti/claim/context"]["title"]; ?>´ como <?php echo explode('#', $post_param['https://purl.imsglobal.org/spec/lti/claim/roles'][0])[1]; ?> </p>
    -->
<?php
    //LAUNCH TYPE:
    //  - LtiResourceLinkRequest
    //  - LtiDeepLinkingRequest
    //  - Otros tipos!!!
    if ($launch->is_resource_launch()) {

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
        $iss_GET =  $serv->service('read', 'Platform', 'id_actividad', $_SERVER['HTTP_ORIGIN'], $_REQUEST);

        // LLAMADA OK
        // Contenido Resultado de las llamadas existe
        //if(($json_obj['result'] === "ok")){
        if(($iss_GET['result'] === "ok")) {

            $target_link_uri=(string)$post_param["https://purl.imsglobal.org/spec/lti/claim/target_link_uri"];
            // IFRAME FULL PAGE cross-browser and fully responsive
            //  https://stackoverflow.com/questions/17710039/full-page-iframe
            // ALTERNATIVES
            //  https://www.geeksforgeeks.org/alternative-to-iframes-in-html5/
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
                '<script>
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
                var idOne= "gepa_"+ Date.now();
                elIframe["setAttribute"]("id", idOne);
                document.getElementById("htmlTest").appendChild(elIframe);
                const iframeHere= "";
                document["getElementById"](idOne)["contentWindow"]["document"].write("<script type=\'text/javascript\'>location.href = \'http://ailanto-dev.intecca.uned.es/publication?id=10220210903095251000000a&actividad=' . explode('/publicacion/', $target_link_uri, true)[1] . '\'\x3c/script>");

                //https://carstenbehrens.com/how-to-send-request-headers-iframe/
                async function getSrc() {
                  const res = await fetch("http://ailanto-dev.intecca.uned.es", {
                    method: \'GET\',
                    headers: {
                      // Here you can set any headers you want
                      "Access-Control-Allow-Headers": "Accept"
                    }
                  });
                  const blob = await res.blob();
                  const urlObject = URL.createObjectURL(blob);
                  document.querySelector(\'iframe\').setAttribute("src", urlObject)
                }
                getSrc();
            </script>';
        }
        else
            echo '¡¡¡¡NOOOO AMIGO NO!!!!';

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
        print_r($launch_id);
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
        $members = $launch->get_nrps()->get_members();
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
            ->set_resource_id($launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);
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

    <!-- Contenido de JWT 2
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
