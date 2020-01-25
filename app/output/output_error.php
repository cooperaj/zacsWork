<?php
switch($rc->errorcode) {
	case '404' :
		header("HTTP/1.x 404 Not Found");
		header("Status: 404 Not Found");
		echo ("<h1>HTTP/1.x 404 Not Found</h1>\n");
		echo ("<p>Sorry, we cannot find the specified file.</p>\n");
		if ( $rc->errormessage && Config::get('debug') ) {
			echo("<pre>Error Message\n-------------\n");
			echo("$rc->errormessage in $rc->errorfile\n");
			echo("$rc->errortrace");
			echo("</pre>");
		}
		break;
	case '500' :
		header("HTTP/1.x 500 Internal Server Error");
		header("Status: 500 Internal Server Error");
		echo ("<h1>HTTP/1.x 500 Internal Server Error</h1>\n");
		echo ("<p>Sorry, I have encountered an error whilst processing this request.</p>\n");
		if ( $rc->errormessage && Config::get('debug') ) {
			echo("<pre>Error Message\n-------------\n");
			echo("$rc->errormessage in $rc->errorfile\n");
			echo("$rc->errortrace");
			echo("</pre>");
		}
		break;
}