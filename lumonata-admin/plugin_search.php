<?php

    define('PLUGINS_PATH','../lumonata-plugins');
    
    require_once('../lumonata_config.php');
    require_once('../lumonata-functions/kses.php');
    require_once('../lumonata-functions/settings.php');
    require_once('../lumonata-functions/plugins.php');
    require_once('../lumonata-classes/directory.php');
    require_once('../lumonata-functions/user.php');
   
    if(!is_user_logged()){
	    header("location:".get_admin_url()."/?state=login");
    }else{
        echo search_plugin($_POST['start'],$_POST['end']);
    } 
?>