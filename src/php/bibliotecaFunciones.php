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


    $registros = $resultado->fetchAll(PDO::FETCH_COLUMN);
    if (in_array($baseDatos, $registros)) {
        return true;
    } else {
        crearBaseDatos($pdo, $sqlBaseDatos);
        return false;
    }
}

//Con la siguiente función vamos a validar el DNI
function validarDNI($dni)
{
    $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
    //Transformamos todo a mayuscula
    $dni = strtoupper($dni);
    //Esta funcion "preg_match" devuleve 1 si coincide con la expresion regular y 0 sino coincide
    if (!preg_match("/^[0-9]{8}[A-Z]$/", $dni)) return false;
    $numero = substr($dni, 0, 8);
    $letra = substr($dni, -1);
    return $letra == $letras[$numero % 23];
}

//Con la siguiente función validamos la cta_bancaria(IBAN) que deberá tener dos letras y 22 números
function validarIBAN($iban)
{
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
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'usuario.email') !== false) {
            echo "<div  class='alert alert-danger'>Email duplicado</div>";
        } elseif (strpos($e->getMessage(), 'usuario.DNI') !== false) {
            echo "<div  class='alert alert-danger'>DNI duplicado</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al insertar: " . $e->getMessage() . "</div>";
        }
    }
}

//Creamos una funcion para validar el acceso de usuarios
function validarAcceso($email, $password, $pdo)
{
    //Lanzamos la consulta para iniciar la verificacion del email y del password
    $stmt = $pdo->prepare("SELECT U.id_usu, U.password, U.Nomb_usu, T.Nomb_tipo
                                  FROM usuario U
                                  JOIN pertenecen P ON U.id_usu = P.id_usu
                                  JOIN tipo T ON P.id_tipo = T.id_tipo
                                  WHERE U.email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        return [
            'id_usu' => $usuario['id_usu'],
            'tipo' => $usuario['Nomb_tipo'],
            'nombre' => $usuario['Nomb_usu']
        ];
    } else {
        return false;
    }
}




