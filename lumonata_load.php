<?php
	if(!isset($lumonata_load)){
		$lumonata_load=true;
		
		/* @load setting here
		  	Author	: Wahya
			Since	: 1.0
		*/
		
		define( 'ABSPATH', dirname(__FILE__) . '/' );
		if( file_exists(ABSPATH . 'lumonata_config.php')){
			require_once(ABSPATH . 'lumonata_config.php');
			require_once(ABSPATH . 'lumonata_include.php');
		}elseif(file_exists(dirname(ABSPATH) . 'lumonata_config.php')){
			require_once(dirname(ABSPATH) . 'lumonata_config.php');
			require_once(dirname(ABSPATH) . 'lumonata_include.php');
		}else{
			define('TEMPLATE_URL',getcwd()); 
			require_once(ABSPATH.'/lumonata-functions/error_handler.php');
			require_once(ABSPATH.'/lumonata-functions/template.php');		
			echo lumonata_die("Config File Not Found!");
		}
		
		
			
		/* @load template here
			Author	: Wahya
			Since	: 1.0
		*/
		
	}
?>