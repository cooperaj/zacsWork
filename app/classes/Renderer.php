<?php 

Loader::import('Config');
Loader::import('Exception_E');
Loader::import('Render_Component');

class Renderer {
	const TEXT = "Text";
	const BINARY = "Binary"; 
	const ERROR = "Error";
	const REDIRECT = "Redirect";

	/* 
	*  The primary method of the renderer. All calls to render should come through this
	*  method. Access to the output methods are left open though just incase.
	*  @param $component The render component object that contains the tree of things to render.
	*/
	public function render(Render_Component $component) {
	    $component->render();
		
	    // Set an output type based on the defined mime type. Defaults to "text"
		$mime = isset($component->mime) ? 
				$this->mimeRenderLookup($component->mime) :
				Renderer::TEXT;
			
		// Check we are rendering text and not an error. Act appropriately.
		$mime = isset($component->errorcode) ? Renderer::ERROR : $mime;
		
		// Check that there is a redirect request. Act appropriately.
		$mime = isset($component->redirecturl) ? Renderer::REDIRECT : $mime;
		
		// Call the appropriate output method
		call_user_func(array(&$this, "output".$mime), 
		        $component, $component->mayCache);	
        
	}

	public function outputText(Render_component $rc, $mayCache = true) {	
        if (!isset($rc->content))
			throw new Exception_E("Unable to determine content to display", 500);    
	    
		if (!$mayCache)
			$this->noCache();
					
		$rc->mime = isset($rc->mime) ? $rc->mime : "text/html; charset=UTF-8";
		
		require_once APP_PATH . OUTPUT_PATH . "output_text.php";
		
		exit;
	}
	
	public function outputBinary(Render_component $rc, $mayCache = true) {
		if (!isset($rc->path) || !isset($rc->mime))
			throw new Exception_E("Unable to determine file location or mimetype", 500);	
			
		if (!$may_cache)
			$this->noCache();
		
		require_once APP_PATH . OUTPUT_PATH . "output_binary.php";
		
		exit;
	}
	
	public function outputError(Render_component $rc) {			
		if (!isset($rc->errormessage) || !isset($rc->errorcode)) {
			$rc->errormessage = null;
			$rc->errorcode = 500;
		}
		
		require_once APP_PATH . OUTPUT_PATH . "output_error.php";
		
		exit;
	}
	
	public function outputRedirect(Render_component $rc) {
		if (!isset($rc->redirecturl)) {
			throw new Exception_E("Redirect url not specified\n".print_r($rc, true), 500);
		}
		
		if (!isset($rc->redirectcode)) {
			$rc->redirectcode = 302;
		}
			
		require_once APP_PATH . OUTPUT_PATH . "output_redirect.php";
		
		exit;
	}
	
	private function noCache() {
		header("Pragma: no-cache");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0, no-transform");
	}
	
	private function mimeRenderLookup( $mimetype ) {
		if ( array_key_exists( $mimetype, Config::getMimetypes() ) ) {
			$array = Config::getMimetypes();
			return $array[$mimetype];
		}
			
		return Renderer::TEXT;
	}
}