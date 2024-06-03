<?php
    include("bd.php");
    $nombreActividad = $_POST['valor'];

    session_start();
    if(isset($_SESSION['usuario'])){
        $correo = $_SESSION['usuario'];
    }
    else{
        $correo = 'no_registrado';
    }
    
    $actividades = getActividadesNombre($nombreActividad,$correo);
    echo json_encode($actividades);
?>