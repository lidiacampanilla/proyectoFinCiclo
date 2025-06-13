document.addEventListener("DOMContentLoaded", function () {
  let form = document.getElementById("loginForm");
  let errorDiv = document.getElementById("error");
  let url = "/php/login.php";

  form.addEventListener("submit", async (e) => {
    //Para evitar recargas
    e.preventDefault();

    //Eliminar mensajes previos
    errorDiv.style.display = "none";
    errorDiv.innerText = "";

    let email = form.email.value.trim();
    let password = form.password.value;

    try {
      // Validar campos. Enviamos los campos email y password al servidor
      let response = await fetch(url, {
        method: "POST",
        headers: { Accept: "application/json" },
        body: new URLSearchParams({ email, password }),
      });
      let text = await response.text();

      let result;
      // Si la respuesta no es JSON, lanzamos un error
      try {
        result = JSON.parse(text);
      } catch (e) {
        throw new Error("Respuesta no es JSON: ");
      }
      // Si la respuesta no es un objeto con la propiedad success, lanzamos un error. La propiedad success debe ser un booleano.
      if (result.success) {
        // Si la respuesta es correcta, redirigimos a usuarios.php
        window.location.href = "/php/usuarios.php";
      } else {
        // Si la respuesta es incorrecta, mostramos el mensaje de error
        errorDiv.innerText = result.message;
        errorDiv.style.display = "block";
      }
    } catch (e) {
      errorDiv.innerText = "Error de conexion. Intentalo mas tarde.";
      errorDiv.style.display = "block";
    }
  });
});
