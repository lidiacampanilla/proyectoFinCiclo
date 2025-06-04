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

tablaDatos($pdo, $baseDatos, $idUsu);
?>