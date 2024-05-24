// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Obtén el formulario por su ID
    const form = document.getElementById('form');

    // Escucha el evento de envío del formulario
    form.addEventListener('submit', function(event) {

      // Reinicia los valores de los campos de entrada del formulario
      form.reset();
    });
  });