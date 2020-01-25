<?php 

Loader::import('Exception_E');

abstract class Controller {
	
    protected $pluginName;
	protected $controllerPath;
	protected $viewPath;
	protected $assetPath;
	
	protected $component;
	
	function __construct() {
	    if (is_null($this->pluginName)) 
	        throw new Exception_E('Unable to discover controller name. $pluginName has not been defined', 500);    
	    
		$this->controllerPath = APP_PATH . SYSTEM_PATH;
		if ( $this->pluginName != 'system' ) {		
			$this->controllerPath = PLUGIN_PATH . $this->pluginName . '/';		
		}
				
		$this->viewPath = $this->controllerPath . VIEW_PATH;
		$this->assetPath = $this->controllerPath . PUBLIC_PATH;
	}

	abstract public function action(Request $request);
	
	public function getRenderComponent() {
		return $this->component;
	}
}