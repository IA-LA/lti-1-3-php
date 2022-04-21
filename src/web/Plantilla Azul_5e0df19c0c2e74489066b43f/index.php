<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../db/iss_target_lti_database.php';

use \IMSGlobal\LTI;

$launch = LTI\LTI_Message_Launch::new(new Iss_Target_Lti_Database())
    ->validate();
?>

<!DOCTYPE html>
<html lang="es"><!-- InstanceBegin template="/Templates/contenidos.dwt" codeOutsideHTMLIsLocked="false" -->
<head>

    <!-- InstanceBeginEditable name="doctitle" -->
    <title>Plantillas CURSOS UNED 2014</title>
    <!-- InstanceEndEditable -->

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="https://descargas.uned.es/publico/cv/templates/disenio/generico/css/kickstart.css" media="all" />
    <link rel="stylesheet" type="text/css" href="https://descargas.uned.es/publico/cv/templates/disenio/generico/style.css" media="all" />
    <link rel="stylesheet" type="text/css" href="https://descargas.uned.es/publico/cv/templates/disenio/generico/print.css" media="print" />
    <link rel="stylesheet" type="text/css" href="https://descargas.uned.es/publico/cv/templates/disenio/grados/grados.css" media="all" />

    <link rel="attachment" id="imageneBanderas" href="https://descargas.uned.es/publico/cv/templates/disenio/generico/css/img/Spain.png" />

    <link rel="index" id="indiceXML" href="xml/indice_es.xml" />
    <link rel="attachment" id="configuracionXML" href="xml/configuracion.xml" />
    <link rel="glossary" id="glosarioXML" href="xml/glosario_es.xml" />
    <link id="xml_filtro_contenido" href="xml/filtro_contenidos.xml" />

    <!-- Javascript -->
    <script type="text/javascript" src="https://descargas.uned.es/publico/cv/templates/scripts/plugins/jquery.min.js"></script>
    <script type="text/javascript" src="https://descargas.uned.es/publico/cv/templates/scripts/general.js"></script>
    <script id="scriptPrincipal" type="text/javascript" src="https://descargas.uned.es/publico/cv/templates/scripts/grados/grados.js"></script>

    <!-- META -->
    <!-- InstanceBeginEditable name="head" -->
    <script type="text/javascript" src="https://descargas.uned.es/publico/cv/templates/scripts/inicio_indice.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="curso UNED" />
    <meta name="keywords" content="" />
    <meta name="author" content="">
    <!-- InstanceEndEditable -->

    <!-- InstanceBeginEditable name="inicializar_javascript" -->
    <script type="text/javascript">
        "https://descargas.uned.es/publico/cv/templates/scripts/inicio_indice.js		$(document).ready(function(e) {
        //SecciÃ³n para personalizar componentes e iniciar otras cuestiones javascript

        });
    </script>
    <!-- InstanceEndEditable -->
</head>

<body>
<!-- barra aviso javascript -->
<noscript>
    <aside>
        <div class="avisobar"><i class="icon-exclamation-sign icon-2x"></i> <strong>Activa javascript!</strong> Para la correcta visualizaciÃ³n del curso es necesario tener activado javascript.
        </div>
    </aside>
</noscript>


<!-- enlaces directos accesibilidad -->
<div class="oculto" id="accesos_directos">
    <p>Accesos directos a las distintas zonas del curso </p>
    <p><a href="#contenidos" title="salto a los contenidos" accesskey="s">Ir a los contenidos</a></p>
    <p><a href="#menu" title="acceso directo al menÃº de navagaciÃ³n" accesskey="4">Ir a men&uacute; navegaci&oacute;n</a></p>
</div>

