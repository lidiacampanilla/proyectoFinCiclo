<?php
   session_start();
   /*El siguiente trozo de codigo en .php validara el email y el password, guardando en sesion el id_usu,
       el tipo de usuario y su nombre, para usarlos en usuarios.php, que sera accesible si el email y el password
       son correctos */
        //Iniciamos la sesion o recupera una existente, para poder utilizar $_SESSION y compartir datos entre paginas
        
        //Utilizamos require_once, para cargar el archivo solo una vez aunque se llame varias veces, y si el archivo
        //no existe, el script se detiene.
        require_once("./mysqlConexion.php");
        require_once("./bibliotecaFunciones.php");
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
          header ('Content-Type: application/json; charset=utf-8');

          $baseDatos="cofradia";
          $pdo=conexion($baseDatos);
        
        //Ahora recogemos los datos del formulario
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $usuario = validarAcceso($email,$password, $pdo);

        
        if($usuario){
          //Guaradmos los datos en sesion
          $_SESSION['id_usu']=$usuario['id_usu'];
          $_SESSION['tipo']=$usuario['tipo'];
          $_SESSION['nombre']=$usuario['nombre'];
          /* header('Location:./usuarios.php');
          exit; */
          echo json_encode(['success'=>true]);
        }else{
          echo json_encode([
            'success'=>false,
            'message'=>'Email o constraseña incorrectos.'
          ]);
          /* echo "email o contraseña incorrectos."; */
        }
        exit;
      }
      ?>