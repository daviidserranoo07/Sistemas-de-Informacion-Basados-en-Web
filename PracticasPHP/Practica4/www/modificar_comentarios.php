<?php
    require_once '/usr/local/lib/php/vendor/autoload.php';
    include("bd.php");

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    session_start();

    if(isset($_SESSION['usuario'])){
        $usuario = getUsuario($_SESSION['usuario']);
        $usuario['conectado'] = true;
    }

    $comentarios = getAllComentarios();

    echo $twig->render('modificar_comentarios.html',['comentarios'=>$comentarios,'usuario'=>$usuario]);
?>