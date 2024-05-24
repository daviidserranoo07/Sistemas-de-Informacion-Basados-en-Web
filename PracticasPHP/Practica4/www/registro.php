<?php
  require_once "/usr/local/lib/php/vendor/autoload.php";
  include("bd.php");

  $loader = new \Twig\Loader\FilesystemLoader('templates');
  $twig = new \Twig\Environment($loader);

  $registrado=true;
  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = $_POST['input-name'];
    $usuario = $_POST['input-username'];
    $correo = $_POST['input-correo'];
    $password = $_POST['input-password'];
    $passwordRepeat = $_POST['input-password-repeat'];
    $registrado=true;
    if(empty($nombre) || empty($usuario) || empty($correo) || empty($password)){
        $registrado=false;
        exit("No se han rellenado todos los campos");
    }else if($password != $passwordRepeat){
        $registrado=false;
        exit("Contraseñas no son iguales");
    }

    $correo = filter_input(INPUT_POST, 'input-correo', FILTER_VALIDATE_EMAIL);
    if($correo==false || empty($correo)){
        $registrado=false;
        exit("Formato de correo incorrecto o no se ha introducido el correo");
    }

    setUsuario($nombre,$usuario,$correo,$password);
    header("Location: login");
  }


  echo $twig->render('registro.html');
?>