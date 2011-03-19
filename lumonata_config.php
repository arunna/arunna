<?	
	/*
		Define the database setting here
	*/	
	
	/*MySQL Hostname*/
	define('HOSTNAME','hostname');
	/*MySQL Database User Name*/
	define('DBUSER','dbuser');
	/*MySQL Database Password*/
	define('DBPASSWORD','dbpassword');
	/*MySQL Database Name*/
	define('DBNAME','dbname');
		
	define('ERR_DEBUG',true);
		
	if(!defined('ROOT_PATH'))
		define('ROOT_PATH',dirname(__FILE__));
		
	require_once(ROOT_PATH."/lumonata-functions/error_handler.php");			
	require_once(ROOT_PATH."/lumonata-classes/db.php");
?>