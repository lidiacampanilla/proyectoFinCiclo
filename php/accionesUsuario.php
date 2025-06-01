<?php
require_once 'bibliotecaFunciones.php';
require_once 'mysqlConexion.php'; // Asegúrate de tener la conexión $pdo

$baseDatos = 'cofradia'; // Cambia por el nombre real de tu base de datos
$tabla = 'USUARIO'; // Cambia si tu tabla de usuarios tiene otro nombre
$pdo = conexion($baseDatos);
session_start();
$idUsu = $_SESSION['id_usu'] ?? null;

if (!$idUsu) {
    echo "<p>Error: Usuario no identificado.</p>";
    exit;
}
$accion = $_POST['accion'] ?? '';

if ($accion === 'modificar') {
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
}
?>