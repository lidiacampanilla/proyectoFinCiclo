document.querySelector("form").addEventListener("submit", function (e) {
  e.preventDefault();

  let form = e.target;
  let formData = new FormData(form);
  let errorDiv = document.getElementById("error");

  //Limpiamos pantalla por si habia algún error antes
  document
    .querySelectorAll(".is-invalid")
    .forEach((el) => el.classList.remove("is-invalid"));

  fetch("/php/registroUsuarios.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text())
    .then((result) => {
      //Se muestra el mensaje devuelto por registroUsuarios.php
      errorDiv.innerHTML = result;
      errorDiv.style.display = "block";

      // Hacer scroll al principio de la página
      window.scrollTo({ top: 0, behavior: "smooth" });

      //Identificamos los campos con error
      if (result.includes("DNI")) {
        document.getElementById("dni").classList.add("is-invalid");
      }
      if (result.includes("email")) {
        document.getElementById("email").classList.add("is-invalid");
      }
      if (result.includes("cuenta")) {
        document.getElementById("cuenta").classList.add("is-invalid");
      }
    })

    .catch((error) => {
      console.error("Error en la peticion:", error);
    });
});
