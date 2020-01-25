<?php

class Util {	
	
	public static function concat_array( $array ) {
		$string = "";
		foreach ( $array as $line ) {
			$string .= $line . "\n";
		}
		return $string;
	}
	
	public static function generate_action_token( $action ) { 
		unset( $_SESSION[$action . '_token'] );
		$_SESSION[$action . '_token'] = self::hash_string( uniqid( rand(), true ) . session_id() . $action );
		return $_SESSION[$action . '_token'];
	}
	
	public static function get_include_contents( $filename ) {
   		if (is_file( $filename )) {
       		ob_start();
       		include $filename;
       		$contents = ob_get_contents();
       		ob_end_clean();
       		return $contents;
   		}
   		return false;
	}
	
	public static function hash_string( $string ) {
		return hash( "md5", $string );
	}
	
	public static function merge_array( array $array0, array $array1 ) {
		
		foreach( $array1 as $k => $v ) {
			if ( array_key_exists( $k, $array0 ) ) {
				if ( !is_numeric( $k ) && !is_array( $array0[$k] ) ) {
					$array0[$k] = array( $array0[$k] );
				}
				
				if ( is_numeric( $k ) ) {
					$array0[] = $v;
				} else if ( is_array( $v ) ) {
					$array0[$k] = self::merge_array( $array0[$k], $v );
				} else {
					array_push( $array0[$k], $v );
				}
			} else {
				$array0[$k] = $v;
			}
		}
		
		return $array0;
	}
	
	public static function query_string_to_array( $string ) {
		$tokens = array();
		$tok = strtok( str_replace( '..', '', $string ), '/' );
		while ( $tok !== false ) {
			$tokens[] = $tok;
			$tok = strtok('/');
		}
		return $tokens;
	}
	
	public static function str_to_proper( $string ) {
		$string = strtolower( $string );
		$string = substr_replace( $string, strtoupper( substr( $string, 0, 1 ) ), 0, 1 );
		return $string;
	}
	
	public static function validate_action_token( $action, $token ) {
		return ( $_SESSION[$action . '_token'] == $token );
	}
	
	/*
	 * Convienience rendercomponent methods
	*/

	public static function get_a_rendercomponent ( $variables, $viewas = "site_links" ) {
		return new RenderComponent( $variables, 
			APP_PATH . VIEW_PATH . "a.view.php", $viewas );
	}
	
	public static function get_link_rendercomponent ( $path, $type = "screen", $viewas = "css" ) {
		return new RenderComponent( array( "path" => $path, "type" => $type ), 
			APP_PATH . VIEW_PATH . "link.view.php", $viewas );
	}
	
	public static function get_script_rendercomponent ( $path, $viewas = "js" ) {
		return new RenderComponent( array( "path" => $path ), 
			APP_PATH . VIEW_PATH . "script.view.php", $viewas );
	}

	public static function make_a_rendercomponent( RenderComponent &$rc, $viewas = "site_links" ) {
		$rc->viewname = APP_PATH . VIEW_PATH . "a.view.php";
		$rc->viewas = $viewas;
	}
}