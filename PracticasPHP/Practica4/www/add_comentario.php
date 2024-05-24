<?php
    include("bd.php");
    $idAc = $_POST['ac'];
    header("Location: actividad/".$idAc);

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        // Obtener los datos del formulario
        $nombre = $_POST['input-usuario'];
        $comentario = $_POST['input-comentario'];
        //Comprobamos que tenga formato de correo, el correo que ha introducido el usuario
        $correo = filter_input(INPUT_POST, 'input-correo', FILTER_VALIDATE_EMAIL);
        if($correo==false || empty($correo)){
            exit("Formato de correo incorrecto o no se ha introducido el correo");
        }
        $moderado = false;

        setComentario($idAc,$correo,$nombre,$comentario,$moderado);
        exit;
    }
?>
