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
            fetch('mostrarDatos.php')
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
                fetch('mostrarDatosPersonales.php')
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('contenidoExtra').innerHTML = html;
                    })
                    .catch(() => {
                        document.getElementById('contenidoExtra').innerHTML = '<div class="alert alert-danger">No se pudo cargar tus datos personales.</div>';
                    });
            };
           // Botón Datos Hermanos
            document.getElementById('btnDatosHermanos').onclick = function() {
                fetch('tablaHermanos.php')
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('contenidoExtra').innerHTML = html;
                    })
                    .catch(() => {
                        document.getElementById('contenidoExtra').innerHTML = '<div class="alert alert-danger">No se pudo cargar la tabla de hermanos.</div>';
                    });
            };
        }
    });
}); 