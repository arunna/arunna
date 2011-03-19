<?php
    require_once('../lumonata_config.php');
    require_once('../lumonata_settings.php');
    require_once('../lumonata-functions/settings.php');
    require_once('../lumonata-classes/actions.php');
    require_once('../lumonata-functions/upload.php');
    require_once('../lumonata-functions/attachment.php');
    require_once('../lumonata-classes/directory.php');
    require_once('../lumonata-functions/friends.php');
    require_once('../lumonata-functions/user.php');
    require_once('../lumonata-functions/paging.php');
    require_once('../lumonata-content/languages/en.php');
    require_once('admin_functions.php');
    require_once('../lumonata-classes/user_privileges.php');
    
    if(!defined('SITE_URL'))
		define('SITE_URL',get_meta_data('site_url'));
    
    if(!is_user_logged()){
	    header("location:".get_admin_url()."/?state=login");
    }else{
        if(is_delete('users')){
            if(!delete_user($_POST['id']))
            echo "<div class=\"alert_red_form\">Deleting process failed.</div>";
        }elseif(is_search()){
	    $sql=$db->prepare_query("select * from lumonata_users where lusername like %s or ldisplay_name like %s or lemail=%s","%".$_POST['s']."%","%".$_POST['s']."%",$_POST['s']);
	    $r=$db->do_query($sql);
            if($db->num_rows($r) > 0)
                echo users_list($r);
            else
                echo "<div class=\"alert_yellow_form\">No result found for <em>".$_POST['s']."</em>. Check your spellling or try another terms</div>";
	}
    }  
?>   