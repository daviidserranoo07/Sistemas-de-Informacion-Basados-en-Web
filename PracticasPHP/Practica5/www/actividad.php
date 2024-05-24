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

    $usuario = [];

    session_start();

    if(isset($_SESSION['usuario'])){
        $usuario = getUsuario($_SESSION['usuario']);
        $usuario['conectado'] = $_SESSION['conectado'];
    }else{
        $usuario['rol'] = 'No Registrado';
    }

    //Usamos la función getActividad pasandole la actividad actual
    //para recuperar la información de dicha actividad
    $actividad = getActividad();
    if($actividad['idAc'] === -1 || ($actividad['publicada'] === 0 && ($usuario['rol'] != 'Administrador' && $usuario['rol'] != 'Gestor' ))){
        header("Location: /home");
        exit();
    }
    $comentarios = getComentarios($idAc);


    //Le pasamos a la plantilla una variable actividad que es una array
    //que contiene toda la información de la actividad
    echo $twig->render('actividad.html',['actividad'=>$actividad,'comentarios'=>$comentarios,'usuario'=>$usuario]);
?>