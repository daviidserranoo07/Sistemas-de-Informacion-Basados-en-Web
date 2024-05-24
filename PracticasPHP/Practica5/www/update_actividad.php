<?php    
    include("bd.php");
    header("Location: home");

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $idAc = $_POST['input-id'];
        $nombreActividad = $_POST['input-name'];
        $descripcion = $_POST['input-descripcion'];
        $recomendaciones = $_POST['input-recomendaciones'];
        $fecha = $_POST['input-fecha'];
        $ubicacionNombre = $_POST['input-ubicacion-nombre'];
        $ubicacionUrl = $_POST['input-ubicacion-url'];
        $precio = $_POST['input-precio'];
        $publicada = $_POST['input-publicada'];
        $ruta="";

        if(isset($_FILES['input-image'])){
            $errors[] = array();
            $file_name = $_FILES['input-image']['name'];
            $file_size = $_FILES['input-image']['size'];
            $file_tmp = $_FILES['input-image']['tmp_name'];
            $file_type = $_FILES['input-image']['type'];
            $file_name_parts = explode('.', $_FILES['input-image']['name']);
            $file_ext = strtolower(end($file_name_parts));

            $extensions = array("jpeg","jpg","png");

            if(in_array($file_ext, $extensions) === false){
                $errors[] = "Extensión no permitida, eliga una imagen JPEG o PNG.";
            }

            if($file_size>2097152){
                $errors[] = 'Tamaño del fichero demasiado grande';
            }
            
            if (sizeof($errors)<=1) {
              move_uploaded_file($file_tmp, "img/" . $file_name);
              $ruta = "../img/" . $file_name;
            }
        }

        updateActividad($idAc,$nombreActividad,$descripcion,$precio,$recomendaciones,$fecha,$ubicacionNombre,$ubicacionUrl,$ruta,$publicada);
    }
    
?>