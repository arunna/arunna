<?php
	add_actions('personal-settings',"personal_settings");
	function personal_settings(){
		//set tabs
		$tabs=array('notifications'=>'Notifications',
					'themes'=>'Themes',
					'privacy'=>'Privacy');
		
		//set template
		set_template(TEMPLATE_PATH."/personal-settings.html",'personal_settings');
		
		add_block('notificationSettings','notificationBlock','personal_settings');
		
		$tabb='';
		if(empty($_GET['tab']))
			$the_tab='notifications';
		else
			$the_tab=$_GET['tab'];
		
		add_variable('tab',set_tabs($tabs,$the_tab));
		
		//configure button
		add_variable('save_changes_botton',save_changes_botton());
		if($the_tab=='notifications'){
			add_actions('section_title','Notifications - Personal Settings');
			
			if(is_save_changes($_GET['state']))
				if(update_notifications_personal_settings())
					add_variable('alert',"<div class=\"alert_green_form\">".UPDATE_SUCCESS."</div>");
				else
					add_variable('alert',"<div class=\"alert_red_form\">".UPDATE_FAILED."</div>");
					
			
			
			$alert_on_register='';
			$alert_on_comment='';
			$alert_on_comment_reply='';
			$alert_on_liked_post='';
			$alert_on_liked_comment='';
			$temp=NULL;
			
			$alert_on_like_post="";
			$friendLikeCommentPost='';
			$friendLikeYourCommentFriendPost='';
			
			$temp=is_user_alert_on_comment($_COOKIE['user_id']);
			if($temp==1)
				$alert_on_comment="checked=\"checked\"";
			elseif($temp==NULL){
				if(alert_on_comment())
					$alert_on_comment="checked=\"checked\"";
			}
			
			$temp=is_user_alert_on_comment_reply($_COOKIE['user_id']);
			if($temp==1){
				$alert_on_comment_reply="checked=\"checked\"";
			}elseif ($temp==NULL){
				if(alert_on_comment_reply())
					$alert_on_comment_reply="checked=\"checked\"";	
			}
			
			$temp=is_user_alert_on_liked_comment($_COOKIE['user_id']);
			if($temp==1){
				$alert_on_liked_comment="checked=\"checked\"";
			}elseif($temp==NULL){
				if(alert_on_liked_comment())
					$alert_on_liked_comment="checked=\"checked\"";	
			}
			
			$temp=is_user_alert_on_liked_post($_COOKIE['user_id']);
			if($temp==1){
				$alert_on_liked_post="checked=\"checked\"";
			}elseif($temp==NULL){
				if(alert_on_liked_post())
					$alert_on_liked_post="checked=\"checked\"";
			}
			
			$temp=is_user_alert_on_like_post($_COOKIE['user_id']);
			if($temp==1){
				$alert_on_like_post="checked=\"checked\"";
			}elseif($temp==NULL){
				$alert_on_like_post="checked=\"checked\"";
			}	
			
			$temp=is_user_alert_on_friendLikeCommentPost($_COOKIE['user_id']);
			if($temp==1){
				$friendLikeCommentPost="checked=\"checked\"";
			}elseif($temp==NULL){
				$friendLikeCommentPost="checked=\"checked\"";
			}
			
			$temp=is_user_alert_on_friendLikeYourCommentFriendPost($_COOKIE['user_id']);
			
			if($temp==1){
				$friendLikeYourCommentFriendPost="checked=\"checked\"";
			}elseif($temp==NULL){
				$friendLikeYourCommentFriendPost="checked=\"checked\"";
			}	
				

			add_variable('alert_on_comment',$alert_on_comment);
			add_variable('alert_on_comment_reply',$alert_on_comment_reply);
			add_variable('alert_on_liked_post',$alert_on_liked_post);
			add_variable('alert_on_liked_comment',$alert_on_liked_comment);
			
			add_variable('alert_on_like_your_post',$alert_on_like_post);
			add_variable('friend_like_friend_comment_onpost',$friendLikeCommentPost);
			add_variable('friend_like_your_comment_friendpost',$friendLikeYourCommentFriendPost);
			
			parse_template('notificationSettings','notificationBlock');
			return return_template('personal_settings');
		} 	
	}
	
	function is_user_alert_on_comment($user_id){
		$return=get_meta_data("alert_on_comment","personal_settings",$user_id);
		return ($return==NULL)?1:$return;
		
	}
	
	function is_user_alert_on_comment_reply($user_id){
		$return =get_meta_data("alert_on_comment_reply","personal_settings",$user_id);
		return ($return==NULL)?1:$return;
		
	}
	
	function is_user_alert_on_liked_post($user_id){
		$return = get_meta_data("alert_on_liked_post","personal_settings",$user_id);
		return ($return==NULL)?1:$return;
		
	}
	
	function is_user_alert_on_liked_comment($user_id){
		$return = get_meta_data("alert_on_liked_comment","personal_settings",$user_id);
		return ($return==NULL)?1:$return;
	}
	
	function is_user_alert_on_like_post($user_id){
		$return = get_meta_data("alert_on_like_your_post","personal_settings",$user_id);
		return ($return==NULL)?1:$return;
	}
	
	function is_user_alert_on_friendLikeCommentPost($user_id){
		$return = get_meta_data("friend_like_friend_comment_onpost","personal_settings",$user_id);
		return ($return==NULL)?1:$return;
	}
	function is_user_alert_on_friendLikeYourCommentFriendPost($user_id){
		$return = get_meta_data("friend_like_your_comment_friendpost","personal_settings",$user_id);
		return ($return==NULL)?1:$return;
	}
	
	function update_notifications_personal_settings(){
		if(isset($_POST['alert_on_comment']))
				$_POST['alert_on_comment']=1;
		else
			$_POST['alert_on_comment']=0;
			
		if(isset($_POST['alert_on_comment_reply']))
			$_POST['alert_on_comment_reply']=1;
		else
			$_POST['alert_on_comment_reply']=0;
		
		if(isset($_POST['alert_on_liked_post']))
			$_POST['alert_on_liked_post']=1;
		else
			$_POST['alert_on_liked_post']=0;
		
		if(isset($_POST['alert_on_liked_comment']))
			$_POST['alert_on_liked_comment']=1;
		else
			$_POST['alert_on_liked_comment']=0;
		
		if(isset($_POST['alert_on_like_your_post']))
			$_POST['alert_on_like_your_post']=1;
		else
			$_POST['alert_on_like_your_post']=0;
			
		if(isset($_POST['friend_like_friend_comment_onpost']))
			$_POST['friend_like_friend_comment_onpost']=1;
		else
			$_POST['friend_like_friend_comment_onpost']=0;
			
			
		if(isset($_POST['friend_like_your_comment_friendpost']))
			$_POST['friend_like_your_comment_friendpost']=1;
		else
			$_POST['friend_like_your_comment_friendpost']=0;

		foreach($_POST as $key=>$val){
			if($key!='save_changes')
			$update=update_meta_data($key,$val,"personal_settings",$_COOKIE['user_id']);
		}
		
		if($update)return true;
		else return false;
	}
?>