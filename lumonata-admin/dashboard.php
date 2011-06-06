<?php
		
	if( file_exists('../lumonata_config.php')){
		$SINGLE_FILE=true;
		require_once('../lumonata_config.php');
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
	    require_once('../lumonata-functions/kses.php');
	    require_once('../lumonata-functions/rewrite.php');
	    require_once('../lumonata-functions/comments.php');
	    require_once('../lumonata-functions/articles.php');
	    require_once('../lumonata-functions/friends.php');
	}elseif(file_exists('lumonata_config.php')){
		$SINGLE_FILE=false;
		require_once('lumonata_config.php');
		require_once('lumonata-functions/settings.php');
		require_once('lumonata-classes/actions.php');
	    require_once('lumonata-functions/upload.php');
	    require_once('lumonata-functions/attachment.php');
	    require_once('lumonata-classes/directory.php');
	    require_once('lumonata-functions/user.php');
	    require_once('lumonata-functions/paging.php');
	    require_once('lumonata-content/languages/en.php');
	    require_once('admin_functions.php');
	    require_once('lumonata-classes/user_privileges.php');
	    require_once('lumonata-functions/kses.php');
	    require_once('lumonata-functions/rewrite.php');
	    require_once('lumonata-functions/comments.php');
	    require_once('lumonata-functions/articles.php');
	    require_once('lumonata-functions/friends.php');
	}
	if(!defined('SITE_URL'))
		    define('SITE_URL',get_meta_data('site_url'));
		    
	if(is_user_logged()){
		set_timezone(get_meta_data('time_zone'));
		if(!empty($_POST['dash_order']) && !empty($_POST['update'])){
			if(!is_user_logged()){
				header("location:".get_admin_url()."/?state=login");
			}else{  
				update_dashboard($_POST['update']);
			}
		}
		if(!empty($_POST['postit'])){
			
			if(isset($_POST['share_to']))
				$saveit=save_article($_POST['status'], '', 'publish', 'status', 'allowed','',$_POST['share_to']);
			else
				$saveit=save_article($_POST['status'], '', 'publish', 'status', 'allowed');
				
			$id=mysql_insert_id();
			if($saveit){
				$query=$db->prepare_query("SELECT * FROM lumonata_articles 
									 WHERE larticle_status='publish' AND larticle_id=%d",$id);
				echo dashboard_update_list($query);
			}
		}
		if(!empty($_POST['feeds_type'])){
			echo more_feeds($_POST['page'], $_POST['feeds_type']);
			
		}
	}
	
	function get_dashboard(){
		global $the_function;
		global $db;
		//dashboard left
		if(!is_user_logged())
		header("location:".get_admin_url());
		
		if(!isset($_GET['tab'])){
			
			add_actions('basic_dashboard_left','dashboard_latest_update');
		}else{
			
			$query=$db->prepare_query("SELECT * FROM lumonata_articles 
									 WHERE larticle_status='publish' AND
									  ( lpost_by in(
									 		SELECT a.lfriend_id 
											FROM lumonata_friendship a, lumonata_friends_list_rel b
											WHERE a.luser_id=".$_COOKIE['user_id']." AND 
													lstatus='connected' AND 
													a.lfriendship_id=b.lfriendship_id AND 
													b.lfriends_list_id=%d )
									 		OR lpost_by=".$_COOKIE['user_id']."
									 	)AND
									 	( lshare_to in (
									 		SELECT c.lfriends_list_id
											FROM lumonata_friends_list_rel c, lumonata_friendship d
											WHERE c.lfriendship_id=d.lfriendship_id AND d.lfriend_id=".$_COOKIE['user_id']." and d.lstatus='connected'
									 	    )OR lshare_to=%d OR (lshare_to=0 AND lpost_by <> ".$_COOKIE['user_id'].")
									 	)
									 ORDER BY lpost_date DESC",
										base64_decode($_GET['tab']),
										base64_decode($_GET['tab']));
			
			
			add_actions('basic_dashboard_left','dashboard_latest_update',$query,0,true,'post_per_list');
		}
		add_actions('basic_dashboard_right','dashboard_invite_friends');
		add_actions('basic_dashboard_right','friend_thumb_list',$_COOKIE['user_id']);
				
		$dashboard="<h1>Dashboard</h1>";
		$dashboard.="<div id=\"dashboard_left\">";
			
			$dashboard.="<div class=\"home_plug1\" id=\"dash_left\">";
			$dashboard.=attemp_actions('basic_dashboard_left');
			$dashboard.=attemp_actions('dashboard_left');
			$dashboard.="</div>";
			
		$dashboard.="</div>";
				
		
		$dashboard.="<div id=\"dashboard_right\">";
		$dashboard.=attemp_actions('basic_dashboard_right');
		$dashboard.=attemp_actions('dashboard_right');
		$dashboard.="</div>";
		
		add_actions('section_title','Dashboard');
		
		return $dashboard;
	}
	
	
	
	function dashboard_latest_update($query='',$comment_per_page=0,$post_box=true,$feeds_type='all_feeds'){
		global $db;
		
		add_actions('admin_tail','get_javascript','textarea-expander');
		add_actions('admin_tail','view_more_tabs_js');
		$return='';
		//$return="<h2>Latest Update</h2>";
		if($post_box){
		
		$fl_tab=get_friend_list($_COOKIE['user_id']);
		
		$share_to_id= (isset($_GET['tab']))?kses(base64_decode($_GET['tab']),array()):0;
		
		$return="<ul class=\"tabs\">";
			if(!isset($_GET['tab'])){
				$return.="<li class=\"active\"><a href=\"".get_state_url('dashboard')."\">Everyone</a></li>";
			}else{ 
				$return.="<li><a href=\"".get_state_url('dashboard')."\">Everyone</a></li>";
			}
			
			if(count($fl_tab)>0){
				if(count($fl_tab['friends_list_id'])>3)
				$max=3;
				else 
				$max=count($fl_tab['friends_list_id']);
				
				for($tb=0;$tb<$max;$tb++){
					
					if(isset($_GET['tab']) && $fl_tab['friends_list_id'][$tb]==$share_to_id){
						$return.="<li class=\"active\"><a href=\"".get_tab_url(base64_encode($fl_tab['friends_list_id'][$tb]))."\">".$fl_tab['list_name'][$tb]."</a></li>";
					}else{ 
						$return.="<li><a href=\"".get_tab_url(base64_encode($fl_tab['friends_list_id'][$tb]))."\">".$fl_tab['list_name'][$tb]."</a></li>";
					}
				}
			}
			if(isset($_GET['mta'])){
				$mta=base64_decode($_GET['mta']);
				$mta=json_decode($mta,true);
				$return.="<li class=\"active\"><a href=\"".get_tab_url(base64_encode($share_to_id))."&mta=".kses($_GET['mta'],array())."\">".$mta[base64_decode($_GET['tab'])]."</a></li>";
			}
			$return.="<li><a href=\"".get_state_url('friends')."&tab=manage-friend-list\"><img src=\"http://".TEMPLATE_URL."/images/friend_list.png\" border=\"0\" style=\"margin-top:0px;padding-bottom:0px;\" /></a></li>";
			
			if(count($fl_tab)>0){
				if(count($fl_tab['friends_list_id'])>3){
				$return.="<li class=\"view_more_tabs\">";
				$return.="<a href=\"javascript:;\" ><img src=\"http://".TEMPLATE_URL."/images/ico-arrow.png\" border=\"0\" style=\"margin-top:15px;padding-bottom:11px;\" /></a>";
					$return.="<ul id=\"sub_tab\">";
						for($tb=3;$tb<=count($fl_tab['friends_list_id'])-1;$tb++){
							$mta=array($fl_tab['friends_list_id'][$tb]=>$fl_tab['list_name'][$tb]);
							$mta=json_encode($mta);
							$mta=base64_encode($mta);
							$return.="<li><a href=\"".get_tab_url(base64_encode($fl_tab['friends_list_id'][$tb]))."&mta=".$mta."\">".$fl_tab['list_name'][$tb]."</a></li>";
						}
					$return.="</ul>";
				$return.="</li>";
				}
			}
			
  		$return.="</ul>";
				
		}else{
			$return.="<div>";
		}
		
		if($post_box){
			$return.="<div class=\"tab_container\">";
			$return.="<div class=\"input_post\">";
			$return.="<input type=\"text\" value=\"Share your thought\" id=\"share_thought\" class=\"postbox\" style=\"color:#bbb;\" />";
			$return.="<div id=\"postbox\" style=\"display:none;\">";
				$return.="<textarea type=\"text\" name=\"postbox\" id=\"postarea\" class=\"expand50-1000\"  /></textarea>";
				$return.="<div style=\"text-align:right;width:100%;padding:0;margin:10px 0 0 0;\">
								<img src=\"".get_theme_img()."/loader.gif\" id=\"post_loading\" style=\"display:none;\" />
								<input id=\"postit\" type=\"button\" value=\"Share\" name=\"postit\" class=\"btn_post\">
						  </div>";
			$return.="</div>";
			$return.="</div>";
			$return.="<script type=\"text/javascript\">
							$('#share_thought').click(function(){
								$('#share_thought').hide();
								$('#postbox').show();
								$('textarea[name=postbox]').focus();
							});
							$('#share_thought').focus(function(){
								$('#share_thought').hide();
								$('#postbox').show();
								$('textarea[name=postbox]').focus();
							});
							$('#postarea').blur(function(){
									if($('#postarea').val()==''){
										$('#postbox').hide();
	        							$('#share_thought').show();
	        						}
							});
							$('#postarea').keyup(function(){
								if($('#postarea').val().length > 600){
									$('#postarea').val($('#postarea').val().substr(0,600));
								}
							});
							$('#postit').click(function(){
							 
							   if($.trim($('#postarea').val())!=''){
									$('#post_loading').show();
									$('#postit').attr('disabled',true);
									$('#postarea').attr('disabled',true);";
									if($feeds_type=='post_per_list'){
										$return.="$.post('dashboard.php','postit=status&share_to=".$share_to_id."&status='+encodeURIComponent($('#postarea').val()),function(data){";
									}else{ 
										$return.="$.post('dashboard.php','postit=status&status='+encodeURIComponent($('#postarea').val()),function(data){";
									}
		                            	$return.="
		                            	    $('#postit').attr('disabled',false);
		                             		$('#postarea').attr('disabled',false);
		                             		$('#post_loading').hide();
		                             		$('#noupdates_alert').remove();
		                            		$('#postbox').hide();
		        							$('#share_thought').show();
		        							$('#postarea').val('');
		        							$('#refresh_update').prepend(data);
		        							
		                             });
		                             
		                             
		                        }else{
		                        	$('#postarea').val('');
		                        	$('#postarea').focus();
								}
							});
					 </script>";
		}
		
		$viewed=status_viewed();
		if(empty($query)){
						
			$query=$db->prepare_query("SELECT * FROM lumonata_articles 
									 WHERE larticle_status='publish' AND
									  ( lpost_by in(
									 		SELECT lfriend_id 
									 		FROM lumonata_friendship 
									 		WHERE luser_id=%d AND lstatus='connected' )
									 		OR lpost_by=%d
									 	)
									 	AND
									 	(lshare_to in (
									 		SELECT a.lfriends_list_id
											FROM lumonata_friends_list_rel a,lumonata_friendship b
											WHERE a.lfriendship_id=b.lfriendship_id AND
													b.luser_id in (
												      	SELECT lfriend_id 
												 		FROM lumonata_friendship 
												 		WHERE luser_id=%d AND lstatus='connected' )
												 		AND b.lstatus = 'connected'
														AND b.lfriend_id =%d
									 		)  OR lshare_to=0 
									 	)
									 	
									 ORDER BY lpost_date DESC
									 LIMIT 0, %d",
									$_COOKIE['user_id'],
									$_COOKIE['user_id'],
									$_COOKIE['user_id'],
									$_COOKIE['user_id'],
									$viewed);
			
			$query_cnt=$db->prepare_query("SELECT * FROM lumonata_articles 
									 WHERE larticle_status='publish' AND
									  ( lpost_by in(
									 		SELECT lfriend_id 
									 		FROM lumonata_friendship 
									 		WHERE luser_id=%d AND lstatus='connected' )
									 		OR lpost_by=%d
									 	)AND
									 	( lshare_to in (
									 		SELECT a.lfriends_list_id
											FROM lumonata_friends_list_rel a,lumonata_friendship b
											WHERE a.lfriendship_id=b.lfriendship_id AND
											      	b.luser_id in (
											      	SELECT lfriend_id 
											 		FROM lumonata_friendship 
											 		WHERE luser_id=%d AND lstatus='connected' )
											 		AND b.lstatus = 'connected'
													AND b.lfriend_id =%d
											      )
											      OR lshare_to=0 
									 	    )
									 	
									 ORDER BY lpost_date DESC",
									$_COOKIE['user_id'],
									$_COOKIE['user_id'],
									$_COOKIE['user_id'],
									$_COOKIE['user_id']);
		}else{
			$query_cnt=$query;
			$query=$query." LIMIT 0, $viewed";
			
		}
		if($comment_per_page!=0 || is_dashboard())
		$comment_per_page=get_meta_data('comment_per_page');
		
		$return.=dashboard_update_list($query,$comment_per_page);
		
		if(isset($_GET['tab']))$tab=$_GET['tab'];
		else $tab='';
		
		if(isset($_GET['id']))$uid=$_GET['id'];
		else $uid='';
		
		if(count_rows($query_cnt) > $viewed)
		$return.="<div id=\"more_feeds_2\">
						<script type=\"text/javascript\">
								$(function(){
									$('#load_more_2').click(function(){
									
										$('.load_more_feeds').html('<img src=\"http://".TEMPLATE_URL."/images/loading.gif\">');
										
										$.post('dashboard.php',{ 'feeds_type' : '".$feeds_type."', 'page' : '2', 'tab' : '".$tab."', 'uid' : '".$uid."' },function(data){
											
										 	$('#more_feeds_2').html(data);
										});
										
									});
								});
						 </script>
						 <div class=\"load_more_feeds\"><a href=\"#more_feeds_2\" id=\"load_more_2\">Load More...</a></div>
				  </div>";
		
			
		$return.="</div>";
		
		return $return;
	}
	function more_feeds($page,$feeds_type){
		global $db;
		if(!isset($page)){
		   $page=2;
		}
		
		if(isset($_POST['tab']))
			$tab=base64_decode($_POST['tab']);
		else 
			$tab='';
			
		if(isset($_POST['uid']))
			$uid=$_POST['uid'];
		else 
			return;
		
		$viewed=status_viewed();
				
		$limit=($page-1)*$viewed;
		if($feeds_type=='all_feeds'){
			$query=$db->prepare_query("SELECT * FROM lumonata_articles 
									 WHERE larticle_status='publish' AND
									  ( lpost_by in(
									 		SELECT lfriend_id 
									 		FROM lumonata_friendship 
									 		WHERE luser_id=%d AND lstatus='connected' )
									 		OR lpost_by=%d
									 	)
									 	AND
									 	(lshare_to in (
									 		SELECT a.lfriends_list_id
											FROM lumonata_friends_list_rel a,lumonata_friendship b
											WHERE a.lfriendship_id=b.lfriendship_id AND
													b.luser_id in (
												      	SELECT lfriend_id 
												 		FROM lumonata_friendship 
												 		WHERE luser_id=%d AND lstatus='connected' )
												 	AND b.lstatus = 'connected'
													AND b.lfriend_id =%d
									 		)  OR lshare_to=0 
									 	)
									 	
									 ORDER BY lpost_date DESC
									 LIMIT %d, %d",
									$_COOKIE['user_id'],
									$_COOKIE['user_id'],
									$_COOKIE['user_id'],
									$_COOKIE['user_id'],
									$limit,
									$viewed);
			
			$query_cnt=$db->prepare_query("SELECT * FROM lumonata_articles 
									 WHERE larticle_status='publish' AND
									  ( lpost_by in(
									 		SELECT lfriend_id 
									 		FROM lumonata_friendship 
									 		WHERE luser_id=%d AND lstatus='connected' )
									 		OR lpost_by=%d
									 	)AND
									 	( lshare_to in (
									 		SELECT a.lfriends_list_id
											FROM lumonata_friends_list_rel a,lumonata_friendship b
											WHERE a.lfriendship_id=b.lfriendship_id AND
											      	b.luser_id in (
											      	SELECT lfriend_id 
											 		FROM lumonata_friendship 
											 		WHERE luser_id=%d AND lstatus='connected' )
											 		AND b.lstatus = 'connected'
													AND b.lfriend_id =%d
											      )
											      OR lshare_to=0 
									 	    )
									 	
									 ORDER BY lpost_date DESC",
									$_COOKIE['user_id'],
									$_COOKIE['user_id'],
									$_COOKIE['user_id'],
									$_COOKIE['user_id']);
									
		}elseif($feeds_type=='my_feeds'){
			
			$query=$db->prepare_query("SELECT * FROM lumonata_articles 
								 WHERE larticle_status='publish' AND lpost_by=%d
								 ORDER BY lpost_date DESC
								 LIMIT %d, %d",
									$_COOKIE['user_id'],
									$limit,
									$viewed);
			
			$query_cnt=$db->prepare_query("SELECT * FROM lumonata_articles 
								 WHERE larticle_status='publish' AND lpost_by=%d
								 ORDER BY lpost_date DESC",$_COOKIE['user_id']);
			
			
		}elseif($feeds_type=='everyone_friend_feed'){
			
			$query=$db->prepare_query("SELECT * FROM lumonata_articles 
										 WHERE larticle_status='publish' AND
										 lpost_by = %d AND
										 lshare_to=0
										 ORDER BY lpost_date DESC
								 		 LIMIT %d, %d",
											$uid,
											$limit,
											$viewed);
			
			$query_cnt=$db->prepare_query("SELECT * FROM lumonata_articles 
										 WHERE larticle_status='publish' AND
										 lpost_by = %d AND
										 lshare_to=0
										 ORDER BY lpost_date DESC",
										 $uid);
			
			
		}elseif($feeds_type=='friend_feed'){
			
			$query=$db->prepare_query("SELECT * FROM lumonata_articles 
										 WHERE larticle_status='publish' AND
										 lpost_by = %d AND
										 	( lshare_to in (
										 		SELECT a.lfriends_list_id
												FROM lumonata_friends_list_rel a, lumonata_friendship b
												WHERE a.lfriendship_id=b.lfriendship_id AND b.lfriend_id=%d and lstatus='connected'
										 	    )OR lshare_to=0
										 	)
										 ORDER BY lpost_date DESC
										 LIMIT %d, %d",
										$uid,
										$_COOKIE['user_id'],
										$limit,
										$viewed);
			
			$query_cnt=$db->prepare_query("SELECT * FROM lumonata_articles 
										 WHERE larticle_status='publish' AND
										 lpost_by = %d AND
										 	( lshare_to in (
										 		SELECT a.lfriends_list_id
												FROM lumonata_friends_list_rel a, lumonata_friendship b
												WHERE a.lfriendship_id=b.lfriendship_id AND b.lfriend_id=%d and lstatus='connected'
										 	    )OR lshare_to=0
										 	)
										 ORDER BY lpost_date DESC",
										 $uid,
										 $_COOKIE['user_id']);
			
			
		}elseif($feeds_type=='post_per_list'){
			$query=$db->prepare_query("SELECT * FROM lumonata_articles 
									 WHERE larticle_status='publish' AND
									  ( lpost_by in(
									 		SELECT a.lfriend_id 
											FROM lumonata_friendship a, lumonata_friends_list_rel b
											WHERE a.luser_id=".$_COOKIE['user_id']." AND 
													lstatus='connected' AND 
													a.lfriendship_id=b.lfriendship_id AND 
													b.lfriends_list_id=%d )
									 		OR lpost_by=".$_COOKIE['user_id']."
									 	)AND
									 	( lshare_to in (
									 		SELECT c.lfriends_list_id
											FROM lumonata_friends_list_rel c, lumonata_friendship d
											WHERE c.lfriendship_id=d.lfriendship_id AND d.lfriend_id=".$_COOKIE['user_id']." and d.lstatus='connected'
									 	    )OR lshare_to=%d OR (lshare_to=0 AND lpost_by <> ".$_COOKIE['user_id'].")
									 	)
									 ORDER BY lpost_date DESC
									 LIMIT %d,%d",
										$tab,
										$tab,
										$limit,
										$viewed);
			
			$query_cnt=$db->prepare_query("SELECT * FROM lumonata_articles 
									 WHERE larticle_status='publish' AND
									  ( lpost_by in(
									 		SELECT a.lfriend_id 
											FROM lumonata_friendship a, lumonata_friends_list_rel b
											WHERE a.luser_id=".$_COOKIE['user_id']." AND 
													lstatus='connected' AND 
													a.lfriendship_id=b.lfriendship_id AND 
													b.lfriends_list_id=%d )
									 		OR lpost_by=".$_COOKIE['user_id']."
									 	)AND
									 	( lshare_to in (
									 		SELECT c.lfriends_list_id
											FROM lumonata_friends_list_rel c, lumonata_friendship d
											WHERE c.lfriendship_id=d.lfriendship_id AND d.lfriend_id=".$_COOKIE['user_id']." and d.lstatus='connected'
									 	    )OR lshare_to=%d OR (lshare_to=0 AND lpost_by <> ".$_COOKIE['user_id'].")
									 	)
									 ORDER BY lpost_date DESC",
										$tab,
										$tab);
			
		}
		if($comment_per_page!=0 || is_dashboard())
			$comment_per_page=get_meta_data('comment_per_page');
			
		$return=dashboard_update_list($query,$comment_per_page);
		
		
		
		if(count_rows($query_cnt) > ($viewed * $page)){
			$page++;
			$return.="<div id=\"more_feeds_".$page."\">
							<script type=\"text/javascript\">
								$(function(){
									$('#load_more_".$page."').click(function(){
										$('.load_more_feeds').html('<img src=\"http://".TEMPLATE_URL."/images/loading.gif\" border=\"0\">');
										$.post('dashboard.php',{ 'feeds_type' : '".$feeds_type."', 'page' : '".$page."', 'tab' : '".base64_encode($tab)."', 'uid' : '".$uid."' },function(data){
										 	$('#more_feeds_".$page."').html(data);
										});
										
									});
								});
					  		</script>
					  		 <div class=\"load_more_feeds\" id=\"load_more_".$page."\"><a href=\"#more_feeds_".$page."\" id=\"load_more_".$page."\">Load More...</a></div>
					  </div>";
		}
		return $return;
	}
	function get_status(){
		global $db;
		$query=$db->prepare_query("SELECT * 
								   FROM lumonata_articles 
								   WHERE larticle_status='publish' AND 
								   lsef=%s",$_GET['status']);
		$result=$db->do_query($query);
		$data=$db->fetch_array($result);
		add_actions('section_title',$data['larticle_title']);
		$content="<div id=\"dashboard_left\"><div class=\"home_plug1\">".dashboard_latest_update($query,0)."</div></div>";
		$content.="<div id=\"dashboard_right\">".attemp_actions('details_status_right')."</div>";
		return $content;
	}
	add_actions("status","get_status");
	add_actions("details_status_right",""); // add function to the right side of the details status
	
	function dashboard_update_list($query,$comment_per_page=0,$page=2){
		global $db;
		$return="";
		$result=$db->do_query($query);
		if($db->num_rows($result)<1){
			return "<div id=\"refresh_update\">
					<div id=\"noupdates_alert\" >
						<div style=\"width:95%;padding:5px;background-color:#84ff90;color:#333333;font-size:14px;margin:20px 0 20px 20px;-moz-border-radius:5px;-webkit-border-radius:5px;\">
							There are no updates yet!
						</div>
					</div>
				</div>";
		}else{$return.="<div id=\"refresh_update\"><div id=\"noupdates_alert\"></div></div>";}
		
		while($data=$db->fetch_array($result)){
			$name=get_display_name($data['lpost_by']);
			
			$sql=$db->prepare_query("SELECT lfriendship_id 
										FROM lumonata_friendship
										WHERE luser_id=%d AND lfriend_id=%d",$_COOKIE['user_id'],$data['lpost_by']);
			$result_fs=$db->do_query($sql);
			$data_fs=$db->fetch_array($result_fs);
			$flist=the_fs_list($data_fs['lfriendship_id']);
			
			//$user=fetch_user($data['lpost_by']);
			$return.="<div class=\"the_feeds clearfix\" >";
				$return.="<div class=\"the_feeds_avatar\">";
				$return.="<a href=\"".user_url($data['lpost_by'])."\"><img src=\"".get_avatar($data['lpost_by'],2)."\" alt=\"$name\" title=\"$name\" border=\"0\" /></a>";
				$return.="</div>";
				$return.="<div class=\"the_feeds_content\" id=\"the_feeds_".$data['larticle_id']."\">";
					$return.="<div class=\"the_author\">";
						$return.="<a href=\"".user_url($data['lpost_by'])."\">$name</a> ".$flist;
						$return.="<br /><span style='color:#CCC;'>".get_additional_field($data['lpost_by'], "one_liner", "user")."</span>";
					$return.="</div>";
					
					if(is_administrator($data['lpost_by'])){
						if($data['lpost_by']==$_COOKIE['user_id'])
							$post_login_admin=true;
						else 
							$post_login_admin=false;	
					}else{
						$post_login_admin=true;
					}
					
										
					if($post_login_admin){
						$return.="<div class=\"delete_post\" id=\"delete_post_".$data['larticle_id']."\" style=\"display:none;\">
										<div class=\"delete_icon\" id=\"delete_".$data['larticle_id']."\">&nbsp;</div>
								  </div>";
						if($data['lpost_by']==$_COOKIE['user_id']){
							$msg="Are you sure want to remove this post ?";
							//add_actions('admin_tail','delete_confirmation_box',$data['larticle_id'],$msg,"articles.php","the_feeds_".$data['larticle_id'],'delete_post=delete_post&id='.$data['larticle_id']);
							$return.=delete_confirmation_box($data['larticle_id'],$msg,"articles.php","the_feeds_".$data['larticle_id'],'delete_post=delete_post&id='.$data['larticle_id']);
						}else{
							$msg="Are you sure want to unfollow ".$name."?. <br />";
							//add_actions('admin_tail','delete_confirmation_box',$data['larticle_id'],$msg,"../lumonata-functions/friends.php","the_feeds_".$data['larticle_id'],'unfollow=unfollow&id='.$data['lpost_by']);
							$return.=delete_confirmation_box($data['larticle_id'],$msg,"../lumonata-functions/friends.php","the_feeds_".$data['larticle_id'],'unfollow=unfollow&id='.$data['lpost_by']);
						}
					}
					$return.="<br clear=\"left\" />";
					if($data['larticle_type']=="status")
					$return.="<p>".activate_URLs(nl2br($data['larticle_title']))."</p>";
					
					$return.="<div class=\"the_post\">";
					if($data['larticle_type']!="status"){
						$return.="<p><a href=\"".permalink($data['larticle_id'])."\" class=\"the_post_title\">".$data['larticle_title']."</a><p>";
						$return.="<p class=\"the_text_content\">".substr(strip_tags($data['larticle_content']),0,300)."<p>";
					}
					$return.="<span class=\"comment_date\">".nicetime($data['lpost_date'],date("Y-m-d H:i:s"))."</span>";
					$return.="</div>";
					
				$return.="</div>";
				
				$return.=comments($data['larticle_id'], $data['lcomment_status'],$comment_per_page,3);
			$return.="</div>";
			$return.="<script type=\"text/javascript\">
							$(function(){
								$('#the_feeds_".$data['larticle_id']."').mouseover(function(){
									
									$('#delete_post_".$data['larticle_id']."').show();
								});
								$('#the_feeds_".$data['larticle_id']."').mouseout(function(){
									$('#delete_post_".$data['larticle_id']."').hide();
								});
							});
					  </script>";
			
		}
		
		return $return;
	}
	function hide_postbox_onmouseup(){
        return "<script type=\"text/javascript\">
                    var mouse_is_inside = false;
                    $(document).ready(function(){
                         
                        $('#postbox').hover(function(){ 
                            mouse_is_inside=true; 
                        }, function(){ 
                            mouse_is_inside=false; 
                        });
                        
                       
                        $('body').mousedown(function(){
                            if(!mouse_is_inside) {
        						$('#postbox').hide();
        						$('#share_thought').show();
        					}
                        });
                    });

                   
                </script>";
    }
	function view_more_tabs_js(){
			return "<script type=\"text/javascript\">
						mouse_is_inside=false; 
                    	$(document).ready(function(){
                    		$('.view_more_tabs').click(function(){
                    			$('#sub_tab').slideToggle(100);
							});
							
							$('.view_more_tabs').hover(function(){ 
	                            mouse_is_inside=true; 
	                        }, function(){ 
	                            mouse_is_inside=false; 
	                        });
							
							$('body').mousedown(function(){
								if(!mouse_is_inside) {
                    				$('#sub_tab').slideUp(100);
                    			}
							});
						});
                	</script>";
		
	}
	
	
	
?>