<?php

	if ( !defined('MEMORY_LIMIT') )
	define('MEMORY_LIMIT', '128M');

	if ( function_exists('memory_get_usage') && ( (int) @ini_get('memory_limit') < abs(intval(MEMORY_LIMIT)) ) )
	@ini_set('memory_limit', MEMORY_LIMIT);
	
	//turn off magic quotes
	//set_magic_quotes_runtime(0);
	@ini_set("magic_quotes_runtime", 0);
	@ini_set('magic_quotes_sybase', 0);
	
	
	
	/*TURN OFF GLOBAL VARIABEL*/
	function unregister_GLOBALS_VARS(){
		if ( !ini_get('register_globals') )
			return;

		if ( isset($_REQUEST['GLOBALS']) )
			die('GLOBALS overwrite attempt detected');

		// Variables that shouldn't be unset
		$noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES', 'table_prefix');

		$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
		foreach ( $input as $k => $v )
			if ( !in_array($k, $noUnset) && isset($GLOBALS[$k]) ) {
				$GLOBALS[$k] = NULL;
				unset($GLOBALS[$k]);
			}
	}
	unregister_GLOBALS_VARS();
	
	
	
	
	// Fix empty PHP_SELF
	$PHP_SELF = $_SERVER['PHP_SELF'];
	if ( empty($PHP_SELF) )
		$_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace("/(\?.*)?$/",'',$_SERVER["REQUEST_URI"]);
	
	// Change the ERR_DEBUG=true in lumonata-config.php to show the error durring the Developments
	if (defined('ERR_DEBUG') and ERR_DEBUG == true) {
		//error_reporting(E_ERROR);	
		error_reporting(E_ALL);	
	}else {
		error_reporting(0);	
	}
	
	/* 	
		Define JSON Function if PHP Version older that PHP 5.2.0 
	*/
	if (!function_exists('json_decode') ){
    	function json_decode($content, $assoc=false){ //JSON decode function
        	require_once(ROOT_PATH."/lumonata-classes/json.php");
            if ( $assoc ){
            	$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        	}else{
                 $json = new Services_JSON;
            }
        	return $json->decode($content);
		}
	}
	
	if (!function_exists('json_encode') ){
		function json_encode($content){ //JSON encode function
			require_once(ROOT_PATH."/lumonata-classes/json.php");
			$json = new Services_JSON;	   
			return $json->encode($content);
		}
	}
	
	
	
?>