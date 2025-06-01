<?php
//Hacemos una función para crear la base de datos, le pasamos por parametro la conexión y la consulta en la que creamos la base de datos y las tablas

function crearBaseDatos($pdo, $sqlBaseDatos)
{
    $pdo->query($sqlBaseDatos);
}

//Con la siguiente función comprobamos si existe la base de datos y si no es así la creará llamando a la funcion crearBaseDatos, pasamos por parametro la conexión,la base de Datos y la consulta en la que creamos la base de datos y las tablas
function existeBaseDatos($pdo, $baseDatos, $sqlBaseDatos)
{
    $resultado = $pdo->query('SHOW DATABASES');

    /*$registros = $resultado->fetchAll();
   for ($i = 0; $i < count($registros); $i++) {
        if ($registros[$i] == $baseDatos) {
            return true;
        } else {
            crearBaseDatos($pdo, $sqlBaseDatos);
        }
    }*/
    $registros = $resultado ->fetchAll(PDO::FETCH_COLUMN);
    if (in_array ($baseDatos, $registros)){
        return true;
    }else{
        crearBaseDatos($pdo, $sqlBaseDatos);
        return false;
    }
}

//Con la siguiente función vamos a validar el DNI
function validarDNI($dni){
    $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
    //Transformamos todo a mayuscula
    $dni = strtoupper($dni);
    //Esta funcion "preg_match" devuleve 1 si coincide con la expresion regular y 0 sino coincide
    if (!preg_match("/^[0-9]{8}[A-Z]$/",$dni)) return false;
    $numero = substr ($dni, 0, 8);
    $letra = substr($dni, -1);
    return $letra == $letras[$numero % 23];
}

//Con la siguiente función validamos la cta_bancaria(IBAN) que deberá tener dos letras y 22 números
function validarIBAN ($iban){
    return preg_match("/^[A-Z]{2}[0-9]{22}$/", strtoupper($iban));
}

//Con la siguiente función insertaremos datos en cualquier tabla
function insertar($pdo, $baseDatos, $tabla, $datos)
{
    //Nos aseguramos de estar usando la base de datos
    $pdo->query("USE $baseDatos");

    //Del array datos obtenemos las columnas y los valores
    $columnas = array_keys($datos);
    $valores = array_values($datos);

    //Para hacer una funcion que sirva para cualquier tabla vamos a preparar las distintas partes del INSERT.Primero vamos a preparar los valores, que como no los sabemos, vamos a utilizar ?. Con implode conseguimos unir los valores del array en una cadena separada por ','. Con 'array_fill' obtenemos un array desde la posicion 0, con tantos elementos como columnas tengamos ($columnas), y los elementos del array son las '?'.
    $parametros = implode(',', array_fill(0, count($columnas), '?'));

    //Definimos la consulta. Con "implode" obtenemos los nombres de las columnas en una cadena separada por ','
    $consulta = "INSERT INTO $tabla (" . implode(',', $columnas) . ") VALUES ($parametros)";

    //Preparamos la consulta para evitar inyecciones de codigo, utilizamos try..catch para manejar los errores, ya que en la conexion hemos utilazado ERRMODE_EXCEPTION
    try {
        //Preparamos nuestra consulta
        $stmt = $pdo->prepare($consulta);
        //Ejecutamos, con esto conseguimos sustituir los ? por los valores obtenidos con array_values.
        $stmt->execute($valores);
        /* echo ("Se ha insertado el registro correctamente"); */
    } catch (PDOException $e) {
        print "<p>Código de Error:" . $e->getCode() . "<br>El Mensaje es: " . $e->getMessage() . "</p>";
        exit;
    }
}

