//GESTIONAMOS PALABRAS PROHIBIDAS
var palabrasProhibidas = ["puta","cabron","gilipollas","pollas","tonto","retrasado","puto"]; //Defino palabras prohibidas
var comentarioInput = document.getElementById("input-comment");//Obtengo el comentario que se va escribiendo

comentarioInput.addEventListener("input",function(){ //Cuando se escribe en el input de comentario se ejecuta esta función
        var comentario = comentarioInput.value; //Obtengo el comentario escrito

        palabrasProhibidas.forEach(function(palabra){ //Define las expresiones regulares de las palabras prohibidas y las sustituyo por astersicos
            var regex = new RegExp('\\b' + palabra + '\\b','gi'); //La \\b define los limites de las palabras y g para todas las palabras que haya e i para no diferenciar entre mayusculas y minúsculas
            comentario = comentario.replace(regex,'*'.repeat(palabra.length)); //Reemplazo las palabras de las expresiones regulares por asteriscos
        });

        comentarioInput.value = comentario; //Sustituyo el comentario que teniamos en el input por el nuevo quitando palabras prohibidas
});