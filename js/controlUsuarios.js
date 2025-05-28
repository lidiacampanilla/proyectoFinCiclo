document.addEventListener("DOMContentLoaded", ()=>{
    if (tipoUsuario === "nazareno"){
        document.getElementById("nazarenos").style.display = "block";
    }else if (tipoUsuario === "mantilla"){
        document.getElementById("mantillas").style.display = "block";
    }else if (tipoUsuario === "administrador"){
        document.getElementById("administrador").style.display = "block";
    }else if (tipoUsuario === "junta"){
        document.getElementById("junta").style.display = "block";
    }else {
        document.getElementById("otros").style.display = "block";
    }
});