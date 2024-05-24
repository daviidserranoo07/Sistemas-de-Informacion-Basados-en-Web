<?php    
    include("bd.php");
    header("Location: home");

    function image($nombre){
        $ruta = "";
        if(isset($_FILES[$nombre])){
            $errors = [];
            $file_name = $_FILES[$nombre]['name'];
            $file_size = $_FILES[$nombre]['size'];
            $file_tmp = $_FILES[$nombre]['tmp_name'];
            $file_type = $_FILES[$nombre]['type'];
            $file_name_parts = explode('.', $_FILES[$nombre]['name']);
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
              
              $ruta = "../img/" .  $file_name;
            }
        }
        return $ruta;
    }

    //var_dump($_REQUEST);

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $nombreActividad = $_POST['input-name'];
        $descripcion = $_POST['input-descripcion'];
        $recomendaciones = $_POST['input-recomendaciones'];
        $fecha = $_POST['input-fecha'];
        $ubicacionNombre = $_POST['input-ubicacion-nombre'];
        $ubicacionUrl = $_POST['input-ubicacion-url'];
        $precio = $_POST['input-precio'];
        $portada=image('input-portada');
        $foto1=image('input-foto-1');
        $foto2=image('input-foto-2');
        $publicada = $_POST['input-publicada'];

        setActividad($nombreActividad,$precio,$descripcion,$recomendaciones,$fecha,$ubicacionNombre,$ubicacionUrl,$portada,$foto1,$foto2,$publicada);
    }
    
?>