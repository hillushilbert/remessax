<?php

namespace Skynix\Remessax;

use Iterator;

class TituloList implements Iterator {
	
	private $var = array();
	private $del = array();
	public function __construct($array)
	{
		if (is_array($array) ) {
		  $this->var = $array;
		}
	}

	public function clean(){
		foreach($this->del as $key){
			unset($this->var[$key]);
		}
		$this->del = array();
		$this->rewind();	
	}
	
	public function rewind() {
		//echo "rewinding\n";
		reset($this->var);
	}

	public function current() {
		$var = current($this->var);
		/*
		if(is_object($var))
			echo "current: ".$var->to_string()."\n";
		else
			echo "current: ".$var."\n";
		*/
		return $var;
	}

	public function key() {
		$var = key($this->var);
		//echo "key: $var\n";
		return $var;
	}

	public function next() {
		$var = next($this->var);
		/*
		if(is_object($var))
			echo "next: ".$var->to_string()."\n";
		else
			echo "next: ".$var."\n";
		*/
		return $var;
	}

	public function valid() {
		$var = $this->current() !== false;
		//echo "valid: $var\n";
		return $var;
	}

    public function checkItemInvalido($item) {
        $find = false;
		if(isset($this->var[$item])){
			$this->del[] = $item;
			$find = true;       
        }
		if(!$find) var_dump($item);
        
    }	
	
}

?>