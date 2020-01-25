<?php 

Loader::import('Config');
Loader::import('Controller');
Loader::import('Render_Component');
Loader::import('Render_LayoutHelper');
Loader::import('Request');

class IndexController extends Controller {
    
    protected $pluginName = 'index';
    
	public function action(Request $request) {
	    $this->component = new Render_Component( $this->controllerPath. VIEW_PATH . 'index.view.php' );
		$this->component->layout->title = $_SERVER['HTTP_HOST'] . ' is under contruction';
		$this->component->layout->sitename = $_SERVER['HTTP_HOST'];
		
		$this->component->sitename = $_SERVER['HTTP_HOST'];
	}
}