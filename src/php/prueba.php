<?php
// testConexion.php
require_once("./mysqlConexion.php");
try {
    $pdo = conexion("cofradia");
    echo "Conexión exitosa";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>