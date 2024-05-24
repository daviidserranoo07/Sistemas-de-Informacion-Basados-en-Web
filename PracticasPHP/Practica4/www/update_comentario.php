<?php
    include("bd.php");
    $idAc = $_POST['ac'];
    header("Location: modificar_comentarios");

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $nombre = $_POST['input-usuario'];
        $nuevoComentario = $_POST['input-comentario'];
        $id_comentario = $_POST['input-id-comentario'];
        $correo = filter_input(INPUT_POST, 'input-correo', FILTER_VALIDATE_EMAIL);
        if($correo==false || empty($correo)){
            exit("Formato de correo incorrecto o no se ha introducido el correo");
        }

        // Obtener la fecha actual
        $valorDeTimezone='Europe/Madrid';
        date_default_timezone_set ($valorDeTimezone);
        $fecha=date('d-m-Y');
        $moderado = true;
        updateComentario($idAc,$correo,$nuevoComentario,$fecha,$id_comentario,$moderado);
        exit;
    }
?>