<?php

class Attributes{

	public $data = array();

	public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
    	if(array_key_exists($name, $this->data)){
    		return $this->data[$name];
    	}
    	return "";
    }

    public function __isset($name){
    	return array_key_exists($name, $this->data);
    }

    public function getHTML($name, $replaceEnterToBr = true){
    	$value = "";
    	if(array_key_exists($name, $this->data)){
    		$value = escapeQuote($this->data[$name]);
    	}

    	if ($replaceEnterToBr){
    		$value = str_replace("\r\n","<br />", $value);
    	}

    	return $value;
    }

    public function getKeys(){
    	return array_keys($this->data);
    }

}

?>