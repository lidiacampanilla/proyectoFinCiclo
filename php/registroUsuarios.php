<?php
      include("./mysqlConexion.php");
      include("./bibliotecaFunciones.php");
      
      
      $baseDatos="COFRADIA";
      $pdo=conexion($baseDatos);
      
      //Si existen los campos del formulario los extraemos para poder insertarlos en la tabla de la base de datos. 

      if (isset ($_POST['dni']) && ($_POST['email']) && ($_POST['nombre']) && ($_POST['apellidos'])
      &&($_POST['password']) && ($_POST['direccion']) && ($_POST['poblacion']) && ($_POST['cp']) && ($_POST['provincia'])
      &&($_POST['cuenta'])&&($_POST['tipoHermano'])) {

        //Ya que vamos a utilizar varias tablas de la base de datos para insertar datos, iniciamos transaccion para evitar registros a medias
        $pdo->beginTransaction(); 

        try{
          $dni = trim($_POST['dni']);
          $email = trim($_POST['email']);
          $nombre = trim($_POST['nombre']);
          $apellidos = trim($_POST['apellidos']);
          $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
          $direccion = trim($_POST['direccion']);
          $poblacion = trim($_POST['poblacion']);
          $cp = trim($_POST['cp']);
          $provincia = trim($_POST['provincia']);
          $cuenta = strtoupper(trim($_POST['cuenta']));
          $tipo = trim($_POST['tipoHermano']);
         
        //Vamos a validar el DNI
        if (!validarDNI($dni)) {
          //Lanzamos una excepcion
          throw new Exception("El DNI no es valido");
        }

        //Validamos el IBAN
        if (!validarIBAN($cuenta)){
            //Lanzamos una excepcion
          throw new Exception("El número de cuenta no es valido");
        }

        //Vamos a comprobar si el DNI o el email ya existen, ya que son campos unicos
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM USUARIO WHERE DNI = ?");
        $stmt -> execute([$dni]);
        if ($stmt->fetchColumn()>0) throw new Exception("DNI ya registrado");

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM USUARIO WHERE email = ?");
        $stmt -> execute([$email]);
        if ($stmt->fetchColumn()>0) throw new Exception("email ya registrado");

        //Para insertar en la tabla Usuario hacemos un array con los datos recogidos del formulario
        $datosUsuario = ['dni'=>$dni,'email'=>$email,'Nomb_usu'=>$nombre,'Ape_usu'=>$apellidos,'password'=>$password,'direccion'=>$direccion,
                    'poblacion'=>$poblacion, 'cod_postal'=>$cp, 'provincia'=>$provincia, 'cta_bancaria'=>$cuenta];

        $baseDatos='COFRADIA';
        $tabla='USUARIO';
        insertar($pdo,$baseDatos,$tabla,$datosUsuario);
        
        //Extraemos el id_usu que se ha creado automaticamente al insertar el usuario
        $id_usu = $pdo->lastInsertId();

        //Apartir del tipoHermano extraemos el id_tipo
        $stmt = $pdo->prepare("SELECT id_tipo FROM TIPO WHERE Nomb_tipo = ?");
        $stmt -> execute([$tipo]);
        $id_tipo = $stmt->fetchColumn();
        //Validamos el tipo de Hermano
        if (!$id_tipo){
            //Lanzamos una excepcion
          throw new Exception("El tipo de Hermano no valido");
        }

        //Insertamos en la tabla PERTENECEN
            $datosPertenecen = [
              'id_usu'=>$id_usu,
              'id_tipo'=> $id_tipo
            ];
            $tablaPer = 'PERTENECEN';
           
            insertar($pdo,$baseDatos,$tablaPer,$datosPertenecen);
         
        //Ahora confirmamos la transaccion
        $pdo->commit(); 
        echo "ok";
        exit;
        }catch (Exception $e){
          //Si hay algun error revertimos la transaccion
          $pdo->rollBack(); 
          echo "Error: ".$e->getMessage();
        }
      }
      ?>