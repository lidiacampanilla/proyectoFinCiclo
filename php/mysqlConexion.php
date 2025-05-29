<?php

function conexion($baseDatos){
    //Creamos las variables de conexión
    $server = "localhost";
    $user = "root";
    $password = "";

    //Utilizamos la estructura try...catch para controlar los errores
    try {
        //Creamos la variable de conexión a mysql
        $pdo = new PDO("mysql:host=$server;dbname=$baseDatos", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
         // Puedes loguear el error aquí si lo necesitas
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos.'
        ]);
        exit;
    }
    return $pdo;
}

function conexionSinBase(){
    //Creamos las variables de conexión
    $server = "localhost";
    $user = "root";
    $password = "";

    //Utilizamos la estructura try...catch para controlar los errores
    try {
        //Creamos la variable de conexión a mysql
        $pdo = new PDO("mysql:host=$server", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        print "<p>Código de Error:" . $e->getCode() . "<br>El Mensaje es: " . $e->getMessage() . "</p>";
        //echo "Error en la conexion: ".$e->getMessage();
        exit;
    }
    return $pdo;
}