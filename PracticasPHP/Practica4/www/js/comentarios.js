import Swal from 'https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/+esm'

document.addEventListener("DOMContentLoaded",function(){
    var addForm = document.getElementById("form");

    addForm.addEventListener("submit",function(){
        var comentario = document.getElementById("input-comment").value;

        //Compruebo que ningun campo esta vacío y en caso de estarlo mando alerta avisando
        if(comentario.trim() === '' ){
            Swal.fire({
                title: '<h4 style="color: black">No ha escrito ningun comentario</h4>',
                icon: 'error',
            });
            event.preventDefault();
            return;
        }
        
    });
});

