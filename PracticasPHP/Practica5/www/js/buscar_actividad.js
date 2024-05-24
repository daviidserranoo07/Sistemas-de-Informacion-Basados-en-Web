$(document).ready(function() {
    console.log('Script cargado');
    $('#input-busqueda').on('input', function() {
        console.log($('#input-busqueda').val());
        buscarActividad($('#input-busqueda').val());
    });
});


function buscarActividad(valor) {
    $.ajax({
        data: {valor},
        url: '../buscar_actividad.php',
        type: 'POST',
        success: function(response) {
            var actividades = JSON.parse(response);
            var actividadesContainer = $('.container-rows');
            actividadesContainer.empty();

            actividades.forEach(function(actividad) {
                var actividadElement = $('<div>').addClass('actividad');
                var linkElement = $('<a>').attr('href', '../actividad/' + actividad.id).attr('id','nombre');
                var nombreElement = $('<h3>').html(actividad.nombre.replace(new RegExp(valor, 'gi'), function(match) {
                    return '<span class="resaltar">' + match + '</span>';
                }));
                
                linkElement.append(nombreElement);

                if (actividad.publicada) {
                    var publicadaElement = $('<h3>').attr('id', 'publicada').text('(Publicada)');
                    linkElement.append(publicadaElement);
                } else {
                    var noPublicadaElement = $('<h3>').attr('id', 'no-publicada').text('(No Publicada)');
                    linkElement.append(noPublicadaElement);
                }

                actividadElement.append(linkElement);

                var modificarLinkElement = $('<a>').attr('href', 'modificar_actividad/' + actividad.id);
                var modificarImgElement = $('<img>').addClass('edit').attr('src', '../img/lapiz.png');
                modificarLinkElement.append(modificarImgElement);
                actividadElement.append(modificarLinkElement);

                var formElement = $('<form>').attr('id', 'delete-form').attr('action', '../eliminar_actividad.php').attr('method', 'post').attr('enctype', 'multipart/form-data');
                var inputHiddenElement = $('<input>').attr('type', 'hidden').attr('name', 'id').attr('value', actividad.id);
                var inputImageElement = $('<input>').attr('type', 'image').attr('src', '../img/papelera.png').addClass('edit');
                formElement.append(inputHiddenElement, inputImageElement);
                actividadElement.append(formElement);

                actividadesContainer.append(actividadElement);
            });
        }
     });
}