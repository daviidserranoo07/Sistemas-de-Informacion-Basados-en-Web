<?php
    require_once '/usr/local/lib/php/vendor/autoload.php';
    include("bd.php");

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    $usuario = [];

    session_start();

    if(isset($_SESSION['usuario'])){
        $usuario = getUsuario($_SESSION['usuario']);
        $usuario['conectado'] = true;
    }

    echo $twig->render('perfil.html',['usuario'=>$usuario]);
?>