<?php

class Exception_E extends Exception {
	
	private $source;
	
	public function __construct( $message, $code = 0, Exception $e = null ) {
		$this->source = $e;
		parent::__construct( $message, $code );
	}
	
	public function getFullTraceAsString() {
	    if(isset($this->source)) {
		    $trace = "\n\nCaused by\n" . $this->source->getMessage() . " in " . 
		            $this->source->getFile() . "\n" .
				    ( ( $this->source instanceof Exception_E ) 
				            ? $this->source->getFullTraceAsString()
				            : $this->source->getTraceAsString() );	
	    } else {
	        $trace = '';	
	    }
		
		return $this->getTraceAsString() . $trace;
	}
}