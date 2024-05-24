<?php
    include("bd.php");
    $idAc = $_POST['ac'];
    header("Location: actividad/".$idAc);

    //Me muestra todos los valores que recibo desde la pagina
    //var_export($_REQUEST); 

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Obtener los datos del formulario
        $nombre = $_POST['input-name'];
        $comentario = $_POST['input-comentario'];
        if(empty($nombre) || empty($comentario)){
            exit("No se han rellenado todos los campos");
        }
        $correo = filter_input(INPUT_POST, 'input-correo', FILTER_VALIDATE_EMAIL);
        if($correo==false || empty($correo)){
            exit("Formato de correo incorrecto o no se ha introducido el correo");
        }
        if($_FILES['input-image']['error'] != UPLOAD_ERR_NO_FILE){
            $url_foto = "../img/".basename($_FILES["input-image"]["name"]);
        }else{
            $url_foto="../img/user-photo.jpg";
        }

        // Obtener la fecha actual
        $valorDeTimezone='Europe/Madrid';
        date_default_timezone_set ($valorDeTimezone);
        $fecha=date('d-m-Y');
        setComentario($idAc,$nombre,$url_foto,$correo,$comentario,$fecha);
        exit;
    }
?>
