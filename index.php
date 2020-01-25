<?php
// Load the class loader.
require_once("app/classes/Loader.php");
Loader::import('Bootstrap');

// Bootstrap the system.
Bootstrap::init();