//Con la siguiente funcion hacemos una tabla que muestre todas las hermanos, trabajamos con las tablas USUARIOS y TIPO, para mostrar tambien los tipos de hermanos.
function tablaGestionHer($pdo, $baseDatos, $idUsu)
{
    $pdo->query("USE $baseDatos");

    // Consulta para obtener todos los usuarios y su tipo
    $consulta = "SELECT U.*, T.Nomb_tipo, T.id_tipo 
                 FROM usuario U
                 JOIN pertenecen P ON U.id_usu = P.id_usu
                 JOIN tipo T ON P.id_tipo = T.id_tipo";
    $resultado = $pdo->query($consulta);
    $registros = $resultado->fetchAll(PDO::FETCH_ASSOC);

    if (!$registros) {
        echo "<p class='p-3 mb-2 bg-info text-dark'>No hay ningún registro en la tabla</p>\n";
        return;
    }

    // Obtener todos los tipos para el select
    $stmtTipos = $pdo->query("SELECT id_tipo, Nomb_tipo FROM tipo");
    $tipos = $stmtTipos->fetchAll(PDO::FETCH_ASSOC);

    echo "<div class='container mt-4'>";
    echo "<form method='post' id='formGestionHer'>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered table-hover align-middle table-sm'>";
    echo "<thead class='table-success'>";
    echo "<tr>";
    echo "<th style='width:30px;'><input type='checkbox' id='checkAll'></th>"; // Checkbox para seleccionar todos

    // Cabeceras dinámicas
    foreach (array_keys($registros[0]) as $columna) {
        if ($columna === 'id_tipo') continue; // Ocultamos id_tipo si no quieres mostrarlo
        echo "<th style='font-size:0.95em;'>" . htmlspecialchars($columna) . "</th>";
    }
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($registros as $i => $registro) {
        echo "<tr>";
        // Checkbox para seleccionar el registro
        echo "<td><input type='checkbox' name='elegido[]' value='" . htmlspecialchars($registro['id_usu']) . "'></td>";

        foreach ($registro as $columna => $valor) {
            if ($columna === 'id_tipo') continue;

            echo "<td style='padding:2px 4px;'>";
            // Campo oculto para id_usu (solo una vez por fila)
            if ($columna == 'id_usu') {
                echo "<input type='hidden' name='id_usu[$i]' value='" . htmlspecialchars($valor) . "'>";
                echo htmlspecialchars($valor);
            }
            // Campo password: solo muestra 4 asteriscos, no editable
            elseif ($columna == 'password') {
                echo "<input type='text' class='form-control form-control-sm' value='****' readonly style='width:60px;text-align:center;background-color:#e9ecef;'>";
            }
            // Campo Nomb_tipo: select editable
            elseif ($columna == 'Nomb_tipo') {
                echo "<select class='form-select form-select-sm' name='Nomb_tipo[$i]' style='min-width:90px;max-width:120px;'>";
                foreach ($tipos as $tipo) {
                    $selected = ($tipo['Nomb_tipo'] == $valor) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($tipo['Nomb_tipo']) . "' $selected>" . htmlspecialchars($tipo['Nomb_tipo']) . "</option>";
                }
                echo "</select>";
            }
            // Otros campos: input editable compacto
            else {
                echo "<input type='text' class='form-control form-control-sm' name='{$columna}[$i]' value='" . htmlspecialchars($valor) . "' style='min-width:80px;max-width:130px;'>";
            }
            echo "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";


    // Mostrar las operaciones permitidas para el usuario logueado
    $stmtTipo = $pdo->prepare("SELECT T.id_tipo FROM usuario U
        JOIN pertenecen P ON U.id_usu = P.id_usu
        JOIN tipo T ON P.id_tipo = T.id_tipo
        WHERE U.id_usu = ?");
    $stmtTipo->execute([$idUsu]);
    $id_tipo_usuario = $stmtTipo->fetchColumn();
    if ($id_tipo_usuario) {
        //Función que muestra los botones de operaciones permitidas para el usuario logueado
        mostrarBotonesOperaciones($pdo, $baseDatos, $id_tipo_usuario);
    }
    echo "</form>";
    echo "</div>";
}

//Con la siguiente funcion hacemos una tabla que muestre los datos de un usuario en concreto, pasamos por parametro la conexión, la base de datos y el id del usuario que queremos mostrar.
function tablaDatos($pdo, $baseDatos, $idUsu)
{
    //Nos aseguramos de estar utilizando nuestra base de datos
    $pdo->query("USE $baseDatos");

    //Preparamos la consulta para obtener los datos del usuario especificado por su id_usu, junto con el tipo de usuario. Utilizamos JOIN para unir las tablas usuario, pertenecen y tipo.
    $consulta = "SELECT U.*, T.Nomb_tipo, T.id_tipo FROM usuario U
    JOIN pertenecen P ON U.id_usu = P.id_usu
    JOIN tipo T ON P.id_tipo = T.id_tipo
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
        echo "<div class='row justify-content-center'>";
        echo "<div class='col-md-8 col-lg-6'>";
        echo "<div class='formulario'>";
        echo "<form method='post' action=''>";
        $usuario = $resultado[0]; // Asumimos que solo hay un usuario para este id_usu
        // Añadimos un campo oculto para el id_usu para poder identificarlo en las operaciones posteriores
        echo "<input type='hidden' name='id_usu' value='" . htmlspecialchars($usuario['id_usu']) . "'>";

        // Obtener todos los tipos para el select
        $stmtTipos = $pdo->query("SELECT id_tipo, Nomb_tipo FROM tipo");
        $tipos = $stmtTipos->fetchAll(PDO::FETCH_ASSOC);
        foreach ($usuario as $columna => $valor) {
            // No mostrar id_usu ni id_tipo en el formulario
            if ($columna == 'id_usu' || $columna == 'id_tipo') {
                continue;
            }

            // OCULTAR Nomb_tipo si el usuario es administrador o junta, ya que estos tipos no se pueden modificar
            // Convertimos a minúsculas para evitar problemas de mayúsculas/minúsculas
            if (
                $columna == 'Nomb_tipo' &&
                (
                    strtolower($usuario['Nomb_tipo']) === 'administrador' ||
                    strtolower($usuario['Nomb_tipo']) === 'junta'
                )
            ) {
                continue;
            }

            $readonly = ($columna == 'id_usu') ? 'readonly' : '';
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
                        continue; // Saltar estos tipos, no se muestran en el select de esta tabla
                    }
                    $selected = ($tipo['Nomb_tipo'] == $valor) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($tipo['Nomb_tipo']) . "' $selected>" . htmlspecialchars($tipo['Nomb_tipo']) . "</option>";
                }
                echo "</select>";
            } elseif ($columna == 'password') {
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
        // Botones para modificar o borrar el usuario
        echo "<div class='text-center mt-4'>";
        echo "<button type='button' class='btn btn-secondary btn-operacion me-2' data-accion='modificar' data-id='2' name='modificar' class='btn btn-primary me-2'>Modificar</button>";
        echo "<button type='button' class='btn btn-secondary btn-operacion me-2' data-accion='borrar' data-id='3' name='borrar' class='btn btn-danger'>Borrar</button>";
        echo "</div>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
}

//Con la siguiente función mostramos los botones de operaciones que tiene asignadas el usuario logueado, pasamos por parametro la conexión, la base de datos y el id del tipo de usuario.
function mostrarBotonesOperaciones($pdo, $baseDatos, $id_tipo)
{
    $pdo->query("USE $baseDatos");

    $sqlOperaciones = "SELECT O.id_ope, O.Nomb_ope, O.Descrip_ope
        FROM operaciones O
        JOIN realizan R ON O.id_ope = R.id_ope
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

                $stmt = $pdo->prepare($consulta);
                $stmt->execute($valores);
            } catch (PDOException $e) {
                echo "<p>Error al modificar:" . $e->getMessage() . "</p>";
            }
        }
    }
    echo ("La modificación se ha realizado correctamente");
}

