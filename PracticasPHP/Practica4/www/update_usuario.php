<?php    
    include("bd.php");

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $url_foto="";
        $nombre = $_POST['input-name'];
        $usuario = $_POST['input-usuario'];
        $password = $_POST['input-password'];
        $correoNuevo = filter_input(INPUT_POST, 'input-correo-nuevo', FILTER_VALIDATE_EMAIL);
        $correoAntiguo = filter_input(INPUT_POST, 'input-correo-antiguo', FILTER_VALIDATE_EMAIL);
        if($correoNuevo==false && !empty($correoNuevo)){
            exit("Formato de correo incorrecto o no se ha introducido el correo");
        }

        $foto = 'input-image';

        if(isset($_FILES[$foto])){
            $errors = [];
            $file_name = $_FILES[$foto]['name'];
            $file_size = $_FILES[$foto]['size'];
            $file_tmp = $_FILES[$foto]['tmp_name'];
            $file_type = $_FILES[$foto]['type'];
            $file_name_parts = explode('.', $_FILES[$foto]['name']);
            $file_ext = strtolower(end($file_name_parts));


            $extensions = array("jpeg","jpg","png");

            if(in_array($file_ext, $extensions) === false){
                $errors[] = "Extensión no permitida, eliga una imagen JPEG o PNG.";
            }

            if($file_size>2097152){
                $errors[] = 'Tamaño del fichero demasiado grande';
            }
            
            if (empty($errors)) {
              move_uploaded_file($file_tmp, "img/" . $file_name);
              
              $url_foto = "../img/" .  $file_name;
            }
        }

        updateUsuario($nombre,$usuario,$correoNuevo,$correoAntiguo,$password,$url_foto);
        
        header("Location: perfil");
    }
?>