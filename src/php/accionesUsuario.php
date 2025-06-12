<?php
require_once 'bibliotecaFunciones.php';
require_once 'mysqlConexion.php';

$baseDatos = 'cofradia';
$tabla = 'usuario';
$pdo = conexion($baseDatos);
session_start();
$idUsu = $_SESSION['id_usu'] ?? null;

if (!$idUsu) {
    echo "<p>Error: Usuario no identificado.</p>";
    exit;
}
$accion = $_POST['accion'] ?? '';

if ($accion === 'insertar') {
    $datos = $_POST;
    //Eliminamos el campo accion del array de datos, ya que no es un campo de la tabla
    unset($datos['accion']);

    // Obtener id_tipo a partir de Nomb_tipo
    $id_tipo = null;
    if (isset($datos['Nomb_tipo'])) {
        $stmtTipo = $pdo->prepare("SELECT id_tipo FROM tipo WHERE Nomb_tipo = ?");
        $stmtTipo->execute([$datos['Nomb_tipo']]);
        $id_tipo = $stmtTipo->fetchColumn();
        unset($datos['Nomb_tipo']);
    }

    // Hashear la contraseña si está presente
    if (isset($datos['password']) && trim($datos['password']) !== '') {
        $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
    }

    //Funcion que inserta un usuario en la base de datos, que esta en la biblioteca de funciones
    // Antes de insertar, validamos los campos DNI, email y cta_bancaria, con el .js



    // Validación de DNI

    if (isset($datos['DNI'])) {
        $dnis = is_array($datos['DNI']) ? $datos['DNI'] : [$datos['DNI']];
        foreach ($dnis as $dni) {
            if (!validarDNI($dni)) {
                echo "<div class='alert alert-danger'>El DNI no es válido</div>";
                exit;
            }
        }
    }
    // Validación de IBAN (si tienes el campo)
    if (isset($datos['cta_bancaria'])) {
        $cuentas = is_array($datos['cta_bancaria']) ? $datos['cta_bancaria'] : [$datos['cta_bancaria']];
        foreach ($cuentas as $cuenta) {
            if (!validarIBAN($cuenta)) {
                echo "<div class='alert alert-danger'>El número de cuenta no es válido</div>";
                exit;
            }
        }
    }
    insertar($pdo, $baseDatos, $tabla, $datos);

    // Obtener el último id_usu insertado
    $id_usu = $pdo->lastInsertId();

    // Ahora inserta en PERTENECEN
    if ($id_tipo && $id_usu) {
        $stmtPert = $pdo->prepare("INSERT INTO pertenecen (id_usu, id_tipo) VALUES (?, ?)");
        $stmtPert->execute([$id_usu, $id_tipo]);
    }
    //Tras insertar, mostramos la tabla de gestión de hermanos
    tablaGestionHer($pdo, $baseDatos, $idUsu);
} elseif ($accion === 'modificar') {
    $datos = $_POST;
    unset($datos['accion']);
    // Modificación múltiple o individual de Nomb_tipo
    if (isset($datos['Nomb_tipo'])) {
        if (is_array($datos['Nomb_tipo'])) {
            foreach ($datos['Nomb_tipo'] as $i => $nomb_tipo) {
                $stmtTipo = $pdo->prepare("SELECT id_tipo FROM tipo WHERE Nomb_tipo = ?");
                $stmtTipo->execute([$nomb_tipo]);
                $nuevoIdTipo = $stmtTipo->fetchColumn();
                if ($nuevoIdTipo && isset($datos['id_usu'][$i])) {
                    $stmtPert = $pdo->prepare("UPDATE pertenecen SET id_tipo = ? WHERE id_usu = ?");
                    $stmtPert->execute([$nuevoIdTipo, $datos['id_usu'][$i]]);
                }
            }
        } else {
            $stmtTipo = $pdo->prepare("SELECT id_tipo FROM tipo WHERE Nomb_tipo = ?");
            $stmtTipo->execute([$datos['Nomb_tipo']]);
            $nuevoIdTipo = $stmtTipo->fetchColumn();
            if ($nuevoIdTipo) {
                $stmtPert = $pdo->prepare("UPDATE pertenecen SET id_tipo = ? WHERE id_usu = ?");
                $stmtPert->execute([$nuevoIdTipo, $datos['id_usu']]);
            }
        }
        unset($datos['Nomb_tipo']);
    }
    // Formatea los datos para la función modificar
    foreach ($datos as $key => $value) {
        if (!is_array($value)) {
            $datos[$key] = [$value];
        }
    }
    //Para controlar los campos DNI, email y cta_bancaria, utilizamos el siguiente código. Hacemos un array para recoger los errores que puedan surgir en la validación de los campos.
    // Validar los campos antes de modificar
    $errores = [];

    // Validar DNI
    if (isset($datos['DNI'])) {
        $dni = is_array($datos['DNI']) ? $datos['DNI'] : [$datos['DNI']];
        foreach ($dni as $i => $d) {
            if (!validarDNI($d)) {
                $errores[] = "DNI ERRONEO";
            } else {
                // Comprobar si el DNI ya existe en la base de datos
                // Excluyendo el usuario actual (id_usu)
                $id_usu_actual = isset($datos['id_usu'][$i]) ? $datos['id_usu'][$i] : $datos['id_usu'];
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE dni = ? AND id_usu != ?");
                $stmt->execute([$d, $id_usu_actual]);
                if ($stmt->fetchColumn() > 0) {
                    $errores[] = "DNI ya registrado";
                }
            }
        }
    }
    // Validar email, solo comprobamos si el email introducido ya existe en la base de datos, excluyendo el usuario actual (id_usu)
    // Si es un array, recorremos cada elemento; si es un string, lo convertimos a array para validarlo
    if (isset($datos['email'])) {
        $email = is_array($datos['email']) ? $datos['email'] : [$datos['email']];
        foreach ($email as $i => $e) {
            $id_usu_actual = isset($datos['id_usu'][$i]) ? $datos['id_usu'][$i] : $datos['id_usu'];
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = ? AND id_usu != ?");
            $stmt->execute([$e, $id_usu_actual]);
            if ($stmt->fetchColumn() > 0) {
                $errores[] = "Email ya registrado";
            }
        }
    }
    // Validar cta_bancaria
    if (isset($datos['cta_bancaria'])) {
        $cta_bancaria = is_array($datos['cta_bancaria']) ? $datos['cta_bancaria'] : [$datos['cta_bancaria']];
        // Validar cada cuenta bancaria
        // Si es un array, recorremos cada elemento
        // Si es un string, lo convertimos a array para validarlo
        foreach ($cta_bancaria as $c) {
            if (!validarIBAN($c)) {
                $errores[] = "Cuenta bancaria no válida";
            }
        }
    }

    // Si hay errores, mostramos un mensaje y no hacemos la modificación
    if (!empty($errores)) {
        echo "<div class='alert alert-danger'>Errores encontrados: " . implode(', ', $errores) . "</div>";
        exit;
    }
    //Con el siguiente código, vamos a modificar los campos de contraseña y hashearla si no están vacíos
    if (isset($datos['password'])) {
        if (is_array($datos['password'])) {
            foreach ($datos['password'] as $i => $pass) {
                if (trim($pass) !== '') {
                    $datos['password'][$i] = password_hash($pass, PASSWORD_DEFAULT);
                } else {
                    unset($datos['password'][$i]); // Si está vacío, no modificar
                }
            }
            if (empty($datos['password'])) unset($datos['password']);
        } else {
            if (trim($datos['password']) !== '') {
                $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
            } else {
                unset($datos['password']);
            }
        }
    }
    modificar($pdo, $baseDatos, $tabla, $datos);
    // Si es modificación múltiple, muestra tablaGestionHer; si es individual, tablaDatos
    if (isset($datos['id_usu']) && count($datos['id_usu']) > 1) {
        tablaGestionHer($pdo, $baseDatos, $idUsu);
    } else {
        tablaDatos($pdo, $baseDatos, $idUsu);
    }
} elseif ($accion === 'borrar') {
    if (isset($_POST['elegido'])) {
        // Borrado múltiple
        foreach ($_POST['elegido'] as $idBorrar) {
            // Función que borra un usuario de la base de datos, que esta en la biblioteca de funciones
            borrar($pdo, $baseDatos, $tabla, $idBorrar);
        }
        // Tras borrar, mostramos la tabla de gestión de hermanos
        tablaGestionHer($pdo, $baseDatos, $idUsu);
    } else {
        // Borrado individual
        //Función que borra un usuario de la base de datos, que esta en la biblioteca de funciones
        borrar($pdo, $baseDatos, $tabla, $idUsu);
        echo "<div class='alert alert-warning'>Sentimos mucho tu baja</div>";
    }
} elseif ($accion === 'filtrar') {
    $nombre = $_POST['nombre'] ?? '';
    $nomb_tipo = $_POST['nomb_tipo'] ?? '';
    //Funcion que muestra la tabla de gestión de hermanos filtrada que esta en la biblioteca de funciones
    tablaGestionHerFiltrada($pdo, $baseDatos, $idUsu, $nombre, $nomb_tipo);
} else {
    echo "<p>Acción no reconocida.</p>";
}