//Con la siguiente funcion borramos un usuario por su id, pasamos por parametro la conexión, la base de datos, la tabla y el id del usuario que queremos borrar.
function borrar($pdo, $baseDatos, $tabla, $id_usu)
{
    // Nos aseguramos de estar en la base de datos
    $pdo->query("USE $baseDatos");

    // Preparamos la consulta para borrar el usuario por su id_usu
    $consulta = "DELETE FROM `$tabla` WHERE id_usu = ?";
    try {
        $stmt = $pdo->prepare($consulta);
        $stmt->execute([$id_usu]);
    } catch (PDOException $e) {
        echo "<p>Error al eliminar: " . $e->getMessage() . "</p>";
    }
}

//Con la siguiente funcion hacemos una tabla que muestre los hermanos filtrados por nombre y tipo, pasamos por parametro la conexión, la base de datos, el id del usuario logueado y los filtros de nombre y tipo.
function tablaGestionHerFiltrada($pdo, $baseDatos, $idUsu, $nombre = '', $nomb_tipo = '')
{
    $pdo->query("USE $baseDatos");

    // Construir la consulta con filtros dinámicos
    $sql = "SELECT U.*, T.Nomb_tipo, T.id_tipo 
            FROM usuario U
            JOIN pertenecen P ON U.id_usu = P.id_usu
            JOIN tipo T ON P.id_tipo = T.id_tipo
            WHERE 1=1";
    $params = [];

    if ($nombre !== '') {
        $sql .= " AND U.Nomb_usu LIKE ?";
        $params[] = "%$nombre%";
    }
    if ($nomb_tipo !== '') {
        $sql .= " AND T.Nomb_tipo LIKE ?";
        $params[] = "%$nomb_tipo%";
    }

    $resultado = $pdo->prepare($sql);
    $resultado->execute($params);
    $registros = $resultado->fetchAll(PDO::FETCH_ASSOC);

    if (!$registros) {
        echo "<p class='p-3 mb-2 bg-info text-dark'>No hay ningún registro que cumpla el filtro</p>\n";
        return;
    }

    // Obtener todos los tipos para el select
    $stmtTipos = $pdo->query("SELECT id_tipo, Nomb_tipo FROM tipo");
    $tipos = $stmtTipos->fetchAll(PDO::FETCH_ASSOC);

    echo "<div class='container mt-4'>";
    echo "<form method='post' id='formGestionHer'>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered table-hover align-middle table-sm'>";
    echo "<thead class='table-success'>";
    echo "<tr>";
    echo "<th style='width:30px;'><input type='checkbox' id='checkAll'></th>";

    foreach (array_keys($registros[0]) as $columna) {
        if ($columna === 'id_tipo') continue;
        echo "<th style='font-size:0.95em;'>" . htmlspecialchars($columna) . "</th>";
    }
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($registros as $i => $registro) {
        echo "<tr>";
        echo "<td><input type='checkbox' name='elegido[]' value='" . htmlspecialchars($registro['id_usu']) . "'></td>";
        foreach ($registro as $columna => $valor) {
            if ($columna === 'id_tipo') continue;
            echo "<td style='padding:2px 4px;'>";
            if ($columna == 'id_usu') {
                echo "<input type='hidden' name='id_usu[$i]' value='" . htmlspecialchars($valor) . "'>";
                echo htmlspecialchars($valor);
            } elseif ($columna == 'password') {
                echo "<input type='text' class='form-control form-control-sm' value='****' readonly style='width:60px;text-align:center;background-color:#e9ecef;'>";
            } elseif ($columna == 'Nomb_tipo') {
                echo "<select class='form-select form-select-sm' name='Nomb_tipo[$i]' style='min-width:90px;max-width:120px;'>";
                foreach ($tipos as $tipo) {
                    $selected = ($tipo['Nomb_tipo'] == $valor) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($tipo['Nomb_tipo']) . "' $selected>" . htmlspecialchars($tipo['Nomb_tipo']) . "</option>";
                }
                echo "</select>";
            } else {
                echo "<input type='text' class='form-control form-control-sm' name='{$columna}[$i]' value='" . htmlspecialchars($valor) . "' style='min-width:80px;max-width:130px;'>";
            }
            echo "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    echo "</form>";

    // Mostrar las operaciones permitidas para el usuario logueado
    $stmtTipo = $pdo->prepare("SELECT T.id_tipo FROM usuario U
        JOIN pertenecen P ON U.id_usu = P.id_usu
        JOIN tipo T ON P.id_tipo = T.id_tipo
        WHERE U.id_usu = ?");
    $stmtTipo->execute([$idUsu]);
    $id_tipo_usuario = $stmtTipo->fetchColumn();
    if ($id_tipo_usuario) {
        mostrarBotonesOperaciones($pdo, $baseDatos, $id_tipo_usuario);
    }

    echo "</div>";
}


//Con la siguiente funcion seleccionamos la fila o filas elegidas por el usuario para eliminarlas de la tabla. Le pasamos por parametro la conexion, la base de datos, la tabla y los datos obtenidos del formulario tablaEliminar
function borrarSeleccion($pdo, $baseDatos, $tabla, $datos)
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
