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


document.addEventListener('DOMContentLoaded', function() {
    const perfilBtn = document.getElementById('miPerfilBtn');
    const divMiPerfil = document.getElementById('miPerfil');
    const avisosDiv = document.getElementById('avisos');
    const avisosTitulo = document.getElementById('avisosTitulo');
    if (!perfilBtn || !divMiPerfil) return;

    perfilBtn.addEventListener('click', function(e) {
        e.preventDefault();
        divMiPerfil.style.display = 'block';
        if (avisosDiv) avisosDiv.style.display = 'none'; 
        if (avisosTitulo) avisosTitulo.style.display = 'none';    
           
    
        //Limpiamos el contenido previo
        divMiPerfil.innerHTML = '<h2>Mi Perfil</h2>';

        // Tipos de usuario normales
        const tiposNormales = ['mantilla', 'nazareno', 'costalero', 'otros'];

        if (tiposNormales.includes(tipoUsuario.toLowerCase())) {
            // Redirige a mostrarDatos.php
            fetch('/php/mostrarDatos.php')
                .then(res => res.text())
                .then(html => {
                    divMiPerfil.innerHTML += html;
                })
                .catch(() => {
                    divMiPerfil.innerHTML += '<div class="alert alert-danger">No se pudo cargar tus datos.</div>';
                });
        } else if (tipoUsuario.toLowerCase() === 'junta' || tipoUsuario.toLowerCase() === 'administrador') {
            divMiPerfil.innerHTML += `
                <button class="btn btn-primary m-2" id="btnMisDatos">Mis Datos</button>
                <button class="btn btn-secondary m-2" id="btnDatosHermanos">Datos Hermanos</button>
                <div id="contenidoExtra"></div>
            `;

            // Botón Mis Datos
            document.getElementById('btnMisDatos').onclick = function() {
                // Limpiamos el contenido previo
                divMiPerfil.innerHTML = '<h2>Mis Datos</h2>';
                fetch('/php/mostrarDatos.php')
                .then(res => res.text())
                .then(html => {
                    divMiPerfil.innerHTML += html;
                })
                .catch(() => {
                    divMiPerfil.innerHTML += '<div class="alert alert-danger">No se pudo cargar tus datos.</div>';
                });
            };
            //Esta función activa el checkbox "checkAll" para seleccionar/desmarcar todos los checkboxes de la lista de hermanos
            function activarCheckAll() {
            const checkAll = document.getElementById('checkAll');
            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    const checks = document.querySelectorAll("input[name='elegido[]']");
                    checks.forEach(chk => chk.checked = this.checked);
                });
            }
        }
           // Botón Datos Hermanos
            document.getElementById('btnDatosHermanos').onclick = function() {
                // Limpiamos el contenido previo
                /* divMiPerfil.innerHTML = '<h2>¡¡¡¡Datos Hermanos</h2>'; */
                fetch('/php/mostrarHermanos.php')
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('contenidoExtra').innerHTML = html;
                        activarCheckAll(); // Activamos el checkbox "checkAll"
                    })
                    .catch(() => {
                        document.getElementById('contenidoExtra').innerHTML = '<div class="alert alert-danger">No se pudo cargar la tabla de hermanos.</div>';
                    });
            };
        }
    });
}); 

// DELEGACIÓN DE EVENTOS PARA BOTONES .btn-operacion
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-operacion');
    if (!btn) return;

    const accion = btn.getAttribute('data-accion');
    const idOperacion = btn.getAttribute('data-id');
    const form = btn.closest('form') || document.querySelector('form');

    if (accion && accion.toLowerCase() === 'modificar') {
        let formData = new FormData(form);
        formData.append('accion', 'modificar');
        fetch('/php/accionesUsuario.php', {
            method: 'POST',
            body: formData
        })
        .then(resp => resp.text())
        .then(html => {
            const contenedor = document.querySelector('.container.mt-4') || document.getElementById('contenidoExtra');
    if (contenedor) contenedor.innerHTML = html;

    // Hacer scroll al principio de la página
       setTimeout(() => {
    window.scrollTo({ top: 0, behavior: "smooth" });
}, 50);
    // Marcar campos inválidos si hay error
    if (html.includes("DNI")) {
        document.querySelectorAll("input[name='dni'], input[name^='dni']").forEach(el => el.classList.add("is-invalid"));
    }
    if (html.includes("Email")) {
        document.querySelectorAll("input[name='email'], input[name^='email']").forEach(el => el.classList.add("is-invalid"));
    }
    if (html.includes("ya registrado")) {
        // Marca ambos por si acaso
        document.querySelectorAll("input[name='dni'], input[name^='dni'], input[name='email'], input[name^='email']").forEach(el => el.classList.add("is-invalid"));
    }


        })
        .catch(err => alert('Error al modificar: ' + err));
        }/* else if (accion && accion.toLowerCase() === 'borrar') {
        if (!confirm('¿Seguro que quieres darte de baja?')) return;
        let formData = new FormData(form);
        formData.append('accion', 'borrar');
        fetch('accionesUsuario.php', {
            method: 'POST',
            body: formData
        })
        .then(resp => resp.text())
        .then(html => {
            const contenedor = document.querySelector('.container.mt-4');
            if (contenedor) contenedor.innerHTML = html;
            setTimeout(() => {
                window.location.href = '/GitHub/proyectoFinCiclo/index.html';
            }, 5000);
        })
        .catch(err => alert('Error al borrar: ' + err));
    }  */
});

