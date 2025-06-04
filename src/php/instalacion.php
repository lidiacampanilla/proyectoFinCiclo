<?php
      require_once("./mysqlConexion.php");
      require_once("./bibliotecaFunciones.php");
      require_once("../scripts/basedatos.php");
      
      $baseDatos="cofradia";
      $pdo=conexionSinBase();

      if(!existeBaseDatos($pdo, $baseDatos,$sqlBaseDatos)){
        crearBaseDatos($pdo, $sqlBaseDatos);
        echo "Base de datos creada Correctamente.";
      }else{
        echo "La base de datos ya existe.";
      }
?>