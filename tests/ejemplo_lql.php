<?php
	/*
	 * Ejemplo de utilizacion del la biblioteca LQLS, equivalente a: LQL sobre Secretary
	 * */
	 
	//... paso 1: incluir el Loader y las funciones utilies (cfg|show)
	include "lib/loader/Main.php";
	include "lib/utils.php";
	//... paso 2: los espacios de nombres a utilizar
	use Loader\Main as Loader;
	use LQL\src\Main as LQL;
	use LQL\src\processor\SQL as ProcessorSQL;
	//... paso 3: configurar el Loader especificandole las direcciones de las dependencias
	Loader::active(array('LQL'=>'lib/lql'));
	//... paso 4: cargar las variables de configuracion 
	$config = cfg();

	//... paso 5: comenzar a utilizar el LQL
	
	/*
	 * configurar el LQL de forma general para todas las consultas
	 * */
	LQL::setting(null, new ProcessorSQL);
	/*
	 * Creando una seleccion simple y obtener el sql
	 * */
	$sql = LQL::create()
		->select('count(j.action) as data1, s.denomination as name')
        ->from('mod_pykota.jobhistory j')
		->compile()
	;
	show($sql);
	/*
	 * Creando una seleccion simple y obtener el sql
	 * */
	$sql = LQL::create()
		->select('t.nombre as mio, t.edad as era')
		->from(LQL::create()
			->select('name as nombre, age as edad, serverid')
			->from('person', 'p'), 't'
		)
		->limit(5)
		->offset(1)
		->compile()
	;
	show($sql);
