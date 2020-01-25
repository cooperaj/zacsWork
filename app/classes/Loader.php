<?php

define( "APP_PATH", "app/" );
define( "CACHE_PATH", "cache/" );
define( "CLASSES_PATH", "classes/" );
define( "CONFIG_PATH", "config/" );
define( "LIB_PATH", "lib/" );
define( "PLUGIN_PATH", "plugins/" );
define( "PUBLIC_PATH", "public/" );
define( "OUTPUT_PATH", "output/" );
define( "SYSTEM_PATH", "system/" );
define( "VIEW_PATH", "view/" );

class Loader {
    
    private static $_instance;    
    private $_classPath;
    private $_loadedClasses;
       
    private function __construct() {
        $this->_classPath = array();
        $this->_loadedClasses = array();
        
        // Add our application classes directory
        $this->_classPath[] = APP_PATH . CLASSES_PATH;
    }
    
    private static function _getLoader() {
        if(!isset(self::$_instance)) {
            self::$_instance = new Loader();
        }
        
        return self::$_instance;
    }
    
    public static function addClassPath($path) {
        if(is_dir($path)) {
            self::_getLoader()->_classPath[] = $path;
        } else {
            throw new Exception("The path '$path' is not a directory", 500);
        }
    }
    
    public static function addIncludePath($path) {
        if(is_dir($path)) {
            set_include_path($path . PATH_SEPARATOR . get_include_path());
        } else {
            throw new Exception("The path '$path' is not a directory", 500);
        }
    }
    
    public static function import($name, $scope = null, $search = true) {
        if(in_array($name . $scope, self::_getLoader()->_loadedClasses)) return true;
        
        $name = str_replace('_', DIRECTORY_SEPARATOR, $name) . '.php';
        
        if ($search) {
            foreach(self::_getLoader()->_classPath as $path) {
                if(is_file($path . $name) && 
                        (($scope != null) ? stristr($path . $name, $scope) : true)) {
                            
                    require_once($path . $name);
                    self::_getLoader()->_loadedClasses[] = $name . $scope;
                    
                    return true;
                }
            }
        } else {
            require_once($name);
            self::_getLoader()->_loadedClasses[] = $name . $scope;
                
            if (class_exists(basename($name))) return true;
        }
        
        return false;
    }    
    
    /**
     * Super magic method that allows you to safely instantiate a Class using a 
     * classname, with a scope if necessary, whilst passing in an array of parameters
     * to be passed as actual parameters (not an array) to the classes constructor.
     * 
     * i.e. calling:
     * Loader::instance('AClass', 'MyClasses', array('parameter1', 'parameter2'));
     * 
     * Will return an instance of 'AClass' equivalent to calling:
     * $class = new AClass('parameter1, 'parameter2');
     *
     * @param string $name The classname of the class you wish to instantiate
     * @param string $scope A scope that you wish to apply to the classes path
     * @return stdClass The class object that you have requested.
     */
    public static function instance($name, $scope = null, $search = true) {
        if (self::import($name, $scope, $search)) {
            if ( !class_exists( $name ) )
                throw new Exception("Could not find class in list of available classes", 500);	

            $numargs = func_num_args();
            $argstring='';
            if ($numargs > 2) {
                $arg_list = func_get_args();
    
                for ($x = 2; $x < $numargs; $x++) {
                    $argstring .= '$arg_list['.$x.']';
                    if ($x != $numargs - 1) $argstring .= ',';
                }
            }       
                
            if ($argstring) return eval("return new $name($argstring);");
            return new $name;
        }
    }
}
