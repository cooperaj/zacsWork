<?php
abstract class Processor {
	abstract public function preProcess (Request $request = null);
	abstract public function postProcess (Request $request = null, 
			Render_Component $rc = null);
}