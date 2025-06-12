<?php
require_once("./mysqlConexion.php");
require_once("./bibliotecaFunciones.php");
require_once("../scripts/basedatos.php");

$baseDatos = "cofradia";
$pdo = conexionSinBase();

//Llamamos a la función que comprueba si existe la base de datos, o bien la crea si no existe, que se encuentra en el archivo bibliotecaFunciones.php
if (!existeBaseDatos($pdo, $baseDatos, $sqlBaseDatos)) {
  crearBaseDatos($pdo, $sqlBaseDatos);
  echo "Base de datos creada Correctamente.";
} else {
  echo "La base de datos ya existe.";
}
?>

<!-- Este archivo se creo para crear la base de datos de la aplicación Cofradía. Cuando se utilizaba xampp para desplegar la aplicación, se ejecutaba este archivo para crear la base de datos.
// Ahora que se utiliza Docker, este archivo se ejecuta automáticamente al iniciar el contenedor de la aplicación. -->