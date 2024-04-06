//CODIGO PARA AÑADIR UN NUEVO COMENTARIO EN EL APARTADO COMENTARIOS
import Swal from 'https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/+esm'

document.addEventListener("DOMContentLoaded",function(){
    var comments = document.getElementById("comments");
    var addForm = document.getElementById("form");
    var formulario = document.getElementById("content-form");
    var commentDate = document.getElementsByClassName("date");

    //Añado la fecha para los dos comentarios que se han insertado con html
    commentDate[0].textContent = obtenerFechaActual();
    commentDate[1].textContent = obtenerFechaActual();

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
            Swal.fire({
                title: '<h4 style="color: black">No ha rellenado todos los campos</h4>',
                icon: 'error',
            });
            return;
        }

        //Compruebo que el formato del correo es correcto
        if(!validarCorreo(correo)){
            Swal.fire({
                title: '<h4>El formato del correo es incorrecto</h4>',
                icon: 'error',
            });
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
        comments.insertBefore(nuevoContenido,formulario);

        //Limpio los campos del formulario
        addForm.reset();
    });
});

function obtenerFechaActual() {
    var fechaHora = new Date();
    var dia = agregarCeroDelante(fechaHora.getDate());
    var mes = agregarCeroDelante(fechaHora.getMonth() + 1); // Los meses comienzan desde 0
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