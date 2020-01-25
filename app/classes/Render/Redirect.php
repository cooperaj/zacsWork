<?php

Loader::import('Render_Component');

class Render_Redirect extends Render_Component {
	
	public function __construct($redirecturl, $redirectcode = 301) {
	    
	    // Construct the parent object.
	    parent::__construct();
	    
	    // Stop the rendering chain at this object.
	    $this->layout = false;
	    
	    // Set up the redirect specific stuff.
	    $this->redirecturl = $redirecturl;
	    $this->redirectcode = $redirectcode;	    
	}	
}