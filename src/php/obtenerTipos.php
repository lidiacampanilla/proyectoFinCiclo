<?php
require_once 'mysqlConexion.php';
$baseDatos = 'cofradia';
$pdo = conexion($baseDatos);
$stmt = $pdo->query("SELECT Nomb_tipo FROM tipo");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
