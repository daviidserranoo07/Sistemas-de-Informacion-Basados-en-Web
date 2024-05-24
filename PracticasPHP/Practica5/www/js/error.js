// Función para mostrar el mensaje de error
function mostrarError() {
    document.getElementById("error-mensaje").style.display = "block";
}

// Verificar si hay un parámetro de URL indicando que el inicio de sesión ha fallado
const urlParams = new URLSearchParams(window.location.search);
const loginError = urlParams.get('error');
if (loginError === '1') {
    mostrarError();
}