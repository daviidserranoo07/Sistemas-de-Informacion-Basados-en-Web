<?php    
    include("bd.php");

    //var_dump($_REQUEST);

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $idAc=$_POST['id'];
        $idComentario=$_POST['id_comentario'];
        $correo=$_POST['correo'];

        eliminarComentario($idAc, $idComentario, $correo);
        
        header("Location: actividad/".$idAc);
    }
?>