function cargarTiposEnSelect(select) {
    fetch('/php/obtenerTipos.php')
        .then(res => res.json())
        .then(tipos => {
            select.innerHTML = '';
            tipos.forEach(tipo => {
                let option = document.createElement('option');
                option.value = tipo.Nomb_tipo;
                option.textContent = tipo.Nomb_tipo;
                select.appendChild(option);
            });
        });
}

// Delegación de eventos para los botones de operaciones
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-operacion');
    if (!btn) return;
    const accion = btn.getAttribute('data-accion').toLowerCase();

    // INSERTAR
    if (accion === 'insertar') {
        const tabla = document.querySelector('#formGestionHer table tbody');
        if (tabla) {
            let columnas = tabla.parentElement.querySelectorAll('thead th');
            let fila = document.createElement('tr');
            fila.innerHTML = `<td></td>` +
                Array.from(columnas).slice(1).map((th) => {
                    let name = th.textContent.trim();
                    if (name === 'password') {
                        return `<td><input type="password" class="form-control form-control-sm" name="password"></td>`;
                    } else if (name === 'Nomb_tipo') {
                        return `<td><select class="form-select form-select-sm" name="Nomb_tipo"></select></td>`;
                    } else if (name === 'id_usu') {
                        return `<td></td>`;
                    } else {
                        return `<td><input type="text" class="form-control form-control-sm" name="${name}"></td>`;
                    }
                }).join('');
            tabla.prepend(fila);
            // Cargar tipos en el select
            fila.querySelectorAll('select[name="Nomb_tipo"]').forEach(select => {
                cargarTiposEnSelect(select);
            });
            // Botón para guardar la inserción
            let btnGuardar = document.createElement('button');
            btnGuardar.type = 'button';
            btnGuardar.textContent = 'Guardar';
            btnGuardar.className = 'btn btn-success btn-sm ms-2';
            btnGuardar.onclick = function () {
                let formData = new FormData();
                fila.querySelectorAll('input, select').forEach(input => {
                    if (input.name && input.value !== "****" && input.name !== "id_usu") formData.append(input.name, input.value);
                });
                formData.append('accion', 'insertar');
                fetch('/php/accionesUsuario.php', {
                    method: 'POST',
                    body: formData
                })
                .then(resp => resp.text())
                .then(html => {
                    const contenedor = document.getElementById('contenidoExtra');
                    if (contenedor) {
                         contenedor.innerHTML = html;
                    } else {
                        alert('No se encontró el contenedor contenidoExtra');
                    }
                    activarCheckAll();
                });
            };
            fila.appendChild(btnGuardar);
        }
    }
    // MODIFICAR (múltiple)
    else if (accion === 'modificar') {
        let form = document.getElementById('formGestionHer');
        
        let formData = new FormData(form);
        formData.append('accion', 'modificar');
        fetch('/php/accionesUsuario.php', {
            method: 'POST',
            body: formData
        })
        .then(resp => resp.text())
        .then(html => {
            document.getElementById('contenidoExtra')./* innerHTML = html; */
            activarCheckAll();
        });
    }

    // BORRAR (múltiple)
    else if (accion === 'borrar') {
    // Busca el formulario más cercano al botón o el de gestión múltiple
    let form = document.getElementById('formGestionHer') || btn.closest('form');
    let formData = new FormData(form);
    formData.append('accion', 'borrar');
    fetch('/php/accionesUsuario.php', {
        method: 'POST',
        body: formData
    })
    .then(resp => resp.text())
    .then(html => {
        // Si el formulario NO es el de gestión múltiple, es perfil individual
        if (!form.id || form.id !== 'formGestionHer') {
           document.getElementById('miPerfil').innerHTML = html;
            console.log('Redirigiendo a /index.html en 5 segundos');
            setTimeout(() => {
                window.location.href = '/index.html';
            }, 5000);
        } else {
            // Si es gestión de hermanos, solo actualiza la tabla
            document.getElementById('contenidoExtra').innerHTML = html;
            activarCheckAll();
        }
    });
}

    // FILTRAR
    else if (accion === 'filtrar') {
        // Muestra un pequeño formulario de filtro
        document.getElementById('contenidoExtra').innerHTML = `
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

fetch('/php/obtenerTipos.php')
    .then(res => res.json())
    .then(tipos => {
        const select = document.getElementById('selectTipoFiltro');
        tipos.forEach(tipo => {
            let option = document.createElement('option');
            option.value = tipo.Nomb_tipo;
            option.textContent = tipo.Nomb_tipo;
            select.appendChild(option);
        });
    });
        document.getElementById('formFiltrar').onsubmit = function(ev) {
            ev.preventDefault();
            let formData = new FormData(this);
            formData.append('accion', 'filtrar');
            fetch('/php/accionesUsuario.php', {
                method: 'POST',
                body: formData
            })
            .then(resp => resp.text())
            .then(html => {
                document.getElementById('contenidoExtra').innerHTML = html;
                activarCheckAll();
            });
        };
    }
});


