<?php 

Loader::import('Config');
Loader::import('Controller');
Loader::import('Exception_E');
Loader::import('Renderer');
Loader::import('Render_Component');
Loader::import('Request');
Loader::import('Sanitizer');
Loader::import('Session');

class Bootstrap {
		
	private static $lookAndFeel;
		
	private static $request;
	
	private static $plugins;
	private static $processors;
	private static $renderer;
	
	public static function init() {
		
		// Load system plugin
		Loader::addClassPath(APP_PATH . SYSTEM_PATH . CLASSES_PATH);
		
		// Get all the plugins that need to be loaded and add their paths
		// to the classpath
		self::$plugins = Config::getPlugins();
		foreach ( array_keys( self::$plugins ) as $plugin ) {
		    try {
		        // Load the plugins classpath into the loader.
		        if ($plugin . '/' != SYSTEM_PATH)
			        Loader::addClassPath(PLUGIN_PATH . $plugin . "/" . CLASSES_PATH);
		    } catch(Exception $e) {
		        // This is a non fatal error and we are too early to build a nice output
		        // so just dump it to the servers error_log.
		        trigger_error($e->getMessage(), E_USER_WARNING);
		    }
		}
		
	    // Set up the request object.
	    self::$request = Loader::instance('Request');
				
		// Create a processors array to store our processing objects.
		self::$processors = array();
		
		// Set up our global rendering object.
		self::$renderer = Loader::instance('Renderer');
		
		// Kick off the application
		self::handle_request();
	}
	
	public static function handle_request() {
		try {
			// Start session
			Session::start();
			
    		// Check that we have a path to access. If we don't, i.e. $p = '' then set 
    		// the default path as defined in the configuration xml.
    		if (self::$request->p == '') 
    		    self::$request->p = Config::get('defaultpath');
    		    
    		// Pull our path apart and stick the bits we want into the request.
    		$tokens = Sanitizer::processQuery(self::$request->p);
    		self::$request->controllerName = array_shift($tokens);
    		self::$request->actionName = isset($tokens[0]) ? array_shift($tokens) : '';
    		self::$request->actionParameters = isset($tokens[0]) ? $tokens : array();
			
			// Create a lookAndFeel object. This handles the setting up of the root
			// Render_Component object.
			$lnfplugin_name = Config::get('lookandfeel');
			$lnfclassname = self::$plugins[$lnfplugin_name]["lnf"];
			self::$lookAndFeel = Loader::instance($lnfclassname, null, Config::get('lookandfeel'));
			
			// Run through the processing sequence.
			self::_preProcess();
			self::_process();
			self::_postProcess();
			self::_render();
				
		} catch ( Exception_E $e ) {
			$error = new Render_Component();
			$error->errormessage = $e->getMessage(); 
			$error->errorcode = $e->getCode();
			$error->errorfile = $e->getFile();
			$error->errortrace = $e->getFullTraceAsString();
			self::$renderer->render($error);
		}				
	}
	
	private static function _preProcess() {
	    // Instantiate all processors and keep them for the post process phase.
		foreach ( Config::getProcessors() as $processor ) {
			try {
				self::$processors[$processor] = Loader::instance($processor);
			} catch( Exception_E $e ) {
				throw new Exception_E( 'Could not load plugin', 500, $e );
			}
			
			// for speed carry out processing within discovery loop
			self::$processors[$processor]->preProcess(self::$request);
		}
	}
	
	private static function _process() {
		$classname = self::$plugins[self::$request->controllerName]['controller'];
		
		if ( !isset( $classname ) ) 
			throw new Exception_E('Plugin has not been defined', 404);		
			
		if (!Loader::import($classname))
		    throw new Exception_E('Plugin can not be found in the Classpath', 500);
		
		$pluginClass = new ReflectionClass($classname);		
		if (!$pluginClass->isSubclassOf('Controller')) 
		    throw new Exception_E("Plugin does not extend Controller class", 500);
		    
		if (!$pluginClass->hasMethod('action' . self::$request->actionName))	{	
		    $pluginMethod = $pluginClass->getMethod('action');    
		} else {		    
		    $pluginMethod = $pluginClass->getMethod('action' . self::$request->actionName);
		}
		    
	    if ($pluginMethod->isPublic()) {	        
	        $controller = Loader::instance($classname);
	        $pluginMethod->invoke($controller, self::$request); 
	        self::$lookAndFeel->attach($controller->getRenderComponent());		        
	    } else {
	        throw new Exception_E("Action has not been defined correctly", 500);	    
	    }
	}
	
	private static function _postProcess() {
		foreach (self::$processors as $processor) {
			$processor->postProcess(self::$request, self::$lookAndFeel->getRoot());
		}
	}
	
	private static function _render() {
		self::$renderer->render(self::$lookAndFeel->getRoot());	
	}	
}

