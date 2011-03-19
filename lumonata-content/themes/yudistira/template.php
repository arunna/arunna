<?php
	/*
		Title: Yudistira
		Preview: yudistira.jpg
		Author: Wahya Biantara
		Author Url: http://wahya.biantara.com
		Description: Yudistira is the default themes since July 2010
	*/
	
	/*Set the default template name is index.html*/
	
	set_template(TEMPLATE_PATH."/index.html");
	
	/*set the block name of the template as mainBlock*/
	add_block('mainBlock','mBlock');
	
	/*set the template variable here*/
	
	/*CSS*/
	add_variable('style_sheet',get_css());
	
	/*Image location of choosen template*/
	add_variable('theme_img',get_theme_img());
	
	/*Get the Web Title*/
	add_variable('web_title',web_title());
	
	/* Meta Title */
	$meta_title=run_actions("meta_title");
	
	if(!empty($meta_title)){
		add_variable('meta_title',run_actions("meta_title"));
	}else{
		if(is_category('appname=login') || is_page("page_name=login")){
			add_variable('meta_title',"Login - ".web_title());
		}elseif(is_category('appname=register') || is_page("page_name=register")){
			add_variable('meta_title',"Register - ".web_title());
		}else{
			add_variable('meta_title',get_post_title("-").web_title());
		}
	}
	/*Custome Header*/
	add_variable('custome_header',  the_header());
	
	/*Meat Keywords*/
	add_variable('meta_keywords',run_actions("meta_keywords"));
	
	/*Meta Description*/
	add_variable('meta_description',run_actions("meta_description"));
	
	/* Get the tagline */
	add_variable('tagline',web_tagline());
	
	/* Get the site URI */
	add_variable('site_url',site_url());
	
	/* The jQuery */
	add_variable('jquery',get_javascript('jquery-1.4.4.min'));
	
	/*The Content*/
	add_variable('content_area',$thecontent);
	
	/*Pages as Top Menu*/
	add_variable('top_menu',the_page_menu());
	
	/*Main Menu Set As Right Menu*/
	add_variable('right_menu',the_menus('menuset=main menu'));
	
	/*The Categories As Menu on the right side*/
	add_variable('article_categories',get_the_categories('app_name=articles'));
	
	/*Attempt the action that already add in the whole script*/
	
	/*Attemp header*/
	add_variable('header',attemp_actions('header'));
	
	/*Attemp Tail*/
    add_variable('tail',attemp_actions('tail'));
    
	/*Parse the template*/
	parse_template('mainBlock','mBlock');
	
	/*Print the template*/
	print_template(); 
?>