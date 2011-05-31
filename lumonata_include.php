<?php
	
	define('PLUGINS_PATH',ROOT_PATH.'/lumonata-plugins');
	define('APPS_PATH',ROOT_PATH.'/lumonata-apps');
	define('FUNCTIONS_PATH',ROOT_PATH.'/lumonata-functions');
	define('CLASSES_PATH',ROOT_PATH.'/lumonata-classes');
	define('ADMIN_PATH',ROOT_PATH.'/lumonata-admin');
	define('CONTENT_PATH',ROOT_PATH.'/lumonata-content');
	
	
	require_once(ROOT_PATH."/lumonata-classes/admin_menu.php");
	
	require_once(ROOT_PATH."/lumonata-classes/user_privileges.php");
	require_once(ROOT_PATH."/lumonata-admin/admin_functions.php");
	require_once(ROOT_PATH."/lumonata-functions/themes.php");
	require_once(ROOT_PATH."/lumonata-functions/kses.php");
	require_once(ROOT_PATH."/lumonata-classes/directory.php");
	require_once(ROOT_PATH."/lumonata-functions/paging.php");
	require_once(ROOT_PATH."/lumonata_settings.php");
	require_once(ROOT_PATH."/lumonata-functions/settings.php");
	require_once(ROOT_PATH."/lumonata-functions/mail.php");
	require_once(ROOT_PATH."/lumonata-functions/rewrite.php");
	require_once(ROOT_PATH.'/lumonata-functions/upload.php');
	require_once(ROOT_PATH."/lumonata-content/languages/".is_language('en').".php");
	require_once(ROOT_PATH."/lumonata-classes/post.php");
	require_once(ROOT_PATH."/lumonata-functions/articles.php");
	require_once(ROOT_PATH."/lumonata-classes/actions.php");
	require_once(ROOT_PATH."/lumonata-functions/notifications.php");
	require_once(ROOT_PATH."/lumonata-functions/taxonomy.php");
	require_once(ROOT_PATH."/lumonata-functions/plugins.php");
	require_once(ROOT_PATH."/lumonata-functions/comments.php");
	require_once(ROOT_PATH."/lumonata-functions/feeds.php");
	require_once(ROOT_PATH."/lumonata-functions/menus.php");
	require_once(ROOT_PATH."/lumonata-functions/friends.php");
	
	if(!defined('SITE_URL'))
		define('SITE_URL',get_meta_data('site_url'));
	
	$SINGLE_FILE=false;
	/*SMTP SERVER */
	define('SMTP_SERVER',get_meta_data('smtp_server'));
	
	$table_prefix  = 'lumonata_';
	
	/*SET TIMEZONE*/
	set_timezone(get_meta_data('time_zone'));
	 
	
	require_once(ROOT_PATH."/lumonata_functions.php");
	
	//set the template that used
	
	require(ROOT_PATH."/lumonata_themes.php");
	
	
	
?>