<?php
	function get_admin(){
		
		//set template
		set_template(TEMPLATE_PATH."/index.html");
		
		//set block
		add_block('adminArea','aBlock');
		
		//add variable
		add_variable('web_title',web_title());
		add_variable('style_sheet',get_css());
		add_variable('jquery',get_javascript('jquery'));
		add_variable('jquery_ui',get_javascript('jquery_ui'));
		add_variable('navigations',get_javascript('navigation'));
		
		add_variable('navmenu',get_nav_menu());
		
		if(is_dashboard()){
			add_variable('content_area',get_dashboard());
		}
		if(is_global_settings())
			add_variable('content_area',get_global_settings());
		
		if(is_themes())	{
			add_variable('content_area',get_themes());
		}
		//print the template
		parse_template('adminArea','aBlock');
		print_template(); 
		
	}
	function set_tabs($tabs,$selected_tab){
		$tab='';
		foreach($tabs as $key=>$val){
			if($selected_tab==$key)
			$tab.="<li class=\"active\"><a href=\"".get_tab_url($key)."\">$val</a></li>";
			else
			$tab.="<li><a href=\"".get_tab_url($key)."\">$val</a></li>";
			
		}
		
		return $tab;
	}
	function get_global_settings(){
		//set tabs
		$tabs=array('general'=>'General',
					'reading'=>'Reading',
					'writing'=>'Writing',
					'comments'=>'Comments',
					'notifications'=>'Notifications',
					'categories'=>'Expertise Categories',
				    'tags'=>'Expertise Tags');
		
		//set template
		set_template(TEMPLATE_PATH."/global.html",'global');
		
		//set block
		add_block('genSetting','settingBlock','global');
		add_block('readingSettings','readingBlock','global');
		add_block('writingSettings','writingBlock','global');
		add_block('commentsSettings','commentsBlock','global');
		add_block('notificationSettings','notificationBlock','global');
		
		/** add variable */
		//configure the tabs
		$tabb='';
		if(empty($_GET['tab']))
			$the_tab='general';
		else
			$the_tab=$_GET['tab'];
		
		add_variable('tab',set_tabs($tabs,$the_tab));
		
		//configure button
		add_variable('save_changes_botton',save_changes_botton());
		
		$update=false;
		if(!empty($_POST['update']))
			$update=$_POST['update'];
			
		//print the template
		if($the_tab=='general'){ //General
			//set the page Title
			add_actions('section_title','General - Settings');
			
			if($update)
				if(update_global_settings())
					add_variable('alert',"<div class=\"alert_green_form\">".UPDATE_SUCCESS."</div>");
				else
					add_variable('alert',"<div class=\"alert_red_form\">".UPDATE_FAILED."</div>");
			
			add_variable('web_title',web_title());
			add_variable('web_name',web_name());
			add_variable('tagline',web_tagline());
			add_variable('site_url','http://'.site_url());
			add_variable('email',get_email());
			add_variable('smtp',get_smtp());
			add_variable('timezone',get_timezone(get_meta_data('time_zone')));
			set_timezone('UTC');
			add_variable('utc_time',date("Y/m/d H:i",time()));
			set_timezone(get_meta_data('time_zone'));
			add_variable('local_time',date("Y/m/d H:i",time()));
			
			$date_format=array('F j, Y','Y/m/d','m/d/Y','d/m/Y');
			$append_dt='';
			$i=1;
			$custome=true;
			foreach($date_format as $val){
				if($val==get_date_format()){
					$checked="checked=\"checked\"";
					$the_format=$val;
					$custome=false;
				}elseif($val!=get_date_format()) {
					$checked="";
					$the_format=get_date_format();
					
				}
				$append_dt.="<input type=\"radio\" name=\"the_date_format\" id=\"date_format_".$i."\" value=\"$val\" $checked />".date($val,time())."<br />";
				$append_dt.="<script type=\"text/javascript\">
								$(function(){
									$('#date_format_".$i."').click(function(){
											$('[name=date_format]').val($('#date_format_".$i."').val());
									});
								});
							</script>";
				$i++;
			}
			if($custome){
				$custome_checked="checked=\"checked\"";
			}else{
				$custome_checked='';
			}
			$append_dt.="<input type=\"radio\" name=\"the_date_format\" id=\"custome_date_format\" value=\"\" $custome_checked />
						Custom Format: <input type=\"text\" name=\"date_format\" value=\"$the_format\" class=\"small_textbox\"  /><br />
						<script type=\"text/javascript\">
						$(function(){
							$('#custome_date_format').click(function(){
								$('[name=date_format]').focus();
							});
							$('[name=date_format]').focus(function(){
								$('#custome_date_format').attr('checked','checked');
							});
						});
						</script>";
			add_variable('date_format',$append_dt);
			
			$time_format=array('g:i a','g:i A','H:i');
			$append_tm='';
			$i=1;
			$custome=true;
			foreach($time_format as $val){
				if($val==get_time_format()){
					$checked="checked=\"checked\"";
					$the_format=$val;
					$custome=false;
				}else {
					$checked="";
					$the_format=get_time_format();
				}
				
				$append_tm.="<input id=\"time_format_".$i."\" type=\"radio\" name=\"the_time_format\" value=\"$val\" $checked />".date($val,time())."<br />";
				$append_tm.="<script type=\"text/javascript\">
								$(function(){
									$('#time_format_".$i."').click(function(){
											$('[name=time_format]').val($('#time_format_".$i."').val());
									});
								});
							</script>";
				$i++;
			}
			
			if($custome){
				$custome_checked="checked=\"checked\"";
			}else{
				$custome_checked='';
			}
			$append_tm.="<input type=\"radio\" name=\"the_time_format\" id=\"custome_time_format\" value=\"\" $custome_checked />
						Custom Format: <input type=\"text\" name=\"time_format\" value=\"$the_format\" class=\"small_textbox\"  /><br />
						<script type=\"text/javascript\">
						$(function(){
							$('#custome_time_format').click(function(){
								$('[name=time_format]').focus();
							});
							$('[name=time_format]').focus(function(){
								$('#custome_time_format').attr('checked','checked');
							});
						});
						</script>";
			
			add_variable('time_format',$append_tm);
			
			$invite_limit=get_meta_data("invitation_limit","global_setting");
			$invite_limit=(empty($invite_limit))?10:$invite_limit;
			
			add_variable('invitation_limit',$invite_limit);
			
			parse_template('genSetting','settingBlock');
			return return_template('global'); 	
		}elseif($the_tab=='reading'){ //Reading
			//set the page Title
			add_actions('section_title','Reading - Settings');
			
			if($update)
				if(update_global_settings()){
					if($_POST['is_rewrite']=='yes'){
						create_htaccess_file();
					}else{
						if( file_exists(ROOT_PATH."/.htaccess") )
						unlink(ROOT_PATH."/.htaccess");	
					}
					add_variable('alert',"<div class=\"alert_green_form\">".UPDATE_SUCCESS."</div>");
				}else{
					add_variable('alert',"<div class=\"alert_red_form\">".UPDATE_FAILED."</div>");
				}	
			
			add_variable('post_viewed',post_viewed());
			add_variable('rss_viewed',rss_viewed());
			add_variable('status_viewed',status_viewed());
			
			//RSS Viewed Options
			$rss_view_format=array('full_text'=>'Full Text','summary'=>'Summary');
			$opt_rss_format='';
			foreach($rss_view_format as $key=>$val){
				if($key==rss_view_format())
					$opt_rss_format.="<input type=\"radio\"  name=\"rss_view_format\" value=\"$key\" checked=\"checked\" />$val <br />";
				else
					$opt_rss_format.="<input type=\"radio\"  name=\"rss_view_format\" value=\"$key\"  />$val <br />";
			}
			
			//SEF URL Options
			$sef_format=array('yes'=>'Yes','no'=>'No');
			$opt_sef_format='';
			foreach($sef_format as $key=>$val){
				if($key==is_rewrite())
					$opt_sef_format.="<input type=\"radio\"  name=\"is_rewrite\" value=\"$key\" checked=\"checked\" />$val <br />";
				else
					$opt_sef_format.="<input type=\"radio\"  name=\"is_rewrite\" value=\"$key\"  />$val <br />";
			}
			
			add_variable('rss_view_format',$opt_rss_format);
			add_variable('sef_format',$opt_sef_format);
			parse_template('readingSettings','readingBlock');
			return return_template('global'); 
		}elseif($the_tab=='writing'){ //Writing
			//set the page Title
			add_actions('section_title','Writing - Settings');
			
			if($update)
				if(update_global_settings())
					add_variable('alert',"<div class=\"alert_green_form\">".UPDATE_SUCCESS."</div>");
				else
					add_variable('alert',"<div class=\"alert_red_form\">".UPDATE_FAILED."</div>");
			$email_format=array('html'=>'HTML','plain_text'=>'Plain Text');
			$text_editor=array('tiny_mce'=>'Tiny MCE','none'=>'None');
			$op_email='';
			foreach($email_format as $key=>$val){
				if($key==email_format())
					$op_email.="<input type=\"radio\" name=\"email_format\" value=\"$key\" checked=\"checked\" />$val <br />";
				else
					$op_email.="<input type=\"radio\" name=\"email_format\" value=\"$key\" />$val <br />";
			}
			
			add_variable('email_format',$op_email);
			$op_text_editor='';
			foreach($text_editor as $key=>$val){
				if($key==text_editor())
					$op_text_editor.="<input type=\"radio\" name=\"text_editor\" value=\"$key\" checked=\"checked\" />$val <br />";
				else
					$op_text_editor.="<input type=\"radio\" name=\"text_editor\" value=\"$key\" />$val <br />";
			}
			
			add_variable('text_editor',$op_text_editor);
			
			add_variable('thumbnail_image_width',thumbnail_image_width());
			add_variable('thumbnail_image_height',thumbnail_image_height());
			add_variable('medium_image_height',medium_image_height());
			add_variable('medium_image_width',medium_image_width());
			add_variable('large_image_height',large_image_height());
			add_variable('large_image_width',large_image_width());
			
			add_variable('list_viewed',list_viewed());
			parse_template('writingSettings','writingBlock');
			return return_template('global'); 
		}elseif($the_tab=='comments'){ //Comments
			//set the page Title
			add_actions('section_title','Comments - Settings');
			
			if(isset($_POST['update_comment_settings']) && $_POST['update_comment_settings']==true)
				if(update_global_settings())
					add_variable('alert',"<div class=\"alert_green_form\">".UPDATE_SUCCESS."</div>");
				else
					add_variable('alert',"<div class=\"alert_red_form\">".UPDATE_FAILED."</div>");
					
			$comment_page_displayed=array('last'=>'Last','first'=>'First');
			$opt_pg_dis='';
			$is_allow_comment='';
			$is_login_to_comment='';
			$is_auto_close_comment='';
			$is_break_comment='';
			$is_allow_post_like='';
			$is_allow_comment_like='';
			
			if(is_allow_comment())
				$is_allow_comment="checked=\"checked\"";
			if(is_login_to_comment())
				$is_login_to_comment="checked=\"checked\"";
			if(is_auto_close_comment())
				$is_auto_close_comment="checked=\"checked\"";
			if(is_break_comment())
				$is_break_comment="checked=\"checked\"";
			if(is_allow_comment_like())	
				$is_allow_comment_like="checked=\"checked\"";
			if(is_allow_post_like())	
				$is_allow_post_like="checked=\"checked\"";
					
			foreach($comment_page_displayed as $key=>$val){
				if($key==comment_page_displayed())
					$opt_pg_dis.="<option value=\"$key\" selected=\"selected\">$val</option>";
				else
					$opt_pg_dis.="<option value=\"$key\" >$val</option>";
			}
			
			add_variable('is_allow_comment',$is_allow_comment);
			add_variable('is_login_to_comment',$is_login_to_comment);
			add_variable('is_auto_close_comment',$is_auto_close_comment);
			add_variable('days_auto_close_comment',days_auto_close_comment());
			add_variable('is_break_comment',$is_break_comment);
			add_variable('comment_per_page',comment_per_page());
			add_variable('comment_page_displayed',$opt_pg_dis);
			add_variable('is_allow_comment_like',$is_allow_comment_like);
			add_variable('is_allow_post_like',$is_allow_post_like);
			
			parse_template('commentsSettings','commentsBlock');
			return return_template('global'); 
		}elseif($the_tab=='notifications'){ 
			//set the page Title
			add_actions('section_title','Notifications - Settings');
			
			if(isset($_POST['update_notifications_settings']) && $_POST['update_notifications_settings']==true)
				if(update_global_settings())
					add_variable('alert',"<div class=\"alert_green_form\">".UPDATE_SUCCESS."</div>");
				else
					add_variable('alert',"<div class=\"alert_red_form\">".UPDATE_FAILED."</div>");
					
			
			
			$alert_on_register='';
			$alert_on_comment='';
			$alert_on_comment_reply='';
			$alert_on_liked_post='';
			$alert_on_liked_comment='';
			
			if(alert_on_register())
				$alert_on_register="checked=\"checked\"";
			if(alert_on_comment())
				$alert_on_comment="checked=\"checked\"";
			if(alert_on_comment_reply())
				$alert_on_comment_reply="checked=\"checked\"";
			if(alert_on_liked_post())
				$alert_on_liked_post="checked=\"checked\"";
			if(alert_on_liked_comment())
				$alert_on_liked_comment="checked=\"checked\"";
				
			
			
			add_variable('alert_on_register',$alert_on_register);
			add_variable('alert_on_comment',$alert_on_comment);
			add_variable('alert_on_comment_reply',$alert_on_comment_reply);
			add_variable('alert_on_liked_post',$alert_on_liked_post);
			add_variable('alert_on_liked_comment',$alert_on_liked_comment);
			
			parse_template('notificationSettings','notificationBlock');
			return return_template('global'); 
		}elseif($the_tab=='categories'){ 
			//set the page Title
			 add_actions('section_title','Expertise Categories - Settings');
			 return get_admin_rule('categories','global_settings',"Expertise|Expertise",$tabs);
		}elseif($the_tab=='tags'){ 
			//set the page Title
			 add_actions('section_title','Expertise Tags - Settings');
			 return get_admin_rule('tags','global_settings',"Expertise|Expertise",$tabs);
		}
		
		
	}
	function update_global_settings(){
		$thumbnail_image_width="";
		$thumbnail_image_height="";
		$medium_image_width="";
		$medium_image_height="";
		$large_image_width="";
		$large_image_height="";
		$thumbnail_image_size="";
		$medium_image_size="";
		$large_image_size="";
		
		if(isset($_POST['thumbnail_image_width'])){
			
			if($_POST['thumbnail_image_width']=="" || !isInteger($_POST['thumbnail_image_width']))
				$_POST['thumbnail_image_width']=thumbnail_image_width();
			
			 
		}
		if(isset($_POST['thumbnail_image_height'])){
			if($_POST['thumbnail_image_height']=="" || !isInteger($_POST['thumbnail_image_height']))
				$_POST['thumbnail_image_height']=thumbnail_image_height();
			
		}
		
		if(isset($_POST['medium_image_width'])){
			if($_POST['medium_image_width']=="" || !isInteger($_POST['medium_image_width']))
				$_POST['medium_image_width']=medium_image_width();
		}
		
		if(isset($_POST['medium_image_height'])){
			if($_POST['medium_image_height']=="" || !isInteger($_POST['medium_image_height']))
				$_POST['medium_image_height']=medium_image_height();
		}
		
		if(isset($_POST['large_image_width'])){
			if($_POST['large_image_width']=="" || !isInteger($_POST['large_image_width']))
				$_POST['large_image_width']=large_image_width();
		}
		if(isset($_POST['large_image_height'])){
			if($_POST['large_image_height']=="" || !isInteger($_POST['large_image_height']))
				$_POST['large_image_height']=large_image_height();
		}
		
		if(isset($_POST['thumbnail_image_width']) && isset($_POST['thumbnail_image_height'])){
			$update=update_meta_data('thumbnail_image_size',$_POST['thumbnail_image_width'].':'.$_POST['thumbnail_image_height']);
			
		}
		if(isset($_POST['medium_image_width']) && isset($_POST['medium_image_height'])){
			$update=update_meta_data('medium_image_size',$_POST['medium_image_width'].':'.$_POST['medium_image_height']);
		}
		if(isset($_POST['large_image_width']) && isset($_POST['large_image_height'])){
			if($large_image_size!=':')$update=update_meta_data('large_image_size',$_POST['large_image_width'].':'.$_POST['large_image_height']);
		}
		if(isset($_POST['update_comment_settings']) && $_POST['update_comment_settings']==true){
			if(isset($_POST['is_allow_comment']))
				$_POST['is_allow_comment']=1;
			else
				$_POST['is_allow_comment']=0;
			
			if(isset($_POST['is_login_to_comment']))
				$_POST['is_login_to_comment']=1;
			else
				$_POST['is_login_to_comment']=0;
			
			if(isset($_POST['is_auto_close_comment']))
				$_POST['is_auto_close_comment']=1;
			else
				$_POST['is_auto_close_comment']=0;
			
			if(isset($_POST['is_break_comment']))
				$_POST['is_break_comment']=1;
			else
				$_POST['is_break_comment']=0;
			
			if(isset($_POST['is_allow_comment_like']))
				$_POST['is_allow_comment_like']=1;
			else
				$_POST['is_allow_comment_like']=0;
			
			if(isset($_POST['is_allow_post_like']))
				$_POST['is_allow_post_like']=1;
			else
				$_POST['is_allow_post_like']=0;
			
			
			
		}
		
		if(isset($_POST['update_notifications_settings']) && $_POST['update_notifications_settings']==true){
			if(isset($_POST['alert_on_register']))
				$_POST['alert_on_register']=1;
			else
				$_POST['alert_on_register']=0;
			
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
		}
		foreach($_POST as $key=>$val){
			$val=str_replace('http://','',$val);
			
			//value should be integer
			if($key=='post_viewed' || $key=='rss_viewed' || $key=='status_viewed' || $key=='invitation_limit'){
				if($val=="" || !isInteger($val))$val=10;
			}elseif($key=='list_viewed'){
				if($val=="" || !isInteger($val))$val=30;
			}
			if($key=="invitation_limit")
			    $update=update_meta_data($key,$val,"global_setting");
			else
			    $update=update_meta_data($key,$val);
		}
		
		
		
		if($update)return true;
		else return false;
	}
	
	
	function get_admin_user(){
		global $db;
		$tabs=array('my-updates'=>'My Updates','my-profile'=>'My Profile','profile-picture'=>'Profile Picture','eduwork'=>'Education & Work');
		$alert='';
		
		
		if(isset($_GET['tab']))
			$selected_tab=$_GET['tab'];
		else
			$selected_tab='';
			
		$the_tabs=set_tabs($tabs,$selected_tab);
		
		//Saving Proccess
		if(is_save_changes()){
			
			$alert='';
			$thebirthday="0000-00-00";
			
			if(is_add_new()){
				$validation_rs=is_valid_user_input($_POST['username'],$_POST['first_name'],$_POST['last_name'],$_POST['password'],$_POST['re_password'],$_POST['email'],$_POST['sex'],$_POST['website']);
				if($validation_rs=="OK"){
					
					if(!empty($_POST['birthday']) && !empty($_POST['birthmonth']) && !empty($_POST['birthyear'])){
						$thebirthday=$_POST['birthmonth']."/".$_POST['birthday']."/".$_POST['birthyear'];
						$thebirthday=date("Y-m-d",strtotime($thebirthday));
					}	
					
					
					$display_name=$_POST['first_name']." ".$_POST['last_name'];
										
					if(save_user($_POST['username'],$_POST['password'],$_POST['email'],$_POST['sex'],$_POST['user_type'],$thebirthday,$_POST['status'],$display_name)){
						$user_id=mysql_insert_id();
						add_friend_to_admin($user_id);
						
						//add additional field for invitation limit
				        $invite_limit=get_meta_data("invitation_limit");
				        if(is_administrator($user_id))
				        $invite_limit=-1;
				        add_additional_field($user_id, "invite_limit", $invite_limit, "user");
						
						if(!empty($_POST['first_name']))
						add_additional_field($user_id,'first_name',$_POST['first_name'],'user');
						
						//if(!empty($_POST['last_name']))
						add_additional_field($user_id,'last_name',$_POST['last_name'],'user');
						
						//if(!empty($_POST['website']))
						add_additional_field($user_id,'website',$_POST['website'],'user');
						
						//send message is checked
						if(isset($_POST['send'])){
							$token=md5($_POST['username'].$_POST['email'].$_POST['password']).".";
							send_register_notification($_POST['username'], $_POST['email'], $_POST['password'], $token);
						}
						
						header("location:".get_state_url('users')."&prc=add_new");
					}
				}else{
					$alert=$validation_rs;
				}
			}elseif(is_edit()){
				
				$validation_rs=is_valid_user_input($_POST['username'][0],$_POST['first_name'][0],$_POST['last_name'][0],$_POST['password'][0],$_POST['re_password'][0],$_POST['email'][0],$_POST['sex'][0],$_POST['website'][0]);
				
				if(!isset($_POST['expertise'][0]))
				$validation_rs="<div class=\"alert_red_form\">Choose the category that represent your self!</div>";
				
				if($validation_rs=="OK"){	
					
					if(!empty($_POST['birthday'][0]) && !empty($_POST['birthmonth'][0]) && !empty($_POST['birthyear'][0])){
						$thebirthday=$_POST['birthmonth'][0]."/".$_POST['birthday'][0]."/".$_POST['birthyear'][0];
						$thebirthday=date("Y-m-d",strtotime($thebirthday));
					}	
					
					if(edit_user($_GET['id'],$_POST['display_name'][0],$_POST['password'][0],$_POST['email'][0],$_POST['sex'][0],$_POST['user_type'][0],$thebirthday,$_POST['status'][0])){
						edit_additional_field($_GET['id'],'first_name',$_POST['first_name'][0],'user');
						edit_additional_field($_GET['id'],'last_name',$_POST['last_name'][0],'user');
						edit_additional_field($_GET['id'],'website',$_POST['website'][0],'user');
						edit_additional_field($_GET['id'],'bio',$_POST['bio'][0],'user');
						edit_additional_field($_GET['id'],'one_liner',$_POST['one_liner'][0],'user');
						edit_additional_field($_GET['id'],'location',$_POST['location'][0],'user');
					
						//add additional field for invitation limit if the value is not exist
				        $invite_limit=get_additional_field($_GET['id'], "invite_limit", "user");
				        if(empty($invite_limit)){
				            $invite_limit=get_meta_data("invitation_limit");
				            if(is_administrator($_GET['id']))
				            $invite_limit=-1;
				            add_additional_field($_GET['id'], "invite_limit", $invite_limit, "user");
				        }
						//send message is checked
						if(isset($_POST['send'][0])){
							$token=md5($_POST['username'][0].$_POST['email'][0])."#";
							send_register_notification($_POST['username'][0], $_POST['email'][0], $_POST['password'][0], $token);
						}
						
						//Expertise Category

						delete_rules_relationship("app_id=".$_GET['id'],'categories','global_settings');
						foreach($_POST['expertise'][0] as $key=>$value){
							insert_rules_relationship($_GET['id'], $value);
						}
						
						//Tags
						delete_rules_relationship("app_id=".$_GET['id'],'tags','profile');
						if(isset($_POST['tagit'][0])){
							foreach($_POST['tagit'][0] as $key=>$value){
								$rule_id=insert_rules(0, $value, '', 'tags', 'profile');
								insert_rules_relationship($_GET['id'], $rule_id);
							}
						}
					   	//Skills
					   	delete_rules_relationship("app_id=".$_GET['id'],'skills','profile');
						if(isset($_POST['skillit'][0])){
							foreach($_POST['skillit'][0] as $key=>$value){
								$rule_id=insert_rules(0, $value, '', 'skills', 'profile');
								insert_rules_relationship($_GET['id'], $rule_id);
							}
						}
						
						header("location:".get_state_url('users'));
						
					}
				}else{
					$alert=$validation_rs;
				}
			}elseif(is_edit_all()){
				
				foreach($_POST['select'] as $key=>$val){
					$validation_rs=is_valid_user_input($_POST['username'][$key],$_POST['first_name'][$key],$_POST['last_name'][$key],$_POST['password'][$key],$_POST['re_password'][$key],$_POST['email'][$key],$_POST['sex'][$key],$_POST['website'][$key]);
					if($validation_rs=="OK"){	
						if(!empty($_POST['birthday'][$key]) && !empty($_POST['birthmonth'][$key]) && !empty($_POST['birthyear'][$key])){
							$thebirthday=$_POST['birthmonth'][$key]."/".$_POST['birthday'][$key]."/".$_POST['birthyear'][$key];
							$thebirthday=date("Y-m-d",strtotime($thebirthday));
						}
						if(edit_user($val,$_POST['display_name'][$key],$_POST['password'][$key],$_POST['email'][$key],$_POST['sex'][$key],$_POST['user_type'][$key],$thebirthday,$_POST['status'][$key])){
							edit_additional_field($val,'first_name',$_POST['first_name'][$key],'user');
							edit_additional_field($val,'last_name',$_POST['last_name'][$key],'user');
							edit_additional_field($val,'website',$_POST['website'][$key],'user');
							edit_additional_field($val,'bio',$_POST['bio'][0],'user');
							edit_additional_field($val,'one_liner',$_POST['one_liner'][0],'user');
							edit_additional_field($val,'location',$_POST['location'][0],'user');
						
							//add additional field for invitation limit if the value is not exist
    				        $invite_limit=get_additional_field($val, "invite_limit", "user");
    				        
    				        if($invite_limit==NULL){
    				            $invite_limit=get_meta_data("invitation_limit");
    				            if(is_administrator($val))
				                    $invite_limit=-1;
                                				                
    				            add_additional_field($val, "invite_limit", $invite_limit, "user");
    				        }
							
						}
						
						//send message is checked
						if(isset($_POST['send'][$key])){
							$token=md5($_POST['username'][$key].$_POST['email'][$key])."#";
							send_register_notification($_POST['username'][$key], $_POST['email'][$key], $_POST['password'][$key], $token);
						}
						
						//Expertise Category
						delete_rules_relationship("app_id=".$val,'categories','global_settings');
						foreach($_POST['expertise'][$key] as $keyex=>$value){
							insert_rules_relationship($val, $value);
						}
						
						//Tags
						delete_rules_relationship("app_id=".$val,'tags','profile');
						if(isset($_POST['tagit'][$key])){
							foreach($_POST['tagit'][$key] as $keytag=>$value){
								$rule_id=insert_rules(0, $value, '', 'tags', 'profile');
								insert_rules_relationship($val, $rule_id);
							}
						}
					   	//Skills
					   	delete_rules_relationship("app_id=".$val,'skills','profile');
						if(isset($_POST['skillit'][$key])){
							foreach($_POST['skillit'][$key] as $keyskill=>$value){
								$rule_id=insert_rules(0, $value, '', 'skills', 'profile');
								insert_rules_relationship($val, $rule_id);
							}
						}
					}else{
						$alert=$validation_rs;
						break;
					}
				}
				if($validation_rs=="OK")header("location:".get_state_url('users'));
			}
			add_variable('alert',$alert);
		}
		
		//Is add new user
		if(is_add_new()){
			//set template
			set_template(TEMPLATE_PATH."/users.html",'users');
			
			//set block
			add_block('usersAddNew','uAddNew','users');
			add_block('usersEdit','uEdit','users');
			add_block('profilePicture','pPicture','users');
			add_block('educationWork','eduWork','users');
                        
			//set the page Title
			add_actions('section_title','Add New User');
			
			
			//set varibales
			add_variable('website','http://');
			add_actions('header_elements','get_javascript','password_strength');
			add_actions('header_elements','get_javascript','password');
			if(is_save_changes()){
				add_variable('user_name',$_POST['username']);
				add_variable('first_name',$_POST['first_name']);
				add_variable('last_name',$_POST['last_name']);
				add_variable('email',$_POST['email']);
				add_variable('website',$_POST['website']);
			}
			add_variable('prc','Add New User');
			
			add_variable('tabs',$the_tabs);
			add_variable('save_user',button("button=save_changes&label=Save User"));
			add_variable('cancel',button("button=cancel",get_state_url('users')));

            //USER TYPE
			$user_tpye="<p><label>User Type:</label></p><select name=\"user_type\">";
			foreach(user_type() as $key=>$val){
				if(is_save_changes()){
					if($key==$_POST['user_type'])
						$user_tpye.="<option value=\"$key\" selected=\"selected\">$val</option>";
				}
				$user_tpye.="<option value=\"$key\">$val</option>";
			}
			$user_tpye.="</select>";
			add_variable('user_type',$user_tpye);

             //SEX
			$sex="<select name=\"sex\">";
                        $sex.="<option value=\"\">Select Sex</option>";
                        $sexar=array('1'=>'Male','2'=>'Female');
			foreach($sexar as $key=>$val){
				if(is_save_changes()){
					if($key==$_POST['sex'])
						$sex.="<option value=\"$key\" selected=\"selected\">$val</option>";
				}
				$sex.="<option value=\"$key\">$val</option>";
			}
			$sex.="</select>";
			add_variable('sex',$sex);

			add_variable("send_checked","");
			
			//Birthday
			$birthday=(isset($_POST['birthday']))?$_POST['birthday']:"";
			$birthmonth=(isset($_POST['birthmonth']))?$_POST['birthmonth']:"";
			$birthyear=(isset($_POST['birthyear']))?$_POST['birthyear']:"";
			get_date_picker("admin_tail",$birthday,$birthmonth,$birthyear);
			
			//user status
			$user_status=array(0=>"Waiting Email Validation",1=>"Active",2=>"Block");
			$the_status="<p><label>Status:</label></p><select name=\"status\">";
			foreach ($user_status as  $key=>$val){
				if(is_save_changes()){
					if($key==$_POST['sex'])
					$the_status.="<option value=\"$key\" selected=\"selected\">$val</option>";
				}elseif($key==1){
					$the_status.="<option value=\"$key\" selected=\"selected\">$val</option>";
				}else{	
					$the_status.="<option value=\"$key\" >$val</option>";
				}
			}
			$the_status.="</select>";
			add_variable('user_status',$the_status);
			
			parse_template('usersAddNew','uAddNew');
			
			return return_template('users');
			
		}elseif(is_edit()){ //if edit single user
			//set template
			set_template(TEMPLATE_PATH."/users.html",'users');
			
			//set block
			add_block('usersEdit','uEdit','users');
			add_block('usersAddNew','uAddNew','users');
            add_block('profilePicture','pPicture','users');
            add_block('educationWork','eduWork','users');
                        
			//set the page Title
			add_actions('section_title','Edit User');
			
			//set varibales
			add_variable('i',0);
			add_variable('website','http://');
			add_actions('header_elements','get_javascript','password_strength');
			//add_actions('header_elements','get_javascript','password');
			if(is_save_changes()){
				add_variable('username',$_POST['username'][0]);
				add_variable('first_name',$_POST['first_name'][0]);
				add_variable('last_name',$_POST['last_name'][0]);
				add_variable('email',$_POST['email'][0]);
				add_variable('website',$_POST['website'][0]);
				add_variable('one_liner',$_POST['one_liner'][0]);
				add_variable('location',$_POST['location'][0]);
				
				//find the user type
				$user_tpye="<p><label>User Type:</label></p><select name=\"user_type[0]\">";
				foreach(user_type() as $key=>$val){
					if($key==$_POST['user_type'][0])
						$user_tpye.="<option value=\"$key\" selected=\"selected\">$val</option>";
					else
						$user_tpye.="<option value=\"$key\">$val</option>";
				}
				$user_tpye.="</select>";
				
				//find user display name
				$display_name="<select name=\"display_name[0]\">";
				foreach(opt_display_name($_GET['id']) as $key=>$val){
					if($key==$_POST['display_name'][0])
						$display_name.="<option value=\"$key\" selected=\"selected\">$val</option>";
					else
						$display_name.="<option value=\"$key\">$val</option>";
				}
				$display_name.="</select>";

                //FIND SEX
                $sex="<select name=\"sex[0]\">";
                $sex.="<option value=\"\">Select Sex</option>";
                $sexar=array('1'=>'Male','2'=>'Female');
                foreach($sexar as $key=>$val){
                	if($key==$_POST['sex'][0])
                         $sex.="<option value=\"$key\" selected=\"selected\">$val</option>";
                    else
                    	 $sex.="<option value=\"$key\">$val</option>";
                }
                $sex.="</select>";
                add_variable('sex',$sex);
				
                if(isset($_POST['send'][0]))
                	$send_checked="checked=\"checked\"";
                else 
                	$send_checked=="";
                
               	//birthday
                $birthday=(isset($_POST['birthday'][0]))?$_POST['birthday'][0]:"";
				$birthmonth=(isset($_POST['birthmonth'][0]))?$_POST['birthmonth'][0]:"";
				$birthyear=(isset($_POST['birthyear'][0]))?$_POST['birthyear'][0]:"";
				get_date_picker("admin_tail",$birthday,$birthmonth,$birthyear,true,0);
				
				//user status
				$user_status=array(0=>"Waiting Email Validation",1=>"Active",2=>"Block");
				$the_status="<p><label>Status:</label></p><select name=\"status[0]\">";
				foreach ($user_status as  $key=>$val){
					if($key==$_POST['sex'][0]){
						$the_status.="<option value=\"$key\" selected=\"selected\">$val</option>";
					}else{	
						$the_status.="<option value=\"$key\" >$val</option>";
					}
				}
				$the_status.="</select>";
				add_variable('user_status',$the_status);
				if(is_administrator()){
					$value="<fieldset>
		                		<p><label>Send Details?</label></p>
		                		<input type=\"checkbox\" name=\"send[0]\" $send_checked /> Send all details to the new user via email.
		        			</fieldset>";
					add_variable('send_option', $value);
				}
                
			}else{
				$d=fetch_user($_GET['id']);
				add_variable('username',$d['lusername']);
				add_variable('first_name',get_additional_field($_GET['id'],'first_name','user'));
				add_variable('last_name',get_additional_field($_GET['id'],'last_name','user'));
				add_variable('email',$d['lemail']);
				$website=get_additional_field($_GET['id'],'website','user');
				if(empty($website))
					$website='http://';
				else
					$website=get_additional_field($_GET['id'],'website','user');
					
				add_variable('website',$website);
				add_variable('one_liner',get_additional_field($_GET['id'],'one_liner','user'));
				add_variable('location',get_additional_field($_GET['id'],'location','user'));
				
				//find the user type
				$user_tpye="<p><label>User Type:</label></p><select name=\"user_type[0]\">";
				foreach(user_type() as $key=>$val){
					if($key==$d['luser_type'])
						$user_tpye.="<option value=\"$key\" selected=\"selected\">$val</option>";
					else
						$user_tpye.="<option value=\"$key\">$val</option>";
				}
				$user_tpye.="</select>";
				
				//find user display name
				$display_name="<select name=\"display_name[0]\">";
				foreach(opt_display_name($d['luser_id']) as $key=>$val){
					if($key==$d['ldisplay_name'])
						$display_name.="<option value=\"$key\" selected=\"selected\">$val</option>";
					else
						$display_name.="<option value=\"$key\">$val</option>";
				}
				$display_name.="</select>";

                //FIND SEX
                $sex="<select name=\"sex[0]\">";
                $sex.="<option value=\"\">Select Sex</option>";
                $sexar=array('1'=>'Male','2'=>'Female');
                foreach($sexar as $key=>$val){
                    if($key==$d['lsex'])
                	     $sex.="<option value=\"$key\" selected=\"selected\">$val</option>";
                    else
                    	 $sex.="<option value=\"$key\">$val</option>";
                }
                $sex.="</select>";
                add_variable('sex',$sex);
                
                
				if(is_administrator()){
					$value="<fieldset>
		                		<p><label>Send Details?</label></p>
		                		<input type=\"checkbox\" name=\"send[0]\"  /> Send all details to the new user via email.
		        			</fieldset>";
					add_variable('send_option', $value);
				}
                
                //birthday
                $birthday=(!empty($d['lbirthday']))?date("j",strtotime($d['lbirthday'])):"";
				$birthmonth=(!empty($d['lbirthday']))?date("n",strtotime($d['lbirthday'])):"";
				$birthyear=(!empty($d['lbirthday']))?date("Y",strtotime($d['lbirthday'])):"";
				get_date_picker("admin_tail",$birthday,$birthmonth,$birthyear,true,0);
				
				//user status
				$user_status=array(0=>"Waiting Email Validation",1=>"Active",2=>"Block");
				$the_status="<p><label>Status:</label></p><select name=\"status[0]\">";
				
				foreach ($user_status as  $key=>$val){
					if($key==$d['lstatus']){
						$the_status.="<option value=\"$key\" selected=\"selected\">$val</option>";
					}else{	
						$the_status.="<option value=\"$key\" >$val</option>";
					}
				}
				$the_status.="</select>";
				add_variable('user_status',$the_status);
			}
			
			//Expertise Category
			$expert=get_expertise_categories();
			
			$my_expert=fetch_rulerel_by_group_type($_GET['id'],'global_settings','categories',true);
			
			$exprt="";
			while($the_expert=$db->fetch_array($expert)){
				
				if(in_array($the_expert['lrule_id'], $my_expert)){
					$exprt.="<div class=\"expertise_item\" id=\"expertise_item_".$the_expert['lrule_id']."\" style=\"background-color:#FFCC66;\">".$the_expert['lname']."</div>";
					$exprt.="<div style=\"display:none;\"><input type=\"checkbox\" name=\"expertise[0][]\" id=\"expertise_checked_".$the_expert['lrule_id']."\" value=\"".$the_expert['lrule_id']."\" checked=\"checked\" />".$the_expert['lname']."</div>";
				}else{ 
					$exprt.="<div class=\"expertise_item\" id=\"expertise_item_".$the_expert['lrule_id']."\">".$the_expert['lname']."</div>";
					$exprt.="<div style=\"display:none;\"><input type=\"checkbox\" name=\"expertise[0][]\" id=\"expertise_checked_".$the_expert['lrule_id']."\" value=\"".$the_expert['lrule_id']."\"  />".$the_expert['lname']."</div>";
				}
				
				$exprt.="<script type=\"text/javascript\">
								$(function(){
										$('#expertise_item_".$the_expert['lrule_id']."').click(function(){
											if($('#expertise_checked_".$the_expert['lrule_id']."').attr('checked')){
												$('#expertise_checked_".$the_expert['lrule_id']."').removeAttr('checked');
												$('#expertise_item_".$the_expert['lrule_id']."').css('background-color','#FFF');
											}else{
												$('#expertise_checked_".$the_expert['lrule_id']."').attr('checked','checked');
												$('#expertise_item_".$the_expert['lrule_id']."').css('background-color','#FFCC66');
											}
										});
								  });
							  </script>";
					
			}
				
			add_variable('expertise_categories',$exprt);
			
			//Expertise Tags
			$exprt_tags_result=fetch_rulerel_by_group_type($_GET['id'],"profile","tags");
			$thetag="";
			
			$i=1;
			while($tags=$db->fetch_array($exprt_tags_result)){
					$thetag.="<div class=\"expert_tag_list tag_index_0 clearfix\" id=\"the_tag_list_0_".$i."\">";
					$thetag.="<div class=\"expert_tag_name\" style=\"width:auto;font-size:12px;\">".trim($tags['lname'])."</div>";
					$thetag.="<div class=\"expert_tag_action\">";
					$thetag.="<a href=\"javascript:;\" id=\"remove_tag_".$i."\" onclick=\"$('#the_tag_list_0_".$i."').animate({'background-color':'#FF6666' },500);$('#the_tag_list_0_".$i."').remove();\">X</a>";
					$thetag.="</div>";
					$thetag.="<input type=\"hidden\" name=\"tagit[0][]\" value=\"".trim($tags['lname'])."\" />";
					$thetag.="</div>";
				
				$i++;
			}
			add_variable('thetags',$thetag);
			
			//Expertise Skills
			$exprt_skill_result=fetch_rulerel_by_group_type($_GET['id'],"profile","skills");
			$theskill="";
			
			$i=1;
			while($skills=$db->fetch_array($exprt_skill_result)){
					$theskill.="<div class=\"expert_tag_list skill_index_0 clearfix\" id=\"the_skill_list_0_".$i."\">";
					$theskill.="<div class=\"expert_tag_name\" style=\"width:auto;font-size:12px;\">".trim($skills['lname'])."</div>";
					$theskill.="<div class=\"expert_tag_action\">";
					$theskill.="<a href=\"javascript:;\" id=\"remove_skill_".$i."\" onclick=\"$('#the_skill_list_0_".$i."').animate({'background-color':'#FF6666' },500);$('#the_skill_list_0_".$i."').remove();\">X</a>";
					$theskill.="</div>";
					$theskill.="<input type=\"hidden\" name=\"skillit[0][]\" value=\"".trim($skills['lname'])."\" />";
					$theskill.="</div>";
				
				$i++;
			}
			add_variable('theskills',$theskill);
				
			add_variable('prc','Edit User');
			
			add_variable('tabs',$the_tabs);
			add_variable('save_user',button("button=save_changes&label=Save User"));
			add_variable('cancel',button("button=cancel",get_state_url('users')));
			add_variable('user_type',$user_tpye);
			add_variable('display_name',$display_name);
			
			parse_template('usersEdit','uEdit');
			
			return return_template('users');
		}elseif(is_edit_all()){
			//set template
			set_template(TEMPLATE_PATH."/users.html",'users');
			
			//set block
			add_block('loopUser','lUser','users');
			add_block('usersEdit','uEdit','users');
			add_block('usersAddNew','uAddNew','users');
            add_block('profilePicture','pPicture','users');
            add_block('educationWork','eduWork','users');
                        
			//set the page Title
			add_actions('section_title','Edit User');
			
			//set varibales
			
			add_variable('website','http://');
			add_actions('header_elements','get_javascript','password_strength');
			//add_actions('header_elements','get_javascript','password');
			
			
			foreach($_POST['select'] as $key=>$val){
				if(is_save_changes()){
					add_variable('username',$_POST['username'][$key]);
					add_variable('first_name',$_POST['first_name'][$key]);
					add_variable('last_name',$_POST['last_name'][$key]);
					add_variable('email',$_POST['email'][$key]);
					add_variable('website',$_POST['website'][$key]);
					add_variable('one_liner',$_POST['one_liner'][$key]);
					add_variable('location',$_POST['location'][$key]);
					
					//find the user type
					$user_tpye="<p><label>User Type:</label></p><select name=\"user_type[$key]\">";
					foreach(user_type() as $ukey=>$uval){
						if($ukey==$_POST['user_type'][$key])
							$user_tpye.="<option value=\"$ukey\" selected=\"selected\">$uval</option>";
						else
							$user_tpye.="<option value=\"$ukey\">$uval</option>";
					}
					$user_tpye.="</select>";
					
					//find user display name
					$display_name="<select name=\"display_name[$key]\">";
					foreach(opt_display_name($val) as $dkey=>$dval){
						if($dkey==$_POST['display_name'][$key])
							$display_name.="<option value=\"$dkey\" selected=\"selected\">$dval</option>";
						else
							$display_name.="<option value=\"$dkey\">$dval</option>";
					}
					$display_name.="</select>";

                    //FIND SEX
                    $sex="<select name=\"sex[$key]\">";
                    $sex.="<option value=\"\">Select Sex</option>";
                    $sexar=array('1'=>'Male','2'=>'Female');
                    foreach($sexar as $skey=>$sval){
                           if($skey==$_POST['sex'][$key])
                    			$sex.="<option value=\"$skey\" selected=\"selected\">$sval</option>";
                           else
                           		$sex.="<option value=\"$skey\">$sval</option>";
                    }
                    $sex.="</select>";
                    add_variable('sex',$sex);
                    
                    if(isset($_POST['send'][$key]))
	                	$send_checked="checked=\"checked\"";
	                else 
	                	$send_checked=="";
                    
					if(is_administrator()){
						$value="<fieldset>
			                		<p><label>Send Details?</label></p>
			                		<input type=\"checkbox\" name=\"send[".$key."]\" $send_checked /> Send all details to the new user via email.
			        			</fieldset>";
						add_variable('send_option', $value);
					}
                    
	                //birthday
                    $birthday=(isset($_POST['birthday'][$key]))?$_POST['birthday'][$key]:"";
					$birthmonth=(isset($_POST['birthmonth'][$key]))?$_POST['birthmonth'][$key]:"";
					$birthyear=(isset($_POST['birthyear'][$key]))?$_POST['birthyear'][$key]:"";
					get_date_picker("admin_tail",$birthday,$birthmonth,$birthyear,true,$key);
					
					//user status
					$user_status=array(0=>"Waiting Email Validation",1=>"Active",2=>"Block");
					$the_status="<p><label>Status:</label></p><select name=\"status[$key]\">";
					foreach ($user_status as  $xkey=>$xval){
						if($xkey==$_POST['status'][$key]){
							$the_status.="<option value=\"$xkey\" selected=\"selected\">$xval</option>";
						}else{	
							$the_status.="<option value=\"$xkey\" >$xval</option>";
						}
					}
					$the_status.="</select>";
					add_variable('user_status',$the_status);
					
				}else{
					$d=fetch_user($val);
					add_variable('username',$d['lusername']);
					add_variable('first_name',get_additional_field($val,'first_name','user'));
					add_variable('last_name',get_additional_field($val,'last_name','user'));
					add_variable('email',$d['lemail']);
					$website=get_additional_field($val,'website','user');
					if(empty($website))
						$website='http://';
					else
						$website=get_additional_field($val,'website','user');
						
					add_variable('website',$website);
					add_variable('one_liner',get_additional_field($val,'one_liner','user'));
					add_variable('location',get_additional_field($val,'location','user'));
					
					//find the user type
					$user_tpye="<p><label>User Type:</label></p><select name=\"user_type[$key]\">";
					foreach(user_type() as $ukey=>$uval){
						if($ukey==$d['luser_type'])
							$user_tpye.="<option value=\"$ukey\" selected=\"selected\">$uval</option>";
						else
							$user_tpye.="<option value=\"$ukey\">$uval</option>";
					}
					$user_tpye.="</select>";
					
					//find user display name
					$display_name="<select name=\"display_name[$key]\">";
					foreach(opt_display_name($d['luser_id']) as $dkey=>$dval){
						if($dkey==$d['ldisplay_name'])
							$display_name.="<option value=\"$dkey\" selected=\"selected\">$dval</option>";
						else
							$display_name.="<option value=\"$dkey\">$dval</option>";
					}
					$display_name.="</select>";

                     //FIND SEX
                     $sex="<select name=\"sex[$key]\">";
                     $sex.="<option value=\"\">Select Sex</option>";
                     $sexar=array('1'=>'Male','2'=>'Female');
                     foreach($sexar as $skey=>$sval){
                        if($skey==$d['lsex'])
                             $sex.="<option value=\"$skey\" selected=\"selected\">$sval</option>";
                        else
                             $sex.="<option value=\"$skey\">$sval</option>";
                                        }
                     $sex.="</select>";
                     add_variable('sex',$sex);
                
					 if(is_administrator()){
						$value="<fieldset>
			                		<p><label>Send Details?</label></p>
			                		<input type=\"checkbox\" name=\"send[".$key."]\" /> Send all details to the new user via email.
			        			</fieldset>";
						add_variable('send_option', $value);
					 }
                	
                	//birthday
                    $birthday=(!empty($d['lbirthday']))?date("j",strtotime($d['lbirthday'])):"";
					$birthmonth=(!empty($d['lbirthday']))?date("n",strtotime($d['lbirthday'])):"";
					$birthyear=(!empty($d['lbirthday']))?date("Y",strtotime($d['lbirthday'])):"";
					get_date_picker("admin_tail",$birthday,$birthmonth,$birthyear,true,$key);
					
					//user status
					$user_status=array(0=>"Waiting Email Validation",1=>"Active",2=>"Block");
					$the_status="<p><label>Status:</label></p><select name=\"status[$key]\">";
					foreach ($user_status as  $xkey=>$xval){
						if($xkey==$d['lstatus']){
							$the_status.="<option value=\"$xkey\" selected=\"selected\">$xval</option>";
						}else{	
							$the_status.="<option value=\"$xkey\" >$xval</option>";
						}
					}
					$the_status.="</select>";
					add_variable('user_status',$the_status);
				}
				
				//Expertise Category
				$expert=get_expertise_categories();
				$my_expert=fetch_rulerel_by_group_type($val,'global_settings','categories',true);
				
				$exprt="";
				while($the_expert=$db->fetch_array($expert)){
					
					if(in_array($the_expert['lrule_id'], $my_expert)){
						$exprt.="<div class=\"expertise_item\" id=\"expertise_item_".$key."_".$the_expert['lrule_id']."\" style=\"background-color:#FFCC66;\">".$the_expert['lname']."</div>";
						$exprt.="<div style=\"display:none;\"><input type=\"checkbox\" name=\"expertise[".$key."][]\" id=\"expertise_checked_".$key."_".$the_expert['lrule_id']."\" value=\"".$the_expert['lrule_id']."\" checked=\"checked\" />".$the_expert['lname']."</div>";
					}else{ 
						$exprt.="<div class=\"expertise_item\" id=\"expertise_item_".$key."_".$the_expert['lrule_id']."\">".$the_expert['lname']."</div>";
						$exprt.="<div style=\"display:none;\"><input type=\"checkbox\" name=\"expertise[".$key."][]\" id=\"expertise_checked_".$key."_".$the_expert['lrule_id']."\" value=\"".$the_expert['lrule_id']."\"  />".$the_expert['lname']."</div>";
					}
					
					$exprt.="<script type=\"text/javascript\">
									$(function(){
											$('#expertise_item_".$key."_".$the_expert['lrule_id']."').click(function(){
												if($('#expertise_checked_".$key."_".$the_expert['lrule_id']."').attr('checked')){
													$('#expertise_checked_".$key."_".$the_expert['lrule_id']."').removeAttr('checked');
													$('#expertise_item_".$key."_".$the_expert['lrule_id']."').css('background-color','#FFF');
												}else{
													$('#expertise_checked_".$key."_".$the_expert['lrule_id']."').attr('checked','checked');
													$('#expertise_item_".$key."_".$the_expert['lrule_id']."').css('background-color','#FFCC66');
												}
											});
									  });
								  </script>";
						
				}
					
				add_variable('expertise_categories',$exprt);
				
				//Expertise Tags
				$exprt_tags_result=fetch_rulerel_by_group_type($val,"profile","tags");
				$thetag="";
				
				$i=1;
				while($tags=$db->fetch_array($exprt_tags_result)){
						$thetag.="<div class=\"expert_tag_list tag_index_".$key." clearfix\" id=\"the_tag_list_".$key."_".$i."\">";
						$thetag.="<div class=\"expert_tag_name\" style=\"width:auto;font-size:12px;\">".trim($tags['lname'])."</div>";
						$thetag.="<div class=\"expert_tag_action\">";
						$thetag.="<a href=\"javascript:;\" id=\"remove_tag_".$i."\" onclick=\"$('#the_tag_list_".$key."_".$i."').animate({'background-color':'#FF6666' },500);$('#the_tag_list_".$key."_".$i."').remove();\">X</a>";
						$thetag.="</div>";
						$thetag.="<input type=\"hidden\" name=\"tagit[".$key."][]\" value=\"".trim($tags['lname'])."\" />";
						$thetag.="</div>";
					
					$i++;
				}
				add_variable('thetags',$thetag);
				
				//Expertise Skills
				$exprt_skill_result=fetch_rulerel_by_group_type($val,"profile","skills");
				$theskill="";
				
				$i=1;
				while($skills=$db->fetch_array($exprt_skill_result)){
						$theskill.="<div class=\"expert_tag_list skill_index_".$key." clearfix\" id=\"the_skill_list_".$key."_".$i."\">";
						$theskill.="<div class=\"expert_tag_name\" style=\"width:auto;font-size:12px;\">".trim($skills['lname'])."</div>";
						$theskill.="<div class=\"expert_tag_action\">";
						$theskill.="<a href=\"javascript:;\" id=\"remove_skill_".$i."\" onclick=\"$('#the_skill_list_".$key."_".$i."').animate({'background-color':'#FF6666' },500);$('#the_skill_list_".$key."_".$i."').remove();\">X</a>";
						$theskill.="</div>";
						$theskill.="<input type=\"hidden\" name=\"skillit[".$key."][]\" value=\"".trim($skills['lname'])."\" />";
						$theskill.="</div>";
					
					$i++;
				}
				add_variable('theskills',$theskill);
				
				add_variable('i',$key);
				add_variable('userid',$val);
				add_variable('user_type',$user_tpye);
				add_variable('display_name',$display_name);
				parse_template('loopUser','lUser',true);
			}
			add_variable('prc','Edit User');
			add_variable('is_edit_all',"<input type=\"hidden\" name=\"edit\" value=\"Edit\">");
			add_variable('tabs',$the_tabs);
			add_variable('save_user',button("button=save_changes&label=Save User"));
			add_variable('cancel',button("button=cancel",get_state_url('users')));
			parse_template('usersEdit','uEdit');
			return return_template('users');
		}elseif(is_delete_all()){
			add_actions('section_title','Delete User');
			$warning="<form action=\"\" method=\"post\">";
			if(count($_POST['select'])==1)
				$warning.="<div class=\"alert_red_form\"><strong>Are you sure want to delete this user:</strong>";
			else
				$warning.="<div class=\"alert_red_form\"><strong>Are you sure want to delete these users:</strong>";
				
			$warning.="<ol>";	
			foreach($_POST['select'] as $key=>$val){
				$d=fetch_user($val);
				$warning.="<li>".$d['ldisplay_name']."</li>";
				$warning.="<input type=\"hidden\" name=\"id[]\" value=\"".$d['luser_id']."\">";
			}
			$warning.="</ol></div>";
			$warning.="<div style=\"text-align:right;margin:10px 5px 0 0;\">";
			$warning.="<input type=\"submit\" name=\"confirm_delete\" value=\"Yes\" class=\"button\" />";
			$warning.="<input type=\"button\" name=\"confirm_delete\" value=\"No\" class=\"button\" onclick=\"location='".get_state_url('users')."'\" />";
			$warning.="</div>";
			$warning.="</form>";
			
			return $warning;
		}elseif(is_confirm_delete()){
			foreach($_POST['id'] as $key=>$val){
				delete_user($val);
			}
		}elseif(is_profile()){
			return edit_profile();
		}elseif(is_profile_picture()){
            return edit_profile_picture();
        }
		
		//Display Users Lists
		if(is_num_users()>0){
			return get_users_list($the_tabs);
		}
		
		
			
		
	}
	
	function is_dashboard(){
		if(!empty($_GET['state']) && $_GET['state']=='dashboard') return true;
		else return false;
	}
	function is_global_settings(){
		if(!empty($_GET['state']) && $_GET['state']=='global_settings' && is_grant_app('global_settings')) return true;
		else return false;
	}
	function is_admin_application(){
		if(!empty($_GET['state']) && $_GET['state']=='applications' && is_grant_app('applications')) return true;
		else return false;
	}
	function is_admin_plugin(){
		if(!empty($_GET['state']) && $_GET['state']=='plugins' && is_grant_app('plugins')) return true;
		else return false;
	}
	function is_admin_themes(){
		if(!empty($_GET['state']) && $_GET['state']=='themes' && is_grant_app('themes') ) return true;
		else return false;
	}
	function is_admin_comment(){
		if(!empty($_GET['state']) && $_GET['state']=='comments' && is_grant_app('comments') ) return true;
		else return false;
	}
	function is_admin_article(){
		if(!empty($_GET['state']) && $_GET['state']=='articles' && is_grant_app('articles')) return true;
		else return false;
	}
	function is_admin_page(){
		if(!empty($_GET['state']) && $_GET['state']=='pages' && is_grant_app('pages')) return true;
		else return false;
	}
	function is_admin_user(){
		if(!empty($_GET['state']) && $_GET['state']=='users' && is_grant_app('users')) return true;
		else return false;
	}
	function is_profile(){
		if(!empty($_GET['state']) && $_GET['state']=='my-profile' &&  isset($_GET['tab']) && $_GET['tab']=='my-profile') return true;
		if((!empty($_GET['state']) && $_GET['state']=='users' &&  isset($_GET['tab']) && $_GET['tab']=='my-profile')) return true;
		else return false;
	}
	function is_user_updates(){
		if(!empty($_GET['state']) && $_GET['state']=='my-profile')return true;
				
		if((!empty($_GET['state']) && $_GET['state']=='my-profile')  &&  isset($_GET['tab']) && $_GET['tab']=='my-updates') return true;

		if((!empty($_GET['state']) && $_GET['state']=='users' &&  isset($_GET['tab']) && $_GET['tab']=='my-updates')) return true;
		else return false;
	}
	
    function is_profile_picture(){
		if((!empty($_GET['state']) && $_GET['state']=='my-profile')  &&  isset($_GET['tab']) && $_GET['tab']=='profile-picture') return true;

		if((!empty($_GET['state']) && $_GET['state']=='users' &&  isset($_GET['tab']) && $_GET['tab']=='profile-picture')) return true;
		else return false;
	}
	function is_profile_eduwork(){
		if((!empty($_GET['state']) && $_GET['state']=='my-profile')  &&  isset($_GET['tab']) && $_GET['tab']=='eduwork') return true;

		if((!empty($_GET['state']) && $_GET['state']=='users' &&  isset($_GET['tab']) && $_GET['tab']=='eduwork')) return true;
		else return false;
	}
	function is_logout(){
		if(!empty($_GET['state']) && $_GET['state']=='logout') return true;
		else return false;
	}
	
	function is_state($state){
		if(!empty($_GET['state']) && $_GET['state']==$state && is_grant_app($state) ) return true;
		else return false;
	}
	function is_add_new(){
		
		if(((isset($_GET['prc']) && $_GET['prc']=='add_new') || isset($_POST['add_new'])) && allow_action($_GET['state'],'insert'))
			return true;
		return false;
		
	}
	function is_edit(){
		if((isset($_GET['prc']) && $_GET['prc']=='edit' && allow_action($_GET['state'],'update')))
			return true;
		return false;
		
	}
	function is_edit_all(){
		if((isset($_POST['edit']) && $_POST['edit']=='Edit' && allow_action($_GET['state'],'update')))
			return true;
		return false;
	}
	function is_edit_all_comment(){
		if((isset($_POST['edit']) && $_POST['edit']=='Edit Comments' && allow_action($_GET['state'],'update')))
			return true;
		return false;
	}
	
	function is_delete($app_name){
		if((isset($_POST['prc']) && $_POST['prc']=='delete' && allow_action($app_name,'delete')))
			return true;
		return false;
		
	}
	/*function is_approve($app_name){
		if((isset($_POST['prc']) && $_POST['prc']=='approve' && allow_action($app_name,'approve')))
			return true;
		return false;
	}
	function is_disapprove($app_name){
		if((isset($_POST['prc']) && $_POST['prc']=='disapprove' && allow_action($app_name,'approve')))
			return true;
		return false;
	}*/
	function is_delete_all(){
		if((isset($_POST['delete']) && $_POST['delete']=='Delete' && allow_action($_GET['state'],'delete')))
			return true;
		return false;
	}
	function is_confirm_delete(){
		if((isset($_POST['confirm_delete']) && $_POST['confirm_delete']=='Yes' && allow_action($_GET['state'],'delete')))
			return true;
		return false;
	}
	function is_search(){
		if((isset($_POST['prc']) && $_POST['prc']=='search'))
			return true;
		if((isset($_POST['search']) && $_POST['search']=='yes'))
			return true;
		return false;
		
	}
	function is_save_changes($state=''){
		if(empty($state)){
			if(isset($_POST['save_changes']) && (allow_action($_GET['state'],'update') || allow_action($_GET['state'],'insert') || allow_action($_GET['state'],'approve')))
				return true;
				return false;
		}else{
			if(isset($_POST['save_changes']) && (allow_action($state,'update') || allow_action($state,'insert') || allow_action($state,'approve')))
				return true;
				return false;
		}
		
	}
	function is_save_draft(){
		if(isset($_POST['save_draft']) && allow_action($_GET['state'],'insert'))
			return true;
		return false;
	}
	function is_publish(){
		if(isset($_POST['publish']) && allow_action($_GET['state'],'insert') && $_COOKIE['user_type']!='contributor')
			return true;
		return false;
	}
	function is_unpublish(){
		if(isset($_POST['unpublish']) && allow_action($_GET['state'],'insert'))
			return true;
		return false;
	}
	function is_approved(){
		$return=false;
		
		if(isset($_POST['publish']) && allow_action($_GET['state'],'approve') && (is_administrator() || is_editor()))
			$return=true;
			
		if((isset($_POST['prc']) && $_POST['prc']=='approve' && allow_action($_POST['state'],'approve')))
			$return=true;
			
		return $return;
	}
	function is_disapproved(){
		$return=false;
		if(isset($_POST['unpublish']) && allow_action($_GET['state'],'approve') && (is_administrator() || is_editor()))
			$return=true;
			
		if((isset($_POST['prc']) && $_POST['prc']=='disapprove' && allow_action($_POST['state'],'approve')))
			$return=true;
			
		return $return;
	}
	function is_saved(){
		if(isset($_POST['article_saved'])) return true;
		else return false;
	}
	

?>