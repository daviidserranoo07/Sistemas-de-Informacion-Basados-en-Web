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
                    'portada' => $row['ruta']
                );
            }
        }
        
        $res->close();

        return $actividades;
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
            'idAc'=>$_GET['ac'],
            'nombre'=>'Por Defecto',
            'fecha'=>'Por Defecto',
            'descripcion'=>'Por Defecto',
            'precio'=>'Por Defecto',
            'recomendaciones'=>'Por Defecto',
            'ubicacion_nombre'=>'Por Defecto',
            'ubicacion_url'=>'Por Defecto'
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
                               'portada' => $row['ruta']
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

        // Consultar la tabla Fotos para obtener la información de cada foto
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

        // Agregar las fotos a la actividad
        $actividad['fotos'] = $fotos;

        $res->close();

        return $actividad;
    }

    function getComentarios($idAc){

        $conexionBD = ConexionBD::obtenerInstancia();
        $conexion = $conexionBD->obtenerConexion();
    
        $stmt = $conexion->prepare("SELECT * FROM Comentarios WHERE id_actividad = ?");
        $stmt->bind_param("i", $_GET['ac']);
        $stmt->execute();
        $res = $stmt->get_result();
    
        $comentarios = array();
    
        if($res->num_rows > 0){
            while ($row = $res->fetch_assoc()) {
                $comentarios[] = array(
                    'id_actividad' => $row['id_actividad'],
                    'nombre_usuario' => $row['nombre_usuario'],
                    'url_foto_perfil' => $row['url_foto_perfil'],
                    'correo' => $row['correo'],
                    'comentario' => $row['comentario'],
                    'fecha_comentario' => $row['fecha_comentario']
                );
            }
        }

        $res->close();

        return $comentarios;
    }
    

    function setComentario($idAc,$nombre,$url_foto,$correo,$comentario,$fecha){

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
        $comentarioMinuscula = 
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
    
        $insercion = $conexion->prepare("INSERT INTO Comentarios (id_actividad,nombre_usuario,url_foto_perfil,correo,comentario,fecha_comentario) VALUES (?, ?, ?, ?, ?, ?)");
        
        $fecha = date("Y-d-m H:i:s");
        $correoModificado = strtolower($correo);
        $nombreModificado = strtolower($nombre);

        $insercion->bind_param("isssss", $idAc,$nombreModificado,$url_foto,$correoModificado,$comentarioModificado,$fecha);
    
        // Ejecutar la consulta preparada
        $insercion->execute();
    }
?>