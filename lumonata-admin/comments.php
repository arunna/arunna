<?php
    require_once('../lumonata_config.php');
    require_once('../lumonata_settings.php');
    require_once('../lumonata-functions/settings.php');
    require_once('../lumonata-classes/actions.php');
    require_once('../lumonata-functions/upload.php');
    require_once('../lumonata-functions/attachment.php');
    require_once('../lumonata-classes/directory.php');
    require_once('../lumonata-functions/user.php');
    require_once('../lumonata-functions/paging.php');
    require_once('../lumonata-content/languages/en.php');
    require_once('admin_functions.php');
    require_once('../lumonata-classes/user_privileges.php');
    require_once('../lumonata-functions/comments.php');
    require_once('../lumonata-functions/taxonomy.php');
    
    if(!defined('SITE_URL'))
		define('SITE_URL',get_meta_data('site_url'));
    
    if(!is_user_logged()){
	    header("location:".get_admin_url()."/?state=login");
    }elseif(is_delete($_POST['state'])){
        if(!delete_comment($_POST['id']))
            echo "<div class=\"alert_red_form\">Deleting process failed.</div>";
    }elseif(is_approved()){
    	if(!update_comment_status($_POST['id'],'approved')){
            echo "<div class=\"alert_red_form\">Process failed.</div>";
    	}else{
        	echo count_comment_status("moderation")."|".count_comment_status("approved");	
        }
    }elseif(is_disapproved())  {
    	if(!update_comment_status($_POST['id'],'moderation')){
            echo "<div class=\"alert_red_form\">Process failed.</div>";
    	}else{
        	echo count_comment_status("moderation")."|".count_comment_status("approved");	
        }
    }else{
    	$comment=fetch_comment($_POST['id']);
    	if($comment['luser_id']==$_COOKIE['user_id']){
    		 if(!delete_comment($_POST['id']))
            	echo "<div class=\"alert_red_form\">Deleting process failed.</div>";
    	}
    }
?>   