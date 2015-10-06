<?php
	/*
	 * Ejemplo de utilizacion del la biblioteca LQLS, equivalente a: LQL sobre Secretary
	 * */
	 
	//... paso 1: incluir el Loader y las funciones utilies (cfg|show)
	include "lib/loader/Main.php";
	include "lib/utils.php";
	//... paso 2: los espacios de nombres a utilizar
	use Loader\Main as Loader;
	use Secretary\src\server\Main as Secretary;
	//... paso 3: configurar el Loader especificandole las direcciones de las dependencias
	Loader::active(array( 'Secretary'=>'lib/secretary'));
	//... paso 4: cargar las variables de configuracion 
	$config = cfg();
	//... paso 5: comenzar a utilizar el Secretary
	
	$dbmanager = new Secretary($config['db']);
	$out = $dbmanager->query('SELECT * FROM person');
	show($out);
