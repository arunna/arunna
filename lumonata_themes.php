<?php
	//Check default template that set in database here
	
	if($LUMONATA_ADMIN==false){
		if(is_preview())
			$theme=$_GET['theme'];
		else
			$theme=get_meta_data('front_theme','themes');
			
		define('TEMPLATE_PATH',ROOT_PATH.'/lumonata-content/themes/'.$theme);
		define('TEMPLATE_URL',SITE_URL.'/lumonata-content/themes/'.$theme);
		
		define('FRONT_TEMPLATE_PATH',ROOT_PATH.'/lumonata-content/themes');
		define('FRONT_TEMPLATE_URL',SITE_URL.'/lumonata-content/themes');
		
		define('LUMONATA_ADMIN', FALSE);
	}else{
		define('LUMONATA_ADMIN', TRUE);
		if(is_preview())
			$theme=$_GET['theme'];
		else
			$theme=get_meta_data('admin_theme','themes');
			
		define('TEMPLATE_PATH',ROOT_PATH.'/lumonata-admin/themes/'.$theme);
		define('TEMPLATE_URL',SITE_URL.'/lumonata-admin/themes/'.$theme);
		
		define('FRONT_TEMPLATE_PATH',ROOT_PATH.'/lumonata-content/themes');
		define('FRONT_TEMPLATE_URL',SITE_URL.'/lumonata-content/themes');
		
		define('ADMIN_TEMPLATE_PATH',ROOT_PATH.'/lumonata-admin/themes');
		define('ADMIN_TEMPLATE_URL',SITE_URL.'/lumonata-admin/themes');
	}
		
	if(!empty( $theme ))
		$theme=$theme;
	else
		$theme='default';
	
		
		
	/*
		After the templates name is found, the next step then is to configure the templates
		and set all variable that need to be set in the template
	*/		
		
	require_once(ROOT_PATH."/lumonata-functions/template.php");
	
	/*
	 * Configure and get the content to put in content area
	 * */
	if($LUMONATA_ADMIN==false){
		if(is_home()){
			$thecontent=the_looping_articles();
		}elseif(is_details()){
			$thecontent=article_detail();
		}elseif(is_category()){
			$thecontent=the_looping_categories();
		}elseif(is_tag()){
			$thecontent=the_looping_tags();
		}elseif(is_category('appname=register')){
			$thecontent=signup_user();
		}elseif(is_category('appname=login')){
			$thecontent=sign_in_form();
			add_actions('meta_title','Login');
		}elseif(is_category('appname=verify')){
			$thecontent=verify_account();
		}elseif(is_page("page_name=register")){
			$thecontent=signup_user();
		}elseif(is_page("page_name=login")){
			$thecontent=signup_user();
		}elseif(is_page()){
			$thecontent=article_detail();
		}else{
		
			$thecontent=run_actions("thecontent");
			if(!empty($thecontent))
				$thecontent=$thecontent;
			else 
				$thecontent="Data not found";
		}
		add_actions('header','get_custome_bg');
	}else{
		if(is_dashboard()){
			add_actions('header_elements','get_javascript','dashboard');
			$thecontent=get_dashboard();
		}elseif(is_global_settings()){
			$thecontent=get_global_settings();
		}elseif(is_admin_themes()){
			$thecontent=get_themes();
		}elseif(is_admin_page()){
			$thecontent=get_admin_page();
		}elseif(is_admin_article()){
			$thecontent=get_admin_article();
		}elseif(is_profile_eduwork ()){
			$thecontent=profile_eduwork();
		}elseif(is_profile_picture ()){
			$thecontent=edit_profile_picture();
		}elseif(is_profile()){
			$thecontent=edit_profile();
		}elseif(is_user_updates()){
			$thecontent=user_updates();
		}elseif(is_admin_user()){
			$thecontent=get_admin_user();
		}elseif(is_admin_plugin()){
			$thecontent=get_plugins();
		}elseif(is_admin_comment()){
			$thecontent=admin_comments();
		}elseif(is_logout()){
			do_logout();
		}else{
			if(isset($_GET['sub'])){
				if(is_grant_app($_GET['sub']))
				$thecontent=run_actions($_GET['sub']);
				
			}elseif(isset($_GET['state'])){
				if(is_grant_app($_GET['state']))
				$thecontent=run_actions($_GET['state']);
			}
			if(!empty($thecontent))
				$thecontent=$thecontent;
			else
				$thecontent="<div class=\"alert_red_form\">You don't have an authorization to access this page</div>";
		}
		
	}
	
	function get_custome_bg(){
		$bgcolor=get_meta_data('custome_bg_color','themes');
		$bgimage_prev=get_meta_data('custome_bg_image_preview','themes');
		$bgimage=get_meta_data('custome_bg_image','themes');
		$bgpos=get_meta_data('custome_bg_pos','themes');
		$bgrepeat=get_meta_data('custome_bg_repeat','themes');
		$bgattach=get_meta_data('custome_bg_attachment','themes');
		
		$style_ent="";
		$style="";
		
		if(!empty($bgcolor))
			$style_ent.="background-color:#".$bgcolor.";";
		
		if(!empty($bgimage))
			$style_ent.="background-image: url('http://".site_url().$bgimage."');";
			
			
		if(!empty($bgpos))
			$style_ent.="background-position:".$bgpos.";";
	
		if(!empty($bgrepeat))
			$style_ent.="background-repeat:".$bgrepeat.";";
		
			
		if(!empty($bgattach))
			$style_ent.="background-attachment:".$bgattach.";";
		
		if(!empty($style_ent))
				return $style="<style type=\"text/css\">body{ ".$style_ent." }</style>";
		
	}
	
	
		
	if( file_exists(TEMPLATE_PATH."/template.php")){
		require_once(TEMPLATE_PATH."/template.php");
	}else{
		echo lumonata_die("<h1>Template File Not Found</h1><p>Make sure that you already create the <code>template.php</code> at ".TEMPLATE_PATH."</p>");
	}
	
	
	
?>