<?php

Loader::import('Cache');
Loader::import('Database');
Loader::import('Util');

class Config {
	
	private static $configfile = "application.xml";
	
	private static $instance;
	
	private $hashmap = array();
	
	private $plugins = array();
	private $processors = array();
	private $databases = array();
	private $mimetypes = array();
	
	private function __construct() {
		$xmldata = @simplexml_load_file( 
				APP_PATH . CONFIG_PATH . self::$configfile );
		
		if ( !$xmldata )
			throw new Exception( 'Cannot load configuration file at ' . 
                    APP_PATH . CONFIG_PATH . self::$configfile .  
                    ' This is a fatal error.', 500 );
		
		// Load the path that the app is at.
		$this->hashmap['apppath'] = str_replace( basename( $_SERVER['PHP_SELF'] ), '', $_SERVER['PHP_SELF'] );
		
		// Load the default action
		$this->hashmap['defaultpath'] = (string) $xmldata->defaultpath;
		if ( empty( $this->hashmap['defaultpath'] ) ) $this->hashmap['defaultpath'] = 'system/index';
		
		// Load the L'N'F
		$this->hashmap['lookandfeel'] = (string) $xmldata->lookandfeel;
		if ( empty( $this->hashmap['lookandfeel'] ) ) $this->hashmap['lookandfeel'] = 'system';
		
		// Are we in debug mode.
		$this->hashmap['debug'] = (string) $xmldata->debug;
	    if ( $this->hashmap['debug'] == "on" ) {
	        $this->hashmap['debug'] = true;
	    } else {
	        $this->hashmap['debug'] = false;
	    }
		
		// Populate plugins registry with the system plugin.
		$system_plugin['lnf'] = 'SystemLookAndFeel';
		$system_plugin['controller'] = 'SystemController';			
		$this->plugins['system'] = $system_plugin;
		$this->processors['SystemProcessor'] = array();
		
		// Load the plugin-centric variables we need from the config file
		foreach ( $xmldata->pluginregistry->plugin as $plugin ) {
			$tmp = array();
			
			//Load the description
			$tmp['description'] = (string) $plugin['description'];
			
			// Load the plugins controller
			if ( isset( $plugin->controller ) ) {
				$tmp['controller'] = (string) $plugin->controller['class'];
			}
			
			// Load the plugins look and feel
			if ( isset( $plugin->lnf ) ) {
				$tmp['lnf'] = (string) $plugin->lnf['class'];
			}
			
			// Load the plugins processors
			foreach ( $plugin->processor as $processor ) {
				$option = array();
				foreach ( $processor->config as $config ) {
					$option[(string) $config['option']] = (string) $config;
				}
				$this->processors[(string) $processor['class']] = $option;
			}	
				
			
			$this->plugins[(string) $plugin['name']] = $tmp;
		}
		
		// Load the databases
		foreach ( $xmldata->databaseregistry->database as $database ) {			
			$tmp = new Database(
				(string) $database->credentials["username"],
				(string) $database->credentials["password"],
				(string) $database->location["host"],
				(string) $database->location["db"],
				(string) $database["type"]
			);		
			
			$this->databases[(string) $database["id"]] = $tmp;
		}
		
		// Load the mimetypes.
		foreach ( $xmldata->mimetypemapping->mime as $mime ) {
			$this->mimetypes[(string) $mime] = (string) $mime["type"];	
		}		
	}
	
	private static function _getConfig() {
		$cache = new Cache();
		if ( empty( self::$instance ) ) {
			if ( $config = $cache->fetch( Util::hash_string( self::$configfile ), 86400 ) ) {
				if ( $config instanceof Config ) 
					self::$instance = $config;
			} else {
				self::$instance = new Config();
				
				if ( !self::$instance->hashmap['debug']) {
					$cache->store( Util::hash_string( self::$configfile ), self::$instance );
				} else {
					$cache->purge( Util::hash_string( self::$configfile ) );
				}
			}
		}
		
		return self::$instance;
	}
	
	public static function get($name, $default = null) {
	    if(isset(self::_getConfig()->hashmap[$name])) {
	        return self::$instance->hashmap[$name];
	    } else {
	        return $default;
	    }
	}
	
	public static function getPlugins() {
		return self::_getConfig()->plugins;
	}
	
	public static function getProcessors() {
		return array_keys( self::_getConfig()->processors );
	}
	
	public static function getProcessorConfig(Processor $processor) {
		return self::_getConfig()->processors[get_class( $processor )];
	}
	
	public static function getDatabase( $id ) {
		if ( !isset( self::_getConfig()->databases[$id] ) )
			throw new Exception( "Database connection not defined", 500 );
		
		return self::_getConfig()->databases[$id];
	}
	
	public static function getMimetypes() {
		return self::_getConfig()->mimetypes;
	}
}