<?php
/**
 * @description LQL it's alternative Light Query Language suport for PHP library
 * @author		Antonio Membrides Espinosa
 * @package    	model
 * @date		15/05/2013
 * @license    	GPL
 * @version    	1.0
 * @require		SQLDriver
 */
namespace LQL\src;
class Main
{
	public $executor;
	public $processor;	
	protected $commands;
	protected static $obj = false;

	public function __construct($executor=null, $processor=null){
		$this->commands = array();
		$this->executor = $executor ? $executor : new Executor();
		$this->processor = $processor ? $processor : new Processor();
		$this->executor->clear();
		$this->processor->clear();
	}
	public function __call($action, $arguments){
	   if(!method_exists($this, $action)){
		  $this->commands[$action][] = $arguments;
		  return $this;
	   }
	}
	
	public function setting($executor=null, $processor=null){
		$this->executor = $executor ? $executor : $this->executor;
		$this->processor = $processor ? $processor : $this->processor;
		return $this;
	}
	public function clear(){
		unset($this->commands);
		$this->commands = array();
		return $this;
	}
	
	public function compile($force=false){
		return $this->processor->compile($this->commands, $force);
	}
	public function query($data=false, $force=false){
		return $this->execute($data, $force);
	}
	public function execute($data=false, $force=false){
		return $this->executor->execute(
			$this->processor
				 ->setting($data)
				 ->compile($this->commands, $force)
		);
	}
	public function fetchAll($sql=false){
		return $this->execute();
	}
	public function fetchArray($sql=false){
		return $this->execute();
	}

	public static function create($executor=null, $processor=null){
		$executor = $executor ? $executor : (static::$obj ? static::$obj->executor : $executor);
		$processor = $processor ? $processor : (static::$obj ? static::$obj->processor : $processor);
		return new static($executor, $processor);
	}
	public static function this($executor=null, $processor=null){
		static::$obj = static::$obj ? static::$obj : static::create($executor, $processor);
		return static::$obj;
	}
}
/**
 * @description LQL Executor Iface
 * @author		Antonio Membrides Espinosa
 * @package    	model
 * @date		15/05/2013
 * @license    	GPL
 * @version    	1.0
 * @require		SQLDriver
 */
class Executor
{
	protected $cache;
	public 		function clear(){
		$this->cache = '';
	}
	public function execute($data){
		return $data;
	}
	public function setting($cache=false){ return $this;}
}
/**
 * @description LQL Processor Iface
 * @author		Antonio Membrides Espinosa
 * @package    	model
 * @date		15/05/2013
 * @license    	GPL
 * @version    	1.0
 * @require		SQLDriver
 */
class Processor
{
	protected 	$cache;
	protected 	$jobs;
	
	protected 	function evaluate($value, $quote=false){}
	protected 	function generate(&$tmp=false, $jobs=false){}
	protected 	function format($key, $value){}
	
	public 		function clear(){}
	public 		function setting($cache=false){ return $this;}
	public 		function compile($struct, $force=false){return $struct;}
}
