<?php

Loader::import('LookAndFeel');

class IndexLookAndFeel extends LookAndFeel {

	public function __construct() {
		parent::__construct('index');

		$this->rootComponent->layout->css($this->lnfpath. PUBLIC_PATH . 'style.css');
		$this->rootComponent->layout->assetpath = $this->lnfpath. PUBLIC_PATH;
	}

}
