<?php
      
	if(isset($_GET['editlist']) && isset($_GET['id'])){
			
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
	    	require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	require_once('../lumonata-classes/actions.php');
			echo myfriend_lists($_COOKIE['user_id'],$_GET['id'],$_GET['friend_id'],$_GET['key'],$_GET['redirect']);
		}
	}elseif(isset($_GET['manage_list']) && $_GET['manage_list']=='invite'){
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
			
			require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	require_once('../lumonata-classes/actions.php');
	    	echo invitation_box();
	    	
		}
	}elseif(isset($_POST['manage_list']) && $_POST['manage_list']=='invite'){
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
			
			require_once('../lumonata_settings.php');
			require_once('kses.php');
	    	require_once('settings.php');
	    	require_once('../lumonata-classes/actions.php');
	    	require_once('mail.php');
	    	if(isset($_POST['send_invite'])){
	    	    
    	    	if(isset($_POST['emails']) && !empty($_POST['emails'])){
    		        $user=fetch_user($_COOKIE['user_id']);
    		        $the_email=nl2br($_POST['emails']);
    		        $the_email=explode("<br />", $the_email);
    		        
    		        $sent=0;
                    $invite_limit=get_additional_field($_COOKIE['user_id'], "invite_limit", "user");
                    $count=$invite_limit;
                    
    		        $html="<div style=\"width:100%;height:90px;overflow:auto;\"><table>";
    		        foreach ($the_email as $key=>$value){
    		            $value=str_replace("<br>", "", trim($value));
    		            $value=str_replace("<br />", "", trim($value));
    		            if(!empty($value)){
        		            if(isEmailAddress(trim($value))){
        		                  $enc_ulid=base64_encode($_POST['list_id']);
        		                  if($count>0){
            		                  $send_invite=invitation_mail($user['lemail'],$user['ldisplay_name'],$value,$_POST['personal_message'],$enc_ulid);
            		                  if($send_invite){
            		                      $count--;
            		                      $sent++;
            		                      $no=$key+1;
                        		          $html.="<tr>
                                       				<td>".$no.".</td>
                                       				<td>".$value."</td>
                                      		  	</tr>";
            		                  }
            		              }elseif($count==-1){ //for unlimited invitation
        	                          	  $send_invite=invitation_mail($user['lemail'],$user['ldisplay_name'],$value,$_POST['personal_message'],$enc_ulid);
        	                          	  $sent++;
            		                      $no=$key+1;
                        		          $html.="<tr>
                                       				<td>".$no.".</td>
                                       				<td>".$value."</td>
                                      		  	</tr>";
        	                      }else break;
        		            }
        		        }
    		        }
    		        $html.="</table></div>";
    		        
    		        
    		        if($invite_limit!=-1){
        			    $invite_limit=$invite_limit-$sent;
        			    edit_additional_field($_COOKIE['user_id'], "invite_limit", $invite_limit, "user");
    		        }
    		        if($sent!=0){
    		            echo "<div class='alert_green'>You successfully invited ".$sent." person to ".$_POST['list_name']." friend list.</div>";
    		            echo $html;
    		        }else{ 
    		            echo "<div class='alert_yellow'>No invitation was sent. Be sure to input correct email address.</div>";
    		        }
    		        	
    		        	        
        		    
    		    }else{ 
    		        echo "<div class='alert_yellow'>No invitation was sent. Be sure to input correct email address.</div>";
    		    }
	    	}else{ 
    		        echo "<div class='alert_yellow'>No invitation was sent. Be sure to input correct email address.</div>";
    		}
    		
		}
	}elseif(isset($_GET['manage_list']) && $_GET['manage_list']=='add_friend'){
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
			
			require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	require_once('../lumonata-classes/actions.php');	
	    	if(isset($_POST['save']) && isset($_POST['selected'])){
	    		foreach($_POST['selected'] as $key=>$val){
	    			$the_arr[$val]=$_POST['friend_list_id'];
	    		}
	    		add_multiple_friend_list_rel($_COOKIE['user_id'],$_POST['friend_list_id'],$the_arr);
	    		header("location:".$_GET['redirect']."&tab=manage-friend-list");
	    		
	    	}else{
	    		echo add_friends_to_list_box($_COOKIE['user_id']);
	    	}
		}
	}elseif(isset($_GET['add_friend'])){
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
			require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	require_once('../lumonata-classes/actions.php');	
	    	echo myfriend_lists($_COOKIE['user_id'],$_GET['friendship_id'],$_GET['friend_id'],$_GET['key'],$_GET['redirect'],true,$_GET['type']);
		}
	}elseif(isset($_POST['unfollow'])){
		
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
			require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	require_once('../lumonata-classes/actions.php');	
	    	if(edit_friendship($_COOKIE['user_id'], $_POST['id'],'unfollow',false))
	    		echo "OK";
	    	else 
	    		echo "ERROR";
		}
	}elseif(isset($_POST['follow'])){
		
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
			require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	require_once('../lumonata-classes/actions.php');	
	    	
	    	if(edit_friendship($_COOKIE['user_id'], $_POST['id'],'connected',false))
	    		echo "OK";
	    	else 
	    		echo "ERROR";
		}
	}elseif(isset($_POST['sort_list'])){
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
			require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	require_once('../lumonata-classes/actions.php');
			update_order_id($_POST['friendItem'], "lumonata_friends_list", "lfriends_list_id", 0);
		}
		
	}elseif(isset($_POST['list_name'])){
		
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
	    	require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	require_once('../lumonata-classes/actions.php');
	    	$r=add_friend_list($_COOKIE['user_id'],$_POST['list_name']);
	    	if(is_integer($r)){
	    		
	    		echo "<div style='border-bottom:1px solid #f0f0f0;margin:5px 0;padding-bottom:5px;' id='list_id_".$r."' class='thelist'>
						<div style='width:310px;height:auto;float:left;cursor:pointer;' id='list_panel_".$r."'>
							<input name='thelist[]' type='checkbox' value='".$r."' checked='checked'/>
							<a href='javascript:;' style='text-decoration:none;font-weight:bold;' title='Click to Edit' id='edit_list_".$r."'>
								".$_POST['list_name']." <span id='click_to_edit_".$r."' style='font-size:9px;color:#ccc;margin-left:5px;display:none;'>Click to edit</span>
							</a>
							<span id='list_text_".$r."' style='display:none;' >
								<input type=\"text\" value=\"".$_POST['list_name']."\" id=\"editedname_".$r."\" style='border:1px solid #ccc;width:80%;padding:4px;' />
							</span>
						</div>
						<div style='width:60px;font-size:10px;float:left;'>
							<a href='javascript:;' id='delete_list_".$r."'>Delete List</a>
						</div>
						<br clear='left' />
					</div>
					<script type='text/javascript'>
						$(function(){
														
							$('#edit_list_".$r."').click(function(){
								$('#edit_list_".$r."').hide();
								$('#list_text_".$r."').show();
								$('#editedname_".$r."').focus();
							});
							
							$('#list_panel_".$r."').click(function(){
								$('#edit_list_".$r."').hide();
								$('#list_text_".$r."').show();
								$('#editedname_".$r."').focus();
							});
							
							$('#list_panel_".$r."').mouseover(function(){
								$('#click_to_edit_".$r."').show();
							});
							
							$('#list_panel_".$r."').mouseout(function(){
								$('#click_to_edit_".$r."').hide();
							});
							
							$('#delete_list_".$r."').click(function(){
								$('#loadit').show();
								$.post('../lumonata-functions/friends.php',
								{ 'delete_list_id'  : '".$r."'
								},function(theRespose){
									$('#list_id_".$r."').css({ 'background-color' : '#FF6666' }).delay(1000).fadeOut(80);
									$('#err').html(theRespose);
								});
								$('#loadit').hide();
							});
							
							$('#editedname_".$r."').blur(function(){
								$('#edit_list_".$r."').show();
								$('#list_text_".$r."').hide();
								
							});
							
							$('#editedname_".$r."').keypress(function(e){
								if(e.keyCode==13){
									$.post('../lumonata-functions/friends.php',
									{ 'list_id'  : '".$r."',
									  'new_name' : 	$('#editedname_".$r."').val()
									},function(theRespose){
										$('#edit_list_".$r."').html(theRespose);
									});
									
									$('#edit_list_".$r."').show();
									$('#list_text_".$r."').hide();
								}
							});
						});
						
					</script>
					";
	    	}
		}
	}elseif(isset($_POST['key'])){
		
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
			require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	require_once('mail.php');
	    	
	    	if($_POST['is_friend_request']){
	    		if($_POST['request_type']=='add'){
			    	if(add_friendship($_POST['user_id'], $_POST['friend_id'],'pending')){
			    		$_POST['id']=mysql_insert_id();
			    		friend_request_mail($_POST['user_id'], $_POST['friend_id']);
			    		if(!add_friendship($_POST['friend_id'],$_POST['user_id'])){
			    			echo "<div class='alert_red'>
					    	 		<h1 style='margin:0;padding:0px;'>Something went wrong !</h1>
					    	 		<p>Please try again later</p>
					    	 		</div>
					    		 ";
			    		}
			    	}else{
			    		echo "<div class='alert_red'>
				    	 		<h1 style='margin:0;padding:0px;'>Something went wrong !</h1>
				    	 		<p>Please try again later</p>
				    	 		</div>
				    		 ";
			    	}
	    		}elseif($_POST['request_type']=='confirm'){
	    			if(!edit_friendship($_POST['user_id'],$_POST['friend_id'],'connected')){
	    				echo "<div class='alert_red'>
				    	 		<h1 style='margin:0;padding:0px;'>Something went wrong !</h1>
				    	 		<p>Please try again later</p>
				    	 		</div>
				    		 ";
	    			}else{
	    			    $invited=fetch_user($_POST['user_id']);
	    			    $inviter=fetch_user($_POST['friend_id']);
	    			    
	    			    $inviter_fname=get_additional_field($_POST['friend_id'], 'first_name', 'user');
	    			    $inviter_lname=get_additional_field($_POST['friend_id'], 'last_name', 'user');
	    			    $name=$inviter_fname." ".$inviter_lname;
	    			    
	    			    if(empty($name))
	    			    $name=$inviter['ldisplay_name'];
	    			    
	    			    request_approved_mail($inviter['lemail'],$name, $_POST['user_id'],$invited['lsex']);
	    			}
	    		}elseif($_POST['request_type']=='confirm_nofollow'){
	    			if(!edit_friendship($_POST['user_id'],$_POST['friend_id'],'unfollow',false) && 
	    			   !edit_friendship($_POST['friend_id'],$_POST['user_id'],'connected',false)){
	    				echo "<div class='alert_red'>
				    	 		<h1 style='margin:0;padding:0px;'>Something went wrong !</h1>
				    	 		<p>Please try again later</p>
				    	 		</div>
				    		 ";
	    			}else{
	    			    $invited=fetch_user($_POST['user_id']);
	    			    $inviter=fetch_user($_POST['friend_id']);
	    			    
	    			    $inviter_fname=get_additional_field($_POST['friend_id'], 'first_name', 'user');
	    			    $inviter_lname=get_additional_field($_POST['friend_id'], 'last_name', 'user');
	    			    $name=$inviter_fname." ".$inviter_lname;
	    			    
	    			    if(empty($name))
	    			    $name=$inviter['ldisplay_name'];
	    			    
	    			    request_approved_mail($inviter['lemail'],$name, $_POST['user_id'],$invited['lsex']);
	    			}
	    		}
	    	}
	    	
	    	$dl=delete_friend_list_rel($_POST['id']);
			if(isset($_POST['thelist']) && count($_POST['thelist'])>0){
				
				foreach ($_POST['thelist'] as $key=>$val){
					$return=add_friend_list_rel($_POST['id'],$val);
				}
				
			}
			
			echo "<div class='alert_green'>Saving process has been sent succesfully.</div>
				  <script type='text/javascript'>
				  		setTimeout(function(){
				  			$('".$_POST['key']."').colorbox.close();
                         	
                    	}, 2000);
                    	
						setTimeout(function(){
				  			window.location='".$_POST['refresh']."'
                    	}, 3000);
                    	
				  </script>";

		}
		
		
	}elseif(isset($_POST['list_id'])){
		
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
	    	require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	require_once('../lumonata-classes/actions.php');
	    	if(edit_list_name($_POST['list_id'], $_POST['new_name'])){
	    		echo $_POST['new_name'];
	    	}
		}
	}elseif(isset($_POST['delete_list_id'])){
		
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
	    	require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	require_once('../lumonata-classes/actions.php');
	    	delete_friend_list($_POST['delete_list_id']);
		}
	}elseif(isset($_POST['friend_id'])){
		require_once('../lumonata_config.php');
		require_once 'user.php';
		if(is_user_logged()){
	    	require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	if(!(is_administrator($_POST['friend_id']) || is_administrator())){
	    		delete_friend($_POST['user_id'],$_POST['friend_id']);
	    	}elseif(isset($_POST['frq']) && !is_administrator($_POST['friend_id'])){
	    		delete_friend($_POST['user_id'],$_POST['friend_id']);
	    	}
		}
	}else{
		if(isset($_GET['coleked_id'])){
			
			require_once('../lumonata_config.php');
			require_once 'user.php';
			if(is_user_logged()){
				
				require_once('../lumonata_settings.php');
	    		require_once('settings.php');
	    		if(!defined('SITE_URL'))
				define('SITE_URL',get_meta_data('site_url'));
				echo colek_box($_GET['coleked_id']);
			}
		}elseif(isset($_POST['coleked_id'])){
			require_once('../lumonata_config.php');
			require_once 'user.php';
			if(is_user_logged()){
				require_once('../lumonata_settings.php');
	    		require_once('settings.php');
	    		require_once('mail.php');
	    		if(!defined('SITE_URL'))
				define('SITE_URL',get_meta_data('site_url'));
				
				if(colek_mail($_POST['coleked_id'],$_POST['person_who_colek_id'])){
					echo "OK";
				}else{
					echo "<div>Something went wrong. Please try again later</div>";
				}
			}
		}elseif(isset($_POST['search']) || isset($_POST['top_search'])){
			require_once('../lumonata_config.php');
			require_once 'user.php';
			if(is_user_logged()){
				require_once('../lumonata_settings.php');
	    		require_once('settings.php');
	    		
	    		if(!defined('SITE_URL'))
				define('SITE_URL',get_meta_data('site_url'));
				
				
				if(isset($_POST['fid'])){
					$friend_result=friend_search($_POST['s'],$_POST['fid']);
					$friend_of_friend=true;
				}else{
					if(isset($_POST['top_search'])){
						$friend_result=search_all_user($_POST['s']);
						$friend_of_friend=false;
					}else{
						$friend_result=friend_search($_POST['s'],$_COOKIE['user_id']);
						$friend_of_friend=false;
					}
				}
				
				if(count($friend_result)>0)
					if(isset($_POST['top_search']))
						echo top_search_result($friend_result);
					else 
						echo friends_list_array($friend_result,$friend_of_friend);
				else 
					echo "<div class=\"alert_yellow_form\">No result found for <em>".$_POST['s']."</em>.</div>";
				
			}
		}else{
			add_actions('friends','friendship');
		}
		
	}
	
	
	function colek_box($coleked_id){
		
		$user=fetch_user($coleked_id);
		
		
		switch ($user['lsex']){
			case 2:
				$heshe="she";
				$hisher="her";
				break;
				
			case 1:
				$heshe="he";
				$hisher="his";
				break;
				
		}
		return "<div id=\"colek_form\">
    					<div style=\"margin: 5px 0;\"><h3>Colek ".$user['ldisplay_name']."?</h3></div>
    					<div class=\"clearfix\">
    						<div style=\"float:left;width:50px;hight:50px;\">
    							<img src=\"".get_avatar($coleked_id,2)."\" />
    						</div>
    						<div style=\"float:left;width:250px;hight:100px;margin-left:2px;background:#ccc;color:#333;padding:10px;\" >
    							Colek ".$user['ldisplay_name']." and ".$heshe." will notified through ".$hisher." email to see your profile
    						</div>
    					</div>
    					
    					<div style=\"text-align: right;margin: 5px 0;\">
    						<img src=\"".get_admin_url()."/includes/media/loader.gif\" class=\"loading_colek\" style=\"display:none;\"  />
        	  				<input type=\"button\" name=\"colek\" value=\"Colek\" class=\"button\" />
        	  			</div>
    			</div>
    			
    			<script type=\"text/javascript\">
					$(function(){
						$('input[name=colek]').click(function(){
							$('.loading_colek').show();
							$.post('../lumonata-functions/friends.php',
									{ 
										'coleked_id' 		    : '".$user['luser_id']."',
									   	'person_who_colek_id'	: '".$_COOKIE['user_id']."',
									   	'send_colek'			: true
									},
									function(theResponse){
										if(theResponse=='OK'){
											$('.loading_colek').hide();
											$('#colek').colorbox.close();
										}else{
											$('#colek_form').html(theResponse);
										}
									}
							);
						});
					});
				</script>
    			";
	}
	function invitation_box(){
	    
	    $invite_credit=get_additional_field($_COOKIE['user_id'], "invite_limit", "user");
		if($invite_credit==-1)
		$invite_credit="unlimited";
		
		if($invite_credit>1 || $invite_credit==-1)
		    $invite_credit=$invite_credit." invitations";
		else 
		    $invite_credit=$invite_credit." invitation";
		
		return "<div id=\"invite_form\">
    				<form method='post' action=''>
    					<div style=\"margin: 5px 0;\"><h3>You have ".$invite_credit." credit</h3></div>
    					<div style=\"margin: 5px 0;\"><strong>Invite Friends to ".$_GET['list_name']." List</strong> (One email address per row)</div>
    					<textarea rows='5' cols='50' name='invited_email' style='border:1px solid #ccc;'></textarea>
    					
    					<div style=\"margin: 5px 0;\"><strong>Personal Message</strong></div>
    					<textarea rows='5' cols='50' name='personal_message' style='border:1px solid #ccc;height:50px;'></textarea>
    					
    					<div style=\"text-align: right;margin: 5px 0;\">
        	  				<input type=\"button\" name=\"invite_to_list\" value=\"Send Invites\" class=\"button\" />
        	  			</div>
    				</form>
    			</div>
				<script type=\"text/javascript\">
					$(function(){
						$('input[name=invite_to_list]').click(function(){
							
							$.post('../lumonata-functions/friends.php',
									{ 
										'emails' 				: $('textarea[name=invited_email]').val(),
										'personal_message'		: $('textarea[name=personal_message]').val(),
									   	'list_id'				: ".$_GET['list_id'].",
									   	'list_name'				: '".$_GET['list_name']."',
									   	'manage_list'			: 'invite',
									   	'send_invite'			: true
									},
									function(theResponse){
										$('#invite_form').html(theResponse);
									}
							);
						});
					});
				</script>
				";
	}
	
	function add_friends_to_list_box($user_id){
		global $db;
		
		
		$the_query=$db->prepare_query("SELECT * FROM lumonata_friendship
										   WHERE luser_id=%d AND lstatus='connected'",$_COOKIE['user_id']);
		$num_rows=$db->num_rows($db->do_query($the_query));
		
		$ln_arr=get_friend_list_by_id($_GET['list_id']);
		$friends=myfriends($user_id, 0, $num_rows);
		
		if(!defined('SITE_URL'))
		define('SITE_URL',get_meta_data('site_url'));
		$rdirect=get_state_url('friends');		
		$return="<form method=\"post\" action=\"../lumonata-functions/friends.php?manage_list=add_friend&amp;list_id=".$_GET['list_id']."&amp;redirect=".$rdirect."\">";
		$return.="<div style='font-weight:bold;background:#ccc;padding:5px;width:410px;font-size:14px;'>Add Friends to List</div>";
		$return.="<div style='font-weight:bold;padding:5px;width:410px;border-bottom:1px solid #ccc;'>".$ln_arr['llist_name']."</div>";
		if(count($friends)>0 && count($friends['fid'])>0){
			$return.="<div style=\"width:420px;height:350px;overflow:auto;\">";
			for($i=0;$i<count($friends['fid']);$i++){
				$sql=$db->prepare_query("SELECT * FROM lumonata_friends_list_rel
											WHERE lfriendship_id=%d AND lfriends_list_id=%d",$friends['fid'][$i],$_GET['list_id']);
				$r=$db->do_query($sql);
				if($db->num_rows($r)>0){
					$style="style=\"background-color:#FFCC66\"";
					$selected="<span style=\"display:none;\" >
							  <input type=\"checkbox\" id=\"checked_".$i."\" value=\"".$friends['fid'][$i]."\" name=\"selected[]\" checked=\"checked\" />
							  </span>";
				}else{
					$style="";
					$selected="<span style=\"display:none;\" >
								<input type=\"checkbox\" id=\"checked_".$i."\" value=\"".$friends['fid'][$i]."\" name=\"selected[]\" />
								</span>";
				}
				
				$return.="<div class=\"fl_item clearfix\" id=\"fl_item_".$i."\">";
					$return.="<div $style class=\"clearfix\" id=\"fl_item_bg_".$i."\">";
						$return.="<div class=\"fl_image\">";
							$return.="<img src=\"".get_avatar($friends['friend_id'][$i],2)."\" />";
						$return.="</div>";
						$return.="<div class=\"fl_name\">";
						$return.=$friends['name'][$i];
						$return.=$selected;
						$return.="</div>";
					$return.="</div>";
				$return.="</div>";
				$return.="<script type=\"text/javascript\">
							$(function(){
								$('#fl_item_".$i."').mouseover(function(){
									
									if(!$('#checked_".$i."').attr('checked'))
									$('#fl_item_bg_".$i."').css('background-color','#fff3db');
								});
								$('#fl_item_".$i."').mouseout(function(){
									if(!$('#checked_".$i."').attr('checked'))
									$('#fl_item_bg_".$i."').css('background-color','#FFF');
								});
								
								$('#fl_item_".$i."').click(function(){
									if($('#checked_".$i."').attr('checked')){
										$('#checked_".$i."').removeAttr('checked');
										$('#fl_item_bg_".$i."').css('background-color','#FFF');
									}else{
										$('#checked_".$i."').attr('checked','checked');
										$('#fl_item_bg_".$i."').css('background-color','#FFCC66');
									}
											
								});
							});					
					  </script>";
				
			}
			$return.="<br clear=\"left\" />";
			
			$return.="</div>";
			$return.="<div style=\"text-align:right;\">
						<input type=\"button\" name=\"close\" value=\"Cancel\" class=\"button\" >
						<input type=\"submit\" name=\"save\" value=\"Save\" class=\"button_bold\">
						<input type=\"hidden\" name=\"friend_list_id\" value=\"".$_GET['list_id']."\">
					  </div>
					  <script type=\"text/javascript\">
					  	$('input[name=close]').click(function(){
							$('#add_friend_to_list_".$_GET['list_id']."').colorbox.close();
						});
					  </script>
					  ";
			
		}
		$return.="</form>";
		return $return;
	}
	

	function delete_friend($user_id,$friend_id=null){
		global $db;
		if($friend_id==null)
			$query=$db->prepare_query("DELETE a,b 
										FROM lumonata_friendship a
										LEFT JOIN lumonata_friends_list_rel b ON a.lfriendship_id=b.lfriendship_id 
										WHERE a.luser_id=%d OR a.lfriend_id=%d",$user_id,$user_id);
		else 
			$query=$db->prepare_query("DELETE a,b 
										FROM lumonata_friendship a
										LEFT JOIN lumonata_friends_list_rel b ON a.lfriendship_id=b.lfriendship_id 
										WHERE (a.luser_id=%d AND a.lfriend_id=%d) OR (a.luser_id=%d AND a.lfriend_id=%d)",$user_id,$friend_id,$friend_id,$user_id);
				
		return $db->do_query($query);
	}
	
	function delete_friend_list($list_id){
		global $db;
		$query=$db->prepare_query("DELETE a,b FROM 
								   lumonata_friends_list a
								   LEFT JOIN lumonata_friends_list_rel b ON a.lfriends_list_id=b.lfriends_list_id 
								   WHERE a.lfriends_list_id=%d",$list_id);
		
		return $db->do_query($query);
	}
	function edit_list_name($list_id,$new_name){
		global $db;
		$query=$db->prepare_query("UPDATE 
								   lumonata_friends_list
								   SET llist_name=%s 
								   WHERE lfriends_list_id=%d",$new_name,$list_id);
		return $db->do_query($query);
	}
	function delete_friend_list_rel($friendship_id){
		global $db;
		$query=$db->prepare_query("DELETE FROM 
								   lumonata_friends_list_rel 
								   WHERE lfriendship_id=%d",$friendship_id);
		return $db->do_query($query);
	}
	function delete_friendship_by_listId($list_id){
		global $db;
		$query=$db->prepare_query("DELETE FROM 
								   lumonata_friends_list_rel 
								   WHERE lfriends_list_id=%d",$list_id);
		return $db->do_query($query);
	}
	function add_friend_list_rel($friendship_id,$friends_list_id){
		global $db;
		
		
		$query=$db->prepare_query("INSERT INTO lumonata_friends_list_rel(lfriendship_id,lfriends_list_id) 
									VALUES(%d,%s)",$friendship_id,$friends_list_id);
		return $db->do_query($query);
		
		
	}
	function num_friend_list_rel($friendship_id,$friends_list_id){
		global $db;
		
		$query=$db->prepare_query("SELECT * FROM lumonata_friends_list_rel 
									WHERE lfriendship_id=%d AND 
									lfriends_list_id=%d",
									$friendship_id,$friends_list_id);
		$r=$db->do_query($query);
		return $db->num_rows($r);
		
	}
	function add_multiple_friend_list_rel($user_id,$list_id,$friend_rel=array()){
		global $db;

		if(!is_array($friend_rel) || empty($friend_rel))
		return;
		
		
		//delete
		if(delete_friendship_by_listId($list_id))		
		//add the new one
		foreach ($friend_rel as $friendship_id=>$list_id){
			if(num_friend_list_rel($friendship_id,$list_id)<1){
				add_friend_list_rel($friendship_id,$list_id);
				
			}
		}
		
	}	
	function add_friend_list($user_id,$list_name){
		global $db;
		$num_my_friend_list=1;
		$num_my_friend_arr=get_friend_list($user_id);
		
		if(count($num_my_friend_arr)>0){
			$num_my_friend_list=count($num_my_friend_arr['friends_list_id'])+1;
		}
		$query=$db->prepare_query("INSERT INTO lumonata_friends_list(luser_id,llist_name,lorder) 
									VALUES(%d,%s,%d)",$user_id,$list_name,$num_my_friend_list);
		
		if($db->do_query($query))
			return mysql_insert_id();
	}
	
	function get_friend_list($user_id){
		global $db;
		$return=array();
		
		$query=$db->prepare_query("SELECT lfriends_list_id,llist_name 
								   FROM lumonata_friends_list
								   WHERE luser_id=%d ORDER BY lorder",$user_id);
		
		$result=$db->do_query($query);
		
		while($data=$db->fetch_array($result)){
			$return['friends_list_id'][]=$data['lfriends_list_id'];
			$return['list_name'][]=$data['llist_name'];
		}
		
		return $return;
	}
	function get_friend_list_by_id($flid){
		global $db;
		$return=array();
		
		$query=$db->prepare_query("SELECT lfriends_list_id,llist_name 
								   FROM lumonata_friends_list
								   WHERE lfriends_list_id=%d ORDER BY lorder",$flid);
		
		$result=$db->do_query($query);
		return $db->fetch_array($result);
		/*while($data=$db->fetch_array($result)){
			$return['friends_list_id'][]=$data['lfriends_list_id'];
			$return['list_name'][]=$data['llist_name'];
		}*/
		
		return $return;
	}
	
	function friendship_list_name($id){
		global $db;
		$return=array();
		
		$query=$db->prepare_query("SELECT a.llist_name 
								   FROM lumonata_friends_list a, lumonata_friends_list_rel b
								   WHERE b.lfriendship_id=%d AND a.lfriends_list_id=b.lfriends_list_id ",$id);
		
			
		$result=$db->do_query($query);
		while($data=$db->fetch_array($result)){
			$return['list_name'][]=$data['llist_name'];
		}
		
		return $return;
	}
	function flist_name($id){
		global $db;
		$return=array();
		
		$query=$db->prepare_query("SELECT a.llist_name 
								   FROM lumonata_friends_list a
								   WHERE a.lfriends_list_id=%d ",$id);
			
		$result=$db->do_query($query);
		while($data=$db->fetch_array($result)){
			$return['list_name'][]=$data['llist_name'];
		}
		
		return $return;
	}
	
	function friend_thumb_list($user_id){
		if(is_dashboard()){
			if(!isset($_GET['tab'])){
	   			$myfriends=myfriends($user_id, 0, 12);
	   			$flist_name=array();
	   			$list_id=0;
	   			$friend_cnt=count_all_friend($user_id);
			}else{
				$list_id=base64_decode($_GET['tab']); 
	   			$myfriends=myfriends($user_id, 0, 12,$list_id);
	   			$flist_name=flist_name(base64_decode($_GET['tab']));
	   			$friend_cnt=count_all_friend($user_id,$list_id);
			}
		}else{
			$friend_cnt=count_all_friend($user_id);
			$flist_name=array();
			$myfriends=myfriends($user_id, 0, 12);
		}
   				
		$friends_html='';
		tooltips('friends');
		
		
		
		
		if(count($flist_name)>0){
			$fl_name=" in ".$flist_name['list_name'][0];
			$fl_label=$flist_name['list_name'][0];
			$fl_label_to=' to '.$fl_label;
		}else{ 
			$fl_name='';
			$fl_label='';
			$fl_label_to='';
		}
		
		if(count($myfriends)>0){
						
			$friends_html.="<div  class='clearfix'>";
			$friends_html.="<h2>Friends $fl_name (".$friend_cnt.")</h2>";
			
			
			foreach ($myfriends['id'] as $key=>$val){
				$friends_html.="<div style='width:50px:height:50px;overflow:hidden;margin:3px 3px;float:left'>
									<a href=\"".get_state_url('my-profile')."&id=".$myfriends['id'][$key]."\" rel=\"friends\" title=\"".$myfriends['name'][$key]."\">
										<img src='".$myfriends['avatar'][$key]."' border='0' />
									</a>
								</div>";
			}
	   		
			if(is_dashboard()){
		    	$friends_html.="<script type=\"text/javascript\">
									$(function(){
										$('#invite_friend_fl').colorbox();
									});
								</script>	
								<div style='width:50px:height:50px;overflow:hidden;margin:5px 5px;float:left'>
									<a href=\"../lumonata-functions/friends.php?manage_list=invite&amp;list_name=".$fl_label."&list_id=".$list_id."\" id=\"invite_friend_fl\" rel=\"friends\" title=\"Add friends ".$fl_label_to."\">
										<img src='".get_theme_img()."/add-more-friend.png' border='1' />
									</a>
								</div>
								";
			}
			
			$friends_html.="</div>";
			
			if(is_dashboard()){
				$prolink=get_state_url('friends');
			}else{
				if(isset($_GET['tab']) && $_GET['tab']=="my-updates")
					$prolink=get_state_url('friends');
				elseif(isset($_GET['id']))
					$prolink=get_state_url('friends')."&tab=".$_GET['id'];
				else 
					$prolink=get_state_url('friends');
			}
			$friends_html.="<div style=\"background:#f0f0f0;border-bottom:1px solid #ccc;margin-bottom:10px;padding:3px;text-align:right;\">
									<a href=\"".$prolink."\">View All</a>
								</div>";
		}else{
			  if(is_dashboard()){
				  $friends_html.="<div  class='clearfix'>";
				  $friends_html.="<h2>Add Friend ".$fl_label_to."</h2>";
				  $friends_html.="	<script type=\"text/javascript\">
										$(function(){
											$('#invite_friend_fl').colorbox();
										});
									</script>	
									<div style='width:50px:height:50px;overflow:hidden;margin:5px 5px;float:left'>
										<a href=\"../lumonata-functions/friends.php?manage_list=invite&amp;list_name=".$fl_label."&list_id=".$list_id."\" id=\"invite_friend_fl\" rel=\"friends\" title=\"Add more friends\">
											<img src='".get_theme_img()."/add-more-friend.png' border='1' />
										</a>
									</div>
									";
				  $friends_html.="</div>";
			  }
		}
		return $friends_html;
   }
	function myfriend_lists($user_id,$friendship_id,$friendid,$key,$redirect,$is_friend_request=false,$request_type='add'){
		global $db;
		
			
		
		$html='';
		$query=$db->prepare_query("SELECT b.lfriendship_id,a.lfriends_list_id,a.llist_name 
								   FROM lumonata_friends_list a
								   LEFT JOIN lumonata_friends_list_rel b ON a.lfriends_list_id=b.lfriends_list_id AND b.lfriendship_id=%d
								   WHERE a.luser_id=%d ",$friendship_id,$user_id);
		
		$result=$db->do_query($query);
		
		$friend=fetch_user($friendid);
		
		if($is_friend_request){
			if($request_type=='add'){
				$title_label="Sending ".$friend['ldisplay_name']." a friend request.";
				$action_label="Send Request";
			}elseif($request_type=='confirm'){
				$title_label="Confirm ".$friend['ldisplay_name']." as a friend.";
				$action_label="Confirm Request";
			}elseif($request_type=='confirm_nofollow'){
				$title_label="Confirm ".$friend['ldisplay_name']." as a friend &amp; Unfollow.";
				$action_label="Confirm Request &amp; Unfollow";
			}
		}else{
			$title_label="Edit Friend List";
			$action_label="Save";
		}
		
		$html.="<div style='font-size:12px;width:400px;height:auto;overflow:hidden;' id='err'>";
		$html.="<div style='font-weight:bold;background:#ccc;padding:5px;'>$title_label</div>";
		$html.="<div style='margin:10px 0;padding:5px 0;' class='clearfix'>
					<div style='width:32px;height:32px;overflow:hidden;float:left;margin:0 5px 0 0;'>
						<img src='".get_avatar($friendid,3)."' alt='".$friend['ldisplay_name']."' title='".$friend['ldisplay_name']."' />
					</div>
					<div style='width:350px;height:32px;overflow:hidden;float:left;'>
						<strong>Create New List:</strong> 
						<input type='text' name='list' value='' style='border:1px solid #ccc;padding:3px;' />
						<input type='button' value='Add' name='add_new_list' class='button' />
					</div>
					<br clear='left' />
				</div>";
		$html.="<div style='background:#f0f0f0;border-top:1px solid #ccc;padding:5px;font-weight:bold;'>Add ".$friend['ldisplay_name']." to your friend list</div>";
		$html.="<script type='text/javascript'>
					$(function(){
						$('input[name=add_new_list]').click(function(){
							$.post('../lumonata-functions/friends.php',{
								'list_name' : $('input[name=list]').val()
							},
							function(theResponse){
								var count=$('.thelist').size();
								if(count==0){
									$('#friend_list').html('');
									$('#friend_list').append(theResponse);
								}else{
									$('.thelist:first').before(theResponse);
								}
								
							});
							$('input[name=list]').val('');
						});
						$('input[name=save_list]').click(function(){
							var tagsArray = new Array(); 
						   	 $('input:checked').each(function(id) {
						   	 	 message = $('input:checked').get(id);
						   	 	 tagsArray.push(message.value);   
							 });
							
							
							$.post('../lumonata-functions/friends.php',{ 
							'thelist[]'		: tagsArray,
							'refresh' 		: '".$redirect."' ,
							'id'			: '".$friendship_id."',
							'key'			: '".$key."',
							'saving_list'	: true,
							'user_id'		: '".$user_id."',
							'friend_id'		: '".$friendid."',
							'request_type' 	: '".$request_type."',
							'is_friend_request': '".$is_friend_request."'
							},
							function(theResponse){
								$('#err').html(theResponse);
							});
						});
						
						$('input[name=close_list]').click(function(){
							$('".$key."').colorbox.close();
						});
					});
				</script>";
		
		
		//$html.="<form action='' method='POST' id='form_list'><span id='err'></span>";
		$html.="<div id='friend_list' style='width:100%;height:200px;overflow:auto;'>";
		if($db->num_rows($result)<1){
			$html.="<div class='alert_yellow' style='margin:5px 0;'>You don't have any friend list</div>";
		}
		
		
			
		while($data=$db->fetch_array($result)){
			if($data['lfriendship_id']!=NULL){
				$checked="checked='checked'";
			}else{
				$checked="";
			}
			
			$html.="<div style='border-bottom:1px solid #f0f0f0;margin:5px 0;padding-bottom:5px;' id='list_id_".$data['lfriends_list_id']."' class='thelist'>
						<div style='width:310px;height:auto;float:left;cursor:pointer;' id='list_panel_".$data['lfriends_list_id']."'>
							<input name='thelist[]' type='checkbox' value='".$data['lfriends_list_id']."' $checked />
							
							<a href='javascript:;' style='text-decoration:none;font-weight:bold;' title='Click to Edit' id='edit_list_".$data['lfriends_list_id']."'>
								".$data['llist_name']."<span id='click_to_edit_".$data['lfriends_list_id']."' style='font-size:9px;color:#ccc;margin-left:5px;display:none;'>Click to edit</span>
							</a>
							<span id='list_text_".$data['lfriends_list_id']."' style='display:none;' >
								<input type=\"text\" value=\"".$data['llist_name']."\" id=\"editedname_".$data['lfriends_list_id']."\" style='border:1px solid #ccc;width:80%;padding:4px;' />
							</span>
						</div>
						<div style='width:60px;font-size:10px;float:left;'>
							<a href='javascript:;' id='delete_list_".$data['lfriends_list_id']."'>Delete List</a>
						</div>
						<br clear='left' />
					</div>";
			$html.="<script type='text/javascript'>
						$(function(){
														
							$('#edit_list_".$data['lfriends_list_id']."').click(function(){
								$('#edit_list_".$data['lfriends_list_id']."').hide();
								$('#list_text_".$data['lfriends_list_id']."').show();
								$('#editedname_".$data['lfriends_list_id']."').focus();
							});
							
							$('#list_panel_".$data['lfriends_list_id']."').click(function(){
								$('#edit_list_".$data['lfriends_list_id']."').hide();
								$('#list_text_".$data['lfriends_list_id']."').show();
								$('#editedname_".$data['lfriends_list_id']."').focus();
							});
							
							$('#list_panel_".$data['lfriends_list_id']."').mouseover(function(){
								$('#click_to_edit_".$data['lfriends_list_id']."').show();
							});
							
							$('#list_panel_".$data['lfriends_list_id']."').mouseout(function(){
								$('#click_to_edit_".$data['lfriends_list_id']."').hide();
							});
							
							$('#delete_list_".$data['lfriends_list_id']."').click(function(){
								$('#loadit').show();
								$.post('../lumonata-functions/friends.php',
								{ 'delete_list_id'  : '".$data['lfriends_list_id']."'
								},function(theRespose){
									$('#list_id_".$data['lfriends_list_id']."').css({ 'background-color' : '#FF6666' }).delay(1000).fadeOut(80);
								});
								$('#loadit').hide();
							});
							
							$('#editedname_".$data['lfriends_list_id']."').blur(function(){
								$('#edit_list_".$data['lfriends_list_id']."').show();
								$('#list_text_".$data['lfriends_list_id']."').hide();
								
							});
							
							$('#editedname_".$data['lfriends_list_id']."').keypress(function(e){
								if(e.keyCode==13){
									$.post('../lumonata-functions/friends.php',
									{ 'list_id'  : '".$data['lfriends_list_id']."',
									  'new_name' : 	$('#editedname_".$data['lfriends_list_id']."').val()
									},function(theRespose){
										$('#edit_list_".$data['lfriends_list_id']."').html(theRespose);
									});
									
									$('#edit_list_".$data['lfriends_list_id']."').show();
									$('#list_text_".$data['lfriends_list_id']."').hide();
								}
							});
						});
						
					</script>";
		}
		
		$html.="</div>";
		$html.="<div style='text-align:right;margin-top:8px;'>
					<img src='http://".site_url()."/lumonata-admin/themes/".get_meta_data('admin_theme','themes')."/images/loader.gif' style='display:none;' id='loadit' />
					<input type='button' value='Cancel' name='close_list' class='button' />
					<input type='button' value='".$action_label."' name='save_list' class='button_bold' />
				</div>";
		//$html.="</form>";
		$html.='</div>';
		return $html;
	}
	function friendship(){
		if(!is_user_logged())
		header("location:".get_admin_url()."?redirect=".cur_pageURL());
		
		$count_friends_req=0;
		$friend_req=myfriend_requests($_COOKIE['user_id']);
		if(isset($friend_req['id']))
		$count_friends_req=count($friend_req['id']);
		
		if($count_friends_req>0)
		$req_no='('.$count_friends_req.')';
		else 
		$req_no='';
		
		$tabs=array('friends'=>'Friends','friend-requests'=>'Friend Requests '.$req_no,'manage-friend-list'=>'Manage Friend List','invite-friends'=>'Invite Friends');
		if(!isset($_GET['tab']) || $_GET['tab']=='friends'){
			return the_friends($tabs);
		}elseif($_GET['tab']=='friend-requests'){
			return friend_requests($tabs);
		}elseif($_GET['tab']=='invite-friends'){
			return invite_friends($tabs);
		}elseif($_GET['tab']=='manage-friend-list'){
			return manage_user_list($tabs);
		}elseif(isset($_GET['tab']) && is_numeric($_GET['tab'])){
			return friend_of_friend($_GET['tab']);
		}elseif($_GET['tab']=='search'){
			return fsearch_results();
		}
		
	}
	function manage_user_list($tabs){
		global $db;$followjs='';
		
		if(isset($_POST['add_list'])){
			add_friend_list($_COOKIE['user_id'], $_POST['friend_list']);
		}
		
		//set template
		set_template(TEMPLATE_PATH."/friends.html",'friends');
		
		//set block
		add_block('friendshipBlock','fBlock','friends');
		add_block('inviteFriendsBlock','ifBlock','friends');
		add_block('friendsRequestBlock','frBlock','friends');
		add_block('manageFriendListBlock','mflBlock','friends');
		
		//Add variable
		//configure the tabs
		$tabb='';
		if(empty($_GET['tab']))
			$the_tab='friends';
		else
			$the_tab=$_GET['tab'];
		

		add_actions('section_title','Manage Friend List');	
		add_variable('title','Manage Friend List');
		add_variable('tabs',set_tabs($tabs,$the_tab));
		
		$the_fl=get_friend_list($_COOKIE['user_id']);
		$fl='
			<form method="post" action="" >
		       <div class="add_new_friend_list_box">
			       Add New Friend List: 
			       <input type="text" name="friend_list" class="medium_textbox" />
			       <input type="submit" name="add_list" value="Add" class="button" />
		       </div>
	       </form>
	       <div class="add_new_friend_list_box" style="background:#FFCC66;">Drag the list to make your priority order</div>
	       <div id="tfriend_list">';
		if(count($the_fl)>0){
			foreach ($the_fl['friends_list_id'] as $key=>$val){
				$fl.='
				   <div class="friends_list_item clearfix" id="friendItem_'.$the_fl['friends_list_id'][$key].'">
				   		<div class="friends_list_name">
				   			<p id="edit_label_'.$the_fl['friends_list_id'][$key].'" class="list_name">'.$the_fl['list_name'][$key].'</p>
				   			<p id="text_list_'.$the_fl['friends_list_id'][$key].'" class="textlist" style="display:none;">
				   				<input type="text" value="'.$the_fl['list_name'][$key].'" name="txt_list_'.$the_fl['friends_list_id'][$key].'"  class="medium_textbox" />
				   			</p>
				   		</div>
				   		<div class="invite_friends_list">
					    	<p id="invite_friend_'.$the_fl['friends_list_id'][$key].'">
						    	<a  href="../lumonata-functions/friends.php?manage_list=invite&amp;list_name='.$the_fl['list_name'][$key].'&list_id='.$the_fl['friends_list_id'][$key].'" id="invite_'.$the_fl['friends_list_id'][$key].'" >
						    		Invite friends to list
						    	</a>
					    	</p>
				    	</div>
				   		<div class="invite_friends_list">
					    	<p>
						    	<a  href="../lumonata-functions/friends.php?manage_list=add_friend&amp;list_id='.$the_fl['friends_list_id'][$key].'" id="add_friend_to_list_'.$the_fl['friends_list_id'][$key].'" >
						    		Add friends to list
						    	</a>
					    	</p>
				    	</div>
				    	<div class="delete_friends_list"><p><a href="javascript:;" rel="delete_'.$the_fl['friends_list_id'][$key].'">&nbsp;</a></p></div>
				   </div> ';
				$fl.=manage_friend_list_js($the_fl['friends_list_id'][$key]);
				$fl.=delete_confirmation_box($the_fl['friends_list_id'][$key], 
					"Are you sure want to delete &quot;".$the_fl['list_name'][$key]."&quot; as your friend list?", 
					"../lumonata-functions/friends.php", 
					"friendItem_".$the_fl['friends_list_id'][$key],
					"delete_list_id=".$the_fl['friends_list_id'][$key]);
			}
		}
		$fl.='</div>';
		add_variable('friend_list',$fl);
		
		parse_template('manageFriendListBlock','mflBlock');
		
		return return_template('friends');
		
	}
	function manage_friend_list_js($id){
		return "<script type=\"text/javascript\">
					$(document).ready(function(){ 	
						$(function() {
							$(\"#tfriend_list\").sortable({ axis:'y',opacity: 0.8, cursor: 'move', update: function() {
								var order = $(this).sortable(\"serialize\") + '&sort_list=true'; 
								$.post(\"../lumonata-functions/friends.php\", order, function(theResponse){
									$(\"#response\").html(theResponse);
								}); 															 
							}								  
							});
						});
						
						$(function(){
							$('#edit_label_".$id."').click(function(){
								$('.list_name').show();
								$('.textlist').hide();
								$('#edit_label_".$id."').hide();
								$('#text_list_".$id."').show();
								$('#text_list_".$id."').focus();
							});
						});
						
						$('#text_list_".$id."').keypress(function(e){
							if(e.keyCode==13){
								
								$.post('../lumonata-functions/friends.php',
								{ 'list_id'  : '".$id."',
								  'new_name' : 	$('input[name=txt_list_".$id."]').val()
								},function(theRespose){
									$('#edit_label_".$id."').html(theRespose);
								});
								
								$('#edit_label_".$id."').show();
								$('#text_list_".$id."').hide();
							}
						});
						
						$('#invite_".$id."').colorbox();
						$('#add_friend_to_list_".$id."').colorbox();
					
					});	
				</script>";
	}
	function count_all_friend($user_id,$list_id=0){
		global $db;
		if(empty($list_id))
			$query=$db->prepare_query("SELECT *
										FROM lumonata_friendship a
										WHERE a.luser_id=%d AND (a.lstatus='connected' OR a.lstatus='unfollow') 
										",$user_id);
		else
			$query=$db->prepare_query("SELECT *
										FROM lumonata_friendship a, lumonata_friends_list_rel b
										WHERE a.luser_id=%d 
										AND (a.lstatus='connected' OR a.lstatus='unfollow') 
										AND b.lfriendship_id=a.lfriendship_id 
										AND b.lfriends_list_id=%d 
										",$user_id,$list_id);
			
		return $num=$db->num_rows($db->do_query($query));
	}
	function myfriends($user_id,$limit,$viewed,$bylist=0){
		global $db;
		$friends=array();
		if(empty($bylist))
			$query=$db->prepare_query("SELECT a.lfriendship_id,a.lfriend_id,a.lstatus
										FROM lumonata_friendship a, lumonata_users b
										WHERE a.luser_id=%d AND (a.lstatus='connected' OR a.lstatus='unfollow') AND a.lfriend_id=b.luser_id
										ORDER BY b.ldlu DESC 
										LIMIT %d,%d",$user_id,$limit,$viewed);
		else 
			$query=$db->prepare_query("SELECT a.lfriendship_id,a.lfriend_id,a.lstatus
										FROM lumonata_friendship a, lumonata_friends_list_rel b , lumonata_users c
										WHERE  a.lfriendship_id=b.lfriendship_id  AND 
											   b.lfriends_list_id=%d AND 
											   a.luser_id=%d AND 
											   (a.lstatus='connected' OR a.lstatus='unfollow') AND 
											   a.lfriend_id=c.luser_id
										ORDER BY c.ldlu DESC 
										LIMIT %d,%d",$bylist,$user_id,$limit,$viewed);

		
			
		$result=$db->do_query($query);
		while($friend=$db->fetch_array($result)){
			$user=fetch_user($friend['lfriend_id']);
			$friends['fid'][]=$friend['lfriendship_id'];
			$friends['username'][]=$user['lusername'];
			$friends['name'][]=$user['ldisplay_name'];
			$friends['id'][]=$user['luser_id'];
			$friends['avatar'][]=get_avatar($user['luser_id'], 2);
			$friends['email'][]=$user['lemail'];
			$friends['status'][]=$friend['lstatus'];
			$friends['friend_id'][]=$friend['lfriend_id'];
			
		}
		return $friends;
	}
	function search_all_user($terms){
		global $db;
		$users=array();
		
		$sql=$db->prepare_query("SELECT * FROM lumonata_users 
								 WHERE ( ldisplay_name like %s 
								 OR lemail like %s 
								 OR lusername like %s ) 
								 AND lstatus=1 order by ldlu desc
								 LIMIT 5",
								 "%".$terms."%",
								 "%".$terms."%",
								 "%".$terms."%");
		$result=$db->do_query($sql);
		
		while($user=$db->fetch_array($result)){
			
			$users['username'][]=$user['lusername'];
			$users['name'][]=$user['ldisplay_name'];
			$users['id'][]=$user['luser_id'];
			$users['avatar'][]=get_avatar($user['luser_id'], 2);
			$users['email'][]=$user['lemail'];

			
		}
		
		return $users;
	}
	function friend_search($sterms='',$user_id){
		global $db;
		$friends=array();
		$viewed=list_viewed();
		
		if(!empty($sterms) && !empty($user_id))
			$query=$db->prepare_query("SELECT a.lfriendship_id,a.lfriend_id,a.lstatus
										FROM lumonata_friendship a, lumonata_users b
										WHERE a.luser_id=%d 
										AND (a.lstatus='connected' OR a.lstatus='unfollow') 
										AND a.lfriend_id=b.luser_id 
										AND b.ldisplay_name like %s
										ORDER BY b.ldlu DESC",$user_id,"%".$sterms."%");
		elseif(empty($sterms) && !empty($user_id)) 
			$query=$db->prepare_query("SELECT a.lfriendship_id,a.lfriend_id,a.lstatus
										FROM lumonata_friendship a, lumonata_users b
										WHERE a.luser_id=%d AND (a.lstatus='connected' OR a.lstatus='unfollow') AND a.lfriend_id=b.luser_id
										ORDER BY b.ldlu DESC 
										LIMIT %d,%d",$user_id,0,$viewed);
		
			
		$result=$db->do_query($query);
		while($friend=$db->fetch_array($result)){
			$user=fetch_user($friend['lfriend_id']);
			$friends['fid'][]=$friend['lfriendship_id'];
			$friends['username'][]=$user['lusername'];
			$friends['name'][]=$user['ldisplay_name'];
			$friends['id'][]=$user['luser_id'];
			$friends['avatar'][]=get_avatar($user['luser_id'], 2);
			$friends['email'][]=$user['lemail'];
			$friends['status'][]=$friend['lstatus'];
			$friends['friend_id'][]=$friend['lfriend_id'];
			
		}
		return $friends;
	}
	function is_my_friend($user_id,$friend_id,$status='connected'){
		global $db;
		$query=$db->prepare_query("SELECT * FROM lumonata_friendship
									WHERE luser_id=%d AND lfriend_id=%d AND lstatus=%s",$user_id,$friend_id,$status);
		if($db->num_rows($db->do_query($query))>0){
			return true;
		}
		
		return false;
	}
	function myfriend_requests($user_id){
		global $db;
		$friends=array();
		$query=$db->prepare_query("SELECT * FROM lumonata_friendship
									WHERE luser_id=%d AND lstatus='onrequest'",$user_id);
		$result=$db->do_query($query);
		while($friend=$db->fetch_array($result)){
			$user=fetch_user($friend['lfriend_id']);
			$friends['fid'][]=$friend['lfriendship_id'];
			$friends['username'][]=$user['lusername'];
			$friends['name'][]=$user['ldisplay_name'];
			$friends['id'][]=$user['luser_id'];
			$friends['avatar'][]=get_avatar($user['luser_id'], 2);
			$friends['email'][]=$user['lemail'];
			$friends['status'][]=$friend['lstatus'];
			
		}
		return $friends;
	}
	function the_fs_list($friendship_id){
		$listed=friendship_list_name($friendship_id);
		$flist='';
		
		if(count($listed)>0){
			$flist.="<span style='font-size:9px;color:#ccc;'>";
			$i=1;
			foreach ($listed['list_name'] as $lkey => $lval){
				if($i!=count($listed['list_name']))
					$flist.=$lval.", ";
				else 
					$flist.=$lval;
				
				$i++;
			}
			$flist.="</span>";
		}
		
		return $flist;
	}
	function the_friends($tabs){
		global $db;$followjs='';
		//set template
		set_template(TEMPLATE_PATH."/friends.html",'friends');
		
		//set block
		add_block('friendshipBlock','fBlock','friends');
		add_block('inviteFriendsBlock','ifBlock','friends');
		add_block('friendsRequestBlock','frBlock','friends');
		add_block('manageFriendListBlock','mflBlock','friends');
		
		//Add variable
		//configure the tabs
		$tabb='';
		if(empty($_GET['tab']))
			$the_tab='friends';
		else
			$the_tab=$_GET['tab'];
		

		add_actions('section_title','Friends');	
		add_variable('title','Friends');
		add_variable('tabs',set_tabs($tabs,$the_tab));
		
		$html='<span id="response"></span>';
		
		$viewed=list_viewed();
        if(isset($_GET['page'])){
            $page= $_GET['page'];
        }else{
            $page=1;
        }
        
        $limit=($page-1)*$viewed;
		
		if(isset($_GET['friend_list'])){
			$friends=myfriends($_COOKIE['user_id'],$limit,$viewed,$_GET['friend_list']);
			$err="You don't have any friend in this list yet";
			
			$the_query=$db->prepare_query("SELECT a.lfriendship_id,a.lfriend_id 
											FROM lumonata_friendship a, lumonata_friends_list_rel b 
											WHERE  	a.lfriendship_id=b.lfriendship_id  AND 
											   		b.lfriends_list_id=%d AND 
											   		a.luser_id=%d AND 
											   		a.lstatus='connected'",$_GET['friend_list'],$_COOKIE['user_id']);
			$num_rows=$db->num_rows($db->do_query($the_query));
			$url=cur_pageURL()."&page=";
		}else{
			$err="You don't have any friend yet.";
			$friends=myfriends($_COOKIE['user_id'],$limit,$viewed);
			
			$the_query=$db->prepare_query("SELECT * FROM lumonata_friendship
										   WHERE luser_id=%d AND lstatus='connected'",$_COOKIE['user_id']);
			$num_rows=$db->num_rows($db->do_query($the_query));
			$url=cur_pageURL()."&page=";
			
		}
		
		if(count($friends)==0){
			$html="<div class=\"alert_yellow_form\">".$err."</div>";
		}else{	
			
			$html.=friends_list_array($friends);
			$html.="<div class=\"paging_right\">". paging($url,$num_rows,$page,$viewed,10)."</div>";
			$friendlist=get_friend_list($_COOKIE['user_id']);
		
			$fl_html="";
			if(count($friendlist)>0)
				foreach ($friendlist['friends_list_id'] as $key=>$val){
					$fl_html.="<div class=\"friend_list\"><a href=\"".get_tab_url('friends')."&friend_list=".$friendlist['friends_list_id'][$key]."\">".$friendlist['list_name'][$key]."</a></div>";	
				}
				$fl_html.="<div class=\"friend_list\"><a href=\"".get_tab_url('friends')."\">All Friends</a></div>";
			
			add_variable('friend_list',$fl_html);
		}
			
		
		add_variable('myfriends_list',$html);
		add_variable('friends_search',search_box('../lumonata-functions/friends.php','friendship','search=true&','left','alert_green_form','Search Friends'));
		parse_template('friendshipBlock','fBlock');
		return return_template('friends'); 	
		
	}
	function friend_of_friend($fid){
		global $db;$followjs='';
		//set template
		set_template(TEMPLATE_PATH."/friends.html",'friends');
		
		//set block
		add_block('friendshipBlock','fBlock','friends');
		add_block('inviteFriendsBlock','ifBlock','friends');
		add_block('friendsRequestBlock','frBlock','friends');
		add_block('manageFriendListBlock','mflBlock','friends');
		
		//Add variable
		//configure the tabs
		$friend=fetch_user($fid);
		
		$tabs=array($fid =>"Friends");
		
		$tabb='';
		if(empty($fid))
			$the_tab='friends';
		else
			$the_tab=$fid;
		

		add_actions('section_title',$friend['ldisplay_name']."'s Friends");	
		add_variable('title',$friend['ldisplay_name']."'s Friends");
		add_variable('tabs',set_tabs($tabs,$the_tab));
		
		$html='<span id="response"></span>';
		
		$viewed=list_viewed();
        if(isset($_GET['page'])){
            $page= $_GET['page'];
        }else{
            $page=1;
        }
        
        $limit=($page-1)*$viewed;
		
		
		$err="You don't have any friend yet.";
		$friends=myfriends($fid,$limit,$viewed);
		
		$the_query=$db->prepare_query("SELECT * FROM lumonata_friendship
									   WHERE luser_id=%d AND lstatus='connected'",$fid);
		
		$num_rows=$db->num_rows($db->do_query($the_query));
		$url=cur_pageURL()."&page=";
			
		
		
		if(count($friends)==0){
			$html="<div class=\"alert_yellow_form\">".$err."</div>";
		}else{	
			
			$html.=friends_list_array($friends,true);
			$html.="<div class=\"paging_right\">". paging($url,$num_rows,$page,$viewed,10)."</div>";

			$fl_html="<div>".dashboard_invite_friends()."</div>";
			
			add_variable('friend_list',$fl_html);
		}
			
		
		add_variable('myfriends_list',$html);
		add_variable('friends_search',search_box('../lumonata-functions/friends.php','friendship','search=true&fid='.$fid.'&','left','alert_green_form','Search Friends'));
		parse_template('friendshipBlock','fBlock');
		return return_template('friends'); 
	}
	
	function fsearch_results(){
		global $db;$followjs='';
		
		if(isset($_GET['s']))
		$terms=kses($_GET['s'],array());
		else 
		$terms="";
		//set template
		set_template(TEMPLATE_PATH."/friends.html",'friends');
		
		//set block
		add_block('friendshipBlock','fBlock','friends');
		add_block('inviteFriendsBlock','ifBlock','friends');
		add_block('friendsRequestBlock','frBlock','friends');
		add_block('manageFriendListBlock','mflBlock','friends');
		
		//Add variable
		//configure the tabs
		
		
		$tabs=array('search' =>"Search For ".$terms);
		
		$tabb='';
		if(empty($fid))
			$the_tab='search';
		else
			$the_tab='search';
		

		add_actions('section_title',"Search Results");	
		add_variable('title',"Search Results");
		add_variable('tabs',set_tabs($tabs,$the_tab));
		
		$html='<span id="response"></span>';
		
		$viewed=list_viewed();
        if(isset($_GET['page'])){
            $page= $_GET['page'];
        }else{
            $page=1;
        }
        
        $limit=($page-1)*$viewed;
		
		
		$err="Result not found.";
		$friends=search_all_user($terms);
		
		if(isset($friends['id']))		
			$num_rows=count($friends['id']);
		else
			$num_rows=0;
		
		$url=cur_pageURL()."&page=";
			
		
		
		if(count($friends)==0){
			$html="<div class=\"alert_yellow_form\">".$err."</div>";
		}else{	
			
			$html.=friends_list_array($friends,true,true);
			$html.="<div class=\"paging_right\">". paging($url,$num_rows,$page,$viewed,10)."</div>";

			$fl_html="<div>".dashboard_invite_friends()."</div>";
			
			add_variable('friend_list',$fl_html);
		}
			
		
		add_variable('myfriends_list',$html);
		//add_variable('friends_search',search_box('../lumonata-functions/friends.php','friendship','search=true&fid='.$fid.'&','left','alert_green_form','Search Friends'));
		parse_template('friendshipBlock','fBlock');
		return return_template('friends'); 
	}
	
	function friends_list_array($friends=array(),$friend_of_friend=false,$search_all_user=false){
		$html='';
		$followjs='';
		if(count($friends)<1)
		return;
		
		foreach($friends['id'] as $key=>$value){
				if(!$search_all_user)
				$flist=the_fs_list($friends['fid'][$key]);
				else 
				$flist='';
				
				$follow_label='';
				
				if(!(is_administrator($friends['id'][$key]))){
					if(!$search_all_user)
						if($friends['status'][$key]=='connected'){
							$follow_label="<p><a class=\"button_add_friend\" id=\"follow_unfollow_".$key."\" >Unfollow</a></p>";
						}else{ 
							$follow_label="<p><a class=\"button_add_friend\" id=\"follow_unfollow_".$key."\" >Follow</a></p>";
						}
					$followjs="$('#follow_unfollow_".$key."').click(function(){
					   				label=jQuery.trim($('#follow_unfollow_".$key."').html());
					   				
					   				if(label=='Follow'){
						   				$.post('../lumonata-functions/friends.php',{ 'follow' : 'follow', 'id' : '".$friends['id'][$key]."' },
						   				function(theResponse){
						   					if(theResponse=='OK'){
						   						$('#follow_unfollow_".$key."').html('Unfollow');
											}
										});
									}else if(label=='Unfollow'){
										$.post('../lumonata-functions/friends.php',{ 'unfollow' : 'unfollow', 'id' : '".$friends['id'][$key]."' },
						   				function(theResponse){
						   					if(theResponse=='OK'){
						   						$('#follow_unfollow_".$key."').html('Follow');
											}
										});
									}
									
								});";
				}
				
				if($friend_of_friend==false){
					$html.='<div class="friends_item clearfix" id="friends_item_'.$key.'">
						    	<div class="friends_avatar">
						    		<a href="'.get_state_url('my-profile').'&id='.$friends['id'][$key].'">
						    			<img src="'.$friends['avatar'][$key].'" title="'.$friends['name'][$key].'" alt="'.$friends['name'][$key].'" />
						    		</a>
						    	</div>
						    	<div class="friends_name"><p><a href="'.get_state_url('my-profile').'&id='.$friends['id'][$key].'">'.$friends['name'][$key].'</a> '.$flist.'</p></div>
						    	<div class="edit_friends_list"><p style="display: none;" id="edit_list_'.$key.'"><a href="../lumonata-functions/friends.php?editlist=true&id='.$friends['fid'][$key].'&friend_id='.$friends['id'][$key].'&redirect='.urlencode(cur_pageURL()).'&key=#colorbox_'.$key.'" id="colorbox_'.$key.'" >Edit Lists</a></p></div>
						    	<div class="follow_unfollow">'.$follow_label.'</div>';
								
								if(!(is_administrator($friends['id'][$key]) || is_administrator())){			
							    	$html.='<div class="delete_friends_list"><p><a href="javascript:;" rel="delete_'.$friends['id'][$key].'">&nbsp;</a></p></div>';
							    	$delete_msg="Are you sure want to delete ".$friends['name'][$key]." from your friend list?";
							    	add_actions('admin_tail','delete_confirmation_box',$friends['id'][$key],$delete_msg,'../lumonata-functions/friends.php','friends_item_'.$key,'friend_id='.$friends['id'][$key].'&user_id='.$_COOKIE['user_id']);
								}
								
					$html.='</div>';
					
					$html.="<script type=\"text/javascript\">";
					$html.="$(function(){";
						$html.=$followjs;			
						$html.="$('#friends_item_".$key."').mouseover(function(){
					        		$('#edit_list_".$key."').show();
					        	});
					        	
					    		$('#friends_item_".$key."').mouseout(function(){
					        		$('#edit_list_".$key."').hide();
					        	});
					        	
					        	$('#colorbox_".$key."').colorbox();
							});
				    		
				    	</script>";
				}else{
					
					if(($_COOKIE['user_id']==$friends['id'][$key]) || is_my_friend($_COOKIE['user_id'], $friends['id'][$key],'connected') || is_my_friend($_COOKIE['user_id'], $friends['id'][$key],'unfollow')){
						$follow_label='';
					}elseif(is_my_friend($_COOKIE['user_id'], $friends['id'][$key],'pending')){
						$follow_label="<span style='color:#CCC;'>Request pending.</span>";
					}elseif(is_my_friend($_COOKIE['user_id'], $friends['id'][$key],'onrequest')){
						$follow_label="<p><a class=\"button_add_friend\" href=\"../lumonata-functions/friends.php?add_friend=true&type=confirm&friendship_id=".$friends['fid'][$key]."&friend_id=".$friends['id'][$key]."&redirect=".urlencode(cur_pageURL())."&key=#add_friend\" id=\"add_friend_".$key."\" >Confirm Request</a></p>";
						$follow_label.="<script type=\"text/javascript\">
								   			$('#add_friend_".$key."').click(function(){
								   				$('#add_friend_".$key."').colorbox();
											});
								   		</script>";
					}else{
						$follow_label="<p><a class=\"button_add_friend\" href=\"../lumonata-functions/friends.php?add_friend=true&type=add&friendship_id=0&friend_id=".$friends['id'][$key]."&redirect=".urlencode(cur_pageURL())."&key=#add_friend\" id=\"add_friend_".$key."\" >Add as friend</a></p>";
						$follow_label.="<script type=\"text/javascript\">
								   			$('#add_friend_".$key."').click(function(){
								   				$('#add_friend_".$key."').colorbox();
											});
								   		</script>";
					}
					
					$html.='<div class="friends_item clearfix" id="friends_item_'.$key.'">
						    	<div class="friends_avatar">
						    		<a href="'.get_state_url('my-profile').'&id='.$friends['id'][$key].'">
						    			<img src="'.$friends['avatar'][$key].'" title="'.$friends['name'][$key].'" alt="'.$friends['name'][$key].'" />
						    		</a>
						    	</div>
						    	<div class="friends_name_fof"><p><a href="'.get_state_url('my-profile').'&id='.$friends['id'][$key].'">'.$friends['name'][$key].'</a> '.$flist.'</p></div>
						    	<div class="fof_add_friend">'.$follow_label.'</div>';
								
								
					$html.='</div>';
				}
				
			}
			return $html;
	}
	
	function top_search_box(){
		add_actions('header_elements','top_search_box_js');
		$box="<div class=\"top_search_wrapper\">
				
					<div class=\"clearfix\">
						<div class=\"input_search_wrapper\">
							<input type=\"text\" name=\"top_search\" value=\"Search\" class=\"search_top_text\" />
						</div>
						<div class=\"button_search_wrapper\">
							<input type=\"submit\" value=\" \" class=\"top_search_button\" id=\"top_search_button\" />
						</div>
					</div>
					
					
				
			</div>
			<div id=\"top_search_result_wrapper\" style=\"display:none;\" >
				<div class=\"top_search_result_wrapper\">
					<div id=\"top_search_result\">
						<br clear=\"all\" />
					</div>
					
				</div>
				<div class=\"more_search_result\">
					<img src=\"http://".SITE_URL."/lumonata-admin/includes/media/loader.gif\" id=\"top_search_loader\"  />
					<a href=\"?state=friends&tab=search\" id=\"more_result_link\"><strong>See more results</strong></a>
				</div>
			</div>
			";
			return $box;
	}
	function top_search_box_js(){
		
		$box="<script type=\"text/javascript\">
				var mouse_on_search=false;
				var activeSel = 0;
				var selected_pro='';
				
				$(document).ready(function(){
					$('input[name=top_search]').focus(function(){
						$('input[name=top_search]').val('');
					});
					
					$('input[name=top_search]').blur(function(){
						if($(this).val()=='')
							$('input[name=top_search]').val('Search');
					});
					
					$('#top_search_button').click(function(){
						if($('input[name=top_search]').val()!='Search'){
							location='?state=friends&tab=search&s='+$('input[name=top_search]').val();
						}
					});
					
					$('input[name=top_search]').keydown(function(event){
					});
					
					$('input[name=top_search]').keyup(function(event){
							var nItem = jQuery('.top_search_result').length;
							
							if(event.which=='38'){
								//alert('up');
								jQuery('.top_search_result').removeClass('active');
								if(activeSel==0){
									jQuery('.top_search_result:last').addClass('active');
									activeSel = nItem;
									//$('input[name=selected_index]').val(nItem);
								}else{
									activeSel -= 1;
									if(activeSel==0){ 
											activeSel=nItem;
											//$('input[name=selected_index]').val(nItem); 
									}
									jQuery('.top_search_result:eq('+(activeSel-1)+')').addClass('active');
									$('input[name=selected_index]').val(activeSel-1);
								}
								selected_pro=jQuery('.top_search_result.active a').attr('href');
								
							}else if(event.which=='40'){
								jQuery('.top_search_result').removeClass('active');
								if(activeSel==0){
									jQuery('.top_search_result:first').addClass('active');
									activeSel = 1;
								}else{
									activeSel += 1;
									if(activeSel>nItem){ activeSel=1; }
									jQuery('.top_search_result:eq('+(activeSel-1)+')').addClass('active');
								}
								selected_pro=jQuery('.top_search_result.active a').attr('href');

							}
							
							if(event.which!='38' && event.which!='40'){
								$('#top_search_result_wrapper').show();
								$('#more_result_link').hide();
								$('#top_search_loader').show();
								
								if($('input[name=top_search]').val()!='Search'){
									 $('#more_result_link').attr('href','?state=friends&tab=search&s='+$('input[name=top_search]').val());
								}
								$.post('../lumonata-functions/friends.php','top_search=true&s='+$('input[name=top_search]').val(),function(data){
									 $('#top_search_result').html(data);
									 $('#top_search_loader').hide();
									 $('#more_result_link').show();
									
								});
							}
							
							if(event.which=='13'){
								if(selected_pro!='')
									location=selected_pro;
							}
					
					});

					
										
                    $('#top_search_result_wrapper').mouseover(function(){ 
                           mouse_on_search=true;
                           
                    }).mouseleave(function(){ 
                           mouse_on_search=false;
                    });
                    
					$('body').mousedown(function(){
						if(!mouse_on_search){
							$('#top_search_result_wrapper').hide();
						}
					});
					
					
					
				});
			 </script>";
		
		return $box;
	}
	function top_search_result($friends=array()){
		
		$result="<div class=\"search_result_header\">People</div>";
		foreach($friends['id'] as $key=>$value){
			$key_1=$key+1;
			$result.="
						<div class=\"top_search_result clearfix\" id=\"top_search_result_".$key."\">
							<div class=\"top_search_avatar\">
								<a href=\"".get_state_url('my-profile')."&id=".$friends['id'][$key]."\">
									<img src=\"".$friends['avatar'][$key]."\" title=\"".$friends['name'][$key]."\" alt=\"".$friends['name'][$key]."\" />
								</a>
							</div>
							<div class=\"top_search_name\">
								<a href=\"".get_state_url('my-profile')."&id=".$friends['id'][$key]."\">
									<strong>".$friends['name'][$key]."</strong>
								</a>
							</div>
							
						</div>
						<script type=\"text/javascript\">
							$(function(){
								$('#top_search_result_".$key."').mouseover(function(){
									$('.top_search_result').removeClass('active');
									$(this).addClass('active');
									activeSel=".$key_1.";
									selected_pro=$('#top_search_result_".$key." a').attr('href');
								});
							});
						</script>
						";
		}
		
		
		return $result;
	}
	
	function colek_button($coleked_id){
		return "<a class=\"button_add_friend_user\" id=\"colek\" href=\"../lumonata-functions/friends.php?colek=true&coleked_id=".$coleked_id."\" >
					Colek
				</a>
				<script type=\"text/javascript\">
		   			$('#colek').click(function(){
		   				$('#colek').colorbox();
					});
		   		</script>
				";
	}
	function add_friend_button($friend_id,$friendship_id,$type='add',$text=''){
		if(empty($text)){
			if($type=='add')
				$text="Add as Friend";
			elseif($type=='confirm') 
				$text="Confirm Friend Request";
			elseif($type=='follow'){
				$text="Follow";
			}elseif($type=='unfollow'){
				$text="Unfollow";
			}
		}
		
		if($type=='follow' || $type=='unfollow'){
			if($type=='follow'){
				$return="<div style='margin:0px 0 10px 0;' >
							<a class=\"button_add_friend_user\" id=\"follow_unfollow\" >
								$text
							</a>";
				$return.=colek_button($friend_id);			
				$return.="<span id='load_follow' style='display:none;margin-left:5px;' >
								<img src='http://".TEMPLATE_URL."/images/loading.gif' />
							</span>
				   		</div>";
			}else{ 
				$return="<div style='margin:0px 0 10px 0;'  >
							<a class=\"button_add_friend_user\" id=\"follow_unfollow\" >
								$text
							</a>";
				$return.=colek_button($friend_id);
				$return.="<span id='load_follow' style='display:none;margin-left:5px;' >
								<img src='http://".TEMPLATE_URL."/images/loading.gif' />
							</span>
				   		</div>";
			}								
		 	$return.="<script type=\"text/javascript\">
		   			$(function(){
			   			$('#follow_unfollow').click(function(){
			   				label=jQuery.trim($('#follow_unfollow').html());
			   				$('#load_follow').show();
			   				if(label=='Follow'){
				   				$.post('../lumonata-functions/friends.php',{ 'follow' : 'follow', 'id' : '".$friend_id."' },
				   				function(theResponse){
				   					if(theResponse=='OK'){
				   						$('#follow_unfollow').html('Unfollow');
									}
								});
							}else if(label=='Unfollow'){
								$.post('../lumonata-functions/friends.php',{ 'unfollow' : 'unfollow', 'id' : '".$friend_id."' },
				   				function(theResponse){
				   					if(theResponse=='OK'){
				   						$('#follow_unfollow').html('Follow');
									}
								});
							}
							$('#load_follow').hide();
						});
						
					});
		   		</script>
		   		";
			return $return;
			
		}else{	
			
			$return="<div style='margin:0px 0 10px 0;' >
						<a class=\"button_add_friend_user\" href=\"../lumonata-functions/friends.php?add_friend=true&type=".$type."&friendship_id=".$friendship_id."&friend_id=".$friend_id."&redirect=".urlencode(cur_pageURL())."&key=#add_friend\" id=\"add_friend\" >
							$text
						</a>";
			$return.=colek_button($friend_id);			
			$return.="</div>
			   		<script type=\"text/javascript\">
			   			$('#add_friend').click(function(){
			   				$('#add_friend').colorbox();
						});
			   		</script>
			   		";
			return $return;
		}
	}
	function friend_requests($tabs){
		//set template
		set_template(TEMPLATE_PATH."/friends.html",'friends');
		
		//set block
		add_block('friendshipBlock','fBlock','friends');
		add_block('inviteFriendsBlock','ifBlock','friends');
		add_block('friendsRequestBlock','frBlock','friends');
		add_block('manageFriendListBlock','mflBlock','friends');
		
		//Add variable
		//configure the tabs
		$tabb='';
		if(empty($_GET['tab']))
			$the_tab='friends';
		else
			$the_tab=$_GET['tab'];
		

		add_actions('section_title','Friends Requests');	
		add_variable('title','Friends Requests');
		add_variable('tabs',set_tabs($tabs,$the_tab));
		//add_variable('avatar',get_avatar($_COOKIE['user_id'], 2));
		
		$friends=myfriend_requests($_COOKIE['user_id']);
		$html='';
		if(count($friends)==0){
			$html="<div class=\"alert_yellow_form\">You don't have any friend request.</div>";
			
		}else{		
			foreach ($friends['id'] as $key=>$value){
				$html.='<div class="friends_item clearfix"  id="friends_item_'.$key.'">
					    	<div class="friends_avatar">
					    		<a href="'.get_state_url('my-profile').'&id='.$friends['id'][$key].'">
					    			<img src="'.$friends['avatar'][$key].'" title="'.$friends['name'][$key].'" alt="'.$friends['name'][$key].'" />
					    		</a>
					    	</div>
					    	<div class="friends_name_request"><p><a href="'.get_state_url('my-profile').'&id='.$friends['id'][$key].'">'.$friends['name'][$key].'</a></p></div>
					    	<div class="edit_friends_list_request">
						    	<p id="edit_list_'.$key.'">
							    	<a  href="../lumonata-functions/friends.php?add_friend=true&type=confirm&friendship_id='.$friends['fid'][$key].'&friend_id='.$friends['id'][$key].'&redirect='.urlencode(cur_pageURL()).'&key=#confirm_'.$key.'" id="confirm_'.$key.'" >
							    		Confirm
							    	</a>
						    	</p>
					    	</div>
					    	<div class="follow_unfollow_request">
						    	<p id="edit_list_'.$key.'">
							    	<a class="button_add_friend"  href="../lumonata-functions/friends.php?add_friend=true&type=confirm_nofollow&friendship_id='.$friends['fid'][$key].'&friend_id='.$friends['id'][$key].'&redirect='.urlencode(cur_pageURL()).'&key=#confirm_'.$key.'" id="confirm_nofollow_'.$key.'" >
							    		No Follow
							    	</a>
						    	</p>
					    	</div>
					    	<div class="delete_friends_list"><p><a href="javascript:;" rel="delete_'.$friends['id'][$key].'">&nbsp;</a></p></div>
					    </div>
					    ';
				$delete_msg="Are you sure ".$friends['name'][$key]." is not your friend?";
				add_actions('admin_tail','delete_confirmation_box',$friends['id'][$key],$delete_msg,'../lumonata-functions/friends.php','friends_item_'.$key,'friend_id='.$friends['id'][$key].'&user_id='.$_COOKIE['user_id'].'&frq=true');
				$html.="<script type=\"text/javascript\">
				   			$('#confirm_".$key."').colorbox();
				   			$('#confirm_nofollow_".$key."').colorbox();
				   		</script>";
				
			}
		}
		
		add_variable('friend_requests_list', $html);
		add_variable('friends_search',search_box('../lumonata-functions/friends.php','friendship','','left','alert_green_form','Search Friends'));
		parse_template('friendsRequestBlock','frBlock');
		return return_template('friends'); 	
	}
	function get_available_services(){
		$available_services=array('gmail'=>array(
											'title'=>'Gmail',
											//'imgico'=>TEMPLATE_URL.'/images/gmail-icon.png',
											'imgico'=>TEMPLATE_URL.'/images/gmail-logo.png',
											'imgurl'=>TEMPLATE_URL.'/images/gmail-icon.png'
											),
									'yahoo'=>array(
											'title'=>'Yahoo!',
											//'imgico'=>TEMPLATE_URL.'/images/yahoo-icon.png',
											'imgico'=>TEMPLATE_URL.'/images/yahoo-logo.png',
											'imgurl'=>TEMPLATE_URL.'/images/yahoo-icon.png'
											),
											
									'hotmail'=>array(
											'title'=>'Windows Live Hotmail',
											//'imgico'=>TEMPLATE_URL.'/images/hotmail-icon.png',
											'imgico'=>TEMPLATE_URL.'/images/hotmail-logo.png',
											'imgurl'=>TEMPLATE_URL.'/images/hotmail-icon.png'
											),
									'csv'=>array(
											'title'=>'Import Contact File',
											//'imgico'=>TEMPLATE_URL.'/images/hotmail-icon.png',
											'imgico'=>TEMPLATE_URL.'/images/email-logo.png',
											'imgurl'=>TEMPLATE_URL.'/images/email-icon.png'
											)
									);	
		return $available_services;
	}
	function dashboard_invite_friends(){
		$available_services=get_available_services();
		$html="<div style='margin-bottom:10px;'>";
			$html.="<h2>Invite Your Friends</h2>";
			$html.="<p>Get connected with your friends and invite them to join. Find your friends in your Gmail, Yahoo and Hotmail account.</p>";
			$enc_available_services=base64_encode(json_encode($available_services));
			$theuri='http://'.site_url().'/lumonata-functions/openinviter/invite.php';
			foreach ($available_services as $key=>$value){
				if(is_array($value))
				$html.="<div style='margin:5px 0;'>
							<a style=\"background:url('http://".$value['imgurl']."') left center no-repeat;height:24px;display:block;text-decoration:none;padding-left:37px;\" href=\"".$theuri."?service=".$key."&logo=".urlencode($value['imgurl'])."&title=".$value['title']."&ts=".$enc_available_services."\" title=\"Invite Your Friends Easily\" class=\"openinviter\">
								".$value['title']."
							</a>
						</div>";
			}
		$html.="</div>";
		return $html;
	}
	function invite_friends($tabs){
	    $sent_to=array();
	    
		$available_services=get_available_services();
		//set template
		set_template(TEMPLATE_PATH."/friends.html",'friends');
		
		//set block
		add_block('friendshipBlock','fBlock','friends');
		add_block('inviteFriendsBlock','ifBlock','friends');
		add_block('friendsRequestBlock','frBlock','friends');
		add_block('manageFriendListBlock','mflBlock','friends');
		
		//Add variable
		//configure the tabs
		$tabb='';
		if(empty($_GET['tab']))
			$the_tab='friends';
		else
			$the_tab=$_GET['tab'];
        
	    if(isset($_GET['snto'])){
	        $sent_list="<div style=\"width:100%;height:90px;overflow:auto;\"><table>";
	        $de_sent_to=base64_decode($_GET['snto']);
	        $de_sent_to=json_decode($de_sent_to,true);
	        
	        foreach ($de_sent_to as $key=>$email){
	            $no=$key+1;
		        $sent_list.="<tr>
               					<td>".$no.".</td>
               					<td>".$email."
               					<input type=\"hidden\" name=\"sent_email[]\" value=\"".$email."\">
               					</td>
               				</tr>";
	        }
	        $sent_list.="</table></div>";
        }
        
	    if(isset($_GET['sent']) && $_GET['sent']=="true"){
    	     add_variable("alert", "You successfully invited ".count($de_sent_to)." person to join ".web_name().".");
	         add_variable("sent_list", $sent_list);
	    }elseif(isset($_GET['sent']) && $_GET['sent']==false){ 
	         add_variable("alert", "No invitation was sent. Be sure to input correct email address.");
	    }
	    	
		if(isset($_POST['invite'])){
		   
		    if(isset($_POST['invited_email']) && !empty($_POST['invited_email'])){
		        $user=fetch_user($_COOKIE['user_id']);
		        $the_email=nl2br($_POST['invited_email']);
		        $the_email=explode("<br />", $the_email);
		        
		        $sent=0;
		        $invite_limit=get_additional_field($_COOKIE['user_id'], "invite_limit", "user");
		        
		        $count=$invite_limit;	
		        	        
    	        foreach ($the_email as $key=>$value){
    	            $value=str_replace("<br>", "", trim($value));
    	            $value=str_replace("<br />", "", trim($value));
    	            if(!empty($value)){
        	            if(isEmailAddress(trim($value))){
        	                  if(isset($_POST['sent_email'])){
        	                      if(!in_array(trim($value), $_POST['sent_email'])){
        	                          if($count>0){
        	                              $send_invite=invitation_mail($user['lemail'],$user['ldisplay_name'],$value,$_POST['personal_message']);
        	                              if($send_invite)
        	                              $count--;
        	                          }elseif($count==-1){ //for unlimited invitation
        	                          	  $send_invite=invitation_mail($user['lemail'],$user['ldisplay_name'],$value,$_POST['personal_message']);	
        	                          }else break;
        	                      }
        	                  }else{
        	                      if($count>0){
            	                      $send_invite=invitation_mail($user['lemail'],$user['ldisplay_name'],$value,$_POST['personal_message']);
            	                      if($send_invite)
        	                          $count--;
        	                      }elseif($count==-1){ //for unlimited invitation
        	                          	  $send_invite=invitation_mail($user['lemail'],$user['ldisplay_name'],$value,$_POST['personal_message']);	
        	                      }else break;
        	                  }
        	                  
            	              $sent_to[]=trim($value);
            	              $sent++;
        	               }
        	        }
    	        }
		        
		        if($invite_limit!=-1){
    			    $invite_limit=$invite_limit - count($sent_to);
    			    edit_additional_field($_COOKIE['user_id'], "invite_limit", $invite_limit, "user");
		        }
		        
		        $en_sent_to=json_encode($sent_to);
		        $en_sent_to=base64_encode($en_sent_to);
		        
		        if($sent!=0)
		            header("location:".cur_pageURL()."&snto=".$en_sent_to."&sent=true");
		        else 
		            header("location:".cur_pageURL()."&sent=false");

		        	
		        	        
    		    
		    }
		}
		
		$enc_available_services=base64_encode(json_encode($available_services));
		
		add_actions('section_title','Invite Friends');
		
		
		add_variable('title','Invite Friends');
		
		$theuri='http://'.site_url().'/lumonata-functions/openinviter/invite.php';
		$services="";
		foreach ($available_services as $key=>$value){
			if(is_array($value))
			$services.="<div class=\"iservices\"><a href=\"".$theuri."?service=".$key."&logo=".urlencode($value['imgurl'])."&title=".$value['title']."&ts=".$enc_available_services."\" title=\"Invite Your Friends Easily\" class=\"openinviter\"><img src=\"http://".$value['imgico']."\" alt=\"".$value['title']."\" title=\"".$value['title']."\" border=\"0\"></a></div>";
		}
		
		$invite_credit=get_additional_field($_COOKIE['user_id'], "invite_limit", "user");
		if($invite_credit==-1)
		    $invite_credit="unlimited";
		
		if($invite_credit>1 || $invite_credit==-1)
		    $invite_credit=$invite_credit." invitations";
		else 
		    $invite_credit=$invite_credit." invitation";    
		    
		add_variable('invite_credit',$invite_credit);
		add_variable('services',$services);
		add_variable('tabs',set_tabs($tabs,$the_tab));
		add_variable('avatar',get_avatar($_COOKIE['user_id'], 2));
		add_variable('friends_search',search_box('../lumonata-functions/friends.php','friendship','','left','alert_green_form','Search Friends'));
		parse_template('inviteFriendsBlock','ifBlock');
		return return_template('friends'); 	
	}
	function add_friendship($user_id,$friend_id,$status='onrequest'){
		global $db;
		if($user_id!=$friend_id){
				$query=$db->prepare_query("INSERT INTO lumonata_friendship (luser_id,lfriend_id,lstatus)
										   VALUES (%d,%d,%s)",$user_id,$friend_id,$status);
				
				$sf=search_friendship($user_id,$friend_id);
				if(count($sf['friend_id']) < 1)
				return $r=$db->do_query($query);
			
		}
		return false;
	}
	function edit_friendship($user_id,$friend_id,$status='',$update_both=true){
		global $db;
		$friends=search_friendship($user_id,$friend_id);
		if(count($friends)==0){
			$return=add_friendship($user_id, $friend_id,$status);
			if($return){
				return $return=add_friendship($friend_id,$user_id,$status);
			}
		}elseif(!empty($status)){
			if($update_both)
				$query=$db->prepare_query("UPDATE lumonata_friendship 
									   SET lstatus=%s 
									   WHERE (luser_id=%d AND lfriend_id=%d) OR
									   (luser_id=%d AND lfriend_id=%d) 
									   ",$status,$user_id,$friend_id,$friend_id,$user_id);
			else 
				$query=$db->prepare_query("UPDATE lumonata_friendship 
									   SET lstatus=%s 
									   WHERE (luser_id=%d AND lfriend_id=%d)
									   ",$status,$user_id,$friend_id);
				
			return $r=$db->do_query($query);
		} 
				
		
	}
	function delete_friendship($user_id){
		
		return delete_friend($user_id);
	}
	function search_friendship($user_id,$friend_id=null){
		global $db;
		$friends=array();
		if($friend_id==null)
			$query=$db->prepare_query("SELECT * FROM lumonata_friendship WHERE luser_id=%d",$user_id);
		else 
			$query=$db->prepare_query("SELECT * FROM lumonata_friendship WHERE luser_id=%d and lfriend_id=%d",$user_id,$friend_id);
	
		$r=$db->do_query($query);
		while($data=$db->fetch_array($r)){
			$friends['friend_id'][]=$data['lfriend_id'];
			$friends['user_id'][]=$data['luser_id'];
			$friends['friendship_id'][]=$data['lfriendship_id'];
		}
		return $friends;
	}
?>