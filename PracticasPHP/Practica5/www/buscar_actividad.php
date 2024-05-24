<?php
    include("bd.php");
    $nombreActividad = $_POST['valor'];
    
    $actividades = getActividadesNombre($nombreActividad);
    echo json_encode($actividades);
?>