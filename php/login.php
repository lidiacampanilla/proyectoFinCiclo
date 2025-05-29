<?php
session_start();
require_once("./mysqlConexion.php");
require_once("./bibliotecaFunciones.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $baseDatos = "cofradia";
    try {
        $pdo = conexion($baseDatos);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos.'
        ]);
        exit;
    }

    // Validar y sanitizar entrada
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode([
            'success' => false,
            'message' => 'Email o contraseña no válidos.'
        ]);
        exit;
    }

    $usuario = validarAcceso($email, $password, $pdo);

    if ($usuario) {
        // Guardamos los datos en sesión
        $_SESSION['id_usu'] = $usuario['id_usu'];
        $_SESSION['tipo'] = $usuario['tipo'];
        $_SESSION['nombre'] = $usuario['nombre'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Email o contraseña incorrectos.'
        ]);
    }
    exit;
}