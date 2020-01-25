<?php
// public/cliserver.php (router script)

if (php_sapi_name() !== 'cli-server') {
    die('this is only for the php development server');
}

$root = __DIR__;

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$page = trim($uri, '/');   

if (file_exists("$root/$page") && is_file("$root/$page")) {
    return false; // serve the requested resource as-is.
    exit;
}

$_SERVER['PHP_SELF'] = '/index.php';
$_REQUEST['p'] = $page;

require_once 'index.php';