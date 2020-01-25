<?php
class Request {   
    
	private $_props = array();

	public function __construct() {
	    foreach($_REQUEST as $k => $v) {
	        $this->{$k} = $v;
	    }
	}
	
	/* Overloading methods */
    public function __get($nm) {
        if (isset($this->_props[$nm])) {
            return $this->_props[$nm];
        }
    }

    public function __set($nm, $val) {
        $this->_props[$nm] = $val;
    }

    public function __isset($nm) {
        return isset($this->_props[$nm]);
    }

    public function __unset($nm) {
        unset($this->_props[$nm]);
    }
}