<?php

Loader::import('Exception_E');

class Session {
    
    private static $instance;

	private function __construct() {
	    session_start();
	}
	
	public static function getSession() {
	    if (!isset(self::$instance))
	        throw new Exception_E('Session has not been bootstraped correctly', 500);
	        
	    return self::$instance;
	}
	
	public static function start() {
	    if (!isset(self::$instance))
	        self::$instance = new Session();
	}
	
	/* Overloading methods */
    public function __get($nm) {
        if (isset($_SESSION[$nm])) {
            return $_SESSION[$nm];
        }
    }

    public function __set($nm, $val) {
        $_SESSION[$nm] = $val;
    }

    public function __isset($nm) {
        return isset($_SESSION[$nm]);
    }

    public function __unset($nm) {
        unset($_SESSION[$nm]);
    }    
}
