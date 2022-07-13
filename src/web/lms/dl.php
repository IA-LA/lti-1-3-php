<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../db/iss_target_lti_database.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

use \IMSGlobal\LTI;
try{
    // REDIRECCION POST
    // JWT Claims decode
    // https://auth0.com/blog/id-token-access-token-what-is-the-difference/
    $post_param = json_decode(JWT::urlsafeB64Decode(explode('.', $_REQUEST['id_token'])[1]), true);
    //print('<p>JWT: ' . $_REQUEST['state'] . '</p>');
    //print_r($post_param);
    //die;

    //RECUPERA LAUNCH
    //$launch = LTI\LTI_Message_Launch::from_cache($launch_id, new Iss_Target_Lti_Database($_REQUEST));
    //$launch = LTI\LTI_Message_Launch::from_cache($launch_id, new Iss_Target_Lti_Database($post_param));
    //->validate();

    //CREA LAUNCH
    $launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database());
    //$launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database($_REQUEST));
    //$launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database($post_param))//;
    //->validate();
    $launch_id = $launch->get_launch_id();

    //LAUNCH TYPE:
    //  - LtiResourceLinkRequest
    //  - LtiDeepLinkingRequest
    //  - LtiSubmissionReviewRequest
    //  - Otros tipos!!!
    if ($launch->is_resource_launch()) {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== LtiResourceLinkRequest
        echo '<!-- <hr/><br/><b>Resource Link Request (' . $launch_id . ') Launch!</b> -->';
        LTI\LTI_OIDC_Login::new(new Iss_Target_Lti_Database())

            // Actividades ECONTENT alojadas en el Servidor o Externas alojadas en otro servidor o Plataforma:
            //      - Internas: (eContent) utiliza un conteniedo .php y un iframe para presentarlo y manejar la llamda POST JWT LTI Claims (similar a la de ./platform/login.php)
            //      - Externas: se redireccionan al $_REQUEST['target_link_uri'] de plataforma o actividad independiente.
            // Login en la Plataforma Consumidora con ID 'iss' de la Actividad LTI debidamente registrada con URL 'target_link_uri'
            // Login:
            //      - En una Plataforma Consumidora Externa de la Actividad LTI y debidamente registradas ambas
            //              => redirecciona hacia 'target_link_uri'
            //      - En la Platforma Consumidora Interna de una Actividad eContent y debidamente registradas ambas
            //              si Plataforma 0{23}[a-f0-9] =>  launch.php (lanzador propio del servidor)
            //              no Plataforma 0{23}[a-f0-9] =>  lms/publish.php (lanzador propio del servidor)
            ->do_oidc_login_redirect(TOOL_REDIR)

            // Redirección hacia 'target_link_uri'
            // https://www.w3docs.com/snippets/php/how-to-redirect-a-web-page-with-php.html
            ->do_js_redirect();

    } else if ($launch->is_deep_link_launch()) {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== LtiDeepLinkingRequest
        echo '<!-- <hr/><br/><b>Deep Linking Request (' . $launch_id . ') Launch!</b> -->';
        $dl = $launch->get_deep_link();
        $resource = LTI\LTI_Deep_Link_Resource::new()
            ->set_type("LtiResourceLinkRequest")
            ->set_url("https://my.tool/launch")
            ->set_custom_params(['my_param' => 'value'])
            ->set_title('My Resource');
        $dl->get_response_jwt([$resource]);
        $dl->output_response_form([$resource]);
        //die;
    }
    else if ($launch->is_submission_review_launch()) {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== LtiSubmissionReviewRequest
        echo '<!-- <hr/><br/><b>Submission Review Request (' . $launch_id . ') Launch!</b> -->';
        $login = LTI\LTI_OIDC_Login::new(new Iss_Target_Lti_Database());
            // Actividades ECONTENT alojadas en el Servidor o Externas alojadas en otro servidor o Plataforma:
            //      - Internas: (eContent) utiliza un conteniedo .php y un iframe para presentarlo y manejar la llamda POST JWT LTI Claims (similar a la de ./platform/login.php)
            //      - Externas: se redireccionan al $_REQUEST['target_link_uri'] de plataforma o actividad independiente.
            // Login en la Plataforma Consumidora con ID 'iss' de la Actividad LTI debidamente registrada con URL 'target_link_uri'
            // Login:
            //      - En una Plataforma Consumidora Externa de la Actividad LTI y debidamente registradas ambas
            //              => redirecciona hacia 'target_link_uri'
            //      - En la Platforma Consumidora Interna de una Actividad eContent y debidamente registradas ambas
            //              si Plataforma 0{23}[a-f0-9] =>  launch.php (lanzador propio del servidor)
            //              no Plataforma 0{23}[a-f0-9] =>  lms/publish.php (lanzador propio del servidor)
        $redirect = $login->do_oidc_login_redirect(TOOL_REDIR);

            // Redirección hacia 'target_link_uri'
            // https://www.w3docs.com/snippets/php/how-to-redirect-a-web-page-with-php.html
        $redirect->do_hybrid_redirect();
    }
    else {
        // https://purl.imsglobal.org/spec/lti/claim/message_type ==== otros tipos
        echo '<!-- <hr/><br/><b>Unknown Request type (' . $launch_id . ') Launch!</b> -->';

        //$launch = LTI\LTI_Message_Launch::from_cache($launch_id, new Iss_Target_Lti_Database($_REQUEST));
        //$launch = LTI\LTI_Message_Launch::from_cache($launch_id, new Iss_Target_Lti_Database($post_param));
        //->validate();
    }
}
catch (IMSGlobal\LTI\OIDC_Exception $e){

    echo ("<h1>Error de credenciales.</h1>");
    exit($e->getMessage());
}
?>