<?php
  require_once "/usr/local/lib/php/vendor/autoload.php";
  include("bd.php");

  //var_dump($_REQUEST);

  $loader = new \Twig\Loader\FilesystemLoader('templates');
  $twig = new \Twig\Environment($loader);

  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $correo = $_POST['input-correo'];
    $password = $_POST['input-password'];

    if(checkLogin($correo, $password)){
      session_start();
      $_SESSION['usuario'] = $correo;
      header("Location: home");
      exit();
    }else{
      header("Location: login.php?error=1");
    }

    
  }
  echo $twig->render('login.html',[]);

?>