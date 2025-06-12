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
// Función para mostrar la tabla de datos del usuario
tablaDatos($pdo, $baseDatos, $idUsu);
?>

<!-- Con este código, se muestra una tabla con los datos del usuario que ha iniciado sesión. -->