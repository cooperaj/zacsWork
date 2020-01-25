<?php

Loader::import('Cache');
Loader::import('Config');
Loader::import('Render_LayoutHelper');
Loader::import('Render_TextHelper');

/**
 * The view layer implementation.
 * 
 * This class allows the framework to construct the view for a particular page.
 * It is responsible for all the information required to output a text page (html,xml,etc)
 * and also for outputting a binary file.
 * 
 * @author Adam Cooper <adam.cooper@nottingham.ac.uk>
 * @access public
 * @package Render
 */
class Render_Component {
    
    private static $_layoutHelper;
    
	private $_attachments = array();
	private $_helpers = array();
	private $_props = array();
	
	public $view;
	public $renderTo;
	public $mayCache;
	
    /**
	 * Constructor for the Render_Component.
	 *
	 * @param string $view The filename of the template to render.
	 * @param string $renderTo The variable name that should contain the rendered content.
	 * @param boolean $endPoint Does this component represent content that is rendered on its own.
	 * @param boolean $mayCache Should the framework cache the output of this component.
	 */	
	public function __construct($view = null, $renderTo = "content") {
        $this->view = $view;
        $this->renderTo = $renderTo;
        
        $this->_helpers = self::_loadHelpers();
        foreach ($this->_helpers as $k => $v) {
            $this->{$k} = $v;
        }
        
        $this->mayCache = true;
        
        $this->debug = Config::get('debug') ? "true" : "false";
        $this->basepath = Config::get('apppath', '/');
    }    
    
    /**
     * Render this component to a variable named by {@link renderTo}.
     */
    public function render() {
        // Do the recursive component by rendering all the children first.
        if ($this->isComposite()) {
			foreach ($this->getAttachments() as $attachment) {
			    // Render each child.
			    $attachment->render();
			    
			    // If the child is a layout then we need to copy the relevant details 
			    // into this component and then return up the render chain.
			    if (!$attachment->layout) {
			        $this->view = $attachment->view;			        
			        $this->renderTo = $attachment->renderTo;
			        $this->mayCache = $attachment->mayCache;
			        $this->_props = $attachment->_export();
			        foreach ($this->_props as $k => $v) {
			            $this->{$k} = $v;
			        }
			        return;
			    }
			    
			    // Figure out where to store the new content
			    // If we already have a child that has claimed a variable name
			    if (isset($this->{$attachment->renderTo})) {
			        // and we haven't made it capable of storing multiple entries.
			        if (!is_array($this->{$attachment->renderTo})) {
			            // turn it into an array
			            $this->{$attachment->renderTo} = array($this->{$attachment->renderTo});
			        }
			        // then add our new value.
			         $this->{$attachment->renderTo}[] = $attachment->{$attachment->renderTo};
			    } else {		
			        // Or just store it.	    
			        $this->{$attachment->renderTo} = $attachment->{$attachment->renderTo};
			    }			   
			}
		}
		
		// If a view is not defined then we should not attempt to render anything.
		if (is_null($this->view))
		    return;
		
		// If the view is defined badly then say so.
		if (!is_file($this->view))
			throw new Exception("Cannot load template: ".$this->view."\n".print_r($this, true), 500);
		
	    // Is this particular component already cached. If so we should load that instead.
		$cache = new Cache();
		$uid = Util::hash_string($this->_flattenPublicProperties());
        if ($this->mayCache && $copy = $cache->fetch($uid, 86400)) {
            $this->{$this->renderTo} = $copy; // Loaded from cache.
		} else {	
		    
		    extract($this->_helpers);
		    
		    ob_start();
       	    include $this->view;
       	    $this->{$this->renderTo} = ob_get_contents();
       	    ob_end_clean();
		    
       	    // If debug is on then don't cache the generated page.
			if (!Config::get('debug') && $this->mayCache) {
				$cache->store($uid, $this->{$this->renderTo});
			}
		}	
    }
	
	public function attach(Render_Component $child) {
		$this->_attachments[] = $child;
	}
	
	public function isComposite() {
		if (count($this->_attachments) > 0)
			return true;
			
		return false;
	}
	
	public function getAttachments() {
		return $this->_attachments;
	}
	
	/* Overloading methods */
    public function __get($nm) {
        if (isset($this->_props[$nm])) {
            return $this->{$nm};
        }
    }

    public function __set($nm, $val) {
        $this->_props[$nm] = $val;
        $this->{$nm} = $val;
    }

    public function __isset($nm) {
        return isset($this->_props[$nm]);
    }

    public function __unset($nm) {
        unset($this->_props[$nm]);
        unset($this->{$nm});
    }

    public function __toString() {
        $this->render();
        return $this->{$this->renderTo};
    }

    public function _export() {
        return $this->_props;
    }
    
    /**
     * Private helper method to flatten desired public properties into a
     * single string.
     * @return string Flattend public properties.
     */
    private function _flattenPublicProperties() {
        $tmp = array($this->assetpath, $this->view, $this->renderTo, $this->mayCache);		
		return implode('', array_merge($this->_props, $tmp));
	}

	/**
	 * Provides a singleton access method to the helper objects
	 *
	 * @return array An array of helper objects.
	 */
    private static function _loadHelpers() {
	    if (!isset(Render_Component::$_layoutHelper)) {
	        Render_Component::$_layoutHelper = Loader::instance('Render_LayoutHelper');
	    }
	    
	    $textHelper = Loader::instance('Render_TextHelper');
	    
	    return array('layout' => Render_Component::$_layoutHelper, 
	            'text' => $textHelper);
	}	
}