<?php
    require_once '/usr/local/lib/php/vendor/autoload.php';
    //Aplicamos patrón Singleton ya que solo queremos una instacia de la clase
    //para no realizar una nueva conexión a la base de datos cada vez
    class ConexionBD{
        private static $instancia = null;

        private $host='database';
        private $usuario='david';
        private $contrasenia='dserrano7';
        private $base_de_datos='SIBW';
        private $puerto='3306';

        private $conexion;

        private function __construct(){
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            try{
                // Crear una nueva conexión
                $this->conexion = new mysqli($this->host, $this->usuario, $this->contrasenia, $this->base_de_datos, $this->puerto);
                $this->conexion->set_charset("utf8mb4");
            }catch(Exception $e){
                error_log($e->getMessage());
                exit("Error de conexión con la base de datos: ".$e->getMessage());
            }
        }

        public static function obtenerInstancia(){
            if(self::$instancia === null){
                self::$instancia = new self();
            }
            return self::$instancia;
        }

        public function obtenerConexion(){
            return $this->conexion;
        }
        
    }

    function getActividades(){
        
        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();

        //Hacemos la consulta a la base de datos
        $stmt = $conexion->prepare("SELECT * FROM Actividad");
        $stmt->execute();
        $res = $stmt->get_result();

        $actividades = array();

        if($res->num_rows > 0){
            while ($row = $res->fetch_assoc()) {
                $actividades[] = array(
                    'id' => $row['id'],
                    'nombre' => $row['nombre'],
                    'portada' => $row['ruta'],
                    'publicada' => $row['publicada']
                );
            }
        }
        
        $res->close();

        return $actividades;
    }

    function getUsuario($correo){

        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();

        $stmt = $conexion->prepare("SELECT * FROM Usuario WHERE correo = ?");
        $stmt->bind_param("s",$correo);
        $stmt->execute();
        $res = $stmt->get_result();

        $usuario = array();

        if($res->num_rows>0){
            $row = $res->fetch_assoc();

            $usuario = array(  'nombre'=>$row['nombre'],
                               'usuario'=>$row['usuario'],
                               'correo'=>$row['correo'],
                               'password'=>$row['password'],
                               'url_foto'=>$row['url_foto_perfil'],
                               'conectado' => false,
                               'rol'=>'registrado'
                            );
        }

        $stmt = $conexion->prepare("SELECT * FROM Asignar WHERE correo = ?");
        $stmt->bind_param("s",$correo);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows>0){
            $row = $res->fetch_assoc();
            $id_rol = $row['id_rol'];    
        }

        if($id_rol==1) $usuario['rol'] = 'Administrador';
        else if($id_rol==2) $usuario['rol'] = 'Registrado';
        else if($id_rol==3) $usuario['rol'] = 'Moderador';
        else if($id_rol==4) $usuario['rol'] = 'Gestor';

        return $usuario;
    }

    function getUsuarios(){

        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();

        $usuarios = array();
        $rol="";

        $stmt = $conexion->prepare("SELECT * FROM Asignar");
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows>0){
            while($row = $res->fetch_assoc()){
                if($row['id_rol']==1) $rol = "Administrador";
                else if($row['id_rol']==4) $rol = "Gestor";
                else if($row['id_rol']==3) $rol = "Moderador";
                else if($row['id_rol']==2) $rol = "Registrado";
                $usuario = array('usuario'=>$row['correo'],
                                'rol' =>  $rol
                                );
                $usuarios[] = $usuario;
            }
        }
        return $usuarios;
    }


    function getActividad(){

        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();


        //Hacemos la consulta a la base de datos
        $stmt = $conexion->prepare("SELECT * FROM Actividad WHERE id = ?");
        $id = filter_input(INPUT_GET, 'ac', FILTER_SANITIZE_NUMBER_INT);
        if ($id === false) {
            exit("El parámetro 'id' no es válido.");
        }
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $actividad = array(
            'idAc'=>-1,
            'nombre'=>'Por Defecto',
            'fecha'=>'Por Defecto',
            'descripcion'=>'Por Defecto',
            'precio'=>'Por Defecto',
            'recomendaciones'=>'Por Defecto',
            'ubicacion_nombre'=>'Por Defecto',
            'ubicacion_url'=>'Por Defecto',
            'publicada' => 'Por Defecto',
        );

        if($res->num_rows>0){
            $row = $res->fetch_assoc();
            $idAc = $_GET['ac'];
            $fechaActividad = $row['fecha'];
            $timestamp = strtotime($fechaActividad);
            $fechaFormateada = date('d/m/Y',$timestamp);
            $precio = $row['precio'];
            $precio_euro = $precio . "€";

            $actividad = array('idAc'=>$idAc,
                               'nombre'=>$row['nombre'],
                               'fecha'=>$fechaFormateada,
                               'descripcion'=>$row['descripcion'],
                               'precio'=>$precio_euro,
                               'recomendaciones'=>$row['recomendaciones'],
                               'ubicacion_nombre'=>$row['ubicacion_nombre'],
                               'ubicacion_url'=>$row['ubicacion_url'],
                               'portada' => $row['ruta'],
                               'publicada' => $row['publicada']
                            );
        }

        // Consultar la tabla FotosActividad para obtener los IDs de las fotos asociadas a esta actividad
        $stmt = $conexion->prepare("SELECT id_foto FROM FotosActividad WHERE id_actividad = ?");
        $stmt->bind_param("i", $idAc);
        $stmt->execute();
        $res = $stmt->get_result();

        // Obtener los IDs de las fotos asociadas a esta actividad
        $foto_ids = array();
        while ($row = $res->fetch_assoc()) {
            $foto_ids[] = $row['id_foto'];
        }

        //Ontenemos para cada id_foto de la actividad su ruta 
        $fotos = array();
        foreach ($foto_ids as $foto_id) {
            $stmt = $conexion->prepare("SELECT * FROM Foto WHERE id_foto = ?");
            $stmt->bind_param("i", $foto_id);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                $row = $res->fetch_assoc();
                    $fotos[] = array(
                                'ruta'=> $row['ruta']
                    );
            }
        }

        // Agregar el array que contiene para cada id_foto su ruta a la actividad
        $actividad['fotos'] = $fotos;

        $res->close();

        return $actividad;
    }

    function getActividadesNombre($nombre,$correo){

        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();
    
        $stmt = $conexion->prepare("SELECT * FROM Actividad WHERE nombre LIKE ?");
        $nombre = "%".$nombre."%";
        $stmt->bind_param("s",$nombre);
        $stmt->execute();
        $res = $stmt->get_result();

        if($correo == 'no_registrado'){
            $usuario = array('rol'=>'no_registrado');
        }else{
            $usuario = getUsuario($correo);
        }
        
        $actividades = array();
    
        if($res->num_rows > 0){
            while ($row = $res->fetch_assoc()) {
                $actividades[] = array(
                    'id' => $row['id'],
                    'nombre' => $row['nombre'],
                    'portada' => $row['ruta'],
                    'publicada' => $row['publicada'],
                    'rol_usuario' => $usuario['rol']
                );
            }
        }
        
        $res->close();
        return $actividades;
    }

    function eliminarActividad($idAc){
        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();

        $stmt = $conexion->prepare("DELETE FROM Comentarios WHERE id_actividad = ?");
        $stmt->bind_param("i", $idAc);
        $stmt->execute();

        $stmt = $conexion->prepare("DELETE FROM FotosActividad WHERE id_actividad = ?");
        $stmt->bind_param("i", $idAc);
        $stmt->execute();

        $stmt = $conexion->prepare("DELETE FROM Actividad WHERE id = ?");
        $stmt->bind_param("i", $idAc);
        $stmt->execute();
    }

    function eliminarComentario($idAc, $idComentario, $correo){
        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();

        $stmt = $conexion->prepare("DELETE FROM Comentarios WHERE id_actividad = ? AND id_comentario = ? AND correo_usuario = ?");
        $stmt->bind_param("iis", $idAc, $idComentario, $correo);
        $stmt->execute();
    }

    function getAllComentarios(){
        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();
    
        $stmt = $conexion->prepare("SELECT * FROM Comentarios");
        $stmt->execute();
        $res = $stmt->get_result();
    
        $comentarios = array();
    
        if($res->num_rows > 0){
            while ($row = $res->fetch_assoc()) {
                // Obtener la información del usuario asociado al comentario
                $usuario = getUsuario($row['correo_usuario']);
                // Agregar el comentario junto con la URL de la foto de perfil del usuario al array de comentarios
                $comentarios[] = array(
                    'id_comentario' => $row['id_comentario'],
                    'usuario' => $usuario['usuario'],
                    'id_actividad' => $row['id_actividad'],
                    'correo' => $row['correo_usuario'],
                    'comentario' => $row['comentario'],
                    'fecha_comentario' => $row['fecha_comentario'],
                    'moderado' => $row['moderado'],
                    'url_foto_perfil' => $usuario['url_foto'] 
                );
            }
        }

        $res->close();
    
        return $comentarios;
    }

    function getComentariosNombre($usuario){
        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();
    
        $stmt = $conexion->prepare("SELECT * FROM Comentarios WHERE usuario LIKE ?");
        $usuario = "%".$usuario."%";
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $res = $stmt->get_result();
    
        $comentarios = array();
    
        if($res->num_rows > 0){
            while ($row = $res->fetch_assoc()) {
                // Obtener la información del usuario asociado al comentario
                $usuarioComentario = getUsuario($row['correo_usuario']);
                // Agregar el comentario junto con la URL de la foto de perfil del usuario al array de comentarios
                $comentarios[] = array(
                    'id_comentario' => $row['id_comentario'],
                    'usuario' => $usuarioComentario['usuario'],
                    'id_actividad' => $row['id_actividad'],
                    'correo' => $row['correo_usuario'],
                    'comentario' => $row['comentario'],
                    'fecha_comentario' => $row['fecha_comentario'],
                    'moderado' => $row['moderado'],
                    'url_foto_perfil' => $usuarioComentario['url_foto'] 
                );
            }
        }

        $res->close();
    
        return $comentarios;
    }

    function getComentarios($idAc){
        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();
    
        $stmt = $conexion->prepare("SELECT * FROM Comentarios WHERE id_actividad = ?");
        $stmt->bind_param("i", $idAc);
        $stmt->execute();
        $res = $stmt->get_result();
    
        $comentarios = array();
    
        if($res->num_rows > 0){
            while ($row = $res->fetch_assoc()) {
                // Obtener la información del usuario asociado al comentario
                $usuario = getUsuario($row['correo_usuario']);
                // Agregar el comentario junto con la URL de la foto de perfil del usuario al array de comentarios
                $comentarios[] = array(
                    'id_comentario' => $row['id_comentario'],
                    'usuario' => $usuario['usuario'],
                    'id_actividad' => $row['id_actividad'],
                    'correo' => $row['correo_usuario'],
                    'comentario' => $row['comentario'],
                    'fecha_comentario' => $row['fecha_comentario'],
                    'moderado' => $row['moderado'],
                    'url_foto_perfil' => $usuario['url_foto']
                );
            }
        }

        $res->close();
    
        return $comentarios;
    }
    
    

    function setComentario($idAc,$correo,$nombre,$comentario,$moderado){

        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();

        $stmt = $conexion->prepare("SELECT * FROM PalabrasProhibidas");
        $stmt->execute();
        $res = $stmt->get_result();
    
        $palabrasProhibidas = array();
    
        if($res->num_rows > 0){
            while ($row = $res->fetch_assoc()) {
                $palabrasProhibidas[] = $row['palabra'];
            }

        }

        //Dividimos el comentario en palabras
        $palabrasComentarios = explode(" ",$comentario);

        foreach ($palabrasComentarios as $indice => $palabra) {
            // Verificar si la palabra está en la lista de palabras prohibidas
            if (in_array(strtolower($palabra), $palabrasProhibidas)) {
                // Si la palabra está en la lista de palabras prohibidas, reemplazarla con asteriscos (*)
                $palabrasComentarios[$indice] = str_repeat("*", strlen($palabra));
            }
        }
    
        // Reconstruir el comentario con las palabras modificadas
        $comentarioModificado = implode(" ", $palabrasComentarios);
    
        $insercion = $conexion->prepare("INSERT INTO Comentarios (id_actividad,correo_usuario,usuario,comentario,fecha_comentario,moderado) VALUES (?, ?, ?, ?, ?, ?)");
        
        date_default_timezone_set('Europe/Madrid');
        $fecha = date("Y-m-d H:i:s");
        $correoModificado = strtolower($correo);
        $nombreModificado = strtolower($nombre);

        $insercion->bind_param("issssi", $idAc,$correoModificado,$nombreModificado,$comentarioModificado,$fecha,$moderado);
    
        // Ejecutar la consulta preparada
        $insercion->execute();
    }

    function setActividad($nombreActividad,$precio,$descripcion,$recomendaciones,$fecha,$ubicacionNombre,$ubicacionUrl,$portada,$foto1,$foto2,$publicada){

        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();

        $stmt = $conexion->prepare("SELECT * FROM Foto WHERE ruta = ?");
        $stmt->bind_param("s", $foto1);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            $insercion = $conexion->prepare("INSERT INTO Foto (ruta) VALUES (?)");
            $insercion->bind_param("s",$foto1);
            $insercion->execute();
            $id_foto1 = $conexion->insert_id;
        }else{
            $row = $result->fetch_assoc();
            $id_foto1 = $row['id_foto'];
        }

        $stmt->free_result();

        $stmt = $conexion->prepare("SELECT * FROM Foto WHERE ruta = ?");
        $stmt->bind_param("s", $foto2);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            $insercion = $conexion->prepare("INSERT INTO Foto (ruta) VALUES (?)");
            $insercion->bind_param("s",$foto2);
            $insercion->execute();
            $id_foto2 = $conexion->insert_id;
        }else{
            $row = $result->fetch_assoc();
            $id_foto2 = $row['id_foto'];
        }

        $stmt->free_result();

        $insercion = $conexion->prepare("INSERT INTO Actividad (nombre,fecha,descripcion,precio,recomendaciones,ubicacion_nombre,ubicacion_url,ruta,publicada) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");       
        $insercion->bind_param("sssissssi",$nombreActividad,$fecha,$descripcion,$precio,$recomendaciones, $ubicacionNombre,$ubicacionUrl,$portada,$publicada);
        $insercion->execute();

        $id_actividad = $conexion->insert_id;

        $insercion = $conexion->prepare("INSERT INTO FotosActividad (id_actividad,id_foto) VALUES (?, ?)");
        $insercion->bind_param("ii",$id_actividad,$id_foto1);
        $insercion->execute();

        $insercion = $conexion->prepare("INSERT INTO FotosActividad (id_actividad,id_foto) VALUES (?, ?)");
        $insercion->bind_param("ii",$id_actividad,$id_foto2);
        $insercion->execute();
    }

    function setUsuario($nombre,$usuario,$correo,$password){

        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();

        $stmt = $conexion->prepare("SELECT * FROM Usuario WHERE usuario = ? OR correo = ?");
        $stmt->bind_param("ss", $usuario,$correo);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows > 0){
            header("Location: registro");
            exit;
        }else{
            $url_foto="../img/user-photo.jpg";
            $password_hash = password_hash($password,PASSWORD_DEFAULT);
            $insercion = $conexion->prepare("INSERT INTO Usuario (nombre,usuario,correo,password,url_foto_perfil) VALUES (?, ?, ?, ?, ?)");
            $insercion->bind_param("sssss", $nombre,$usuario,$correo,$password_hash,$url_foto);
            
            if($insercion->execute()){
                $rol = 2;
                $insercion2 = $conexion->prepare("INSERT INTO Asignar (correo,id_rol) VALUES (?, ?)");
                $insercion2->bind_param("si", $correo,$rol);
                $insercion2->execute();
            }else{
                header("Location: registro");
                exit;
            }
        }
    }

    function updateUsuario($nombre,$usuario,$correoNuevo,$correoAntiguo,$password,$url_foto){

        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();

        $update = $conexion->prepare("
        UPDATE Usuario 
        SET nombre = IF(?='', nombre, ?),
            usuario = IF(?='', usuario, ?),
            password = IF(?='', password, ?),
            url_foto_perfil = IF(?='', url_foto_perfil, ?)
        WHERE correo = ?
        ");
        $password_hash = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : '';
        $update->bind_param("sssssssss", 
            $nombre, $nombre, 
            $usuario, $usuario, 
            $password_hash, $password_hash, 
            $url_foto, $url_foto, 
            $correoAntiguo);
        $update->execute();
        $update->close();

        $conexion->close();
    }

    function updateActividad($idAc,$nombreActividad,$descripcion,$precio,$recomendaciones,$fecha,$ubicacionNombre,$ubicacionUrl,$ruta,$publicada){
        
        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();
        
        $update = $conexion->prepare("
            UPDATE Actividad 
            SET nombre = IF(?='', nombre, ?),
                descripcion = IF(?='', descripcion, ?),
                precio = IF(? IS NULL, precio, ?),
                recomendaciones = IF(?='', recomendaciones, ?),
                fecha = IF(?='', fecha, ?),
                ubicacion_nombre = IF(?='', ubicacion_nombre, ?),
                ubicacion_url = IF(?='', ubicacion_url, ?),
                ruta = IF(?='', ruta, ?),
                publicada = IF(? IS NULL, publicada, ?)
            WHERE id = ?
        ");
        $update->bind_param("ssssiissssssssssiii", 
            $nombreActividad, $nombreActividad, 
            $descripcion, $descripcion, 
            $precio, $precio, 
            $recomendaciones, $recomendaciones, 
            $fecha, $fecha, 
            $ubicacionNombre, $ubicacionNombre, 
            $ubicacionUrl, $ubicacionUrl, 
            $ruta, $ruta, 
            $publicada, $publicada,
            $idAc);

        $update->execute();
        $update->close();
        
        $conexion->close();
    }

    function updateComentario($idAc,$correo,$nuevoComentario,$fecha,$id_comentario,$moderado){
        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();
    
        $fecha = date("Y-m-d H:i:s");
    
        if(!empty($nuevoComentario)){
            $update = $conexion->prepare("UPDATE Comentarios SET comentario = ?, fecha_comentario = ?, moderado = ? WHERE id_actividad = ? AND correo_usuario = ? AND id_comentario = ?");
            $update->bind_param("ssiisi", $nuevoComentario, $fecha,$moderado, $idAc, $correo, $id_comentario);
            $update->execute();
            $update->close();
        }

        $conexion->close();
    }



    function updateRol($correo,$nuevoRol){
        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();

        if($nuevoRol == "Administrador") $rol=1;
        else if($nuevoRol == "Gestor") $rol=4;
        else if($nuevoRol == "Moderador") $rol=3;
        else if($nuevoRol == "Registrado") $rol=2;

        $update = $conexion->prepare("UPDATE Asignar SET id_rol = ? WHERE correo = ?");
        $update->bind_param("is",$rol,$correo);
        $update->execute();
    }

    function checkLogin($correo,$password){
        
        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();

        $stmt = $conexion->prepare("SELECT * FROM Usuario WHERE correo = ?");
        $stmt->bind_param("s",$correo);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if($res->num_rows > 0){
            if(password_verify($password, $res->fetch_assoc()["password"])){
                return true;
            } 
        }
        return false;
    }
?>