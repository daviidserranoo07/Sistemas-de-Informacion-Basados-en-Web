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
            var container = $('.container-columns');
            container.empty();
    
            $.each(actividades, function(i, Actividad) {
                if(Actividad.publicada){
                    var actividadDiv = $('<div>').addClass('actividad');
                    var linkElement = $('<a>').attr('href', 'actividad/' + Actividad.id);
                    var contenidoDiv = $('<div>').addClass('contenido-actividad');
                    var imgElement = $('<img>').attr('src', Actividad.portada).attr('alt', Actividad.nombre);
                    var h3Element = $('<h3>').html(Actividad.nombre.replace(new RegExp(valor, 'gi'), function(match) {
                        return '<span class="resaltar">' + match + '</span>';
                    }));
        
                    contenidoDiv.append(imgElement, h3Element);
                    linkElement.append(contenidoDiv);
                    actividadDiv.append(linkElement);
                    container.append(actividadDiv);
                }
            });
        }
     });
}