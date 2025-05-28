document.addEventListener('DOMContentLoaded',function(){
   console.log("controlAcceso.js cargado");
    let form = document.getElementById('loginForm');
    console.log('form encontrado', form);
    let errorDiv = document.getElementById("error");
    let url = '/proyectoFinCiclo/php/login.php';

    form.addEventListener('submit', async e =>{
        //Para evitar recargas
        e.preventDefault();
        console.log('enviando login a ', url);

          //Eliminar mensajes previos
        errorDiv.style.display = 'none';
        errorDiv.innerText='';

        let email = form.email.value.trim();
        let password = form.password.value;
    
      

        try{
            let response = await fetch(url,{
                method: "POST",
                headers: {'Accept': 'application/json'},
                body: new URLSearchParams({email,password})
            });
            console.log('fetch enviado esperando');

            let result = await response.json();

            console.log('respuesta', result);
            if(result.success){
                window.location.href='./usuarios.php';
            }else{
                errorDiv.innerText = result.message;
                errorDiv.style.display = 'block';
            }
        } catch (e){
            console.error('Error en la solicitud:', e);
            errorDiv.innerText = 'Error de conexion. Intentalo mas tarde.';
            errorDiv.style.display = 'block';
        }
        
    });

});
