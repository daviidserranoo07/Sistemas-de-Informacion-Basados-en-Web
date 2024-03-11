var mostrarComentarios = document.getElementById("button-comments"); /*Coge el boton de los comentarios*/
var comments = document.getElementById("comments");

mostrarComentarios.addEventListener("click",function(){ //Se ejecuta esta función cuando se hace click en el boton de los comentarios
    event.preventDefault();//Evita que se refresca la página cuando lo pulso
    if(comments.classList.contains("oculto")){//Cambia el estilo el display del div de los comentarios de none a grid 
        comments.classList.remove("oculto");
        comments.classList.add("visible")
    }else{
        comments.classList.remove("visible");
        comments.classList.add("oculto")
    }
});