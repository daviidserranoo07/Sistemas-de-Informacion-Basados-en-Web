<?php
    require_once '/usr/local/lib/php/vendor/autoload.php';
    include("bd.php");

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);
    
    //Obtenemos mediante ac el valor de la actividad actual
    $idAc = filter_input(INPUT_GET, 'ac', FILTER_SANITIZE_NUMBER_INT);
    if ($idAc === false) {
        exit("El parámetro 'id' no es válido.");
    }

    //Usamos la función getActividad pasandole la actividad actual
    //para recuperar la información de dicha actividad
    $actividad = getActividad();
    $comentarios = getComentarios($idAc);

    $usuario = [];

    session_start();

    if(isset($_SESSION['usuario'])){
        $usuario = getUsuario($_SESSION['usuario']);
        $usuario['conectado'] = true;
    }


    //Le pasamos a la plantilla una variable actividad que es una array
    //que contiene toda la información de la actividad
    echo $twig->render('actividad.html',['actividad'=>$actividad,'comentarios'=>$comentarios,'usuario'=>$usuario]);
?>