<?php
	$admin_menu= new admin_menu();
	
	class admin_menu{
		function admin_menu(){
			$this->main_menu=array(
						'articles' => 'Articles',
						'pages' => 'Pages',
						'applications' => 'Applications',
						'users'=>'Users',
						'my-profile'=>'Profile',
						'comments' => 'Comments',
						
						);
			
			$this->plugins_menu=array('installed'=>'Installed',
						  'active'=>'Active',
						  'inactive'=>'Inactive');
			
			$this->apps_menu=array();
			
			$this->settings_menu=array(
					'personal-settings' => 'Personal Settings',
					'plugins' => 'Plugins',
					'themes' => 'Themes',
					'global_settings' => 'Settings',
					'menus'=>'Menus'
			);
			
			$this->connection_menu=array(
					'dashboard'=>'Dashboard',
					'notifications' => 'Notifications',
					'friends'=>'Connections',
		            'people'=>'People',
			);
			
		}
		
		function add_main_menu($menu){
			if(!is_array($menu))
				return;
			
			$this->main_menu=array_merge($this->main_menu,$menu);
			
		}
		
		function add_sub_menu($parent,$submenu){
			if(!is_array($submenu))
				return;
			$this->submenu[$parent]=$submenu;
		}
		
		function add_plugins_menu($menu){
			if(!is_array($menu))
				return;
			
			$this->plugins_menu=array_merge($this->plugins_menu,$menu);
		}
		
		function add_apps_menu($menu){
			if(!is_array($menu))
				return;
			
			$this->apps_menu=array_merge($this->apps_menu,$menu);
		}
		function get_admin_menu($type='main_menu'){
			switch ($type){
				case "main_menu":
					$themenu=$this->main_menu;
					break;
				case "connection_menu":
					$themenu=$this->connection_menu;
					break;
				case "settings_menu":
					$themenu=$this->settings_menu;
					break;
				
			}
			
			$menu="<ul>";
			
			foreach($themenu as $key=>$val){
				if($key=='applications'){
					$sub=$this->get_apps_menu();
				}elseif($key=='plugins'){
					$sub=$this->get_plugins_menu();
				}elseif($key=='people'){
					$sub=$this->get_people_categories();
				}else{
					
					if(isset($this->submenu[$key]) && is_array($this->submenu[$key])){
						
						if($_GET['state']==$key)
							$display="";
						else
							$display="style='display:none;'";
							
						$sub="<ul id=\"".$key."_list\" $display>";
			
						foreach($this->submenu[$key] as $subkey=>$subval){
							if(is_preview()){
								$theme=$_GET['theme'];
								$sub.="<li><a href=\"?state=".$key."&sub=$subkey&preview=true&theme=$theme\">$subval</a></li>";
							}else{
								if(is_grant_app($key))
									$sub.="<li><a href=\"?state=".$key."&sub=$subkey\">$subval</a></li>";
							}
						}
						$sub.="</ul>";
						$sub.="<script type=\"text/javascript\">
							$(function(){
								$('a#".$key."').click(function(){
								$('#".$key."_list').slideToggle(100);
								return false;
								});
							});
						       </script>";
					}else{
						$sub="";
					}
				}
				$noti_comment_count="";
				$class_name=$key;
				if($key=='comments'){
					
					$count_comment=count_comment_status('moderation');
					if($count_comment>0){
						$noti_comment_count="<span class=\"count_updates\" id=\"comments_updates\">".$count_comment."</span>";
						$noti_comment_count.="<script type=\"text/javascript\">
													$('#comments_updates').click(function(){
														location=$('#comments').attr('href');
													})
											  </script>";
						
					}
					
						
				}
				
				if($key=='friends'){
					$count_friends_req=0;
					if(isset($_COOKIE['user_id']))
					$friend_req=myfriend_requests($_COOKIE['user_id']);
					if(isset($friend_req['id']))
					$count_friends_req=count($friend_req['id']);
					if($count_friends_req>0){
						$noti_comment_count="<span class=\"count_updates\" id=\"friends_updates\">".$count_friends_req."</span>";
						$noti_comment_count.="<script type=\"text/javascript\">
													$('#friends_updates').click(function(){
														location=$('#friends').attr('href');
													})
											  </script>";
						
					}
					
						
				}
				
				if($key=='notifications'){
					$count_notif=count_notifications($_COOKIE['user_id']);
					
					if($count_notif>0){
						$noti_comment_count="<span class=\"count_updates\" id=\"notification_updates\">".$count_notif."</span>";
						$noti_comment_count.="<script type=\"text/javascript\">
													$(function(){
														$('#notification_updates').click(function(){
															$('#notification_updates').colorbox({ href:$('#notifications').attr('href') });
															$('#notification_updates').hide();
														});
													});
											  </script>";
						
					}else{
						$noti_comment_count="<span class=\"count_updates\" id=\"notification_updates\" style=\"display:none;\">".$count_notif."</span>";
					}
					
					$menu.="<script type=\"text/javascript\">
								$(function(){
									$('#notifications').click(function(){
										$('#notifications').colorbox();
										$('#notification_updates').hide();
									});
								});
						  </script>";	
				}
				
				if(is_preview()){
					$theme=$_GET['theme'];
					if(is_grant_app($key)){
						if($key=="comments" || $key=="friends" || $key=="notifications")
							$menu.="<li class=\"".$class_name."\">
										<div class=\"theanav\">
											<div class=\"theanav_l\">
												<a href=\"?state=$key&preview=true&theme=$theme\" id=\"$key\">".$val."</a>
											</div>
											<div class=\"theanav_r\" >".$noti_comment_count."</div>
											<br clear=\"left\" />
										</div>$sub
									</li>";
						else 
							$menu.="<li class=\"".$class_name."\"><a href=\"?state=$key&preview=true&theme=$theme\" id=\"$key\">".$val." ".$noti_comment_count."</a>$sub</li>";
					}
				}else{
					if(is_grant_app($key)){
						if($key=="comments" || $key=="friends")
							$menu.="<li class=\"".$class_name."\">
										<div class=\"theanav\">
											<div class=\"theanav_l\">
												<a href=\"?state=$key\" id=\"$key\">".$val."</a>
											</div>
											<div class=\"theanav_r\">".$noti_comment_count."</div>
											<br clear=\"left\" />
										</div>
										$sub
									</li>";
						elseif($key=="notifications")
							$menu.="<li class=\"".$class_name."\">
										<div class=\"theanav\">
											<div class=\"theanav_l\">
												<a href=\"../lumonata-functions/notifications.php?notify=show\" id=\"$key\">".$val."</a>
											</div>
											<div class=\"theanav_r\">".$noti_comment_count."</div>
											<br clear=\"left\" />
										</div>
										$sub
									</li>";
						elseif($key=="applications" || $key=="plugins")
							$menu.="<li class=\"".$class_name."\"><a href=\"#\" id=\"$key\">".$val." ".$noti_comment_count."</a>$sub</li>";					
						else 
							$menu.="<li class=\"".$class_name."\"><a href=\"?state=$key\" id=\"$key\">".$val." ".$noti_comment_count."</a>$sub</li>";
					}
				}
			}
			$menu.="</ul>";
			return $menu;
		}
		function get_apps_menu(){
			if($_GET['state']=='applications')
				$display="";
			else
				$display="style='display:none;'";
				
			$menu="<ul id=\"applications_list\" $display>";
			
			$menu_set=$this->apps_menu;
			
			if(empty($menu_set)) return;
			
					
			foreach($menu_set as $key=>$val){
				if(is_grant_app($key)){
					if(is_preview()){
						$theme=$_GET['theme'];
						$menu.="<li><a href=\"?state=applications&sub=$key&preview=true&theme=$theme\">$val</a></li>";
					}else{
						if($key=='installed'){
							if(allow_action('applications','install'))
								$menu.="<li><a href=\"?state=applications&sub=$key\">$val</a></li>";
						}else{
							if(is_grant_app('applications'))
								$menu.="<li><a href=\"?state=applications&sub=$key\">$val</a></li>";
						}
					}
				}
			}
			$menu.="</ul>";
			
			return $menu;
		}
		function get_plugins_menu(){
			if($_GET['state']=='plugins')
				$display="";
			else
				$display="style='display:none;'";
				
			$menu="<ul id=\"plugins_list\" $display>";
			
			$menu_set=$this->plugins_menu;
			
			if(empty($menu_set)) return;
			
					
			foreach($menu_set as $key=>$val){
				if(is_preview()){
					$theme=$_GET['theme'];
					$menu.="<li><a href=\"?state=plugins&sub=$key&preview=true&theme=$theme\">$val</a></li>";
				}else{
					if($key=='installed'){
						if(allow_action('plugins','install'))
							$menu.="<li><a href=\"?state=plugins&sub=$key\">$val</a></li>";
					}else{
						if(is_administrator())
							$menu.="<li><a href=\"?state=plugins&sub=$key\">$val</a></li>";
					}
				}
			}
			$menu.="</ul>";
			
			return $menu;
		}
	    
	    
		function get_people_categories(){
			global $db;
		   	if($_GET['state']=='people')
				$display="";
			else
				$display="style='display:none;'";
				
			$menu="<ul id=\"people_list\" $display>";
				$expert=get_expertise_categories();
				while ($themenu=$db->fetch_array($expert)){
					if(is_preview()){
						$theme=$_GET['theme'];
						$menu.="<li><a href=\"?state=people&cat=".$themenu['lsef']."&preview=true&theme=$theme\">".$themenu['lname']."</a></li>";
					}else{
						$menu.="<li><a href=\"?state=people&cat=".$themenu['lsef']."\">".$themenu['lname']."</a></li>";
					}
				}
			$menu.="</ul>";
			
			return $menu;
		}
	}
    
	function add_main_menu($menu){
		global $admin_menu;
		$admin_menu->add_main_menu($menu);
    }
    function add_sub_menu($parent,$submenu){
		global $admin_menu;
		$admin_menu->add_sub_menu($parent,$submenu);
    }
    function add_apps_menu($menu){
		global $admin_menu;
		$admin_menu->add_apps_menu($menu);
    }
    function add_plugins_menu($menu){
		global $admin_menu;
		$admin_menu->add_plugins_menu($menu);
    }
    function get_admin_menu($type='main_menu'){
		global $admin_menu;
		return $admin_menu->get_admin_menu($type);
    }
		
	
?>