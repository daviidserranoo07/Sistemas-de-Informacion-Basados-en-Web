import Swal from 'https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/+esm'

document.addEventListener("DOMContentLoaded",function(){
    var addForm = document.getElementById("form");

    addForm.addEventListener("submit",function(){
        
        //Obtengo valor de cada campo del formulario
        var nombre = document.getElementById("input-name").value;
        var correo = document.getElementById("input-correo").value;
        var comentario = document.getElementById("input-comment").value;

        //Compruebo que ningun campo esta vacío y en caso de estarlo mando alerta avisando
        if(nombre.trim() === '' || correo.trim() === '' || comentario.trim() === '' ){
            Swal.fire({
                title: '<h4 style="color: black">No ha rellenado todos los campos</h4>',
                icon: 'error',
            });
            event.preventDefault();
            return;
        }

        //Compruebo que el formato del correo es correcto
        if(!validarCorreo(correo)){
            Swal.fire({
                title: '<h4>El formato del correo es incorrecto</h4>',
                icon: 'error',
            });
            event.preventDefault();
            return;
        }
        
    });
});

function validarCorreo(correo){
    var expresion = /\S+@\S+\.\S+/;
    return expresion.test(correo);
}

