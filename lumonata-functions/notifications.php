<?php
	if(isset($_GET['notify'])){
		require_once('../lumonata_config.php');
		require_once 'user.php';
		require_once 'rewrite.php';
		require_once 'articles.php';
		require_once '../lumonata-classes/actions.php';
		require_once 'comments.php';
		
		
		if(is_user_logged()){
	    	require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	
	    	if(!defined("SITE_URL"))
	    		define("SITE_URL",get_meta_data("site_url"));
	    		
	    	/*SET TIMEZONE*/
			set_timezone(get_meta_data('time_zone'));
	    		
			if($_GET['notify']=="show"){
				echo get_unread_notifications();
			}
		}
		
	}else{
		add_actions('notifications','all_notifications');
	}
	function save_notification($post_id,$post_owner,$user_id,$effected_id,$action_name,$share_to){
		global $db;
		$query=$db->prepare_query("INSERT INTO lumonata_notifications
								   (
									   lpost_owner,
								   	   lpost_id,
								   	   luser_id,
									   laffected_user,
									   laction_name,
									   laction_date,
									   lstatus,
									   lshare_to
								   )
								   VALUES(
								       %d,
								   	   %d,	
									   %d, 
									   %d, 
									   %s, 
									   %s, 
									   %s, 
									   %d
								   )",
		                            $post_owner,
									$post_id,
								  	$user_id,
								  	$effected_id, 
								  	$action_name, 
								  	date("Y-m-d H:i:s"),
								  	'unread',
								  	$share_to
								  );
		return $result=$db->do_query($query);
		
	}
	
	function count_notifications($user_id){
		global $db;
		$query=$db->prepare_query("SELECT * FROM lumonata_notifications WHERE laffected_user=%d and lstatus='unread'",$user_id);
		$result=$db->do_query($query);
		return $db->num_rows($result);
	}
	function get_unread_notifications(){
		global $db;
		
		$query=$db->prepare_query("SELECT * 
								   FROM (
								   		   SELECT * 
										   FROM lumonata_notifications 
										   WHERE laffected_user=%d 
										   ORDER BY laction_date DESC
										   
								   ) a
								   GROUP BY a.lpost_id,a.laction_name
								   ORDER BY a.laction_date DESC
								   LIMIT 5
								   ",$_COOKIE['user_id']);
		
		
		$result=$db->do_query($query);
		$notif="<div class=\"notif_wrap\">";
		$key=0;
		$notif="<div class=\"search_result_header\">Notifications</div>";
		
		if($db->num_rows($result)<1)
		$notif.="<p>No new notification</p>";
		
		while($data=$db->fetch_array($result) ){
			
			$notifperpost=get_notify_per_act_post($data['lpost_id'],$data['laction_name']);
			$other="";
			$action_des="";
			
			if(count($notifperpost)>2){
				$user=fetch_user($notifperpost[0]['luser_id']);
				$name1=$user['ldisplay_name'];
				
				$user2=fetch_user($notifperpost[1]['luser_id']);
				$name2=$user2['ldisplay_name'];
				
				$user3=fetch_user($notifperpost[2]['luser_id']);
				$name3=$user3['ldisplay_name'];
				
				$cnt_other=count($notifperpost)-2;
				if($cnt_other>1)
					$other=$cnt_other." other people ";
				else 
					$other="<strong>".ucwords($name3)."</strong>";
				
				$name="<strong>".ucwords($name1)."</strong>, <strong>".ucwords($name2)."</strong> and ".$other;
				
			}elseif(count($notifperpost)==2){
				$user=fetch_user($notifperpost[0]['luser_id']);
				$name1=$user['ldisplay_name'];
				
				$user=fetch_user($notifperpost[1]['luser_id']);
				$name2=$user['ldisplay_name'];
				
				$name="<strong>".ucwords($name1)."</strong> and <strong>".ucwords($name2)."</strong>";
				
			}else{
				$user=fetch_user($notifperpost[0]['luser_id']);
				$name1=$user['ldisplay_name'];
				$name="<strong>".ucwords($name1)."</strong>";
			} 
			
			
			if($data['laction_name']=="comment"){
				$post_id=$data['lpost_id'];
				$permalink=permalink($post_id);
				if($data['lpost_owner']==$data['laffected_user']){
					$action_des="commented on your post";
				}else{
					$writer=fetch_user($data['lpost_owner']);
					$action_des="also commented on <strong>".ucwords($writer['ldisplay_name'])."'s</strong> post";
				}
			}elseif($data['laction_name']=="like"){
				$post_id=$data['lpost_id'];
				$permalink=permalink($post_id);
				
				if($data['lpost_owner']==$data['laffected_user']){
					$action_des="like your post";
				}
			}elseif($data['laction_name']=="like_comment"){
				$comment=fetch_comment($data['lpost_id']);
				$post=fetch_artciles_by_id($comment['larticle_id']);
				$post_id=$comment['larticle_id'];
				$permalink=permalink($post_id);
				
				if($comment['luser_id']==$data['laffected_user'] && $_COOKIE['user_id']==$data['lpost_owner']){
					$action_des="like your comment on your post";
				}elseif($comment['luser_id']==$data['laffected_user'] && $_COOKIE['user_id']!=$data['lpost_owner']){
					$writer=fetch_user($post['lpost_by']);
					$action_des="like your comment on <strong>".ucwords($writer['ldisplay_name'])."'s </strong> post";
				}elseif($comment['luser_id']!=$data['laffected_user'] && $_COOKIE['user_id']==$data['lpost_owner']){
					$writer=fetch_user($comment['luser_id']);
					$action_des="like <strong>".ucwords($writer['ldisplay_name'])."'s</strong> comment on your post";
				}
			}elseif($data['laction_name']=="colek"){
				$action_des="has colek you";
				$permalink="?state=my-profile&id=".$data['luser_id'];
			}
			
			
			
			
			$image="<img src=\"".get_avatar($notifperpost[0]['luser_id'],2)."\" title=\"".ucwords($name1)."\" alt=\"".ucwords($name1)."\" />";
			
			
			
			$notif.="<div class=\"top_search_result clearfix\" id=\"top_search_result_".$key."\">
							<div class=\"top_search_avatar\">
								<a href=\"".$permalink."\">
									$image
								</a>
							</div>
							<div class=\"top_search_name\">
								<a href=\"".$permalink."\">".$name." ".$action_des." <br />
									<span style=\"color:#CCC;\">".nicetime($data['laction_date'],date("Y-m-d H:i:s"))."</span>
								</a>
								
							</div>
							
						</div>
						<script type=\"text/javascript\">
							$(function(){
								$('#top_search_result_".$key."').mouseover(function(){
									$('.top_search_result').removeClass('active');
									$(this).addClass('active');
									selected_pro=$('#top_search_result_".$key." a').attr('href');
								});
							});
						</script>
						";
			$key++;
			mark_as_read($data['lpost_id']);
		}
		$notif.="<div class=\"more_search_result\">
					<a href=\"?state=notifications\" id=\"more_result_link\"><strong>View all notifications</strong></a>
				</div>";
		$notif.="</div>";
		return $notif;
	}
	
	function get_notify_per_act_post($post_id,$action_name){
		global $db;
		$query=$db->prepare_query("SELECT a.*,b.ldisplay_name 
								   FROM lumonata_notifications a, lumonata_users b 
								   WHERE a.lpost_id=%d 
								   AND a.laction_name=%s 
								   AND a.luser_id<>%d
								   AND a.luser_id=b.luser_id
								   GROUP BY a.luser_id
								   ORDER BY a.lnotification_id DESC
								   ",$post_id,$action_name,$_COOKIE['user_id']);
		
		$result=$db->do_query($query);
		while($data=$db->fetch_array($result)){
			$notif[]=$data;
		}
		return $notif;
	}
	function mark_as_read($post_id){
		global $db;
		$query=$db->prepare_query("UPDATE lumonata_notifications
									SET lstatus='read'
									WHERE lpost_id=%d and laffected_user=%d",$post_id,$_COOKIE['user_id']);
		
		return $db->do_query($query);
	}
	
	function all_notifications(){
		global $db;
		add_actions('section_title','Your Notifications');
		
		$query=$db->prepare_query("SELECT * 
								   FROM (
								   		   SELECT * 
										   FROM lumonata_notifications 
										   WHERE laffected_user=%d 
										   ORDER BY laction_date DESC
										   
								   ) a
								   GROUP BY a.lpost_id,a.laction_name
								   ORDER BY a.laction_date DESC
								   LIMIT 30
								   ",$_COOKIE['user_id']);
		
		
		$result=$db->do_query($query);
		$notif="<div class=\"notif_wrap\">";
		$key=0;
		$notif="<h2 style=\"border-bottom:1px solid #CCC;\">Your Notifications</h2>";
		
		if($db->num_rows($result)<1)
		$notif.="<p>No new notification</p>";
		
		while($data=$db->fetch_array($result) ){
			
			$notifperpost=get_notify_per_act_post($data['lpost_id'],$data['laction_name']);
			$other="";
			$action_des="";
			
			if(count($notifperpost)>2){
				$user=fetch_user($notifperpost[0]['luser_id']);
				$name1=$user['ldisplay_name'];
				
				$user2=fetch_user($notifperpost[1]['luser_id']);
				$name2=$user2['ldisplay_name'];
				
				$user3=fetch_user($notifperpost[2]['luser_id']);
				$name3=$user3['ldisplay_name'];
				
				$cnt_other=count($notifperpost)-2;
				if($cnt_other>1){
					$notifperpost_enc=json_encode($notifperpost);
					$notifperpost_enc=base64_encode($notifperpost_enc);
					$other="<a href=\"../lumonata-functions/comments.php?people_like=".$notifperpost_enc."\" class=\"peoplelike\">".$cnt_other." other people</a> ";
				}else{ 
					$other="<a href=\"?state=my-profile&id=".$user3['luser_id']."\">".ucwords($name3)."</a>";
				}
				
				$name="<a href=\"?state=my-profile&id=".$user['luser_id']."\">".ucwords($name1)."</a>, <a href=\"?state=my-profile&id=".$user2['luser_id']."\">".ucwords($name2)."</a> and ".$other;
				
			}elseif(count($notifperpost)==2){
				$user=fetch_user($notifperpost[0]['luser_id']);
				$name1=$user['ldisplay_name'];
				
				$user2=fetch_user($notifperpost[1]['luser_id']);
				$name2=$user2['ldisplay_name'];
				
				$name="<a href=\"?state=my-profile&id=".$user['luser_id']."\">".ucwords($name1)."</a> and <a href=\"?state=my-profile&id=".$user2['luser_id']."\">".ucwords($name2)."</a>";
				
			}else{
				$user=fetch_user($notifperpost[0]['luser_id']);
				$name1=$user['ldisplay_name'];
				$name="<a href=\"?state=my-profile&id=".$user['luser_id']."\">".ucwords($name1)."</a>";
			} 
			
			
			if($data['laction_name']=="comment"){
				$post_id=$data['lpost_id'];
				$permalink=permalink($post_id);
				if($data['lpost_owner']==$data['laffected_user']){
					$action_des="commented on your <a href=\"".$permalink."\">post</a>";
				}else{
					$writer=fetch_user($data['lpost_owner']);
					$action_des="also commented on <a href=\"?state=my-profile&id=".$writer['luser_id']."\">".ucwords($writer['ldisplay_name'])."</a>'s <a href=\"".$permalink."\">post</a>";
				}
			}elseif($data['laction_name']=="like"){
				$post_id=$data['lpost_id'];
				$permalink=permalink($post_id);
				
				if($data['lpost_owner']==$data['laffected_user']){
					$action_des="like your <a href=\"".$permalink."\">post</a>";
				}
			}elseif($data['laction_name']=="like_comment"){
				$comment=fetch_comment($data['lpost_id']);
				$post=fetch_artciles_by_id($comment['larticle_id']);
				$post_id=$comment['larticle_id'];
				$permalink=permalink($post_id);
				
				if($comment['luser_id']==$data['laffected_user'] && $_COOKIE['user_id']==$data['lpost_owner']){
					$action_des="like your comment on your <a href=\"".$permalink."\">post</a>";
				}elseif($comment['luser_id']==$data['laffected_user'] && $_COOKIE['user_id']!=$data['lpost_owner']){
					$writer=fetch_user($post['lpost_by']);
					$action_des="like your comment on <a href=\"?state=my-profile&id=".$writer['luser_id']."\">".ucwords($writer['ldisplay_name'])." </a>'s <a href=\"".$permalink."\">post</a>";
				}elseif($comment['luser_id']!=$data['laffected_user'] && $_COOKIE['user_id']==$data['lpost_owner']){
					$writer=fetch_user($comment['luser_id']);
					$action_des="like <a href=\"?state=my-profile&id=".$writer['luser_id']."\">".ucwords($writer['ldisplay_name'])."</a>'s comment on your <a href=\"".$permalink."\">post</a>";
				}
			}elseif($data['laction_name']=="colek"){
				$action_des="has colek you";
				$permalink="?state=my-profile&id=".$data['luser_id'];
			}
			
			
			
			
			$image="<img src=\"".get_avatar($notifperpost[0]['luser_id'],3)."\" title=\"".ucwords($name1)."\" alt=\"".ucwords($name1)."\" />";
			$notif.="<div class=\"notifications_list clearfix\">
							<div class=\"notifications_avatar\">
									$image
							</div>
							<div class=\"notifications_name\">
									".$name." ".$action_des."<br />
									<span style=\"color:#CCC;\">".nicetime($data['laction_date'],date("Y-m-d H:i:s"))."</span>
								
								
							</div>
							
						</div>
						";
			$notif.="<script type=\"text/javascript\">
						$(function(){
							$('.peoplelike').colorbox();
						});
					</script>";
			$key++;
			mark_as_read($data['lpost_id']);
		}
		$notif.="</div>";
		return $notif;
	}
	
	
?>