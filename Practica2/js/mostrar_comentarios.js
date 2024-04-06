//GESTIONAMOS MOSTRAR O NO COMENTARIOS CUANDO SE PULSE EL BOTÓN
var mostrarComentarios = document.getElementById("button-comments"); //Obtiene el boton de los comentarios
var comments = document.getElementById("comments");//Obtiene el div que contiene toda la sección de comentarios

mostrarComentarios.addEventListener("click",function(){ //Se ejecuta esta función cuando se hace click en el boton de los comentarios
    event.preventDefault();//Evita que se refresca la página cuando lo pulso
    if(comments.classList.contains("oculto")){//Cambia el estilo el display del div de los comentarios de none a grid 
        comments.classList.remove("oculto");
        comments.classList.add("visible")
    }else{//Aquí hace lo contrario cambia el estilo del display de grid a none
        comments.classList.remove("visible");
        comments.classList.add("oculto")
    }
});