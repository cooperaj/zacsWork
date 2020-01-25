<?php

class Cache {
	public function fetch( $uid, $expires = 0 ) {
		if ( file_exists( APP_PATH . CACHE_PATH . $uid ) && 
				( filemtime( APP_PATH . CACHE_PATH . $uid ) > ( time() - $expires ) ) ) {
			return unserialize( implode( "", file( APP_PATH . CACHE_PATH . $uid ) ) );
		} else {
			return false;
		}
	}
	
	public function store( $uid, $item ) {
		$fp = APP_PATH . CACHE_PATH . $uid;
        if ( file_put_contents( $fp, serialize( $item ) ) === false ) {
        	throw new Exception( "Could not write to cache file at $fp", 500 );
        } 
	}	
	
	public function purge( $uid = "all" ) {
		if ( $uid != "all" ) {
		 	if ( file_exists( APP_PATH . CACHE_PATH . $uid ) ) {
		 		unlink( APP_PATH . CACHE_PATH . $uid );
			}			
		} else {
			$dir = opendir( APP_PATH . CACHE_PATH );
			while ( $f = readdir( $dir ) ) {
            	if ( $f == '.' || $f == '..' )
                	continue;
            	if ( !is_dir( APP_PATH . CACHE_PATH . $f ) )
                	unlink( APP_PATH . CACHE_PATH . $f );
        	}
		}
	}
}