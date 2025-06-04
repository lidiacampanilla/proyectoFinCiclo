<?php
require_once 'bibliotecaFunciones.php';
require_once 'mysqlConexion.php'; // Asegúrate de tener la conexión $pdo

$baseDatos = 'cofradia'; // Cambia por el nombre real de tu base de datos
$tabla = 'usuario'; // Cambia si tu tabla de usuarios tiene otro nombre
$pdo = conexion($baseDatos);
session_start();
$idUsu = $_SESSION['id_usu'] ?? null;

if (!$idUsu) {
    echo "<p>Error: Usuario no identificado.</p>";
    exit;
}
$accion = $_POST['accion'] ?? '';

/* if ($accion === 'modificar') {
    // Recoge los datos del formulario
    $datos = $_POST;
    unset($datos['accion']); // Elimina el campo de acción
     // Si se va a modificar el tipo de usuario
    if (isset($datos['Nomb_tipo'])) {
        // Obtener el id_tipo correspondiente
        $stmtTipo = $pdo->prepare("SELECT id_tipo FROM TIPO WHERE Nomb_tipo = ?");
        $stmtTipo->execute([$datos['Nomb_tipo']]);
        $nuevoIdTipo = $stmtTipo->fetchColumn();

        if ($nuevoIdTipo) {
            // Actualizar la tabla PERTENECEN
            $stmtPert = $pdo->prepare("UPDATE PERTENECEN SET id_tipo = ? WHERE id_usu = ?");
            $stmtPert->execute([$nuevoIdTipo, $datos['id_usu']]);
        }
        // Elimina Nomb_tipo para que no intente modificarlo en USUARIO
        unset($datos['Nomb_tipo']);
    }

    // Para la función modificar, los datos deben estar en formato array de arrays (como espera tu función)
    foreach ($datos as $key => $value) {
        $datos[$key] = [$value];
    }

    modificar($pdo, $baseDatos, $tabla, $datos);

    // Vuelve a mostrar los datos actualizados
    tablaDatos($pdo, $baseDatos, $idUsu);
    } elseif ($accion === 'borrar') {
    borrar($pdo, $baseDatos, $tabla, $idUsu);
    echo "<div class='alert alert-warning'>Sentimos mucho tu baja</div>";
} else {
    echo "<p>Acción no reconocida.</p>";
} */

if ($accion === 'insertar') {
    $datos = $_POST;
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

    // NO añadas id_tipo al array de USUARIO
    insertar($pdo, $baseDatos, $tabla, $datos);

    // Obtener el último id_usu insertado
    $id_usu = $pdo->lastInsertId();

    // Ahora inserta en PERTENECEN
    if ($id_tipo && $id_usu) {
        $stmtPert = $pdo->prepare("INSERT INTO pertenecen (id_usu, id_tipo) VALUES (?, ?)");
        $stmtPert->execute([$id_usu, $id_tipo]);
    }

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
            borrar($pdo, $baseDatos, $tabla, $idBorrar);
        }
        tablaGestionHer($pdo, $baseDatos, $idUsu);
    } else {
        // Borrado individual
        borrar($pdo, $baseDatos, $tabla, $idUsu);
        echo "<div class='alert alert-warning'>Sentimos mucho tu baja</div>";
       
    }

} elseif ($accion === 'filtrar') {
    $nombre = $_POST['nombre'] ?? '';
    $nomb_tipo = $_POST['nomb_tipo'] ?? '';
    tablaGestionHerFiltrada($pdo, $baseDatos, $idUsu, $nombre, $nomb_tipo);

} else {
    echo "<p>Acción no reconocida.</p>";
}
?>