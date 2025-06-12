<?php
session_start();
include("./mysqlConexion.php");
include("./bibliotecaFunciones.php");
$baseDatos = "cofradia";
$pdo = conexion($baseDatos);

if (!isset($_SESSION['id_usu'])) {
    echo "<p class='alert alert-danger'>No has iniciado sesión correctamente.</p>";
    exit;
}

$idUsu = $_SESSION['id_usu'];
// Función para mostrar la tabla de gestión de hermanos
tablaGestionHer($pdo, $baseDatos, $idUsu);
?>

<!-- Con este código, se muestra la lista de hermanos asociados al usuario que ha iniciado sesión. Según el tipo de hermano se mostraran diferentes acciones. Si el usuario es administrador, podra insertar, modificar, eliminar o filtrar, y si el hermano pertenece a la Junta de Gobierno, podra borrar, modificar o filtrar.  -->