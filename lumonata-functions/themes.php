<?php
	if(isset($_POST['position']) && isset($_POST['gl']) && !isset($_POST['upload'])){
		require_once('../lumonata-functions/user.php');
		if(is_user_logged()){
			require_once('../lumonata_config.php');
		    require_once('../lumonata_settings.php');
		    require_once('../lumonata-functions/settings.php');
		    require_once('../lumonata-classes/actions.php');
		    
		    if($_POST['position']=="pages")
		    	echo published_pages_themes();
		    elseif($_POST['position']=="home")
		    	echo "Choose your file and upload";
		    else 
				echo published_apps_themes($_POST['position']);
		}
	}
	
	if(isset($_POST['reset_id'])){
		
		require_once('../lumonata-functions/user.php');
		if(is_user_logged()){
			require_once('../lumonata_config.php');
		    require_once('../lumonata_settings.php');
		    require_once('../lumonata-functions/kses.php');
		    require_once('../lumonata-functions/settings.php');
		    require_once('../lumonata-classes/actions.php');
		    
		    $theme=get_meta_data('front_theme','themes');
		    $val=array('position'=>$_POST['position'],'location'=>'/'.$theme.'/images/headers/default.jpg');
			$val=json_encode($val);
		    edit_additional_field($_POST['reset_id'], 'headers', $val, $_POST['app_name']);
		}
	}
	
	if(isset($_POST['remimg'])){
		require_once('../lumonata-functions/user.php');
		if(is_user_logged()){
			require_once('../lumonata_config.php');
		    require_once('../lumonata_settings.php');
		    require_once('../lumonata-functions/kses.php');
		    require_once('../lumonata-functions/settings.php');
		    require_once('../lumonata-classes/actions.php');
		    
		    if($_POST['remimg']=='yes'){
		    	$value=get_additional_field($_POST['id'], 'headers', $_POST['app_name']);
		    	$value=json_decode($value,true);
		    	$filename= $_POST['theme_path'].$value['location'];
		    	unlink($filename);
		    	delete_additional_field($_POST['id'], $_POST['app_name']);
		    }else{
		    	delete_additional_field($_POST['id'], $_POST['app_name']);
		    }
		    
		}
	}
	function get_themes(){
		$tabs=array('front'=>'Website Themes','admin'=>'Admin Themes','header'=>'Header','background'=>'Background');
		$view_themes='';
		$current_theme='';
		$activate=false;
		
		if(!empty($_GET['action']))
			$activate=$_GET['action'];
		
		
		//set template
		set_template(TEMPLATE_PATH."/themes.html",'themes');
		
		//set block
		add_block('frontthemesBlock','ftBlock','themes');
		add_block('adminthemesBlock','atBlock','themes');
		add_block('changeBackgroundBlock','cBBlock','themes');
		add_block('headerBlock','hdBlock','themes');
		//set variable
		
			
		if(empty($_GET['tab']))
			$the_tab='front';
		else
			$the_tab=$_GET['tab'];
		
		$default_parameters = array( 
			'Title' => 'Title', 
			'Preview' => 'Preview', 
			'Author' => 'Author', 
			'AuthorURL' => 'Author URL', 
			'Description' => 'Description' 
			);
		
		if($the_tab=='front'){
			//set the page Title
			add_actions('section_title','Themes - Website Themes');
			if($activate=='activate'){
				update_meta_data('front_theme',$_GET['theme'],'themes');
				
			}elseif($activate=='delete'){
				if(file_exists(FRONT_TEMPLATE_PATH.'/'.$_GET['theme']))
					remove_dir(FRONT_TEMPLATE_PATH.'/'.$_GET['theme']);
			}
			
			

			
			add_variable('tab',set_tabs($tabs,$the_tab));
			foreach(get_themes_dir() as $val){
				
				if(file_exists(FRONT_TEMPLATE_PATH.'/'.$val.'/template.php')){
					$file=FRONT_TEMPLATE_PATH.'/'.$val.'/template.php';
					$parameters = fetch_parameters($file, $default_parameters);
				}else{
					continue;
				}
				
				if(get_meta_data('front_theme','themes')==$val){
					$current_theme="<div class=\"current_theme_preview\">";
					$current_theme.="<h2>Current Theme</h2>";
					$current_theme.="<img src=\"".get_theme_preview($val,$parameters['Preview'])."\" alt=\"".$parameters['Title']."\" title=\"".$parameters['Title']."\" />";
					$current_theme.="<h3>".$parameters['Title']."</h3>";
					$current_theme.="<p><em>".$parameters['Description']."</em></p>";
					$current_theme.="</div>";
				}
				if(get_meta_data('front_theme','themes')!=$val){
					$view_themes.="<div class=\"theme_preview\">";
					$view_themes.="<img src=\"".get_theme_preview($val,$parameters['Preview'])."\" alt=\"".$parameters['Title']."\" title=\"".$parameters['Title']."\" />";
					$view_themes.="<h3>".$parameters['Title']."</h3>";
					$view_themes.="<p><em>".$parameters['Description']."</em></p>";
					$view_themes.="<p><a href=\"".get_tab_url('front')."&theme=$val&action=activate\">Activate</a> | <a class='colorbox_webpage' href=\"http://".site_url()."/?preview=true&theme=$val\" title=\"".$parameters['Title']."\">Preview</a> | <a href=\"".get_tab_url('front')."&theme=$val&action=delete\">Delete</a></p>";
					$view_themes.="</div>";
				}
			}
			add_variable('themes_preview',$view_themes);
			add_variable('current_theme',$current_theme);
			parse_template('frontthemesBlock','ftBlock');
		}elseif($the_tab=='admin'){
			//set the page Title
			add_actions('section_title','Themes - Admin Themes');
			
			if($activate=='activate'){
				update_meta_data('admin_theme',$_GET['theme'],'themes');
				header("location:".get_tab_url('admin'));
			}elseif($activate=='delete'){
				if(file_exists(ADMIN_TEMPLATE_PATH.'/'.$_GET['theme']))
					remove_dir(ADMIN_TEMPLATE_PATH.'/'.$_GET['theme']);
			}
			
			add_variable('tab',set_tabs($tabs,$the_tab));
			foreach(get_themes_dir('admin') as $val){
				
				if(file_exists(ADMIN_TEMPLATE_PATH.'/'.$val.'/template.php')){
					$file=ADMIN_TEMPLATE_PATH.'/'.$val.'/template.php';
					$parameters = fetch_parameters($file, $default_parameters);
				}else{
					continue;
				}
				
				
				if(get_meta_data('admin_theme','themes')==$val){
					$current_theme="<div class=\"current_theme_preview\">";
					$current_theme.="<h2>Current Theme</h2>";
					$current_theme.="<img src=\"".get_theme_preview($val,$parameters['Preview'],'admin')."\" alt=\"".$parameters['Title']."\" title=\"".$parameters['Title']."\" />";
					$current_theme.="<h3>".$parameters['Title']."</h3>";
					$current_theme.="<p><em>".$parameters['Description']."</em></p>";
					$current_theme.="</div>";
				}
				if(get_meta_data('admin_theme','themes')!=$val){
					$view_themes.="<div class=\"theme_preview\">";
					$view_themes.="<img src=\"".get_theme_preview($val,$parameters['Preview'],'admin')."\" alt=\"".$parameters['Title']."\" title=\"".$parameters['Title']."\" />";
					$view_themes.="<h3>".$parameters['Title']."</h3>";
					$view_themes.="<p><em>".$parameters['Description']."</em></p>";
					$view_themes.="<p><a href=\"".get_tab_url('admin')."&theme=$val&action=activate\">Activate</a> | <a class='colorbox_webpage' href=\"".get_admin_url()."/?state=dashboard&preview=true&theme=$val\" title=\"".$parameters['Title']."\">Preview</a> | <a href=\"".get_tab_url('admin')."&theme=$val&action=delete\">Delete</a></p>";
					$view_themes.="</div>";
				}
			}
			add_variable('themes_preview',$view_themes);
			add_variable('current_theme',$current_theme);
			parse_template('adminthemesBlock','atBlock');
			
		}elseif($the_tab=='background'){
			//set the page Title
			$bgcolor=get_meta_data('custome_bg_color','themes');
			$bgimage_prev=get_meta_data('custome_bg_image_preview','themes');
			$bgimage=get_meta_data('custome_bg_image','themes');
			$bgpos=get_meta_data('custome_bg_pos','themes');
			$bgrepeat=get_meta_data('custome_bg_repeat','themes');
			$bgattach=get_meta_data('custome_bg_attachment','themes');
			
			if(isset($_POST['upload'])){
				
				$folder_name=upload_folder_name();
			    if(!defined('FILES_LOCATION'))
				    define('FILES_LOCATION','/lumonata-content/files');
				    
			 	if(!is_dir(FILES_PATH.'/'.upload_folder_name())){
                     if(!create_dir(FILES_PATH.'/'.upload_folder_name()))
                     add_variable('alert',"<div class=\"alert_red_form\" style=\"width:93%;\">Unable to create new folder <code>".FILES_PATH.'/'.$folder_name."</code></div>");
                }
                
				$file_name = $_FILES['background_image']['name'];
                $file_size = $_FILES['background_image']['size'];
                $file_type = $_FILES['background_image']['type'];
                $file_source = $_FILES['background_image']['tmp_name'];
				
				$file_name=character_filter($file_name);
				$file_name_t=file_name_filter($file_name).'-thumbnail'.file_name_filter($file_name,true);
				
				$destination=FILES_PATH.'/'.$folder_name.'/'.$file_name;
				$destination_t=FILES_PATH.'/'.$folder_name.'/'.$file_name_t;
				
				$file_location=FILES_LOCATION.'/'.$folder_name.'/'.$file_name; 
				$file_location_t=FILES_LOCATION.'/'.$folder_name.'/'.$file_name_t;  
				
				if(upload_resize($file_source,$destination_t,$file_type,thumbnail_image_width(),thumbnail_image_height())){    
					if(upload($file_source, $destination)){
						if(empty($bgimage)){
							set_meta_data('custome_bg_image',$file_location,'themes');
							set_meta_data('custome_bg_image_preview',$file_location_t,'themes');
						}else{
							unlink(ROOT_PATH.$bgimage);
							unlink(ROOT_PATH.$bgimage_prev); 
							update_meta_data('custome_bg_image', $file_location,'themes');
							update_meta_data('custome_bg_image_preview', $file_location_t,'themes');
						}
						header("location:".cur_pageURL());
					}
				}
			}
			if(isset($_POST['remove_bg'])){
				$bgimage_prev=get_meta_data('custome_bg_image_preview','themes');
				$bgimage=get_meta_data('custome_bg_image','themes');
				
				unlink(ROOT_PATH.$bgimage);
				unlink(ROOT_PATH.$bgimage_prev);
				
				delete_meta_data('custome_bg_image','themes');
				delete_meta_data('custome_bg_image_preview','themes'); 
				delete_meta_data('custome_bg_pos','themes'); 
				delete_meta_data('custome_bg_repeat','themes'); 
				delete_meta_data('custome_bg_attachment','themes'); 
				header("location:".cur_pageURL());
			}
			
			if(is_save_changes()){
				if(!empty($_POST['custome_color'])){
					if(empty($bgcolor))
						set_meta_data('custome_bg_color',$_POST['custome_color'],'themes');
					else 
						update_meta_data('custome_bg_color', $_POST['custome_color'],'themes');
				}	
				
				if(isset($_POST['position'])){
					if(empty($bgpos))
						set_meta_data('custome_bg_pos',$_POST['position'],'themes');
					else 
						update_meta_data('custome_bg_pos', $_POST['position'],'themes');
				}	
				if(isset($_POST['repeat'])){
					if(empty($bgrepeat))
						set_meta_data('custome_bg_repeat',$_POST['repeat'],'themes');
					else	
						update_meta_data('custome_bg_repeat', $_POST['repeat'],'themes');
				}
				if(isset($_POST['attachment'])){
					if(empty($bgattach))
						set_meta_data('custome_bg_attachment',$_POST['attachment'],'themes');
					else	
						update_meta_data('custome_bg_attachment', $_POST['attachment'],'themes');
				}
				header("location:".cur_pageURL());
			}
			
			add_actions('section_title','Themes - Background');
			add_variable('botton',save_changes_botton());
			add_variable('tab',set_tabs($tabs,$the_tab));
			add_variable('custome_color',$bgcolor);
			
			colorpicker('#custome_color','#background_preview');
			
			
			$style_ent="";
			$style="";
			if(!empty($bgcolor))
					$style_ent="background-color:#".$bgcolor.";";
				
			if(!empty($bgimage_prev)){
				$style_ent.="background-image: url('http://".site_url().$bgimage_prev."');";
				
				$bpos=array('left top'=>'Left','center top'=>'Center','right top'=>'Right');
				$brepeat=array('no-repeat'=>'No Repeat','repeat'=>'Tile','repeat-x'=>'Repeat Horizontally','repeat-y'=>'Repeat Vertically');
				$battachment=array('scroll'=>'Scroll','fixed'=>'Fixed');
				
				
				$position='';
				$repeat='';
				$attach='';
				
				if(!empty($bgpos))
					$style_ent.="background-position:".$bgpos.";";
	
				if(!empty($bgrepeat))
					$style_ent.="background-repeat:".$bgrepeat.";";
				
					
				/*if(!empty($bgattach))
					$style_ent.="background-attachment:".$bgattach.";";
				*/	
				foreach ($bpos as $key=>$val){
					if(!empty($bgpos)){
						if($key==$bgpos){
							$position.='<input type="radio"  name="position" value="'.$key.'" checked="checked">'.$val;
						}else{
							$position.='<input type="radio"  name="position" value="'.$key.'" >'.$val;
						}
					}else{
						if($key=='left top'){
							$position.='<input type="radio"  name="position" value="'.$key.'" checked="checked">'.$val;
						}else{
							$position.='<input type="radio"  name="position" value="'.$key.'" >'.$val;
						}
					}
				}
				
				foreach ($brepeat as $key=>$val){
					if(!empty($bgrepeat)){
						if($key==$bgrepeat){
							$repeat.='<input type="radio"  name="repeat" value="'.$key.'" checked="checked">'.$val;
						}else{
							$repeat.='<input type="radio"  name="repeat" value="'.$key.'" >'.$val;
						}
					}else{
						if($key=='repeat'){
							$repeat.='<input type="radio"  name="repeat" value="'.$key.'" checked="checked">'.$val;
						}else{
							$repeat.='<input type="radio"  name="repeat" value="'.$key.'" >'.$val;
						}
					}
				}
				
			   foreach ($battachment as $key=>$val){
					if(!empty($bgattach)){
						if($key==$bgattach){
							$attach.='<input type="radio"  name="attachment" value="'.$key.'" checked="checked">'.$val;
						}else{
							$attach.='<input type="radio"  name="attachment" value="'.$key.'" >'.$val;
						}
					}else{
						if($key=='scroll'){
							$attach.='<input type="radio"  name="attachment" value="'.$key.'" checked="checked">'.$val;
						}else{
							$attach.='<input type="radio"  name="attachment" value="'.$key.'" >'.$val;
						}
					}
				}
				
				$img_display_opt='<tr>
	             		<td width="200">Position</td>
	             		<td>'.
	             			$position
	             		.'</td>
	             	</tr>
	             	<tr>
	             		<td width="200">Repeat</td>
	             		<td>
	             			'.$repeat.'
	             		</td>
	             	</tr>
	             	
	             	<tr>
	             		<td width="200">Image Attachment</td>
	             		<td>
	             			'.$attach.'
	             		</td>
	             	</tr>';
				$javas="<script type=\"text/javascript\">
							$(function(){
								$('input[name=position]').click(function(){
									$('#background_preview').css({'background-position' : $(this).val()});
								});
								$('input[name=repeat]').click(function(){
									$('#background_preview').css({'background-repeat' : $(this).val()});
								});
								
							});
						</script>";
				add_variable('remove_img',"<input type=\"submit\" name=\"remove_bg\" value=\"Remove background image\" class=\"button\" />");
				add_variable('image_display_options',$img_display_opt);
				add_variable('javas',$javas);
			}
			if(!empty($style_ent))
				$style="style=\"".$style_ent."\"";
			
			add_variable('style',$style);	
						
			parse_template('changeBackgroundBlock','cBBlock');
		}elseif($the_tab=='header'){
			if(isset($_POST['upload']) && isset($_FILES['header'])){
				
				$theme_name=get_meta_data('front_theme','themes');
				$file_name = $_FILES['header']['name'];
	            $file_size = $_FILES['header']['size'];
	            $file_type = $_FILES['header']['type'];
	            $file_source = $_FILES['header']['tmp_name'];
	            $ext=file_name_filter($file_name,true);
	            $thmb_upload=false;
	            if($_POST['upload']=='Upload'){
					if(strtolower(substr($file_name,-4))=='.jpg' || strtolower(substr($file_name,-5))=='.jpeg' || strtolower(substr($file_name,-4))=='.gif' || strtolower(substr($file_name,-4))=='.png' || strtolower(substr($file_name,-4))=='.swf'){
						if(strtolower(substr($file_name,-4))!='.swf'){
							
							$file_name=character_filter($file_name);
							$file_name_t=file_name_filter($file_name).'-thumb'.file_name_filter($file_name,true);
							
							$destination=FRONT_TEMPLATE_PATH.'/'.$theme_name.'/images/headers/'.$file_name;
							$destination_t=FRONT_TEMPLATE_PATH.'/'.$theme_name.'/images/headers/'.$file_name_t;
	
							$file_location='/'.$theme_name.'/images/headers/'.$file_name; 
							
							if(upload_crop($file_source,$destination_t,$file_type,250,60))
							$thmb_upload=true;
							
						}else{
							$file_name=character_filter($file_name);
							$destination=FRONT_TEMPLATE_PATH.'/'.$theme_name.'/images/headers/'.$file_name;
							$file_location='/'.$theme_name.'/images/headers/'.$file_name;
							$thmb_upload=true;
						}
						
						if($thmb_upload==true){
							if(upload($file_source, $destination)){
								if($_POST['position']=='home'){
									$app_id=0;
									$key='headers';
									$val=array('position'=>'Home','location'=>$file_location);
									$val=json_encode($val);
									$app_name='home';
									if(count_additional_field($app_id, $key, $app_name)==0)
										add_additional_field($app_id, $key, $val, $app_name);
									else 
										edit_additional_field($app_id, $key, $val, $app_name);
								}else{
									
									foreach ($_POST['selected'] as $key=>$val){
										
										$position=ucwords(preg_replace("#(_|-)#", " ", $_POST['position']));
										$value=array('position'=>$position." &raquo; ".$_POST['subpos'][$val],'location'=>$file_location);
										$value=json_encode($value);
										if(count_additional_field($val, 'headers', $_POST['position'])==0)
											add_additional_field($val, 'headers', $value, $_POST['position']);
										else 
											edit_additional_field($val, 'headers', $value, $_POST['position']);
									}
								}
		
								header("location:".cur_pageURL());
							}
						}
						
						
					}else{
						add_variable("alert","<div class=\"alert_yellow_form\">Allowed file types: .jpg, .gif, .png and .swf</div>");
					}
	            }elseif($_POST['upload']=='Add'){
	            	if($_POST['position']=='home'){
						$app_id=0;
						$key='headers';
						$val=array('position'=>'Home','location'=>$_POST['selected_header'][0]);
						$val=json_encode($val);
						$app_name='home';
						if(count_additional_field($app_id, $key, $app_name)==0)
							add_additional_field($app_id, $key, $val, $app_name);
						else 
							edit_additional_field($app_id, $key, $val, $app_name);
					}else{
						
						foreach ($_POST['selected'] as $key=>$val){
							
							$position=ucwords(preg_replace("#(_|-)#", " ", $_POST['position']));
							$value=array('position'=>$position." - ".$_POST['subpos'][$val],'location'=>$_POST['selected_header'][0]);
							$value=json_encode($value);
							
							if(count_additional_field($val, 'headers', $_POST['position'])==0)
								add_additional_field($val, 'headers', $value, $_POST['position']);
							else 
								edit_additional_field($val, 'headers', $value, $_POST['position']);
						}
					}

					header("location:".cur_pageURL());
	            	
	            }
	            
			}
			if(is_save_changes()){
	        	
	        	foreach($_POST['selected_header'] as $key=>$val){
	        		
	        		if($_POST['position_lbl'][$key]=='Home'){
	        			$value=array('position'=>'Home','location'=>$val);
						$value=json_encode($value);
	        		}else{
	        			
	        			$value=array('position'=>$_POST['position_lbl'][$key],'location'=>$val);
						$value=json_encode($value);
	        		}
	        		
	        		edit_additional_field($key, 'headers', $value, $_POST['app_name'][$key]);
	        		
	        	}
	        	
	        }
			add_actions('section_title','Themes - Headers');
			add_actions('admin_tail','themes_javascript');
			add_variable('apps_set',attemp_actions('plugin_menu_set'));
			add_variable('tab',set_tabs($tabs,$the_tab));
			add_variable('botton',save_changes_botton());
			add_variable('the_headers',get_listed_headers());
			add_variable('available_headers',get_header_images(0,''));
			parse_template('headerBlock','hdBlock');
		}
		return return_template('themes');
	}
	function get_listed_headers(){
		global $db;
		$list='';
		
		$viewed=list_viewed();
        if(isset($_GET['page'])){
            $page= $_GET['page'];
        }else{
            $page=1;
        }
        
        $limit=($page-1)*$viewed;
		
		$query=$db->prepare_query("SELECT * FROM lumonata_additional_fields
									WHERE lkey='headers' LIMIT %d, %d",$limit,$viewed);
		$result=$db->do_query($query);
		$i=0;
		while($data=$db->fetch_array($result)){
			if($i%2==0){
				$color='#ffffff;';
			}else{ 
				$color='#f0f0f0;';
			}	
			$value=json_decode($data['lvalue'],TRUE);
			
			if(isset($_SERVER['HTTPS'])){
				$location='https://'.FRONT_TEMPLATE_URL.$value['location'];
					
			}else{ 
				$location='http://'.FRONT_TEMPLATE_URL.$value['location'];
			}	
			
			
			
			$list.="<div class=\"clearfix header_row\" style=\"background-color:".$color."\" id=\"row_".$i."\">
			              <div class=\"header_pos\">".$value['position']."</div>";
						 if(substr($location, -4)==".swf"){
						 	list($w, $h, $type, $attr) = getimagesize(FRONT_TEMPLATE_PATH.$value['location']);
						 	$list.="<div class=\"header_img\"><a id=\"colorbox_outside_flash_".$i."\" href=\"".$location."\">View Image</a></div>
						 			<script type=\"text/javascript\">
						 				 $(\"#colorbox_outside_flash_".$i."\").colorbox({iframe:true, innerWidth:".$w.", innerHeight:".$h."});
						 			</script>";
						 }else{
			             	$list.="<div class=\"header_img\"><a rel=\"view_colorbox_elastic\" href=\"".$location."\">View Image</a></div>";
						 }
			             $list.="<div class=\"header_act\">
			              	  <a href=\"javascript:;\" id=\"edit_header_".$i."\">Edit</a> | 
			              	  <a href=\"javascript:;\" id=\"reset_header_".$i."\">Reset</a> | 
			              	  <a href=\"javascript:;\" rel=\"delete_".$data['lapp_id']."\">Remove</a>
				              <script type=\"text/javascript\">
					              $(function(){
						              $(\"#edit_header_".$i."\").click(function(){
						              		$(\"#available_headers_".$i."\").slideToggle();
						              });
						              $(\"a[rel='view_colorbox_elastic']\").colorbox();
						              
						              $(\"#reset_header_".$i."\").click(function(){
						              		$('#row_".$i."').css({'background-color' : '#FFCC66'});
						              		$.post('../lumonata-functions/themes.php',
													{ 'reset_id' : '".$data['lapp_id']."',
													  'app_name' : '".$data['lapp_name']."',
													  'position' : '".$value['position']."',
													  'location' : '".$value['location']."'
													},function(data){}
											);
						              		$('#row_".$i."').animate({'background-color' : '".$color."'});
						              		location='".cur_pageURL()."'
									  });
									  
					              });
				              </script>
			              </div>
			              
			              <div id=\"available_headers_".$i."\" style=\"display: none;border: 1px solid #ccc;background:#fff;margin:30px 0 0 0;\">
			              		".get_header_images($data['lapp_id'],$value['location'])."
			              		<br clear=\"left\" />
			              		<input type=\"hidden\" name=\"position_lbl[".$data['lapp_id']."]\" value=\"".$value['position']."\" />
			              		<input type=\"hidden\" name=\"app_name[".$data['lapp_id']."]\" value=\"".$data['lapp_name']."\" />
			              		
			              </div>
			              <br clear=\"left\" />
	         		</div>
	         		";
			add_actions('admin_tail','delete_confirmation_box',$data['lapp_id'],'Do you also want to delete the image ?','../lumonata-functions/themes.php','row_'.$i,'id='.$data['lapp_id'].'&app_name='.$data['lapp_name'].'&remimg=yes&theme_path='.urlencode(FRONT_TEMPLATE_PATH),'id='.$data['lapp_id'].'&app_name='.$data['lapp_name'].'&remimg=no');
			//delete_confirmation_box($data['lapp_id'],'Do you also want to delete the image ?','../lumonata-functions/themes.php','row_'.$i,'id='.$data['lapp_id'].'&app_name='.$data['lapp_name'].'&remimg=yes&theme_path='.FRONT_TEMPLATE_PATH,'id='.$data['lapp_id'].'&app_name='.$data['lapp_name'].'&remimg=no');
			
			$i++;
			
		}
		$num_rows=count_rows("SELECT * FROM lumonata_additional_fields WHERE lkey='headers'");
		$url=get_tab_url('header')."&page=";
		$list.="<div class=\"paging_right\">". paging($url,$num_rows,$page,$viewed,10)."</div>";
		return $list;
	}
	function get_header_images($id,$location){
		$headers="";
		if(!is_dir(TEMPLATE_PATH.'images/headers'));
		return;
		$theheaders=scan_dir('themes','images/headers');
		
		if(isset($_SERVER['HTTPS']))
			$location='https://'.FRONT_TEMPLATE_URL.$location;	
		else 
			$location='http://'.FRONT_TEMPLATE_URL.$location;
			
		foreach ($theheaders['thumb'] as $key=>$val){
			
			$the_location=preg_replace("#(http://".FRONT_TEMPLATE_URL.")#", "", $theheaders['origin'][$key]);
			
			if($theheaders['origin'][$key]==$location){
				if(substr($theheaders['origin'][$key], -4)=='.swf'){
					list($w, $h, $type, $attr) = getimagesize(FRONT_TEMPLATE_PATH.$the_location);
					$headers.="<div class=\"header_thumb\">
								<a href=\"".$theheaders['origin'][$key]."\" id=\"flash_ourside_".$id."\">
									 <object width=\"250\" height=\"60\" title=\"\" type=\"application/x-shockwave-flash\" data=\"".$theheaders['origin'][$key]."\">
			                  			<param name=\"movie\" value=\"".$theheaders['origin'][$key]."\" />
			                  			<param name=\"quality\" value=\"high\" />
							   			<param name=\"wmode\" value=\"transparent\" />
						  			 </object>
					  			</a>
					  			<br />
								<input type=\"radio\" name=\"selected_header[$id]\" value=\"".$the_location."\" checked=\"checked\" />Choose This
							 </div>
							 <script type=\"text/javascript\">
				 				 $(\"#flash_ourside_".$id."\").colorbox({iframe:true, innerWidth:".$w.", innerHeight:".$h."});
				 			</script>
					  			 ";
				}else{
					$headers.="<div class=\"header_thumb\">
									<a href=\"".$theheaders['origin'][$key]."\" rel=\"colorbox_elastic_".$id."\">
									<img src=\"".$val."\" id=\"preview_header_".$key."\" border=\"0\" />
									</a>
									<br />
									<input type=\"radio\" name=\"selected_header[$id]\" value=\"".$the_location."\" checked=\"checked\" />Choose This
								</div>";
				}
				
			}else {
				if(substr($theheaders['origin'][$key], -4)=='.swf'){
					list($w, $h, $type, $attr) = getimagesize(FRONT_TEMPLATE_PATH.$the_location);
					$headers.="<div class=\"header_thumb\">
								<a href=\"".$theheaders['origin'][$key]."\" id=\"flash_ourside_".$id."\">
									 <object width=\"250\" height=\"60\" title=\"\" type=\"application/x-shockwave-flash\" data=\"".$theheaders['origin'][$key]."\">
			                  			<param name=\"movie\" value=\"".$theheaders['origin'][$key]."\" />
			                  			<param name=\"quality\" value=\"high\" />
							   			<param name=\"wmode\" value=\"transparent\" />
						  			 </object>
					  			</a>
					  			<br />
								<input type=\"radio\" name=\"selected_header[$id]\" value=\"".$the_location."\" />Choose This
							 </div>
							 <script type=\"text/javascript\">
				 				 $(\"#flash_ourside_".$id."\").colorbox({iframe:true, innerWidth:".$w.", innerHeight:".$h."});
				 			</script>
					  			 ";
				}else{
					$headers.="<div class=\"header_thumb\">
									<a href=\"".$theheaders['origin'][$key]."\" rel=\"colorbox_elastic_".$id."\">
										<img src=\"".$val."\" id=\"preview_header_".$key."\" border=\"0\" />
									</a>
									<br />
									<input type=\"radio\" name=\"selected_header[$id]\" value=\"".$the_location."\" />Choose This
								</div>";
				}	
			}
			
		}
		$headers.="<script type=\"text/javascript\">
							$(function(){
								 $(\"a[rel='colorbox_elastic_".$id."']\").colorbox();
							});
					   </script>";
		return $headers;
	}
	
	function themes_javascript(){
		return "<script type=\"text/javascript\">
             		$(document).ready(function(){
             			//reset all to unchecked
				        $('.selected_menu').each(function(){
				            $('.selected_menu').removeAttr('checked');
				        });
				        
				        $('a[rel=select_all]').click(function(){
				        	var selected_all=true;
				        	$('.selected_menu').each(function(){
				            	if(this.checked==false){
				            		this.checked=true;
				            		selected_all=false;
								}
				        	});
				        	
				        	if(selected_all){
				        		$('.selected_menu').each(function(){
					            		this.checked=false;
					        	});
							}
				        	
						});
	             		$('select[name=position]').change(function(){
	             			$('#app_items').html('<img src=\"".get_theme_img()."/loader.gif\" />');
							$.post('../lumonata-functions/themes.php',
							{ position:$('select[name=position]').val(), gl:true },
							function(data){
								$('#app_items').html(data);
							});
		             	});
	             	});
		        </script>";
	}
	function published_pages_themes(){
		global $db;
		$html="";
		$pub_pages=the_published_pages();
		while($data=$db->fetch_array($pub_pages)){
			$html.="<div class=\"pages_name_items clearfix\">";
			$html.="<div class=\"page_name_label\">";
			$html.=$data['larticle_title'];
			$html.="</div>";
			$html.="<div class=\"page_name_check\">";
			$html.="<input type=\"checkbox\" name=\"selected[]\" class=\"selected_menu\" value=\"".$data['larticle_id']."\">";
			$html.="<input type=\"hidden\"  value=\"".$data['larticle_title']."\" name=\"subpos[".$data['larticle_id']."]\" />";
			$html.="</div>";
			$html.="</div>";
		}
		return $html;
	}
	function published_apps_themes($app_name){
		global $db;
		$html="";
		if($app_name=='tags')
			$pub_apps=the_published_tags();
		else
			$pub_apps=the_published_apps($app_name);
			
		while($data=$db->fetch_array($pub_apps)){
			$html.="<div class=\"pages_name_items clearfix\">";
			$html.="<div class=\"page_name_label\">";
			$html.=$data['lname'];
			$html.="</div>";
			$html.="<div class=\"page_name_check\">";
			$html.="<input type=\"checkbox\" name=\"selected[]\" class=\"selected_menu\" value=\"".$data['lrule_id']."\">";
			$html.="<input type=\"hidden\"  value=\"".$data['lname']."\" name=\"subpos[".$data['lrule_id']."]\" />";
			$html.="</div>";
			$html.="</div>";
		}
		return $html;
	}
	function the_header(){
		if(is_home()){
			$value=get_additional_field(0, 'headers', 'home');
			$value=json_decode($value,true);
			return filter_header_file($value['location']);
		}elseif(is_details()){
			foreach (toxo_id('tags') as $val){
				$value=get_additional_field($val, 'headers', 'tags');
				if(!empty($value))break;
			}
			if(empty($value)){
				foreach (toxo_id() as $val){
					$value=get_additional_field($val, 'headers', get_appname());
					if(!empty($value))break;
					else $value=get_additional_field(0, 'headers', get_appname());
				}
			}
			$value=json_decode($value,true);
			return filter_header_file($value['location']);
			
		}elseif(is_category() || is_page()){
			$value=get_additional_field(post_to_id(), 'headers', get_appname());
			$value=json_decode($value,true);
			return filter_header_file($value['location']);
		}
	}
	function filter_header_file($location){
		if(empty($location))
			return "<img src=\"http://".FRONT_TEMPLATE_URL.'/'.get_meta_data('front_theme','themes').'/images/headers/default.jpg'."\" />";
		
		if(substr($location, -4)!=".swf"){
			return "<img src=\"http://".FRONT_TEMPLATE_URL.$location."\" />";
		}else{
			list($w, $h, $type, $attr) = getimagesize(FRONT_TEMPLATE_PATH.$location);
			return "<object width=\"".$w."\" height=\"".$h."\" title=\"\" type=\"application/x-shockwave-flash\" data=\"http://".FRONT_TEMPLATE_URL.$location."\">
                  		<param name=\"movie\" value=\"http://".FRONT_TEMPLATE_URL.$location."\" />
                  		<param name=\"quality\" value=\"high\" />
				   		<param name=\"wmode\" value=\"transparent\" />
			  		</object>	
					";
		}
	}
?>