<?php

Loader::import('Render_Component');

abstract class LookAndFeel {
	
	protected $rootComponent;
	protected $lnfpath;
	
	function __construct($name = "") {		
		$this->lnfpath = APP_PATH . SYSTEM_PATH;
		if ($name != "system") {		
			$this->lnfpath = PLUGIN_PATH . $name . "/";			
		}
				
		// Set up root RenderComponent and app specific render components
		$this->rootComponent = new Render_Component( 
				$this->lnfpath . VIEW_PATH . "template.view.php" );
	    $this->rootComponent->lnfpath = $this->lnfpath;
				
	}
	
	function attach(Render_Component $attachment) {
		$this->rootComponent->attach($attachment);
	}
	
	function getRoot() {
		return $this->rootComponent;
	}
	
	function getPath() {
		return $this->lnfpath;
	}
}
