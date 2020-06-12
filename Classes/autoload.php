<?php

spl_autoload_extensions(".php");

spl_autoload_register(function ($path) {

	if( preg_match('/\\\\/', $path) )
		$path = "Classes". DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $path);

	if ( file_exists("{$path}.php") )
		require_once ("{$path}.php");
});



