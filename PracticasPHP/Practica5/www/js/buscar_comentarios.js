$(document).ready(function() {
    console.log('Script cargado');
    $('#input-busqueda').on('input', function() {
        console.log($('#input-busqueda').val());
        buscarComentario($('#input-busqueda').val());
    });
});


function buscarComentario(valor) {
    $.ajax({
        data: {valor},
        url: '../buscar_comentarios.php',
        type: 'POST',
        success: function(response) {
            var comentarios = JSON.parse(response);
            console.log(comentarios);
            var comentariosContainer = $('.container-rows');
            comentariosContainer.empty();
            
            comentarios.forEach(function(comentario) {
                var comentarioElement = $('<div>').addClass('comment');

                var infoUserElement = $('<div>').addClass('info-user');
                var fotoPerfilElement = $('<img>').addClass('foto-perfil').attr('src', comentario.url_foto_perfil);
                var valorRegex = new RegExp(valor, 'gi');
                var nameUserElement = $('<h5>').html(comentario.usuario.replace(valorRegex, function(match) {
                    return $('<span>').addClass('resaltar').text(match).prop('outerHTML');
                })).addClass('name-user');
                infoUserElement.append(fotoPerfilElement, nameUserElement);

                var infoCommentElement = $('<div>').addClass('info-comment');
                var commentPElement = $('<p>').addClass('comment-p').text(comentario.comentario);
                var commentDateElement = $('<p>').addClass('comment-date').text(comentario.moderado ? 'Moderado: ' + comentario.fecha_comentario : comentario.fecha_comentario);
                infoCommentElement.append(commentPElement, commentDateElement);

                var modificarComentarioElement = $('<div>').attr('id', 'modificar-comentario');
                var formElement = $('<form>').attr('action', '../update_comentario.php').attr('id', 'form').attr('method', 'post').attr('enctype', 'multipart/form-data');
                var inputAcElement = $('<input>').attr('type', 'hidden').attr('name', 'ac').attr('value', comentario.id_actividad);
                var inputIdComentarioElement = $('<input>').attr('type', 'hidden').attr('name', 'input-id-comentario').attr('value', comentario.id_comentario);
                var inputUsuarioElement = $('<input>').attr('type', 'hidden').attr('id', 'input-usuario').attr('name', 'input-usuario').attr('value', comentario.usuario);
                var inputCorreoElement = $('<input>').attr('type', 'hidden').attr('id', 'input-correo').attr('name', 'input-correo').attr('value', comentario.correo);
                var inputComentarioElement = $('<div>').addClass('input-comentario');
                var labelElement = $('<label>').text('Moderar Comentario:');
                var textareaElement = $('<textarea>').attr('id', 'input-comment').attr('name', 'input-comentario');
                inputComentarioElement.append(labelElement, textareaElement);
                var inputSubmitElement = $('<input>').attr('type', 'submit').attr('id', 'set-formulario').attr('name', 'enviar').attr('placeholder', 'Editar Comentario');
                formElement.append(inputAcElement, inputIdComentarioElement, inputUsuarioElement, inputCorreoElement, inputComentarioElement, inputSubmitElement);
                modificarComentarioElement.append(formElement);

                var deleteFormElement = $('<form>').attr('id', 'delete-form').attr('action', '../eliminar_comentario.php').attr('method', 'post').attr('enctype', 'multipart/form-data');
                var inputIdElement = $('<input>').attr('type', 'hidden').attr('name', 'id').attr('value', comentario.id_actividad);
                var inputIdComentarioElement = $('<input>').attr('type', 'hidden').attr('name', 'id_comentario').attr('value', comentario.id_comentario);
                var inputCorreoElement = $('<input>').attr('type', 'hidden').attr('name', 'correo').attr('value', comentario.correo);
                var inputImageElement = $('<input>').attr('type', 'image').attr('src', '../img/papelera.png').addClass('edit');
                deleteFormElement.append(inputIdElement, inputIdComentarioElement, inputCorreoElement, inputImageElement);
                modificarComentarioElement.append(deleteFormElement);

                comentarioElement.append(infoUserElement, infoCommentElement, modificarComentarioElement);

                comentariosContainer.append(comentarioElement);
            });
        }
     });
}