//Creamos una funcion para validar el acceso de usuarios
function validarAcceso($email,$password,$pdo){
    //Lanzamos la consulta para iniciar la verificacion del email y del password
        $stmt = $pdo -> prepare ("SELECT U.id_usu, U.password, U.Nomb_usu, T.Nomb_tipo
                                  FROM USUARIO U
                                  JOIN PERTENECEN P ON U.id_usu = P.id_usu
                                  JOIN TIPO T ON P.id_tipo = T.id_tipo
                                  WHERE U.email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if($usuario && password_verify($password, $usuario['password'])){
          return[
            'id_usu'=>$usuario['id_usu'],
            'tipo'=>$usuario['Nomb_tipo'],
            'nombre'=>$usuario['Nomb_usu']
          ];
        }else{
          return false;
        }
      } 

//Creamos la función para mostrar los usuarios, pasamos por parametro, la conexión, la base de datos y la tabla a mostrar.
function mostrarUsuarios($pdo, $baseDatos, $tabla)
{
    //Nos aseguramos de estar usando la base de datos
    $pdo->query("USE $baseDatos");

    //Creamos la consulta
    $consulta = "SELECT * FROM $tabla";

    //Ejecutamos la consulta con query
    $resultado = $pdo->query($consulta);
    //Lanzamos errores si no se realiza la consulta, si no hay ningun registro(elseif) nos indicara que la tabla esta vacia, sino mostrara la tabla
    if (!$resultado) {
        echo "<p>Error en la consulta SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
        return;
    } //Utilizamos PDO::FETCH_ASSOC , para obtener los datos correctamente sin duplicidad
    elseif (!count($registros = $resultado->fetchAll(PDO::FETCH_ASSOC))) {
        echo "<p class='p-3 mb-2 bg-info text-dark'>No hay ningún registro en la tabla</p>\n";
    } else {
        echo '<div class="album py-5 bg-body-tertiary">';
        echo '<div class="container">';
        echo '<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">';

        //Ahora dividimos cada registro en cada uno de los campos para ir mostrando las imagenes
        foreach ($registros as $registro) {
            $Cod_receta = htmlspecialchars($registro['Cod_receta']);
            $Nomb_receta = htmlspecialchars($registro['Nomb_receta']);
            $Descrip_receta = htmlspecialchars($registro['Descrip_receta']);
            $Foto = htmlspecialchars($registro['Foto']);
            $Doc_pdf = $registro['Doc_pdf'];
            $Tiempo = htmlspecialchars($registro['Tiempo']);


            //Ahora vamos a generar el cuadro de cada receta
            echo '<div class="col">';
            echo '<div class="card shadow-sm">';
            echo '<img src="' . $Foto . '" class="bd-placeholder-img card-img-top" width="100%" height="225" alt="' . $Nomb_receta . '">';
            echo '<div class="card-body">';
            echo ' <h5 class="card-tittle">' . $Nomb_receta . '</h5>';
            echo '<p class="card-text">' . $Descrip_receta . '</p>';
            echo '<div class="d-flex justify-content-between align-items-center">';
            echo '<div class="btn-group">';
            echo '<a href="' . $Doc_pdf . '" class="btn btn-sm btn-outline-secondary" target="_blank">Ver Receta</a>';
            echo "</div>";
            echo '<p class="card-text mr-0">' . $Tiempo . ' min</p>';

            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
}

//La siguiente función la utilizamos para obtener las categorias de la tabla categorias de una forma dinámica
function obtenerCategorias($pdo, $baseDatos)
{
    //Nos aseguramos de estar usando la base de datos
    $pdo->query("USE $baseDatos");

    //Creamos la consulta
    $consulta = "SELECT Cod_categoria,Nomb_categoria FROM CATEGORIAS";
    $resultado = $pdo->query($consulta);

    if ($resultado) {
        while ($categoria = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $Cod_categoria = htmlspecialchars($categoria['Cod_categoria']);
            $Nomb_categoria = htmlspecialchars($categoria['Nomb_categoria']);

            //Construimos nuestro html
            echo "<input type='checkbox' id='categoria$Cod_categoria' name='tipo[]' value='$Cod_categoria'>";
            echo "<label for='categoria$Cod_categoria'>$Nomb_categoria</label><br>";
        }
    } else {
        echo "<p>No hay categorias</p>";
    }
}
//Hacemos la funcion insertar que recibira por parametro la conexion, la base de datos,la tabla y los datos recogidos de la funcion "insertar"


//Con la siguiente funcion hacemos una tabla que muestre todas las recetas, trabajamos con las tablas RECETAS y CATEGORIAS, para mostrar tambien las categorias de las recetas
function tablaGestionRe($pdo, $baseDatos, $tabla1, $tabla2, $tabla3)
{
    //Nos aseguramos de estar utilizando nuestra base de datos
    $pdo->query("USE $baseDatos");

    //Ejecutamos la consulta con query
    $consulta = "SELECT R.Cod_receta,R.Nomb_receta,R.Descrip_receta,R.Foto,R.Tiempo,R.Doc_pdf, GROUP_CONCAT(C.Nomb_categoria SEPARATOR ',') AS Categorias FROM $tabla1 R
      LEFT JOIN $tabla2 P ON R.Cod_receta = P.Cod_Receta
      LEFT JOIN $tabla3 C ON P.Cod_categoria = C.Cod_Categoria
      GROUP BY R.Cod_receta";
    $resultado = $pdo->query($consulta);
    //Lanzamos errores si no se realiza la consulta, si no hay ningun registro(elseif) nos indicara que la tabla esta vacia, sino mostrara la tabla
    if (!$resultado) {
        echo "<p>Error en la consulta SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
    } //Utilizamos PDO::FETCH_ASSOC , para obtener los datos correctamente sin duplicidad
    elseif (!count($registros = $resultado->fetchAll(PDO::FETCH_ASSOC))) {
        echo "<p class='p-3 mb-2 bg-info text-dark'>No hay ningún registro en la tabla</p>\n";
    } else {
        echo "<div class='container mt-4'>";
        echo "<table class='table table-bordered table-sm'>";
        echo "<thead >";
        echo "<tr>";

        //Con las siguientes sentencias cogeria los nombres de las columnas para hacer la cabecera de la tabla de manera NO MANUAL

        foreach (array_keys($registros[0]) as $columna) {
            echo "<th class='bg-success text-white'>" . htmlspecialchars($columna) . "</th>";
        }
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($registros as $registro) {
            echo "<tr>";
            foreach ($registro as $columna => $valor) {
                if($columna == 'Foto' || $columna == 'Doc_pdf'){
                    $valor = basename($valor);
                }
                echo "<td>", $valor, "</td>";
            }
            echo "</tr>";
        }
        echo "<tr>";
        echo "<td colspan='" . count(array_keys($registros[0])) . "'>";
        echo "<p>Elige una opcion</p>";
        echo "<div>";
        echo "<button id='insertar' class='btn btn-primary me-2'>Insertar</button>";
        echo "<button id='modificar' class='btn btn-primary me-2'>Modificar</button>";
        echo "<button id='borrar' class='btn btn-primary'>Borrar</button>";
        echo "</div>";
        echo "</td>";
        echo "</tr>";
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }
}

//Con la siguiente funcion hacemos una tabla que muestre todas las categorias, trabajamos con las tablas RECETAS y PERTENECEN, para poder controlar si se puede borrar o no una categoria
function tablaDatos($pdo, $baseDatos, $idUsu)
{
    //Nos aseguramos de estar utilizando nuestra base de datos
    $pdo->query("USE $baseDatos");

    //Ejecutamos la consulta con query
    $consulta = "SELECT U.*, T.Nomb_tipo, T.id_tipo FROM USUARIO U
    JOIN PERTENECEN P ON U.id_usu = P.id_usu
    JOIN TIPO T ON P.id_tipo = T.id_tipo
    WHERE U.id_usu = ?";
    $stmt = $pdo->prepare($consulta);
    $stmt->execute([$idUsu]);
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //Lanzamos errores si no se realiza la consulta, si no hay ningun registro(elseif) nos indicara que la tabla esta vacia, sino mostrara la tabla
    if (!$resultado) {
        echo "<p id=\"espaciado\">No hay ningún registro en la tabla</p>\n";
    } //Utilizamos PDO::FETCH_ASSOC , para obtener los datos correctamente sin duplicidad
    else {
        echo "<div class='container mt-4'>";
        echo "<form method='post' action=''>";
        $usuario = $resultado[0]; // Asumimos que solo hay un usuario para este id_usu
        // AÑADE ESTE CAMPO OCULTO:
echo "<input type='hidden' name='id_usu' value='" . htmlspecialchars($usuario['id_usu']) . "'>";

// Obtener todos los tipos para el select
        $stmtTipos = $pdo->query("SELECT id_tipo, Nomb_tipo FROM TIPO");
        $tipos = $stmtTipos->fetchAll(PDO::FETCH_ASSOC);
        foreach ($usuario as $columna => $valor) {
            // No permitir modificar la clave primaria ni el tipo de usuario, el campo id_tipo no lo vamos a mostrar.
            if ($columna == 'id_usu' || $columna == 'id_tipo') {
                continue;
            }
            $readonly = ($columna == 'id_usu' ) ? 'readonly' : '';
            $bg = ($readonly) ? "background-color:#e9ecef;" : ""; 
            echo "<div class='mb-3'>";
            echo "<label for='$columna' class='form-label'>" . htmlspecialchars($columna) . "</label>";
             if ($columna == 'Nomb_tipo') {
                // Select editable para tipo de usuario
                echo "<select class='form-control' id='Nomb_tipo' name='Nomb_tipo'>";
                foreach ($tipos as $tipo) {
                    if (
            strtolower($tipo['Nomb_tipo']) === 'junta' ||
            strtolower($tipo['Nomb_tipo']) === 'administrador'
        ) {
            continue; // Saltar estos tipos
        }
                    $selected = ($tipo['Nomb_tipo'] == $valor) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($tipo['Nomb_tipo']) . "' $selected>" . htmlspecialchars($tipo['Nomb_tipo']) . "</option>";
                }
                echo "</select>";
            }elseif($columna == 'password') {
                // Para la contraseña, mostramos un campo de tipo password
                echo "<input type='password' class='form-control' id='$columna' name='$columna' value='" . htmlspecialchars($valor) . "' style='color:gray;$bg' $readonly>";
            } elseif ($columna == 'email') {
                // Para el email, mostramos un campo de tipo email
                echo "<input type='email' class='form-control' id='$columna' name='$columna' value='" . htmlspecialchars($valor) . "' style='color:gray;$bg' $readonly>";
            } else {
                // Para los demás campos, mostramos un campo de texto
                echo "<input type='text' class='form-control' id='$columna' name='$columna' value='" . htmlspecialchars($valor) . "' style='color:gray;$bg' $readonly>";
            }
            
            echo "</div>";
        }
        $id_tipo = $usuario['id_tipo'];
        mostrarBotonesOperaciones($pdo, $baseDatos, $id_tipo);
        echo "</form>";
        echo "</div>";
    }
}
function mostrarBotonesOperaciones($pdo, $baseDatos, $id_tipo)
{
    $pdo->query("USE $baseDatos");

    $sqlOperaciones = "SELECT O.id_ope, O.Nomb_ope, O.Descrip_ope
        FROM OPERACIONES O
        JOIN REALIZAN R ON O.id_ope = R.id_ope
        WHERE R.id_tipo = ?";
    $stmtOpe = $pdo->prepare($sqlOperaciones);
    $stmtOpe->execute([$id_tipo]);
    $operaciones = $stmtOpe->fetchAll(PDO::FETCH_ASSOC);

    if ($operaciones) {
        echo "<div class='mb-3'>";
        echo "<h5>Operaciones disponibles:</h5>";
        foreach ($operaciones as $op) {
            echo "<button type='button' "
                . "class='btn btn-secondary btn-operacion me-2' "
                . "data-accion='" . htmlspecialchars($op['Nomb_ope']) . "' "
                . "data-id='" . htmlspecialchars($op['id_ope']) . "' "
                . "title='" . htmlspecialchars($op['Descrip_ope']) . "'>";
            echo htmlspecialchars($op['Nomb_ope']);
            echo "</button>";
        }
        echo "</div>";
    } else {
        echo "<div class='alert alert-info'>No tienes operaciones asignadas.</div>";
    }
}
function obtenerNumero($pdo, $baseDatos)
{
    //Nos aseguramos de estar usando la base de datos
    $pdo->query("USE $baseDatos");

    //Creamos la consulta
    $consulta = "SELECT Cod_receta FROM RECETAS";
    $resultado = $pdo->query($consulta);

    if ($resultado) {
        while ($numero = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $Cod_receta = htmlspecialchars($numero['Cod_receta']);
            

            //Construimos nuestro html
            echo "<option value='$Cod_receta'>$Cod_receta</option>";
           
        }
    } else {
        echo "<p>No hay recetas</p>";
    }
}

//La siguientes función modificará tan solo los campos que indica el usuaria en la tabla modificar. Como parametros le pasamos la conexión, la base de datos, la tabla y los datos obtenidos por post en el formulario de la tabla modificar.
function modificar($pdo, $baseDatos, $tabla, $datos)
{
    //Nos aseguramos de estar usando la base de datos
    $pdo->query("USE $baseDatos");

   

    //Obtenemos la/las clave Primaria de la tabla en cuestion, para eso tenemos el campo oculto 'opcion'
    $consultaClavePrimaria = $pdo->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='{$baseDatos}' AND TABLE_NAME='{$tabla}' AND COLUMN_KEY='PRI'");

    $clavesPrimarias = $consultaClavePrimaria->fetchAll(PDO::FETCH_COLUMN);

    //Si no hay clave primaria nos salimos
    if (!$clavesPrimarias) {
        echo "<p>Error: No se encontro la clave primaria</p>";
        return;
    }

    //Para poder construir nuestra consulta, necesitamos saber lo que vamos a modificar, los valores y las condiciones para que se produzcan las modificaciones, es muy importante tener encuenta si hay varias claves primarias
    //Primero contamos el numero de filas que tenemos, esto lo conseguimos, contando cualquiera de los campos del array $datos, en este caso cogemos el primero.
    $primerCampo = array_values($datos)[0];
    $numFilas = count($primerCampo);

    //Con el siguiente bucle recorremos toda la tabla para ver que modificaciones se han producido, y lo vamos guardando en varios arrays.
    /* $modificaciones: Se guardan el nombre da la columna junto con la ?
        $condiciones: Se guardan el nombre de la columna de clave primaria con la ?
        $valores: Se guardan los valores de cada uno de los campos de la fila
        $valoresClave: Lo utilizamos para guardar los valores de las claves primarias para posteriormente unirlos con los de los campos a modificar y poder construir correctamente la consulta, ya que los valores modificables van primero y lo ultimo van las condiciones, que son los valores de las claves primarias.
        */
    for ($i = 0; $i < $numFilas; $i++) {
        $modificaciones = [];
        $condiciones = [];
        $valores = [];
        $valoresClave = [];
        //Ahora recorremos las columnas para ir guardando en los arrays 
        foreach ($datos as $columna => $valor) {
            if (in_array($columna, $clavesPrimarias)) {
                $condiciones[] = "`$columna` = ?";
                $valoresClave[] = $valor[$i];
            } else {
                $modificaciones[] = "`$columna` = ?";
                $valores[] = $valor[$i];
            }
        }
        //Unimos los arrays obtenidos para posterirmente ejecutar la consulta
        $valores = array_merge($valores, $valoresClave);

        //Si dicho array no esta vacio entonces lanzamos la consulta para hacer la modificacion de los campos
        if (!empty($modificaciones) && !empty($condiciones)) {
            $consulta = "UPDATE `$tabla` SET " . implode(", ", $modificaciones) . " WHERE " . implode(" AND ", $condiciones);

            try {
                /*var_dump($numFilas);
                var_dump($consulta);
                print_r($valores);*/
                $stmt = $pdo->prepare($consulta);
                $stmt->execute($valores);
            } catch (PDOException $e) {
                echo "<p>Error al modificar:" . $e->getMessage() . "</p>";
            }
        }
    }
    echo ("La modificación se ha realizado correctamente");
}
function borrar($pdo, $baseDatos, $tabla, $id_usu)
{
    // Nos aseguramos de estar en la base de datos
    $pdo->query("USE $baseDatos");

    // Preparamos la consulta para borrar el usuario por su id
    $consulta = "DELETE FROM `$tabla` WHERE id_usu = ?";
    try {
        $stmt = $pdo->prepare($consulta);
        $stmt->execute([$id_usu]);
        echo "<p>Se ha eliminado el registro seleccionado</p>";
    } catch (PDOException $e) {
        echo "<p>Error al eliminar: " . $e->getMessage() . "</p>";
    }
}
//Con la siguiente funcion seleccionamos la fila o filas elegidas por el usuario para eliminarlas de la tabla. Le pasamos por parametro la conexion, la base de datos, la tabla y los datos obtenidos del formulario tablaEliminar
function borrarSeleccion ($pdo, $baseDatos, $tabla, $datos)
{
    //Nos aseguramos de estan en la base de datos
    $pdo->query("USE $baseDatos");


    //Eliminamos del array el campo oculto "opcion" que es el que nos sirve para acceder a la tabla que previamente hemos seleccionado
    unset($datos['opcion']);

    //Obtenemos la clave Primaria de la tabla en cuestion, para eso tenemos el campo oculto 'opcion'
    $consultaClavePrimaria = $pdo->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='{$baseDatos}' AND TABLE_NAME='{$tabla}' AND COLUMN_KEY='PRI'");


    $clavesPrimarias = $consultaClavePrimaria->fetchAll(PDO::FETCH_COLUMN);
    //Si no hay clave primaria nos salimos
    if (!$clavesPrimarias) {
        echo "<p>Error: No se encontro la clave primaria</p>";
        return;
    }


    //Ahora comprobamos que registros se han seleccionado para eliminarlos, comparando los campos de las claves primarias.
    if (isset($datos['elegido']) && is_array($datos['elegido'])) {
        foreach ($datos['elegido'] as $valorClave) {
            try {
                if ($valorClave !== 'borrarTodos') {
                    //Separamos los valores clave que hemos obtenido, que anteriormente habiamos unido con ','.Para utilizarlos a la hora de ejecutar la consulta
                    $valoresClave = explode(',', $valorClave);
                    //Ahora tenemos que construir la condicion WHERE por si hay dos claves primarias y asi poder incluirla en la consulta
                    $condiciones = [];
                    foreach ($clavesPrimarias as $indice => $columna) {
                        $condiciones[] = "`$columna`=?";
                    }
                    $condicion = implode(' AND ', $condiciones);

                    $consulta = "DELETE FROM `$tabla` WHERE $condicion";
                    $stmt = $pdo->prepare($consulta);
                    $stmt->execute($valoresClave);
                } else {
                    $consulta = "DELETE FROM `$tabla`";
                    $stmt = $pdo->prepare($consulta);
                    $stmt->execute();
                }
            } catch (PDOException $e) {
                echo "<p>Error al eliminar: " . $e->getMessage() . "</p>";
            }
        }
        echo "<p>Se ha eliminado el registro seleccionado</p>";
    } else {
        echo "<p>No se ha seleccionado ningun registro para eliminar</p>";
    }
}
//Con la siguiente funcion se podra eliminar totalmente la tabla seleccionada. Pasamos por parametro la conexión, la base de datos y la tabla.
function eliminarTabla($pdo, $baseDatos, $tabla)
{
    //Nos aseguramos de estar en la base de datos
    $pdo->query("USE $baseDatos");

    //Preparamos la consulta
    $consulta = "DROP TABLE `$tabla`";
    try {
        $stmt = $pdo->prepare($consulta);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "<p>Error al modificar:" . $e->getMessage() . "</p>";
    }
}

//Esta función es para encontrar el nombre de la tabla en la consulta, pasamos por parametro la consulta.
function nombreTabla($consulta)
{
    //Ponemos toda la consulta en mayuscula para facilitar la busqueda
    $consultaMayu = trim(strtoupper($consulta));

    //Separamos la consulta por espacios en blanco y lo guardamos en un array.
    $palabras = explode(" ", $consultaMayu);

    //Buscamos la tabla que se utiliza en la consulta, que ira detras de la palabra FROM, la siguiente variable contiene la posicion que ocupa FROM, le sumaremos 1 para encontrar la tabla en cuestion
    $indiceFrom = array_search('FROM', $palabras);

    if ($indiceFrom !== false && isset($palabras[$indiceFrom + 1])) {
        return $palabras[$indiceFrom + 1];
    } else {
        return null;
    }
}

//La siguiente función la utilizaremos para ejecutar la consulta que introduzca el usuario en un textarea.Pasaremos por parametro la conexión, la base de datos y la consulta.
function ejecutarConsulta($pdo, $baseDatos, $consulta)
{
    //Nos aseguramos de estan en la base de datos
    $pdo->query("USE $baseDatos");

    try {
        //Primero vamos a detectar si las consultas lanzadas son "SELECT,SHOW o DESCRIBE" para de esta forma mostrar las tablas correspondientes.Eliminamos todos los espacios del inicio y del fin de la consulta, lo ponemos todo en mayuscula y seleccionamos la primera palabra  de la consulta para analizarla, previamente tenemos que separar con explode todas las palabras de la consulta separadas por espacios en blanco (" ").
        $consultaMayu = trim(strtoupper($consulta));
        $tipoConsulta = explode(" ", $consultaMayu)[0];

        if (in_array($tipoConsulta, ["SELECT", "SHOW", "DESCRIBE"])) {

            //Buscamos el nombre de la tabla y la mostramos
            $tabla = nombreTabla($consulta);
            /* mostrarTablas($pdo, $baseDatos, $consulta, $tabla); */
        } else {
            $stmt = $pdo->prepare($consulta);
            $stmt->execute();

            echo "<p>La consulta se ha ejecutado correctamente</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error al ejecutar la consulta:" . $e->getMessage() . "</p>";
    }
}
