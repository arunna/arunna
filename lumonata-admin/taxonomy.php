<?php
    require_once('../lumonata_config.php');
    require_once('../lumonata_settings.php');
    require_once('../lumonata-functions/settings.php');
    require_once('../lumonata-classes/actions.php');
    require_once('../lumonata-functions/user.php');
    require_once('../lumonata-content/languages/en.php');
    require_once('admin_functions.php');
    require_once('../lumonata-classes/user_privileges.php');
    
    require_once('../lumonata-functions/taxonomy.php');
    
    if(!defined('SITE_URL'))
		define('SITE_URL',get_meta_data('site_url'));
    
    if(!is_user_logged()){
	    header("location:".get_admin_url()."/?state=login");
    }elseif(isset($_POST['insert_rules'])){
	 
		if(insert_rules($_POST['parent'],$_POST['name'],$_POST['description'],$_POST['rule'],$_POST['group'])){
		   if(!empty($_POST['selected'])) {
			$selected=json_decode(rem_slashes($_POST['selected']));
			$merge_selected=array_merge($selected,array(mysql_insert_id()));
		   }else{
			$merge_selected=array(mysql_insert_id());
		   }
		   echo all_categories($_POST['index'],$_POST['rule'],$_POST['group'],$merge_selected);
		}
    }elseif(isset($_POST['update_parent'])){
		$select="<select name=\"parent[".$_POST['index']."]\" >
	                    <option value=\"0\">Parent</option>
	                    ".recursive_taxonomy($_POST['index'],$_POST['rule'],$_POST['group'],'select')."
	                </select>";
		echo $select;		
    }elseif(isset($_POST['update_order'])){
		update_taxonomy_order($_POST['theitem'],$_POST['start'],$_POST['state']);
    }else{
	
        if(is_delete($_POST['state'])){
            if(!delete_rule($_POST['id']))
            echo "<div class=\"alert_red_form\">Deleting process failed.</div>";
        }elseif(is_search()){
	   
	    	$sql=$db->prepare_query("select * from lumonata_rules where lrule=%s and (lname like %s or ldescription like %s) and lgroup=%s",$_POST['rule'],"%".$_POST['s']."%","%".$_POST['s']."%",$_POST['group']);
	    	
	    	$r=$db->do_query($sql);
            if($db->num_rows($r) > 0){
				echo rule_list($r,$_POST['group'],$_POST['rule']);
            }else{
                echo "<div class=\"alert_yellow_form\">No result found for <em>".$_POST['s']."</em>. Check your spellling or try another terms</div>";
            }
	}
    }  
?>   