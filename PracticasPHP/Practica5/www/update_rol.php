<?php    
    include("bd.php");

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $nuevoRol=$_POST['rol-update'];
        $correo=$_POST['input-correo'];

        if($nuevoRol!= null) updateRol($correo, $nuevoRol);
        
        header("Location: modificar_roles");
    }
?>