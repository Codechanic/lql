<?php

	$config["db"]["host"]		= "localhost";		    //... servidor o proveedor de bases de datos
	$config["db"]["user"]		= "postgres";		    //... usuario de una cuenta activa en el servidor de bases de datos
	$config["db"]["pass"]		= "postgres";			//... contraseña requerida para la cuenta activa en el servidor de bases de datos
	$config["db"]["name"]		= "prueba";		        //... nombre de la base de datos a la cual debe conectarse
    $config["db"]["driver"]		= "pgsql";				//... pgsql|mysql
	$config["db"]["log"]		= "log/";
	return $config;
