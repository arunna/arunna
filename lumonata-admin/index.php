<?php

		if( file_exists('../lumonata_load.php')){
			/*define('LUMONATA_ADMIN',true);*/
			$LUMONATA_ADMIN=true;
			require_once('../lumonata_load.php');
		}else{
			require_once('../lumonata-functions/error_handler.php');
			echo lumonata_die("<code>lumonata_load.php</code> File Not Found!");
		}
		
?>