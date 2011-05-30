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
	    		
			if($_GET['notify']=="show"){
				echo get_unread_notifications();
			}
		}
		
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
								   FROM lumonata_notifications 
								   WHERE laffected_user=%d and lstatus='unread'
								   GROUP BY laction_name, luser_id
								   ORDER BY laction_date DESC
								   ",$_COOKIE['user_id']);
		
		$result=$db->do_query($query);
		$notif="<div class=\"notif_wrap\">";
		$key=0;
		$notif="<div class=\"search_result_header\">Notifications</div>";
		while($data=$db->fetch_array($result) ){
			
			$notifperpsot=get_notify_per_act_post($data['lpost_id'],$data['laction_name']);
			$other="";
			$action_des="";
			
			if(count($notifperpsot)>2){
				$user=fetch_user($notifperpsot[0]['luser_id']);
				$name1=$user['ldisplay_name'];
				
				$user2=fetch_user($notifperpsot[1]['luser_id']);
				$name2=$user2['ldisplay_name'];
				
				$other=count($notifperpsot)-2;
				$other=$other." other people ";
				
				$name="<strong>".ucwords($name1)."</strong>, <strong>".ucwords($name2)."</strong> and ".$other;
				
			}elseif(count($notifperpsot)==2){
				$user=fetch_user($notifperpsot[0]['luser_id']);
				$name1=$user['ldisplay_name'];
				
				$user=fetch_user($notifperpsot[1]['luser_id']);
				$name2=$user['ldisplay_name'];
				
				$name="<strong>".ucwords($name1)."</strong> and <strong>".ucwords($name2)."</strong>";
				
			}else{
				$user=fetch_user($notifperpsot[0]['luser_id']);
				$name1=$user['ldisplay_name'];
				$name="<strong>".ucwords($name1)."</strong>";
			} 
			if($data['laction_name']=="comment"){
				$post_id=$data['lpost_id'];
				
				if($data['lpost_owner']==$data['laffected_user']){
					$action_des="commented on your post";
				}else{
					$writer=fetch_user($data['lpost_owner']);
					$action_des="also commented on ".ucwords($writer['ldisplay_name'])."' post";
				}
			}elseif($data['laction_name']=="like"){
				$post_id=$data['lpost_id'];
				
				if($data['lpost_owner']==$data['laffected_user']){
					$action_des="like your post";
				}
			}elseif($data['laction_name']=="like_comment"){
				$comment=fetch_comment($data['lpost_id']);
				$post=fetch_artciles_by_id($comment['larticle_id']);
				$post_id=$comment['larticle_id'];
				
				if($comment['luser_id']==$data['laffected_user'] && $post['lpost_by']=$data['lpost_owner']){
					$action_des="like your comment on your post";
				}elseif($comment['luser_id']==$data['laffected_user'] && $post['lpost_by']!=$data['lpost_owner']){
					$writer=fetch_user($post['lpost_by']);
					$action_des="like your comment on ".ucwords($writer['ldisplay_name'])."' post";
				}elseif($comment['luser_id']!=$data['laffected_user'] && $post['lpost_by']==$data['lpost_owner']){
					$writer=fetch_user($comment['luser_id']);
					$action_des="like ".ucwords($writer['ldisplay_name'])."'s comment on your post";
				}
			}
			
			
			
			
			$image="<img src=\"".get_avatar($notifperpsot[0]['luser_id'],2)."\" title=\"".ucwords($name1)."\" alt=\"".ucwords($name1)."\" />";
			
			
			
			$notif.="<div class=\"top_search_result clearfix\" id=\"top_search_result_".$key."\">
							<div class=\"top_search_avatar\">
								<a href=\"".permalink($post_id)."\">
									$image
								</a>
							</div>
							<div class=\"top_search_name\">
								<a href=\"".permalink($post_id)."\">".$name." ".$action_des."</a>
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
		}
		$notif.="<div class=\"more_search_result\">
					<a href=\"?state=notifications\" id=\"more_result_link\"><strong>See more results</strong></a>
				</div>";
		$notif.="<div class=\"notif_wrap\">";
		return $notif;
	}
	
	function get_notify_per_act_post($post_id,$action_name){
		global $db;
		$query=$db->prepare_query("SELECT * 
								   FROM lumonata_notifications 
								   WHERE lpost_id=%d AND laction_name=%s AND luser_id<>%d
								   GROUP BY luser_id",$post_id,$action_name,$_COOKIE['user_id']);
		
		$result=$db->do_query($query);
		while($data=$db->fetch_array($result)){
			$notif[]=$data;
		}
		return $notif;
	}
?>