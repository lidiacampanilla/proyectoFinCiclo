/* Con este código, se gestiona la visualización de diferentes secciones del perfil de usuario según su tipo, de esta manera se mostrará la información relevante para cada tipo de usuario. */

document.addEventListener("DOMContentLoaded", () => {
  if (tipoUsuario === "nazareno") {
    document.getElementById("nazarenos").style.display = "block";
  } else if (tipoUsuario === "mantilla") {
    document.getElementById("mantillas").style.display = "block";
  } else if (tipoUsuario === "administrador") {
    document.getElementById("administrador").style.display = "block";
  } else if (tipoUsuario === "junta") {
    document.getElementById("junta").style.display = "block";
  } else {
    document.getElementById("otros").style.display = "block";
  }
});

// Esta función se encarga de activar el checkbox "checkAll" para seleccionar/desmarcar todos los checkboxes de la lista de hermanos
function activarCheckAll() {
  let checkAll = document.getElementById("checkAll");
  if (checkAll) {
    // Clonamos el checkbox para evitar problemas de eventos duplicados
    // y lo reemplazamos en el DOM para que se pueda volver a usar
    let nuevo = checkAll.cloneNode(true);
    checkAll.parentNode.replaceChild(nuevo, checkAll);
    nuevo.addEventListener("change", function () {
      // Al cambiar el estado del checkbox "checkAll", se marcan/desmarcan todos los checkboxes de la lista
      let checks = document.querySelectorAll("input[name='elegido[]']");
      checks.forEach((chk) => (chk.checked = this.checked));
    });
  }
}

// Esta función se encarga de cargar los tipos de usuario en el select correspondiente
function cargarTiposEnSelect(select) {
  fetch("/php/obtenerTipos.php")
    .then((res) => res.json())
    .then((tipos) => {
      select.innerHTML = "";
      tipos.forEach((tipo) => {
        let option = document.createElement("option");
        option.value = tipo.Nomb_tipo;
        option.textContent = tipo.Nomb_tipo;
        select.appendChild(option);
      });
    });
}

/* Con este trozo de codigo controlaremos si el usuario quiere ver su perfil una vez logeado */
/* Ademas segun el tipo de usuario mostrara, o bien los datos directamente o bien dos botones,
 que daran la opcion de ver datos personales o datos de los hermanos */
document.addEventListener("DOMContentLoaded", function () {
  let perfilBtn = document.getElementById("miPerfilBtn");
  let divMiPerfil = document.getElementById("miPerfil");
  let avisosDiv = document.getElementById("avisos");
  let avisosTitulo = document.getElementById("avisosTitulo");
  if (!perfilBtn || !divMiPerfil) return;

  perfilBtn.addEventListener("click", function (e) {
    e.preventDefault();
    divMiPerfil.style.display = "block";
    if (avisosDiv) avisosDiv.style.display = "none";
    if (avisosTitulo) avisosTitulo.style.display = "none";

    //Limpiamos el contenido previo
    divMiPerfil.innerHTML = "<h2>Mi Perfil</h2>";

    // Tipos de usuario normales
    let tiposNormales = ["mantilla", "nazareno", "costalero", "otros"];

    if (tiposNormales.includes(tipoUsuario.toLowerCase())) {
      // Redirige a mostrarDatos.php
      fetch("/php/mostrarDatos.php")
        .then((res) => res.text())
        .then((html) => {
          divMiPerfil.innerHTML += html;
        })
        .catch(() => {
          divMiPerfil.innerHTML +=
            '<div class="alert alert-danger">No se pudo cargar tus datos.</div>';
        });
    } else if (
      tipoUsuario.toLowerCase() === "junta" ||
      tipoUsuario.toLowerCase() === "administrador"
    ) {
      divMiPerfil.innerHTML += `
                <button class="btn btn-mis-datos m-2" id="btnMisDatos">Mis Datos</button>
                <button class="btn btn-secondary m-2" id="btnDatosHermanos">Datos Hermanos</button>
                <div id="contenidoExtra"></div>
            `;

      // Botón Mis Datos
      document.getElementById("btnMisDatos").onclick = function () {
        // Limpiamos el contenido previo
        divMiPerfil.innerHTML = "<h2>Mis Datos</h2>";
        fetch("/php/mostrarDatos.php")
          .then((res) => res.text())
          .then((html) => {
            divMiPerfil.innerHTML += html;
          })
          .catch(() => {
            divMiPerfil.innerHTML +=
              '<div class="alert alert-danger">No se pudo cargar tus datos.</div>';
          });
      };

      // Botón Datos Hermanos
      document.getElementById("btnDatosHermanos").onclick = function () {
        fetch("/php/mostrarHermanos.php")
          .then((res) => res.text())
          .then((html) => {
            document.getElementById("contenidoExtra").innerHTML = html;
            activarCheckAll(); // Activamos el checkbox "checkAll"
          })
          .catch(() => {
            document.getElementById("contenidoExtra").innerHTML =
              '<div class="alert alert-danger">No se pudo cargar la tabla de hermanos.</div>';
          });
      };
    }
  });
});

