document.querySelector("form").addEventListener("submit", function(e){
    e.preventDefault();

    let form = e.target;
    let formData = new FormData(form);
    let errorDiv = document.getElementById("error");

    //Limpiamos pantalla por si habia algún error antes
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    fetch("registroUsuarios.php", {
        method: "POST",
        body: formData,
    })
        .then(response => response.text())
        .then(result =>{
            
            if (result.trim() == "ok"){
                window.location.href = "../php/acceso.php";
            }else{
                //Se muestra el mensaje devuelto por registroUsuarios.php
                errorDiv.textContent = result;
                errorDiv.style.display = "block";

                //Identificamos los campos con error
                if (result.includes("DNI")){
                    document.getElementById("dni").classList.add("is-invalid");
                }
                if (result.includes("email")){
                    document.getElementById("email").classList.add("is-invalid");
                }
                if (result.includes("cuenta")){
                    document.getElementById("cuenta").classList.add("is-invalid");
                }

            }
        })

        .catch(error => {
            console.error("Error en la peticion:", error);
        });
});
