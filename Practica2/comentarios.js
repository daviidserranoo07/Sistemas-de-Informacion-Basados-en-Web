document.addEventListener("DOMContentLoaded",function(){
    var mostrarComentarios = document.getElementById("button-comments"); /*Coge el boton de los comentarios*/
    var comments = document.getElementsByClassName("comments");
    var addForm = document.getElementById("form");
    var formulario = document.getElementById("content-form");
    var commentDate = document.getElementsByClassName("date");

    commentDate[0].textContent = obtenerFechaActual();
    commentDate[1].textContent = obtenerFechaActual();


    mostrarComentarios.addEventListener("click",function(){ //Se ejecuta esta función cuando se hace click en el boton de los comentarios
        event.preventDefault();//Evita que se refresca la página cuando lo pulso
        if(comments[0].style.display === "none"){
            comments[0].style.display = "grid"; //Cambia el estilo el display del div de los comentarios de none a grid 
        }else{
            comments[0].style.display = "none";
        }
    });

    addForm.addEventListener("submit",function(){
        event.preventDefault();

         //Obtengo valor de cada campo del formulario
        var nombre = document.getElementById("input-name").value;
        var correo = document.getElementById("input-correo").value;
        var comentario = document.getElementById("input-comment").value;
        var fotoPerfilInput = document.getElementById("input-image");
        var fotoPerfil = fotoPerfilInput.files[0];

        //Compruebo que ningun campo esta vacío y en caso de estarlo mando alerta avisando
        if(nombre.trim() === '' || correo.trim() === '' || comentario.trim() === '' ){
            alert("Por favor, rellene todos los campos.");
            return;
        }

        //Compruebo que el formato del correo es correcto
        if(!validarCorreo(correo)){
            alert("El correo electrónico no es válido");
            return;
        }

        //Creo nuevos elementos para el comentario con sus respectivas clase
        var nuevoContenido = document.createElement("div");
        nuevoContenido.classList.add("comment");
        
        var nombreUsuario = document.createElement("h5");
        nombreUsuario.textContent = nombre;
        nombreUsuario.classList.add("name-user");

        var crearComentario = document.createElement("p");
        crearComentario.textContent = comentario;
        crearComentario.classList.add("comment-p");

        var crearFotoPerfil = document.createElement("img");
        if(fotoPerfil){
            crearFotoPerfil.src = URL.createObjectURL(fotoPerfil);
        }else{//Si el usuario no agrega una foto, ponemos una por defecto
            crearFotoPerfil.src = "/img/user-photo.jpg";
        }
        crearFotoPerfil.classList.add("foto-perfil");

        var fecha = document.createElement("p");
        fecha.textContent = obtenerFechaActual();
        fecha.classList.add("comment-date");

        //Añado elementos a el div creado al principio
        nuevoContenido.appendChild(crearFotoPerfil);
        nuevoContenido.appendChild(nombreUsuario);
        nuevoContenido.appendChild(crearComentario);
        nuevoContenido.appendChild(fecha);

        ///Inserto cada nuevo comentario debajo del formulario y de los comentarios ya existentes
        comments[0].insertBefore(nuevoContenido,formulario);

        //Limpio los campos del formulario
        addForm.reset();
    });
});

function obtenerFechaActual() {
    var fechaHora = new Date();
    var dia = agregarCeroDelante(fechaHora.getDate());
    var mes = agregarCeroDelante(fechaHora.getMonth()) + 1; // Los meses comienzan desde 0
    var año = agregarCeroDelante(fechaHora.getFullYear());
    var horas = agregarCeroDelante(fechaHora.getHours());
    var minutos = agregarCeroDelante(fechaHora.getMinutes());
    var segundos = agregarCeroDelante(fechaHora.getSeconds());

    // Damos formato a la fecha y hora
    return dia + '/' + mes + '/' + año + ' ' + horas + ':' + minutos + ':' + segundos;
}

function agregarCeroDelante(numero) {
    return numero < 10 ? '0' + numero : numero;
}

function validarCorreo(correo){
    var expresion = /\S+@\S+\.\S+/;
    return expresion.test(correo);
}


//GESTIONAMOS PALABRAS PROHIBIDAS
var palabrasProhibidas = ["puta","cabron","gilipollas","pollas","tonto","retrasado","cabrón","puto","cabrones"]; //Defino palabras prohibidas
var comentarioInput = document.getElementById("input-comment");//Obtengo el comentario que se va escribiendo

comentarioInput.addEventListener("input",function(){ //Cuando se escribe en el input de comentario se ejecuta esta función
        var comentario = comentarioInput.value; //Obtengo el comentario escrito

        palabrasProhibidas.forEach(function(palabra){ //Define las expresiones regulares de las palabras prohibidas y las sustituyo por astersicos
            var regex = new RegExp('\\b' + palabra + '\\b','gi'); //La \\b define los limites de las palabras y g para todas las palabras que haya e i para no diferenciar entre mayusculas y minúsculas
            comentario = comentario.replace(regex,'*'.repeat(palabra.length)); //Reemplazo las palabras de las expresiones regulares por asteriscos
        });

        comentarioInput.value = comentario; //Sustituyo el comentario que teniamos en el input por el nuevo quitando palabras prohibidas
});