<?php

Loader::import('Processor');
Loader::import('Render_Component');
Loader::import('Render_LayoutHelper');
Loader::import('Request');

class SystemProcessor extends Processor{
	public function preProcess (Request $request = null) {}
	
	public function postProcess (Request $request = null, Render_Component $rc = null) {
			    			    
	    // Add the debugging console script to the output. If we are not in debug mode then
		// add the placeholder that quashes logging requests.
		$debugpath = Config::get('debug')
				? APP_PATH . SYSTEM_PATH . PUBLIC_PATH . "js/firebug/firebug.js" 
				: APP_PATH . SYSTEM_PATH . PUBLIC_PATH . "js/firebug/firebugx.js";
		$rc->layout->js($debugpath, -10);
		
		// Add the jquery javascript.
	    $rc->layout->js(APP_PATH . SYSTEM_PATH . PUBLIC_PATH . 'js/jquery.js', -9);
	    $rc->layout->js(APP_PATH . SYSTEM_PATH . PUBLIC_PATH . 'js/jquery-ui.js', -8);
	    
	    // Add the system javascript.
	    $rc->layout->js(SYSTEM_PATH . 'app.js', -7);
	}	
}