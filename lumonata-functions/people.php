<?php
	add_actions('people','the_people');
	
	function the_people(){
		add_actions('section_title','People');
		
		add_actions('basic_dashboard_right','dashboard_invite_friends');
		
		if(isset($_GET['cat'])){
			$people=all_people($_GET['cat'],true);
		}elseif(isset($_GET['tag'])){
			$people=all_people($_GET['tag'],false);
		}else{
			$people=all_people();
		}
				
		
		$people.="<div id=\"people_right\">";
		$people.=attemp_actions('basic_dashboard_right');
		$people.=attemp_actions('dashboard_right');
		$people.="</div>";
		
		return $people;
	}
	
	function all_people($tag='',$category=false){
		global $db;
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
		                            	ORDER BY RAND()",$rule['lrule_id']);
		}else{
			$title="People";
			$query=$db->prepare_query("SELECT * FROM lumonata_users 
		                            	ORDER BY RAND()");
		}
		
		$people="<h1>".$title."</h1>";
		$people.="<div id=\"people_left\">";
		$people.="<div class=\"people_wrapper\">";
		
		
		$result=$db->do_query($query);
		if($db->num_rows($result)<1)
		$people.="<div class=\"alert_green_form\">Result not found</div>";
		
		while($data=$db->fetch_array($result)){
			
			if(($_COOKIE['user_id']==$data['luser_id']) || is_my_friend($_COOKIE['user_id'], $data['luser_id'],'connected') || is_my_friend($_COOKIE['user_id'], $data['luser_id'],'unfollow')){
				$follow_label='';
			}elseif(is_my_friend($_COOKIE['user_id'], $data['luser_id'],'pending')){
				$follow_label="<span style='color:#CCC;'>Request pending.</span>";
			}else{
				$follow_label="<p><a class=\"button_add_friend\" style=\"color:#333;\" href=\"../lumonata-functions/friends.php?add_friend=true&type=add&friendship_id=0&friend_id=".$data['luser_id']."&redirect=".urlencode(cur_pageURL())."&key=#add_friend\" id=\"add_friend_".$data['luser_id']."\" >Add Connection</a></p>";
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
			$people.="</div>";
		$people.="</div>";
		return $people;
	}
?>