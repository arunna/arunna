<?php
    require_once('../lumonata_config.php');
    require_once('../lumonata_settings.php');
    require_once('../lumonata-functions/settings.php');
    require_once('../lumonata-classes/actions.php');
    require_once('../lumonata-functions/upload.php');
    require_once('../lumonata-functions/attachment.php');
     require_once('../lumonata-functions/kses.php');
    require_once('../lumonata-classes/directory.php');
    require_once('../lumonata-functions/user.php');
    require_once('../lumonata-functions/paging.php');
    require_once('../lumonata-content/languages/en.php');
    require_once('admin_functions.php');
    require_once('../lumonata-classes/user_privileges.php');
    require_once('../lumonata-functions/articles.php');
    require_once('../lumonata-functions/taxonomy.php');
   
    if(!defined('SITE_URL'))
		define('SITE_URL',get_meta_data('site_url'));
    
    if(!is_user_logged()){
	    header("location:".get_admin_url()."/?state=login");
    }elseif(isset($_POST['update_order'])){
		update_articles_order($_POST['theitem'],$_POST['start'],$_POST['state']);
    }elseif(isset($_POST['delete_post'])){
    	$article=fetch_artciles("id=".$_POST['id']."&type=status");
    	if($_COOKIE['user_id']==$article['lpost_by'])
    	return delete_article($_POST['id'], 'status');
    }elseif(isset($_POST['update_sef'])){
        if(update_sef($_POST['post_id'],$_POST['title'], $_POST['new_sef'],$_POST['type']))
        	echo "BAD";
        else 
        	echo "OK";
        	
    }elseif(isset($_POST['get_sef'])){
        if(empty($_POST['title'])){
           //$num_untitled=is_num_articles('title=Untitled&type='.$_POST['type'])+1;
           $_POST['title']="Untitled";
        }else{
           $_POST['title']=kses(rem_slashes($_POST['title']),$allowedtitletags);
            
        }
        
        $num_by_title_and_type=is_num_articles("title=".$_POST['title']."&type=".$_POST['type']);
        if($num_by_title_and_type>0){
            //$sef=generateSefUrl($_POST['title'])."-".$num_by_title_and_type;
         	for($i=2;$i<=$num_by_title_and_type+1;$i++){
	        	$sef=generateSefUrl($_POST['title'])."-".$i;
	            if(is_num_articles('sef='.$sef.'&type='.$_POST['type']) < 1){
	            	$sef=$sef;
	                break;
	            }
	         }
            
        }else{
            $sef=generateSefUrl($_POST['title']);
        }
        
        if(strlen($sef)>50)$more="...";else $more="";
        
        if($_POST['type']=='pages'){
            $link_structure="http://".site_url()."/";
            $ext="/";
        }else{ 
            $link_structure="http://".site_url()."/".$_POST['type']."/category/";
            $ext=".html";
        } 
        echo "<strong>Permalink:</strong> 
        	  $link_structure
        	  <span id=\"the_sef_".$_POST['index']."\">
    			  <span id=\"the_sef_content_".$_POST['index']."\"  style=\"background:#FFCC66;cursor:pointer;\">".
                      substr($sef,0,50).$more.
                  "
                  </span>
                  $ext
                  <input type=\"button\" value=\"Edit\" id=\"edit_sef_".$_POST['index']."\" class=\"button_bold\">
              </span>
              <span id=\"sef_box_".$_POST['index']."\" style=\"display:none;\">
              <span>
              	<input type=\"text\" name=\"sef_box[".$_POST['index']."]\" value=\"".$sef."\" style=\"border:1px solid #CCC;width:300px;font-size:11px;\" />
              	$ext <input type=\"button\" value=\"Done\" id=\"done_edit_sef_".$_POST['index']."\" class=\"button_bold\">
              </span>
              </span>
              
              <script type=\"text/javascript\">
              		$('#the_sef_".$_POST['index']."').click(function(){
              			$('#the_sef_".$_POST['index']."').hide();
              			$('#sef_box_".$_POST['index']."').show();
              			
    				});
    				$('#edit_sef_".$_POST['index']."').click(function(){
              			$('#the_sef_".$_POST['index']."').hide();
              			$('#sef_box_".$_POST['index']."').show();
    				});
    				$('#done_edit_sef_".$_POST['index']."').click(function(){
    					var new_sef=$('input[name=sef_box[".$_POST['index']."]]').val();
    					if(new_sef.length>50)
    						var more='...'
    					else
    						var more='';
    					$('#the_sef_content_".$_POST['index']."').html(new_sef.substr(0,50)+more);
              			$('#the_sef_".$_POST['index']."').show();
              			$('#sef_box_".$_POST['index']."').hide();
    				});
              </script>
              ";
        
    }else{
        if(is_delete($_POST['state'])){
            if(!delete_article($_POST['id'],$_POST['state']))
            echo "<div class=\"alert_red_form\">Deleting process failed.</div>";
        }elseif(is_search()){
	    
	    if($_COOKIE['user_type']=='contributor' || $_COOKIE['user_type']=='author'){
		$w=" lpost_by=".$_COOKIE['user_id']." AND ";    
	    }else{
		$w="";
	    }
	    
	    $sql=$db->prepare_query("select * from lumonata_articles where $w larticle_type=%s and (larticle_title like %s or larticle_content like %s)",$_POST['state'],"%".$_POST['s']."%","%".$_POST['s']."%");
	    
	    $r=$db->do_query($sql);
            if($db->num_rows($r) > 0){
		if($_POST['state']=='pages')
		    echo pages_list($r);
		else
		    echo article_list($r,$_POST['state']);
            }else{
                echo "<div class=\"alert_yellow_form\">No result found for <em>".$_POST['s']."</em>. Check your spellling or try another terms</div>";
            }
	}
    }  
?>   