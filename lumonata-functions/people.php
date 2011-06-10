<?php
	
    if(isset($_POST['more'])){
    	
    	require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
			require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	require_once('../lumonata-classes/actions.php');
	    	require_once('taxonomy.php');
	    	require_once('friends.php');

	    	if(!defined('SITE_URL'))
				define('SITE_URL',get_meta_data('site_url'));
			
			$theme=get_meta_data('admin_theme','themes');	
			define('TEMPLATE_URL',SITE_URL.'/lumonata-admin/themes/'.$theme);
	    	
    		echo more_people($_POST['tag'],$_POST['category'],$_POST['page'],$_POST['viewed']);
		}
    }else{
		add_actions('people','the_people');
	}
	function the_people(){
		add_actions('section_title','People');
		
		add_actions('basic_people_right','dashboard_invite_friends');
		
		$viewed=10;
		
		if(isset($_GET['cat'])){
			$people=all_people($_GET['cat'],true,$viewed);
		}elseif(isset($_GET['tag'])){
			$people=all_people($_GET['tag'],false,$viewed);
		}else{
			
			$people=all_people('',false,$viewed);
		}
				
		
		$people.="<div id=\"people_right\">";
		$people.=attemp_actions('basic_people_right');
		$people.=attemp_actions('people_right');
		$people.="</div>";
		
		return $people;
	}
	
	function all_people($tag='',$category=false,$viewed=10,$page=1){
		global $db;
		
		$limit=($page-1)*$viewed;
		
		if(!empty($tag)){
			if($category==true)
				$rule=fetch_rule('sef='.$tag.'&group=global_settings');
			else 
				$rule=fetch_rule('sef='.$tag.'&group=profile');
				
			$title="Tagged as ".ucwords($rule['lname']);
			$query=$db->prepare_query("SELECT a.* 
										FROM lumonata_users a,lumonata_rule_relationship b
										WHERE b.lapp_id=a.luser_id 
										AND b.lrule_id=%d 
		                            	ORDER BY a.ldlu DESC
		                            	LIMIT %d,%d",$rule['lrule_id'],$limit,$viewed);
			$query_cnt=$db->prepare_query("SELECT a.* 
										FROM lumonata_users a,lumonata_rule_relationship b
										WHERE b.lapp_id=a.luser_id 
										AND b.lrule_id=%d 
		                            	ORDER BY a.ldlu DESC
		                            	",$rule['lrule_id']);
		}else{
			$title="People";
			$query=$db->prepare_query("SELECT * FROM lumonata_users 
		                            	ORDER BY ldlu DESC
		                            	LIMIT %d,%d",$limit,$viewed);
			
			$query_cnt=$db->prepare_query("SELECT * FROM lumonata_users 
		                            	ORDER BY ldlu DESC
		                            	");
		}
		
		$people="<h1>".$title."</h1>";
		$people.="<div id=\"people_left\">";
		$people.="<div class=\"people_wrapper\">";
		
		
		$result=$db->do_query($query);
		if($db->num_rows($result)<1)
		$people.="<div class=\"alert_green_form\">Result not found</div>";
		
		$people.=fetch_people($result,$page);
			
		if(count_rows($query_cnt) > $viewed){
					$page++;
					$people.="<div id=\"more_feeds_2\">
						<script type=\"text/javascript\">
								$(function(){
									$('#load_more_2').click(function(){
									
										$('.load_more_feeds').html('<img src=\"http://".TEMPLATE_URL."/images/loading.gif\">');
										
										$.post('../lumonata-functions/people.php',{ 'more' : 'more',
															  'tag' : '".$tag."', 
															  'page' : '".$page."', 
															  'viewed' : '".$viewed."', 
															  'category' : '".$category."' },
											function(data){
										 		$('#more_feeds_2').html(data);
											});
										
									});
								});
						 </script>
						 <div class=\"load_more_feeds\"><a href=\"#more_feeds_2\" id=\"load_more_2\">Load More...</a></div>
				  </div>";
		}
		$people.="</div>";
		$people.="</div>";
		
		
		
		return $people;
	}
	
	function more_people($tag='',$category=false,$page=1,$viewed){
		global $db;
		$people='';
		$limit=($page-1)*$viewed;
		
		if(!empty($tag)){
			if($category==true)
				$rule=fetch_rule('sef='.$tag.'&group=global_settings');
			else 
				$rule=fetch_rule('sef='.$tag.'&group=profile');
				
			$title="Tagged as ".ucwords($rule['lname']);
			$query=$db->prepare_query("SELECT a.* 
										FROM lumonata_users a,lumonata_rule_relationship b
										WHERE b.lapp_id=a.luser_id 
										AND b.lrule_id=%d 
		                            	ORDER BY a.ldlu DESC
		                            	LIMIT %d,%d",$rule['lrule_id'],$limit,$viewed);
			$query_cnt=$db->prepare_query("SELECT a.* 
										FROM lumonata_users a,lumonata_rule_relationship b
										WHERE b.lapp_id=a.luser_id 
										AND b.lrule_id=%d 
		                            	ORDER BY a.ldlu DESC
		                            	",$rule['lrule_id']);
		}else{
			$title="People";
			$query=$db->prepare_query("SELECT * FROM lumonata_users 
		                            	ORDER BY ldlu DESC
		                            	LIMIT %d,%d",$limit,$viewed);
			
			$query_cnt=$db->prepare_query("SELECT * FROM lumonata_users 
		                            	ORDER BY ldlu DESC
		                            	");
		}
		
		$result=$db->do_query($query);
		
		$people.=fetch_people($result,$page);
			
		if(count_rows($query_cnt) > ($viewed * $page)){
					$page++;
					$people.="<div id=\"more_feeds_".$page."\">
						<script type=\"text/javascript\">
								$(function(){
									$('#load_more_".$page."').click(function(){
									
										$('.load_more_feeds').html('<img src=\"http://".TEMPLATE_URL."/images/loading.gif\">');
										
										$.post('../lumonata-functions/people.php',{ 'more' : 'more',
															  'tag' : '".$tag."', 
															  'page' : '".$page."', 
															  'viewed' : '".$viewed."', 
															  'category' : '".$category."' },
											function(data){
										 		$('#more_feeds_".$page."').html(data);
											});
										
									});
								});
						 </script>
						 <div class=\"load_more_feeds\"><a href=\"#more_feeds_".$page."\" id=\"load_more_".$page."\">Load More...</a></div>
				  </div>";
		}
		
		return $people;
	}
	
	function fetch_people($result,$page){
		global $db;
		$people='';
		while($data=$db->fetch_array($result)){
			
			if(($_COOKIE['user_id']==$data['luser_id']) || is_my_friend($_COOKIE['user_id'], $data['luser_id'],'connected') || is_my_friend($_COOKIE['user_id'], $data['luser_id'],'unfollow')){
				$follow_label='';
			}elseif(is_my_friend($_COOKIE['user_id'], $data['luser_id'],'pending')){
				$follow_label="<span style='color:#CCC;'>Request pending.</span>";
			}else{
				$follow_label="<p><a class=\"button_add_friend\" style=\"color:#333;\" href=\"../lumonata-functions/friends.php?add_friend=true&type=add&friendship_id=0&friend_id=".$data['luser_id']."&redirect=".urlencode(get_state_url('people')."&page=".$page)."&key=#add_friend\" id=\"add_friend_".$data['luser_id']."\" >Add Connection</a></p>";
				$follow_label.="<script type=\"text/javascript\">
						   			$('#add_friend_".$data['luser_id']."').click(function(){
						   				$('#add_friend_".$data['luser_id']."').colorbox();
									});
						   		</script>";
			}
			
			$people.="<div class=\"the_people clearfix\">";
				$people.="<div class=\"people_thumb\">
								<a href=\"".user_url($data['luser_id'])."\">
									<img src=\"".get_avatar($data['luser_id'],2)."\" alt=\"".$data['ldisplay_name']."\" title=\"".$data['ldisplay_name']."\" />
								</a>
						  </div>";
				$people.="<div class=\"people_description\">
								<h2><a href=\"".user_url($data['luser_id'])."\">".$data['ldisplay_name']."</a></h2>
								<div class=\"oneliner\">".get_additional_field($data['luser_id'], "one_liner", "user")."</div>
								<div class=\"location\"><strong>".get_additional_field($data['luser_id'], "location", "user")."</strong></div>
					      </div>";
				$people.='<div class="fof_add_friend">'.$follow_label.'</div>';
				
			$people.="</div>";
			$people.="<div class=\"tag_area clearix\">".get_user_tags($data['luser_id'])."</div>";
			
		}
		return $people;
	}
?>