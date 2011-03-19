<?php
	/*
		Title: Aruna
		Preview: preview.png
		Author: Wahya Biantara
		Author Url: http://wahya.biantara.com
		Description: Default themes since March 2010
	*/

	//Check if the user is not login yet
	if(!is_user_logged()){
		if(is_login_form())
			/*Return Login Form*/ 
			get_login_form();
		elseif(is_register_form())
			signup_user();
		elseif(is_thanks_page())
			resendPassword();
		elseif(is_verify_account())
			verify_account();
		elseif(is_forget_password()) 
			/*Return Forget Password Form*/
			get_forget_password_form();
		else
			header("location:".get_admin_url()."/?state=login&redirect=".urlencode(cur_pageURL()));
	}else{
		
		/*redirect to home dashboard*/
		if(empty($_GET['state']))
			header("location:".get_admin_url()."/?state=dashboard");
				
		//set template
		set_template(TEMPLATE_PATH."/index.html");
		
		//set block
		add_block('adminArea','aBlock');
		
		//add variable
		
		/*get site URI*/
		add_variable('site_url','http://'.site_url());
		
		/*get website title*/
		add_variable('web_title',web_title());
		
		/*get loged user gravatar*/
		add_variable('avatar',get_avatar($_COOKIE['user_id'], 2));
		
		/*get logout URI*/
		add_variable('logout_link',get_state_url('logout'));
		
		/*get profile URI*/
		if(is_administrator())
			add_variable('profile_link',get_state_url('users')."&tab=my-profile");
		else 
			add_variable('profile_link',get_state_url('my-profile')."&tab=my-profile");
			
		/*get profile updates URI*/
		if(is_administrator())
			add_variable('profile_updates',get_state_url('users')."&tab=my-updates");
		else 
			add_variable('profile_updates',get_state_url('my-profile')."&tab=my-updates");
			
		/*get User Display Name*/
		$d=fetch_user($_COOKIE['user_id']);
		add_variable('displayname',$d['ldisplay_name']);
		
		//setting up the header elements. CSS, Javascript,Jquery And the others
		
		/*Get CSS*/
		add_actions('header_elements','get_css');
		 
			
		
		/*get jQuery UI*/
		add_actions('header_elements','get_javascript','jquery_ui');
		
		/*get jQuery for Navigation*/
		add_actions('header_elements','get_javascript','navigation');
		
		/*get jQuery for colobox popup plugin*/
		add_actions('header_elements','get_javascript','jquery.colorbox');
		
		/*get jQuery for colobox popup function*/
		add_actions('header_elements','get_javascript','colorbox');
		
		/*get jQuery for dialog box*/
		add_actions('header_elements','get_javascript','dialog');
		
		/*get jQuery for tinymce*/
		add_actions('header_elements','get_javascript','tiny_mce/jquery.tinymce');
		
		/*get jQuery for tinymce config*/
		add_actions('header_elements','get_javascript','tiny_mce');
		
		/*get jQuery for form validation*/
		add_actions('header_elements','get_javascript','form_validation');
		
		/*get jQuery for taxonomy function*/
		add_actions('header_elements','get_javascript','taxonomy');
		
		/*get the content area*/
		add_variable('content_area',$thecontent);
		
		//setting up navigation menu
		add_variable('navmenu',get_admin_menu());
		
		//Attempt the action that already add in the whole script
		/*get jQuery*/
		add_variable('jquery',get_javascript('jquery'));
		add_variable('header_elements',attemp_actions('header_elements'));
		add_variable('section_title',attemp_actions('section_title'));
		add_variable('admin_tail',attemp_actions('admin_tail'));
		
		//print the template
		parse_template('adminArea','aBlock');
		print_template(); 
	}	
	
?>