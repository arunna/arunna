<?php
    require_once('../lumonata_config.php');
    require_once('../lumonata_settings.php');
    require_once('../lumonata-functions/settings.php');
    require_once('../lumonata-classes/actions.php');
    require_once('../lumonata-functions/upload.php');
    require_once('../lumonata-functions/attachment.php');
    require_once('../lumonata-classes/directory.php');
    require_once('../lumonata-functions/user.php');
    require_once('../lumonata-functions/paging.php');
    require_once('../lumonata-content/languages/en.php');
    
     
    
    if(!defined('SITE_URL'))
	    define('SITE_URL',get_meta_data('site_url'));
    
    /*
	Check is the user is logged or not
	If user is not logged yet, then redirect user to the login form
    */
    if(!is_user_logged()){
	    header("location:".get_admin_url()."/?state=login");
    }else{
	
        
	
	if(!defined('TEMPLATE_PATH'));
	    define('TEMPLATE_PATH',ROOT_PATH.'/lumonata-admin/');
	
	
	$theme=get_meta_data('admin_theme','themes');
	if(!defined('TEMPLATE_URL'))
	   define('TEMPLATE_URL',SITE_URL.'/lumonata-admin/themes/'.$theme);
	
	if(!defined('FILES_PATH'))
	   define('FILES_PATH',ROOT_PATH.'/lumonata-content/files');    
	
	require_once('../lumonata-functions/template.php');
        
        if(isset($_GET['post_id'])){
            //set template
            set_template(TEMPLATE_PATH."upload-media.html",'uploadMedia');
            
            //set block
            add_block('uploadMediaBlock','upMediaBlock','uploadMedia');
            add_block('imageMediaURLBlock','iMediaBlock','uploadMedia');
            add_block('otherMediaURLBlock','oMediaBlock','uploadMedia');
            add_block('galleryMediaBlock','gMediaBlock','uploadMedia');
            
            add_actions('header_elements','get_javascript','jquery');
            add_actions('header_elements','get_javascript','jquery.colorbox');
            add_actions('header_elements','get_javascript','colorbox');
            add_actions('header_elements','get_javascript','upload-media');
            $file_name='';
            
            add_variable('css','http://'.TEMPLATE_URL.'/css/style.css');
            
            $count_gallery=count_attachment($_GET['post_id']);
            $count_lib=count_attachment();
            $tabs=array("from-computer"=>"From Computer","from-url"=>"From URL","gallery"=>"Gallery ($count_gallery)","library"=>"Library ($count_lib)");
            $tab=set_attachment_tab($tabs);
            add_variable('tabs',$tab);
            add_variable('textarea_id',$_GET['textarea_id']);
        }
        
        //sort order the gallery
	if(isset($_POST['update_media_order'])){
           update_attachment_order($_POST['attachment'],$_POST['start']);
    }elseif(isset($_POST['confirm_delete'])){
	    if($_POST['confirm_delete']=="yes"){
			delete_attachment($_POST['delete_id']);
	    }
	}elseif(isset($_POST['save_changes']) && $_POST['save_changes']=="save_item"){
		if(edit_attachment($_POST['attachment_id'], $_POST['title'],$_POST['order'],$_POST['alt_text'],$_POST['caption']))
		    echo "<div class=\"alert_green\">".UPDATE_SUCCESS."</div>";
	}elseif(isset($_POST['insert']) && !is_array($_POST['insert'])){
	    edit_attachment($_POST['attachment_id'],$_POST['title'],$_POST['order'],$_POST['alt_text'],$_POST['caption']);
	}elseif(isset($_POST['s']) && isset($_POST['tab'])){
	    echo search_attachment_results($_POST['s'],$_POST['tab'],$_POST['article_id'],$_POST['textarea_id']);
	}else{
            
            
            //File uploaded from user computer and then save it into database
            if(isset($_GET['tab']) && $_GET['tab']=='from-computer'){
                if(isset($_POST['upload'])){ //if upload button clicked
                    
                    //Create destination folder if folder is not exist yet
                    if(!is_dir(FILES_PATH.'/'.upload_folder_name())){
                        if(!create_dir(FILES_PATH.'/'.upload_folder_name()))
                            add_variable('alert',"<div class=\"alert_red_form\" style=\"width:93%;\">Unable to create new folder <code>".FILES_PATH.'/'.$folder_name."</code></div>");
                    }
                   
                    $file_name = $_FILES['media']['name'];
                    $file_size = $_FILES['media']['size'];
                    $file_type = $_FILES['media']['type'];
                    $file_source = $_FILES['media']['tmp_name'];
                    
                    
                    if(is_allow_file_type($file_type,$_GET['type'])){
                        if(is_allow_file_size($file_size)){
                            //If file type is Image
                            if($_GET['type']=='image'){
                                if(upload_image_attachment($file_source,$file_type,$file_name,$_GET['post_id'])){
				    
				    $file=attemp_actions('original_file_location');
				    $large_file=attemp_actions('large_file_location');
				    $medium_file=attemp_actions('medium_file_location');
				    $thumb_file=attemp_actions('medium_file_location');
				    
				    $default_title=file_name_filter($file_name);
				    
				    $d=array('lattach_id'=>mysql_insert_id(),
					     'larticle_id'=>$_GET['post_id'],
					     'lattach_loc'=>$file,
					     'lattach_loc_large'=>$large_file,
					     'lattach_loc_medium'=>$medium_file,
					     'lattach_loc_thumb'=>$thumb_file,
					     'ltitle'=>$default_title,
					     'lalt_text'=>'',
					     'lcaption'=>'',
					     'mime_type'=>$file_type,
					     'upload_date'=>date(get_date_format(),time())
					     );
				    
				    add_variable('attachment_details', attachment_details($d,0,$_GET['textarea_id'],$_GET['tab']));
                                    add_actions("bottom_elements","<script type=\"text/javascript\">$('#upload_image_detail').show('slow');</script>");
                                    
                                }
                            }else{
                                if(upload_media_attachment($file_source,$file_type,$file_name,$_GET['post_id'])){
                                   
				    $file="http://".SITE_URL.attemp_actions('original_file_location');
				    $default_title=file_name_filter($file_name);
				    
				    $d=array('lattach_id'=>mysql_insert_id(),
					     'larticle_id'=>$_GET['post_id'],
					     'lattach_loc'=>$file,
					     'ltitle'=>$default_title,
					     'lalt_text'=>'',
					     'lcaption'=>'',
					     'mime_type'=>$file_type,
					     'upload_date'=>date(get_date_format(),time())
					     );
				    
				    add_variable('attachment_details', attachment_details($d,0,$_GET['textarea_id'],$_GET['tab']));
                                    add_actions("bottom_elements","<script type=\"text/javascript\">  $('#upload_image_detail').show('slow');</script>");
                                }
                            }
                            add_variable("delete_box",delete_confirmation_box(mysql_insert_id(),"Are you sure want to delete <code>".$default_title."</code> from the gallery?","upload-media.php","upload_image_detail"));
                        }else{
                            add_variable('alert',"<div class=\"alert_red_form\" style=\"width:93%;\">The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>.</div>");
                        }
                    }else{
                        add_variable('alert',"<div class=\"alert_red_form\" style=\"width:93%;\">File type not allowed</div>");
                    }
                }
		
                add_variable('upload_button',upload_button());
                add_variable('cancel_button',cancel_button());
                parse_template('uploadMediaBlock','upMediaBlock');
                
            //File URL get from another website and then save it into database
            }elseif(isset($_GET['tab']) && $_GET['tab']=='from-url'){
                add_variable('insert_button',button("button=insert&type=button"));
		add_variable('type',$_GET['type']);
		add_variable('textarea_id',$_GET['textarea_id']);
                if($_GET['type']=='image'){
                    add_variable('type_label','Image');
                    parse_template('imageMediaURLBlock','iMediaBlock');
                }elseif($_GET['type']=='flash'){
                    add_variable('type_label','SWF');
                    parse_template('otherMediaURLBlock','oMediaBlock'); 
                }elseif($_GET['type']=='video'){
                    add_variable('type_label','Video');
                    parse_template('otherMediaURLBlock','oMediaBlock'); 
                }elseif($_GET['type']=='music'){
                    add_variable('type_label','Music');
                    parse_template('otherMediaURLBlock','oMediaBlock'); 
                }elseif($_GET['type']=='pdf'){
                    add_variable('type_label','PDF');
                    parse_template('otherMediaURLBlock','oMediaBlock'); 
                }elseif($_GET['type']=='doc'){
                    add_variable('type_label','Document');
                    parse_template('otherMediaURLBlock','oMediaBlock'); 
                }
		
            }
            
            
            
            
        }
        
        if((isset($_GET['tab']) && $_GET['tab']=='gallery')){
	   
            if(isset($_POST['save_all_changes']) && $_POST['save_all_changes']=="Save All Changes"){
                
                for($i=$_POST['start_order'];$i<count($_POST['attachment_id'])+$_POST['start_order'];$i++){
                    edit_attachment($_POST['attachment_id'][$i],$_POST['title'][$i],$_POST['order'][$i],$_POST['alt_text'][$i],$_POST['caption'][$i]);
                }
                add_variable('response',"<div class=\"alert_green\">".UPDATE_SUCCESS."</div>");
                add_actions("bottom_elements","<script type=\"text/javascript\">$('#response').slideDown(500);$('#response').delay(3000);$('#response').slideUp(500);</script>");
            }
             
            add_variable('attachment',get_attachment($_GET['post_id']));
            add_variable('asc_order',get_attachment_tab_url($_GET['tab'])."&sort_order=asc");
            add_variable('desc_order',get_attachment_tab_url($_GET['tab'])."&sort_order=desc");
            
            add_actions('header_elements','get_javascript','jquery_ui');
            parse_template('galleryMediaBlock','gMediaBlock'); 
        }
        
        if(isset($_GET['tab']) && $_GET['tab']=='library'){
		    
            if(isset($_POST['save_all_changes']) && $_POST['save_all_changes']=="Save All Changes"){
               
                for($i=$_POST['start_order'];$i<count($_POST['attachment_id'])+$_POST['start_order'];$i++){
                    edit_attachment($_POST['attachment_id'][$i],$_POST['title'][$i],$_POST['order'][$i],$_POST['alt_text'][$i],$_POST['caption'][$i]);
                }
                add_variable('response',"<div class=\"alert_green\">".UPDATE_SUCCESS."</div>");
                add_actions("bottom_elements","<script type=\"text/javascript\">$('#response').slideDown(500);$('#response').delay(3000);$('#response').slideUp(500);</script>");
            }
            add_variable('attachment',get_attachment());
            add_variable('asc_order',get_attachment_tab_url($_GET['tab'])."&sort_order=asc");
            add_variable('desc_order',get_attachment_tab_url($_GET['tab'])."&sort_order=desc");
            
            add_actions('header_elements','get_javascript','jquery_ui');
            
            parse_template('galleryMediaBlock','gMediaBlock'); 
        }
        
        //Attempt the action that already add in the whole script
        //add_variable('delete_box',delete_confirmation_box(mysql_insert_id(),"Are you sure want to delete <code>$file_name</code> from the gallery?"));
        add_variable('header_elements',attemp_actions('header_elements'));
        add_variable('bottom_elements',attemp_actions('bottom_elements'));
        echo return_template('uploadMedia'); 
        
    }
    
    function set_attachment_tab($tabs){
        $tab='';
       
        foreach($tabs as $key=>$val){
            if($_GET['tab']==$key)
                $tab.="<li class=\"active\"><a href=\"http://".SITE_URL."/lumonata-admin/upload-media.php?tab=".$key."&post_id=".$_GET['post_id']."&type=".$_GET['type']."&textarea_id=".$_GET['textarea_id']."\">$val</a></li>";
            else
                $tab.="<li><a href=\"http://".SITE_URL."/lumonata-admin/upload-media.php?tab=".$key."&post_id=".$_GET['post_id']."&type=".$_GET['type']."&textarea_id=".$_GET['textarea_id']."\">$val</a></li>";
                
        }
        
        return $tab;
    }
    
    function get_attachment_tab_url($tab){
            $page_URL = 'http';
             if (!empty($_SERVER["HTTPS"]))
                    $page_URL .= "s";
                    
                    $page_URL .= "://";
             if ($_SERVER["SERVER_PORT"] != "80"){
                    $page_URL .= SITE_URL.":".$_SERVER["SERVER_PORT"]."/lumonata-admin/upload-media.php?tab=".$tab."&post_id=".$_GET['post_id']."&type=".$_GET['type']."&textarea_id=".$_GET['textarea_id'];
             }else{
                    $page_URL .= SITE_URL."/lumonata-admin/upload-media.php?tab=".$tab."&post_id=".$_GET['post_id']."&type=".$_GET['type']."&textarea_id=".$_GET['textarea_id'];
             }
             
             return $page_URL;
    }
?>