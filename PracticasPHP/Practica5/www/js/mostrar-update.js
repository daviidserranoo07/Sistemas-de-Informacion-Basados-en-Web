var mostrarUpdate = document.getElementById("button-update");
var update = document.getElementById("update");
var informacion = document.getElementById("informacion");

mostrarUpdate.addEventListener("click",function(){
    event.preventDefault();
    if(update.classList.contains("oculto")){
        update.classList.remove("oculto");
        update.classList.add("visible");
        informacion.classList.remove("visible");
        informacion.classList.add("oculto");
        
    }else{
        update.classList.remove("visible");
        update.classList.add("oculto");
        informacion.classList.remove("oculto");
        informacion.classList.add("visible");
    }
});