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

    $actividades = getActividades();

    if($usuario['rol'] != 'Administrador' && $usuario['rol'] != 'Gestor'){
        header("Location: home");
        exit();
    }

    echo $twig->render('modificar_actividades.html',['actividades'=>$actividades,'usuario'=>$usuario]);
?>