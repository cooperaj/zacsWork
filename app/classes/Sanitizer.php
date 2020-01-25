<?php
class Sanitizer {

    public static function processQuery($queryStringToProcess) {
        $tokens = array();
		$tok = strtok( str_replace( '..', '', $queryStringToProcess ), '/' );
		while ( $tok !== false ) {
			$tokens[] = $tok;
			$tok = strtok('/');
		}
		
		if (isset($tokens[1])) $tokens[1] = 
		        self::sanitizeCamelCase(self::sanitizeAlphaNumeric($tokens[1]));
		
		return $tokens;
    }
    
	public static function sanitizeAlphaNumeric($stringToSanitize) {
	    return strtolower(preg_replace('/[^A-Za-z0-9]/', ' ', $stringToSanitize));
	}
    
    public static function sanitizeCamelCase($stringToSanitize) {
		return str_replace(' ', '', ucwords($stringToSanitize));
	}
	
	public static function validateEmail($stringToValidate) {
	    return eregi('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$', $stringToValidate);
	}
}