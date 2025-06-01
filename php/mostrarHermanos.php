<?php
session_start();
include("./mysqlConexion.php");
include("./bibliotecaFunciones.php");
$baseDatos = "COFRADIA";
$pdo = conexion($baseDatos);

if (!isset($_SESSION['id_usu'])) {
    echo "<p class='alert alert-danger'>No has iniciado sesión correctamente.</p>";
    exit;
}

$idUsu = $_SESSION['id_usu'];

tablaGestionHer($pdo, $baseDatos, $idUsu);
?>