<!-- CABECERA -->
<nav class="navbar" id="navegacion_superior">
    <div class="menu_sup">
        <ul class="flechas">
            <li class="ocultar anterior"><a title="anterior" accesskey="9"><img src="https://descargas.uned.es/publico/cv/templates/disenio/generico/css/img/bt_volver.png" alt="anterior" /> </a></li>
            <li class="ocultar siguiente"><a title="siguiente" accesskey="8"><img src="https://descargas.uned.es/publico/cv/templates/disenio/generico/css/img/bt_siguiente.png" alt="siguiente" /> </a></li>
        </ul>
        <a id="menu"></a>
        <ul class="menu">
            <li class="menu_contenidos"><a href="#" accesskey="2"><i class="icon-align-justify" title="menÃº contenidos"></i></a>
                <ul id="desplegable_navegacion">
                    <span class="bandera_idiomas"></span>
                    <li class="show-phone divider actividades"><a href="actividades_es.html"><i class="icon-briefcase"></i><span>ACTIVIDADES</span></a></li>
                    <li class="show-phone divider glosario"><a href="glosario_es.html"><i class="icon-star-empty"></i><span>GLOSARIO</span></a></li>
                    <li class="show-phone divider bibliografia"><a href="bibliografia_es.html"><i class="icon-book"></i><span>BIBLIOGRAFÃA</span></a></li>
                    <li class="show-phone divider galeria"><a href="galeria_es.html"><i class="icon-picture"></i><span>GALERÃA DE IMÃGENES</span></a></li>
                </ul>

            </li>
            <li class="inicio"><a href="../login_econtent.php?iss=http%3A%2F%2Flocalhost:9001&login_hint=123456&target_link_uri=http%3A%2F" accesskey="1"><span>INICIO</span></a></li>
            <li class="hide-phone"><a href="actividades_es.html" accesskey="5"><span>ACTIVIDADES</span></a></li>
            <li class="hide-phone"><a href="glosario_es.html" accesskey="6"><span>GLOSARIO</span></a></li>
            <li class="hide-phone"><a href="bibliografia_es.html" accesskey="7"><span>BIBLIOGRAFÃA</span></a></li>
            <!-- <li class="hide-phone"><a href="galeria_es.html" accesskey="8"><span>GALERÃA</span></a></li> -->
        </ul>
    </div>
    <!-- barra de progreso -->
    <div id="progreso">
        <div class="barraprogreso"></div>
    </div>
    <!-- fin barra de progreso -->
</nav>
<!-- FIN CABECERA -->

<div class="grid" id="cuerpo">

    <header class="col_12 cabecera">
        <hgroup>
            <p class="tipo_educacion">&nbsp;</p>
            <p class="tipo_curso">&nbsp;</p>
            <p class="titulo_curso">&nbsp;</p>
        </hgroup>
    </header>


    <a id="contenidos"></a>
    <article id="contenido">
        <!-- InstanceBeginEditable name="CONTENIDOS" -->

        <header class="col_12">
            <h1 class="titulo_indice" id="titulo_contenido">&nbsp;</h1>
        </header>
        <nav class="col_12" id="indice">
            <ul class="menu_indice">

            </ul>
        </nav><!-- cierra col_12 contenido en columnas -->


        <!-- InstanceEndEditable -->
    </article>

</div> <!-- End Grid -->

<!-- PIE DE PÃGINA -->
<div class="clear"></div>
<div class="grid"><!-- Open Grid para el pie -->

    <footer id="footer">
        <div class="logo_pie">
            <a href="http://www.uned.es" title="web UNED" target="_blank"><img src="https://descargas.uned.es/publico/cv/templates/disenio/generico/css/img/logo_uned.png" alt="UNED" /></a>
        </div>
        <div class="menu_pie">
            <span class="copyright"></span>
            <ul>
                <li class="ayuda"><a href="ayuda_es.html" accesskey="3"><span>Ayuda</span></a></li>
                <li class="accesibilidad"><a href="accesibilidad_es.html" accesskey="0"><span>Accesibilidad</span></a></li>
                <li class="imprimir"><a href="javascript:window.print()" accesskey="p"><span>Imprimir</span></a></li>
            </ul>
        </div>
    </footer><!-- End PIE DE PÃGINA -->

</div><!-- End Grid para el pie -->
<div id="scroll" class="scroll_btn"> <a href="#"><img class="align-right" src="https://descargas.uned.es/publico/cv/templates/disenio/generico/css/img/up.png" width="53" height="34" alt="subir al incio de la pÃ¡gina"></a> </div>
</body>
<!-- InstanceEnd --></html>
