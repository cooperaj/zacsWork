<?php 

Loader::import('Config');
Loader::import('Controller');
Loader::import('Render_Component');
Loader::import('Render_LayoutHelper');
Loader::import('Render_Redirect');
Loader::import('Request');

class SystemController extends Controller {
    
    protected $pluginName = 'system';
    
    public function actionAppJs(Request $request) {
	    $this->component = new Render_Component( $this->controllerPath. VIEW_PATH . 'app.js.view.php' );
	    $this->component->layout = false;
		$this->component->mime = 'application/javascript';	
    }
	
    public function actionIndex(Request $request) {
		if ( Config::get('debug') ) {
		    $this->component = new Render_Component( $this->controllerPath. VIEW_PATH . 'system.view.php' );
			$this->component->layout->title = 'zacsWork - Congratulations, you have successfully installed zacsWork';
			$this->component->layout->sitename = 'zacsWork';
		} else {
	        $this->component = new Render_Redirect(Config::get('apppath'));
		}
	}
    
	public function action(Request $request) {
	    $this->component = new Render_Redirect(Config::get('apppath'));
	}
}