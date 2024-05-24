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
        $usuarios = getUsuarios();
    }else{
        header("Location: home");
        exit();
    }

    if($usuario['rol'] != 'Administrador'){
        $actividades = getActividades();
        header("Location: home");
        exit();
    }

    echo $twig->render('modificar_roles.html',['roles'=>$usuarios,'usuario'=>$usuario]);
?>