<?php

Loader::import('Render_Component');

class Render_LayoutHelper {
    
    const DEFAULT_WEIGHT = 0;
	
	private $_props = array();
	
	private $_js = array();
    private $_css = array();
	
	public function js($path = '', $weight = self::DEFAULT_WEIGHT) {
	    if ($path != '') {
	        $rc = new Render_Component( APP_PATH . VIEW_PATH . 'script.view.php', 'js' );
	        $rc->path = $path;
	        $this->_js[$weight . count($this->_js)] = $rc;
	    } else {
	        $str = '';
	        ksort($this->_js);
	        foreach($this->_js as $rc) {
	            $rc->render();
	            $str .= $rc->{$rc->renderTo} . "\n";
	        }
	        return $str;
	    }
	}
	
	public function css($path = '', $type = 'screen', $weight = self::DEFAULT_WEIGHT) {
	    if ($path != '') {
	        $rc = new Render_Component( APP_PATH . VIEW_PATH . 'link.view.php', 'css' );
	        $rc->path = $path;
	        $rc->type = $type;
	        $this->_css[$weight . count($this->_css)] = $rc;
	    } else {
	        $str = '';	        
	        ksort($this->_css);
	        foreach($this->_css as $rc) {
	            $rc->render();
	            $str .= $rc->{$rc->renderTo} . "\n";
	        }
	        return $str;
	    }
	}
	
	public function linkTo($path = '', $text = '', $options = array()) {
	    // TODO Do something with the options
	    $tmp = new Render_Component(APP_PATH . VIEW_PATH . 'a.view.php');
	    $path != '' ? $tmp->path = $path : false;
	    $text != '' ? $tmp->text = $text : false;
	    return $tmp;
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

    public function __unset($nm){
        unset($this->_props[$nm]);
        unset($this->{$nm});
    }
	
    public function __toString() {
        return get_class($this);
    }
    
}