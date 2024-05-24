<?php
    require_once '/usr/local/lib/php/vendor/autoload.php';
    include("bd.php");

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    $usuario = [];

    session_start();

    if(isset($_SESSION['usuario'])){
        $usuario = getUsuario($_SESSION['usuario']);
        $usuario['conectado'] = $_SESSION['conectado'];
    }else{
        header("Location: home");
        exit();
    }

    if($usuario['rol'] != 'Administrador' && $usuario['rol'] != 'Gestor'){
        $actividades = getActividades();
        header("Location: home");
        exit();
    }

    echo $twig->render('aniadir_actividad.html',['usuario'=>$usuario]);
?>