// Delegación de eventos para los botones de operaciones
document.addEventListener("click", function (e) {
  let btn = e.target.closest(".btn-operacion");
  if (!btn) return;
  let accion = btn.getAttribute("data-accion")?.toLowerCase();

  // INSERTAR
  if (accion === "insertar") {
    //Limpiamos pantalla por si habia algún error antes
    document
      .querySelectorAll(".is-invalid")
      .forEach((el) => el.classList.remove("is-invalid"));
    let tabla = document.querySelector("#formGestionHer table tbody");
    if (tabla) {
      let columnas = tabla.parentElement.querySelectorAll("thead th");
      let fila = document.createElement("tr");
      fila.innerHTML =
        `<td></td>` +
        Array.from(columnas)
          .slice(1)
          .map((th) => {
            let name = th.textContent.trim();
            if (name === "password") {
              return `<td><input type="password" class="form-control form-control-sm" name="password"></td>`;
            } else if (name === "Nomb_tipo") {
              return `<td><select class="form-select form-select-sm" name="Nomb_tipo"></select></td>`;
            } else if (name === "id_usu") {
              return `<td></td>`;
            } else {
              return `<td><input type="text" class="form-control form-control-sm" name="${name}"></td>`;
            }
          })
          .join("");
      //Añadimos la fila al inicio de la tabla          
      tabla.prepend(fila);
      // Cargar tipos en el select
      fila.querySelectorAll('select[name="Nomb_tipo"]').forEach((select) => {
        cargarTiposEnSelect(select);
      });
      // Botón para guardar la inserción
      let btnGuardar = document.createElement("button");
      btnGuardar.type = "button";
      btnGuardar.textContent = "Guardar";
      btnGuardar.className = "btn btn-success btn-sm ms-2";
      btnGuardar.onclick = function () {
        fila
          .querySelectorAll(".is-invalid")
          .forEach((el) => el.classList.remove("is-invalid"));
        let formData = new FormData();
        // Recorremos los inputs de la fila para recoger sus valores
        fila.querySelectorAll("input, select").forEach((input) => {
          if (input.name && input.value !== "****" && input.name !== "id_usu")
            formData.append(input.name, input.value);
        });
        // Añadimos la acción de insertar al formData
        formData.append("accion", "insertar");
        // Enviamos los datos al servidor
        fetch("/php/accionesUsuario.php", {
          method: "POST",
          body: formData,
        })
          .then((resp) => resp.text())
          .then((html) => {
            let contenedor = document.getElementById("contenidoExtra");
            if (contenedor) {
              contenedor.innerHTML = html;
            } else {
              alert("No se encontró el contenedor contenidoExtra");
            }
            activarCheckAll();
          });
      };
      fila.appendChild(btnGuardar);
    }
  }
  // MODIFICAR (múltiple)
  else if (accion === "modificar") {
    let form = btn.closest("form") || document.getElementById("formGestionHer");
    if (!form) return;

    let formData = new FormData(form);
    formData.append("accion", "modificar");
    fetch("/php/accionesUsuario.php", {
      method: "POST",
      body: formData,
    })
      .then((resp) => resp.text())
      .then((html) => {
        let contenedor =
          document.querySelector(".container.mt-4") ||
          document.getElementById("contenidoExtra");
        if (contenedor) contenedor.innerHTML = html;

        // Hacer scroll al principio de la página
        setTimeout(() => {
          window.scrollTo({ top: 0, behavior: "smooth" });
        }, 50);
        // Marcar campos inválidos si hay error, tanto si el nombre del input es "dni" o "email", 
        // como si empieza por "dni" o "email"
        if (html.includes("DNI")) {
          document
            .querySelectorAll("input[name='dni'], input[name^='dni']")
            .forEach((el) => el.classList.add("is-invalid"));
        }
        if (html.includes("Email")) {
          document
            .querySelectorAll("input[name='email'], input[name^='email']")
            .forEach((el) => el.classList.add("is-invalid"));
        }
        if (html.includes("ya registrado")) {
          // Marca ambos por si acaso
          document
            .querySelectorAll(
              "input[name='dni'], input[name^='dni'], input[name='email'], input[name^='email']"
            )
            .forEach((el) => el.classList.add("is-invalid"));
        }
      })
      .catch((err) => alert("Error al modificar: " + err));
  }
  // BORRAR (múltiple)
  else if (accion === "borrar") {
    // Busca el formulario más cercano al botón o el de gestión múltiple
    let form = document.getElementById("formGestionHer") || btn.closest("form");
    if (!form) return;
    let formData = new FormData(form);
    formData.append("accion", "borrar");
    fetch("/php/accionesUsuario.php", {
      method: "POST",
      body: formData,
    })
      .then((resp) => resp.text())
      .then((html) => {
        // Si el formulario NO es el de gestión múltiple, es perfil individual y al borrarlo redirige a index.html
        if (!form.id || form.id !== "formGestionHer") {
          document.getElementById("miPerfil").innerHTML = html;
          setTimeout(() => {
            window.location.href = "/index.html";
          }, 5000);
        } else {
          // Si es gestión de hermanos, solo actualiza la tabla
          document.getElementById("contenidoExtra").innerHTML = html;
          activarCheckAll();
        }
      });
  }

  // FILTRAR
  else if (accion === "filtrar") {
    // Muestra un pequeño formulario de filtro
    document.getElementById("contenidoExtra").innerHTML = `
    <form id="formFiltrar" class="mb-3">
        <div class="row g-2">
            <div class="col">
                <input type="text" name="nombre" class="form-control" placeholder="Nombre">
            </div>
            <div class="col">
                <select name="nomb_tipo" id="selectTipoFiltro" class="form-control">
                    <option value="">Todos los tipos</option>
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </form>
`;

    fetch("/php/obtenerTipos.php")
      .then((res) => res.json())
      .then((tipos) => {
        let select = document.getElementById("selectTipoFiltro");
        tipos.forEach((tipo) => {
          let option = document.createElement("option");
          option.value = tipo.Nomb_tipo;
          option.textContent = tipo.Nomb_tipo;
          select.appendChild(option);
        });
      });
    document.getElementById("formFiltrar").onsubmit = function (ev) {
      ev.preventDefault();
      let formData = new FormData(this);
      formData.append("accion", "filtrar");
      fetch("/php/accionesUsuario.php", {
        method: "POST",
        body: formData,
      })
        .then((resp) => resp.text())
        .then((html) => {
          document.getElementById("contenidoExtra").innerHTML = html;
          activarCheckAll();
        });
    };
  }
});
