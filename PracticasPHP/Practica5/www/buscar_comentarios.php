<?php
    include("bd.php");
    $usuario = $_POST['valor'];
    
    $comentarios = getComentariosNombre($usuario);
    echo json_encode($comentarios);
?>