<?php
	/*
	 * Ejemplo de utilizacion del la biblioteca LQLS, equivalente a: LQL sobre Secretary
	 * */

	//... paso 1: incluir el Loader y las funciones utilies (cfg|show)
	include "lib/loader/Main.php";
	include "lib/utils.php";
	//... paso 2: los espacios de nombres a utilizar
	use Loader\Main as Loader;
	use LQLS\src\Main as LQLS;
	//... paso 3: configurar el Loader especificandole las direcciones de las dependencias
	Loader::active(array(
		'Secretary'=>'lib/secretary',
		'LQL'=>'lib/lql',
		'LQLS'=>'lib/lqls'
	));
	//... paso 4: cargar las variables de configuracion
	$config = cfg();

	//... paso 5: comenzar a utilizar el LQLS

	/*
	 * Creando una seleccion simple y obtener el sql
	 * */
	$sql = LQLS::create($config['db'])
		->select('name as nombre, age as edad, serverid')
		->from('person', 'p')
		->compile()
	;
	show($sql);
	/*
	 * Creando una seleccion simple y obtener el resultado
	 * */
	$sql = LQLS::create($config['db'])
		->select('name as nombre, age as edad, serverid')
		->from('person', 'p')
		->execute()
	;
	/*
	 * Creando una seleccion compuesta sobre la tabla person
	 * */
	$sql = LQLS::create($config['db'])
		->select('t.nombre as mio, t.edad as era')
		->from(LQLS::create($config['db'])
			->select('name as nombre, age as edad, serverid')
			->from('person', 'p'), 't'
		)
		->limit(5)
		->offset(1)
		->execute()
	;
	show($sql);
	/*
	 * Creando un update simple
	 * */
	$sql = LQLS::create($config['db'])
		->update('person')
		->set('age', 15)
		->compile()
	;
	/*
	 * Creando un update de multiples atributos y con condiciones
	 * */
	$sql = LQLS::create()
		->update('person')
		->set(['age', 'name'], [30, 'Aqui actualice'])
		->where('id', '3')
		->execute()
	;
	show($sql, true);
	/*
	 * Creando un insert simple
	 * */
	$sql = LQLS::create($config['db'])
		->insert('person')
		->into('name', 'age', 'id')
		->values('Maria Tusa', 12, 24)
		->compile()
	;
	//show($sql, true);
	/*
	 * Creando un insert compuesto
	 * */
	$sql = LQLS::create($config['db'])
		->insert('person')
		->into('name', 'age', 'id')
		->values('Maria Mria', 12, LQLS::create($config['db'])->select('SUM(id) as cant')->from('person'))
		->compile()
	;
	show($sql, true);
	/*
	 * Creando una consulta de eliminacion con condiciones
	 * */
	$sql = LQLS::create($config['db'])
		->delete('person')
		->where('id', 2)
		->persist()
	;
	show($sql, true);
	/*
	 * Creando una consulta para eliminar la tabla cambios
	 * */
	$sql = LQLS::create($config['db'])
		->drop('tieso')
		->execute()
	;
	echo "drop: "; var_dump($sql);
	/*
	 * Configurando el LQLS de forma general para todas las consultas
	 * */
	LQLS::setting($config['db']);
	/*
	 * Creando una consulta compuesta, simulando un flush de Doctrine 2.0
	 * */
	$sql = LQLS::create()
		->add(LQLS::create()
			->insert('person')
			->into('name', 'age', 'id')
			->values('Mustan Tusa', 12, LQLS::create()->select('SUM(id) as cant')->from('person'))
		)
		->add(LQLS::create()
			->insert('person')
			->into('name', 'age', 'id')
			->values('Mastik Tusa', 12, LQLS::create()->select('SUM(id) as cant')->from('person'))
		)
		->add(LQLS::create()
			->insert('person')
			->into('name', 'age', 'id')
			->values('Mistao Tusa', 12, LQLS::create()->select('SUM(id) as cant')->from('person'))
		)
		->add(LQLS::create()
			->update('person')
			->set(['server', 'serverid'], [45, 'LAST'])
			->where(true)
			->whereIn('name', ['Mistao Tusa', 'Mastik Tusa'])
		)
		->flush()
	;
	show($sql);
	/*
	 * Creando una consulta compuesta
	 * */
	$sql = LQLS::create()
		->select("
					u.serverid,
					u.id as uid,
					count(j.id) as work,
					sum(jobsize) as page
				")
		->addSelect("u.fname", 'table')
		->addSelect(LQLS::create()
			->select("count(j1.fid)")
			->from('mod_pykota.jobhistory j1')
			->innerJoin("mod_pykota.table u1", ' u1.id', "j1.fid")
			->where("j1.action", 'ALLOW')
			->andWhere("j1.fid = u.id")
			->andWhere('j1.serverid = u.serverid')
			->groupBy("j.fid, u.fname, u.serverid, u.id"),
			"allow"
		)
		->addSelect(LQLS::create()
			->select("count(j1.fid)")
			->from('mod_pykota.jobhistory j1')
			->innerJoin("mod_pykota.table u1", ' u1.id', "j1.fid")
			->where("j1.action", 'DENY')
			->andWhere("j1.fid = u.id")
			->andWhere('j1.serverid = u.serverid')
			->groupBy("j.fid, u.fname, u.serverid, u.id"),
			"deny"
		)
		->addSelect(LQLS::create()
			->select("count(j1.fid)")
			->from('mod_pykota.jobhistory j1')
			->innerJoin("mod_pykota.table u1", ' u1.id', "j1.fid")
			->where("j1.action", 'WARN')
			->andWhere("j1.fid = u.id")
			->andWhere('j1.serverid = u.serverid')
			->groupBy("j.fid, u.fname, u.serverid, u.id"),
			"warn"
		)
		->addSelect(LQLS::create()
			->select("count(j1.fid)")
			->from('mod_pykota.jobhistory j1')
			->innerJoin("mod_pykota.table u1", ' u1.id', "j1.fid")
			->where("j1.action", 'CANCEL')
			->andWhere("j1.fid = u.id")
			->andWhere('j1.serverid = u.serverid')
			->groupBy("j.fid, u.fname, u.serverid, u.id"),
			"cancel"
		)
		->from('mod_pykota.jobhistory j')
		->innerJoin("mod_pykota.table u ", 'u.id', "j.fid")
		->groupBy("j.fid, u.fname, u.serverid, u.id")
		->compile()
	;
	show($sql);
