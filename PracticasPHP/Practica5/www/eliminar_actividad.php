<?php    
    include("bd.php");

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $idAc=$_POST['id'];

        eliminarActividad($idAc);
        
        header("Location: modificar_actividad");
    }
?>