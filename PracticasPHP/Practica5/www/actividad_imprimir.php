<?php
    require_once '/usr/local/lib/php/vendor/autoload.php';
    include("bd.php");

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    $actividad = getActividad();

    echo $twig->render('actividad_imprimir.html',['actividad'=>$actividad]);
?>