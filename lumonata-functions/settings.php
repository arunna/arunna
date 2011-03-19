<?
	/**
	 * Return the meta data.
	 * This function is used to get all user meta data setting that can be manage using the administrator area.
	 * The return value is string. When this functions is called, you will need to passing a few variables
	 * depending on the type of meta data that you want to get on each applications.
	 * 
	 *
	 *
	 * @since 1.0.0
	 * @author Wahya Biantara
	 * @param string $meta_name Tag name of the mete data
	 * @param string $app_name Application name that use the meta data. Default value is global_setting
	 * @param integer $app_id If the meta data is used for the spesific application id in spesific application name
	 * @return string Value of meta data in spesific application name
	 */
	function get_meta_data($meta_name,$app_name='global_setting',$app_id=0){
		//defined database class to global variable
		global $db;
		$sql=$db->prepare_query("SELECT lmeta_value 
					FROM lumonata_meta_data 
					WHERE lmeta_name=%s and lapp_name=%s and lapp_id=%d",
					$meta_name,$app_name,$app_id);
		$r=$db->do_query($sql);
		$d=$db->fetch_array($r);
		
		return $d['lmeta_value'];
	}
	
	/**
	 * This function is used to save all user meta data setting that can be manage using the administrator area.
	 * The return value is boolean. When this functions is called, you will need to passing a few variables
	 * depending on the type of meta data that you want to get on each applications.
	 * 
	 *
	 *
	 * @since 1.0.0
	 * @author Wahya Biantara
	 * @param string $meta_name Tag name of the mete data.
	 * @param string $meta_value Value of meta data tag name that you mention. 
	 * @param string $app_name Application name that use the meta data. Default value is global_setting.
	 * @param integer $app_id If the meta data is used for the spesific application id in spesific application name.
	 * @return boolean Return true if the saving process is succesfully and false if failed.
	 */
	
	function set_meta_data($meta_name,$meta_value,$app_name='global_setting',$app_id=0){
		//defined database class to global variable
		global $db;
		$sql=$db->prepare_query("INSERT INTO lumonata_meta_data (lmeta_name,lmeta_value,lapp_name,lapp_id)
					VALUES (%s,%s,%s,%d)",
					$meta_name,$meta_value,$app_name,$app_id);
		$r=$db->do_query($sql);
		return $r;
	}
	/**
	 * This function is used to update all user meta data setting that can be manage using the administrator area.
	 * The return value is boolean. When this functions is called, you will need to passing a few variables
	 * depending on the type of meta data that you want to get on each applications.
	 * 
	 *
	 *
	 * @since 1.0.0
	 * 
	 * @author Wahya Biantara
	 * 
	 * @param string $meta_name Tag name of the mete data.
	 * @param string $meta_value Value of meta data tag name that you mention. 
	 * @param string $app_name Application name that use the meta data. Default value is global_setting.
	 * @param integer $app_id If the meta data is used for the spesific application id in spesific application name.
	 * @return boolean Return true if the updating process is succesfully and false if failed.
	 */
	function update_meta_data($meta_name,$meta_value,$app_name='global_setting',$app_id=0){
		//defined database class to global variable
		global $db;
		if(find_meta_data($meta_name,$app_name,$app_id)){
			$sql=$db->prepare_query("UPDATE lumonata_meta_data
						SET lmeta_name=%s,
						    lmeta_value=%s
						WHERE lmeta_name=%s AND lapp_name=%s AND lapp_id=%d",
						$meta_name,$meta_value,$meta_name,$app_name,$app_id);
			$r=$db->do_query($sql);
		}else{
			$r=set_meta_data($meta_name, $meta_value,$app_name,$app_id);
		}
		return $r;
	}
	/**
	 * This function is used to delete all user meta data setting
	 * The return value is boolean. When this functions is called, you will need to passing a few variables
	 * depending on the type of meta data that you want to get on each applications.
	 * 
	 *
	 *
	 * @since 1.0.0
	 * 
	 * @author Wahya Biantara
	 * 
	 * @param string $meta_name Tag name of the mete data. 
	 * @param string $app_name Application name that use the meta data. Default value is global_setting.
	 * @param integer $app_id If the meta data is used for the spesific application id in spesific application name.
	 * @return boolean Return true if the updating process is succesfully and false if failed.
	 */
	function delete_meta_data($meta_name,$app_name='global_setting',$app_id=0){
		//defined database class to global variable
		global $db;
		$sql=$db->prepare_query("DELETE FROM lumonata_meta_data
								WHERE lmeta_name=%s AND lapp_name=%s AND lapp_id=%d",
									  $meta_name,$app_name,$app_id);
		$r=$db->do_query($sql);
		return $r;
	}
	/*
	 * This function is used to check the mentioned meta data whether it is exist or not in database
	 * 
	 * @since 1.0.0
	 * 
	 * @param string $meta_name Name of meta data to check
	 * @param string $app_name Application name that use the meta data. Default value is global_setting
	 * @param integer $app_id If the meta data is used for the spesific application id in spesific application name
	 * @return boolean return true if it is exist and false if it is not
	 * */
	function find_meta_data($meta_name,$app_name='global_setting',$app_id=0){
		global $db;
		$sql=$db->prepare_query("SELECT lmeta_value 
					FROM lumonata_meta_data 
					WHERE lmeta_name=%s and lapp_name=%s and lapp_id=%d",
					$meta_name,$app_name,$app_id);
		$r=$db->do_query($sql);
		if($db->num_rows($r)>0)
			return true;
		else 
			return false;
	}
	/**
	 * Get the location of CSS file in each template. 
	 * ex: The CSS name is "stytle.css", then you will need to pass "style.css" to define the css file.
	 * If you don't pass any variable, then default file name is "style.css"
	 *
	 * @since 1.0.0
	 *
	 * @param string $filename CSS file name.
	 * @return string Return the HTML tag of CSS file location for each template
	 */
	
	function get_css($filename=''){
		
		if(empty($filename))
			$filename='style.css';
		else
			$filename=$filename;
		
		
		if(file_exists(TEMPLATE_PATH."/css/$filename")){
			return "<link rel=\"stylesheet\" href=\"http://".TEMPLATE_URL."/css/$filename\" type=\"text/css\" media=\"screen\" />";
		}elseif(file_exists(TEMPLATE_PATH."/$filename")){
			return "<link rel=\"stylesheet\" href=\"http://".TEMPLATE_URL."/$filename\" type=\"text/css\" media=\"screen\" />";
		}else{
			if(!defined('LUMONATA_ADMIN'))
				$theme=get_meta_data('front_theme','themes');
			else
				$theme=get_meta_data('admin_theme','themes');
				
			$message="<h1>CSS file not found</h1>
					  <p>Make sure that the CSS file have been created. 
					  Create your CSS file and save as <code>http://".TEMPLATE_URL."/css/$filename</code> at your theme folder (".$theme.") </p>";
			echo lumonata_die($message);
		}
	}
	/**
	 * Get the location of javascript file that located in /lumonata-admin/javascript/. Pass only the name of the file without the extension
	 * ex: The Javascipt name is "javascript.js", then you will need to pass "javascript".
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param string $filename Javascript file name.
	 * @return string Return the HTML tag of javascript file location.
	 */
	function get_javascript($filename){
		
		if(substr($filename, -3,3)==".js"){
			$ext="";
		}else{
			$ext=".js";
		}
		if(file_exists(get_admin_path()."/javascript/".$filename.$ext)){
			return "<script type=\"text/javascript\" src=\"".get_admin_url()."/javascript/".$filename.$ext."\" ></script>";
		}else{
				
			$message="<h1>Javascript file not found</h1>
					  <p>Make sure that the Javascript file have been saved at <code>lumonata-admin/javascript</code> </p>";
			echo lumonata_die($message);
		}
	}
	/**
	 * Get the meta data of online website address 
	 * 
	 *
	 * @since 1.0.0
	 * @return string Return the online website address, without the http://.
	 */
	function site_url(){
		return get_meta_data('site_url');
		
	}
	/**
	 * Check if the current address in browser is home page or not.
	 * 
	 *
	 * @since 1.0.0
	 * @return boolean Return true if the browser address is in home page and false if it is not home page.
	 */
	function is_home(){
		$site_url=trim("http://".site_url(),'/');
		$url=trim("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],'/');
		
		// Strip 'www.' if it is present and shouldn't be
		if ( false === strpos($site_url, '://www.') )
			$url = str_replace('://www.', '://', $url);
			
		if ( false === strpos($url, '://www.') )
			$site_url = str_replace('://www.', '://', $site_url	);
			
		
			
		if($url==$site_url)
			return true;
		return false;
	}
	/**
	 * Get the email address that set in General Global Settings
	 * 
	 *
	 * @since 1.0.0
	 * @return string Return the email address.
	 */
	function get_email(){
		return get_meta_data('email');
	}
	/**
	 * Get the SMTP Server address that set in General Global Settings
	 * 
	 *
	 * @since 1.0.0
	 * @return string Return the SMTP Server address.
	 */
	function get_smtp(){
		return get_meta_data('smtp');
	}
	/**
	 * Get the title of the website that set in General Global Settings
	 * 
	 *
	 * @since 1.0.0
	 * @return string Return website title.
	 */
	function web_title($separator=""){
		$separator=(is_home())?"":$separator;
		return get_meta_data('web_title')." ".$separator." ";
	}
	/**
	 * Get the name of the website that set in General Global Settings
	 * 
	 *
	 * @since 1.0.0
	 * @return string Return website name.
	 */
	function web_name($separator=""){
		$separator=(is_home())?"":$separator;
		return get_meta_data('web_name')." ".$separator." ";
	}
	/**
	 * Get the tagline of the website that set in General Global Settings
	 * 
	 *
	 * @since 1.0.0
	 * @return string Return website tagline.
	 */
	function web_tagline(){
		return get_meta_data('web_tagline');
	}

	/**
	 * Get the images location of selected theme
	 * 
	 *
	 * @since 1.0.0
	 * @return string Return the full address of images location for each template.
	 */
	function get_theme_img(){
		return 'http://'.TEMPLATE_URL."/images";
	}
        
	/**
	 * Get the location of selected theme
	 * 
	 *
	 * @since 1.0.0
	 * @return string Return the full address of selected template.
	 */
	function get_theme(){
		return TEMPLATE_URL;
	}
	
	/**
	 * Get the location of selected theme when you want to preview it before you apply it.
	 * This function could be used for back end and front end.
	 *
	 * @since 1.0.0
	 * @param string $theme Theme name
	 * @param string $preview Value should be set to True when you want to preview the selected theme 
	 * @param string $pos Define which theme that you want to preview. set "front" if you want to preview the front end theme, and "admin" to preview the admin theme.
	 * @return string Return the preview address of selected theme.
	 */
	function get_theme_preview($theme='',$preview='',$pos='front'){
		if(empty($theme) || empty($preview))
			return;
		
		if($pos=='front')
			return 'http://'.FRONT_TEMPLATE_URL.'/'.$theme.'/'.$preview;
		elseif($pos=='admin')
			return 'http://'.ADMIN_TEMPLATE_URL.'/'.$theme.'/'.$preview;
	}
	/**
	 * Get the path location of mentioned theme
	 * This function could be used for back end and front end.
	 *
	 * @since 1.0.0
	 *  
	 * @param string $pos Define which theme that you want to preview. set "front" if you want to preview the front end theme, and "admin" to preview the admin theme.
	 * @return string Return the path location of active theme.
	 */
	function get_themes_dir($pos='front'){
		if($pos=='front')
		return get_dir(FRONT_TEMPLATE_PATH);
		elseif($pos=='admin')
		return get_dir(ADMIN_TEMPLATE_PATH);
	}
	
	/**
	 * Get the /lumonata-admin/ address of active website
	 *
	 * @since 1.0.0
	 *  
	 * @return string Return the address of /lumonata-admin/.
	 */
	function get_admin_url(){
		return 'http://'.SITE_URL.'/lumonata-admin';
	}
	
	/**
	 * Get the path location of /lumonata-admin/
	 *
	 * @since 1.0.0
	 *  
	 * @return string Return the path location /lumonata-admin/.
	 */
	function get_admin_path(){
		return ROOT_PATH."/lumonata-admin";
	}
	/**
	 * Get the date format that set in General Global Settings
	 *
	 * @since 1.0.0
	 *  
	 * @return string Return the date format.
	 */
	function get_date_format(){
		return get_meta_data('date_format');
	}
	
	/**
	 * Get the time format that set in General Global Settings
	 *
	 * @since 1.0.0
	 *  
	 * @return string Return the time format.
	 */
	function get_time_format(){
		return get_meta_data('time_format');
	}
	
	/**
	 * Get the number of post viewed that set in Reading Global Settings
	 *
	 * @since 1.0.0
	 *  
	 * @return integer Return the number of post viewed.
	 */
	function post_viewed(){
		return get_meta_data('post_viewed');
	}
	
	/**
	 * Get the number of RSS viewed that set in Reading Global Settings
	 *
	 * @since 1.0.0
	 *  
	 * @return integer Return the number of RSS viewed.
	 */
	function rss_viewed(){
		return get_meta_data('rss_viewed');
	}
	/**
	 * Get the number of Status viewed that set in Reading Global Settings
	 *
	 * @since 1.0.0
	 *  
	 * @return integer Return the number of Status viewed.
	 */
	function status_viewed(){
		return get_meta_data('status_viewed');
	}
	/**
	 * Get the format of RSS that set in Reading Global Settings. The value can be "full_text" or "summary"
	 *
	 * @since 1.0.0
	 *  
	 * @return string The value can be "full_text" or "summary".
	 */
	function rss_view_format(){
		return get_meta_data('rss_view_format');
	}
	
	/**
	 * This function is used to define if the website is set to using rewrite rule or no.
	 *
	 * @since 1.0.0
	 *  
	 * @return string The value could be "yes" if using rewrite URL and "no" if only using standard URL.
	 */
	function is_rewrite(){
		return get_meta_data('is_rewrite');
	}
	
	/**
	 * This function is used to define if the website is set to using rewrite rule or no.
	 *
	 * @since 1.0.0
	 *  
	 * @return boolean Return true if using rewrite rule and false if no.
	 */
	function is_permalink(){
		return (is_rewrite()=='yes')?true:false;
	}
	
	/**
	 * This function is used to get the active language
	 *
	 * @since 1.0.0
	 *  
	 * @return string Return the active language.
	 */
	function is_language($lang){
		return $lang;
	}
	
	/**
	 * This function is used to get the number of how many list should be viewed on administrator area. 
	 * This meta data is set on Writing Global Setting
	 *
	 * @since 1.0.0
	 *  
	 * @return integer Return the number of list viewed.
	 */
	function list_viewed(){
		return get_meta_data('list_viewed');
	}
	
	/**
	 * This function is used to get email format that will be send in notifications.
	 * The value could be "html" or "plain_text"
	 *
	 * @since 1.0.0
	 *  
	 * @return string Return "html" if the email format send it HTML format and "plain_text" if the email send without HTML.
	 */
	function email_format(){
		return get_meta_data('email_format');
	}
	/**
	 * This function is used to define what is the active text editor.
	 * The default value is tinymce
	 *
	 * @since 1.0.0
	 *  
	 * @return string Return the active text editor.
	 */
	function text_editor(){
		return get_meta_data('text_editor');
	}
	
	/**
	 * Get the format size of thumbnail images
	 * ex: 300:200 means 300pixel in width and 200pixel in height.
	 * You can set it at Writing Global Settings
	 * 
	 * @since 1.0.0
	 *  
	 * @return string Return format size of the thumbnail image.
	 */
	function thumbnail_image_size(){
		return get_meta_data('thumbnail_image_size');
	}
	/**
	 * Split the size format of thumbnail images and get the height value
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return height of thumbnail.
	 */
	function thumbnail_image_height(){
		$image_height=array();
		$image_height=explode(":",thumbnail_image_size());
		if(count($image_height)!=2)
			return 150;
		
		return $image_height[1];
	}
	/**
	 * Split the size format of thumbnail images and get the width value
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return width of thumbnail.
	 */
	function thumbnail_image_width(){
		$image_width=array();
		$image_width=explode(":",thumbnail_image_size());
		
		if(count($image_width)!=2)
			return 150;
		
		return $image_width[0];
	}
	/**
	 * Get the format size of medium images
	 * ex: 500:300 means 500pixel in width and 300pixel in height.
	 * You can set it at Writing Global Settings
	 * 
	 * @since 1.0.0
	 *  
	 * @return string Return format size of the medium image.
	 */
	function medium_image_size(){
		return get_meta_data('medium_image_size');
	}
	/**
	 * Split the size format of medium images and get the height value
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return height of medium images.
	 */
	function medium_image_height(){
		$image_height=array();
		$image_height=explode(":",medium_image_size());
		if(count($image_height)!=2)
			return 300;
		return $image_height[1];
	}
	
	/**
	 * Split the size format of medium images and get the width value
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return width of medium image.
	 */
	function medium_image_width(){
		$image_width=array();
		$image_width=explode(":",medium_image_size());
		if(count($image_width)!=2)
			return 300;
		return $image_width[0];
	}
	/**
	 * is_preview() function is used to get the GET variable that is passed in browser and check the $_GET['preview'] variable.
	 * If the $_GET['preview']==true it means the URL is for preview only
	 * 
	 * @since 1.0.0
	 *  
	 * @return boolean Return true if $_GET['preview']=true and false if $_GET['preview']=false.
	 */
	function is_preview(){
		$preview=false;
		if(!empty($_GET['preview'])){
			$preview=true;
		}
		return $preview;
	}
	
	/**
	 * Get the format size of large images
	 * ex: 1024:800 means 1024pixel in width and 800pixel in height.
	 * You can set it at Writing Global Settings
	 * 
	 * @since 1.0.0
	 *  
	 * @return string Return format size of the large image.
	 */
	function large_image_size(){
		return get_meta_data('large_image_size');
	}
	/**
	 * Split the size format of large images and get the height value
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return height of large image.
	 */
	
	function large_image_height(){
		$image_height=array();
		$image_height=explode(":",large_image_size());
		if(count($image_height)!=2)
			return 1024;
		
		return $image_height[1];
	}
	/**
	 * Split the size format of large images and get the width value
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return width of large image.
	 */
	
	function large_image_width(){
		$image_width=array();
		$image_width=explode(":",large_image_size());
		if(count($image_width)!=2)
			return 1024;
		
		return $image_width[0];
	}
	/**
	 * This function is used to get the default value of comment status in each post.
	 * The value is set at Comment Global Settings. When you set this value to "1", it mean when you creating a post/article, 
	 * the default comment status on that post will be allowed. But you still can modified it at your post then.
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return "true" if comment are allowed and "false" if not allowed.
	 */
	function is_allow_comment(){
		return (get_meta_data('is_allow_comment')==1)?true:false;
	}
	/**
	 * When you set people must login to comment, it mean they will need to sign up as a member first.
	 * Without beeing a member, they can't commenting on your post. But if don't tick this option at Comment General Settings,
	 * before they send the comment, they have to filling their name, email, website address. 
	 * 
	 * After they fill a complete data, they will receve an email that tell us to register as a member to approval.
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return "true" if comment are allowed and "false" if not allowed.
	 */
	function is_login_to_comment(){
		return (get_meta_data('is_login_to_comment')==1)?true:false;
	}
	/**
	 * Get the auto close comment status in each post.
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return "true" if comment are auto closed and "false" if not auto closed.
	 */
	function is_auto_close_comment(){
		return (get_meta_data('is_auto_close_comment')==1)?true:false;
	}
	/**
	 * If auto close is set to "true" then you can used this function 
	 * to get how many days after the post created the comment will be closed
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return number of days.
	 */
	function days_auto_close_comment(){
		return get_meta_data('days_auto_close_comment');
	}
	/**
	 * is_break_comment() is used to get the status of viewing the comment summary at the post / article list. 
	 * The post list could be in home page, category section, tag section or search. 
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return "true" if yes and "false" if no.
	 */
	function is_break_comment(){
		return (get_meta_data('is_break_comment')==1)?true:false;
	}
	/**
	 * If is_break_comment() value is "1", then you will need this function to get how many comment should display
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return number of comment.
	 */
	function comment_per_page(){
		return get_meta_data('comment_per_page');
	}
	/**
	 * If is_break_comment() value is "1", then you will need this function to get how will the comments summary displayed 
	 * Is it from the last comments or the first comments
	 * 
	 * @since 1.0.0
	 *  
	 * @return string Return "first" if the comment summary will be displayed from the first comments and "last" if comment summary displayed from latest comments.
	 */
	function comment_page_displayed(){
		return get_meta_data('comment_page_displayed');
	}
	/**
	 * This function is used to get the value of the like status on the posts.
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return true if people can like posts and false if it is not allowed.
	 */
	function is_allow_post_like(){
		return (get_meta_data('is_allow_post_like')==1)?true:false;
	}
	/**
	 * This function is used to get the value of the like status on the comments.
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return true if people can like comments and false if it is not allowed.
	 */
	function is_allow_comment_like(){
		return (get_meta_data('is_allow_comment_like')==1)?true:false;
	}
	/**
	 * This function is used to get the value of sending an alert when people register as a new member.
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return true if alert is send false if it is not send.
	 */
	function alert_on_register(){
		return (get_meta_data('alert_on_register')==1)?true:false;
	}
	/**
	 * This function is used to get the value of sending an alert when people commenting on a new post.
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return true if alert is send false if it is not send.
	 */
	function alert_on_comment(){
		return (get_meta_data('alert_on_comment')==1)?true:false;
	}
	/**
	 * This function is used to get the value of sending an alert when people comment on a post that you commented.
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return true if alert is send false if it is not send.
	 */
	function alert_on_comment_reply(){
		return (get_meta_data('alert_on_comment_reply')==1)?true:false;
	}
	/**
	 * This function is used to get the value of sending an alert when people comment on a post that you liked.
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return true if alert is send false if it is not send.
	 */
	function alert_on_liked_post(){
		return (get_meta_data('alert_on_liked_post')==1)?true:false;
	}
	/**
	 * This function is used to get the value of sending an alert when people comment on a comment that you liked.
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return integer Return true if alert is send false if it is not send.
	 */
	function alert_on_liked_comment(){
		return (get_meta_data('alert_on_liked_comment')==1)?true:false;
	}
	/**
	 * Define the current url in the browser when you accessing the website
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return string Return current browser page address.
	 */
	function cur_pageURL() {
		 $page_URL = 'http';
		 if (!empty($_SERVER["HTTPS"]))
		 	$page_URL .= "s";
			
		 	$page_URL .= "://";
		 if ($_SERVER["SERVER_PORT"] != "80")
		  $page_URL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		 else
		  $page_URL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		 
		 return $page_URL;
	}
	/**
	 * Get the URL of the mentioned tab
	 * 
	 * 
	 * @since 1.0.0
	 * @param string $tab Tab name 
	 * @return string Return tab URL.
	 */
	function get_tab_url($tab){
		 $page_URL = 'http';
		
		 if (!empty($_SERVER["HTTPS"]))
		 	$page_URL .= "s";
			
		 $page_URL .= "://";
		 if ($_SERVER["SERVER_PORT"] != "80"){
			$sub_req=str_replace($_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"],"", SITE_URL);
			if(isset($_GET['sub']))
				$page_URL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$sub_req.'/lumonata-admin/?state='.$_GET['state'].'&sub='.$_GET['sub'].'&tab='.$tab;
			elseif(isset($_GET['state']))
				$page_URL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$sub_req.'/lumonata-admin/?state='.$_GET['state'].'&tab='.$tab;
			else
				$page_URL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$sub_req.'/lumonata-admin/?tab='.$tab;
		 }else{
			$sub_req=str_replace($_SERVER["SERVER_NAME"],"", SITE_URL); 
			if(isset($_GET['sub']))
				$page_URL .= $_SERVER["SERVER_NAME"].$sub_req.'/lumonata-admin/?state='.$_GET['state'].'&sub='.$_GET['sub'].'&tab='.$tab;
			elseif(isset($_GET['state']))
				$page_URL .= $_SERVER["SERVER_NAME"].$sub_req.'/lumonata-admin/?state='.$_GET['state'].'&tab='.$tab;
			else
				$page_URL .= $_SERVER["SERVER_NAME"].$sub_req.'/lumonata-admin/?tab='.$tab;
		 }
		 
		 return $page_URL;
	}
	/**
	 * Get the URL of the mentioned state
	 * 
	 * 
	 * @since 1.0.0
	 * @param string $state State name 
	 * @return string Return state URL.
	 */
	function get_state_url($state){
		$page_URL = 'http';
		
		 if (!empty($_SERVER["HTTPS"]))
		 	$page_URL .= "s";
			
		 	$page_URL .= "://";
		 if ($_SERVER["SERVER_PORT"] != "80"){
			$sub_req=str_replace($_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"],"", SITE_URL);
			$page_URL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$sub_req.'/lumonata-admin/?state='.$state;
		 }else{
			$sub_req=str_replace($_SERVER["SERVER_NAME"],"", SITE_URL);  
			$page_URL .= $_SERVER["SERVER_NAME"].$sub_req.'/lumonata-admin/?state='.$state;
		 }
		 
		 return $page_URL;
	}
	/**
	 * Get the URL of the mentioned applications
	 * 
	 * 
	 * @since 1.0.0
	 * @param string $app Applications name 
	 * @return string Return applications URL.
	 */
	function get_application_url($app){
		$page_URL = 'http';
		 if (!empty($_SERVER["HTTPS"]))
		 	$page_URL .= "s";
			
		 	$page_URL .= "://";
		 if ($_SERVER["SERVER_PORT"] != "80"){
			$page_URL .= SITE_URL.":".$_SERVER["SERVER_PORT"].'/lumonata-admin/?state=applications&sub='.$app;
		 }else{
			$page_URL .= SITE_URL.'/lumonata-admin/?state=applications&sub='.$app;
		 }
		 
		 return $page_URL;
	}
	function add_function($tag,$function_name,$args=NULL){
		global $the_function;
		
		if(empty($tag))
			return false;
		elseif($args===NULL)
			$the_function=array('tag'=>$tag,'function_name'=>$function_name,'args'=>NULL);	
		else{
			$args=func_get_args($args);
			$the_function=array('tag'=>$tag,'function_name'=>$function_name,'args'=>$args);
		}	
		
		return $the_function;
	}
	/**
	 * Merge the timezone that is already define in the system with manual timezone
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return array Return timezone that is already merged.
	 */
	function timezone(){
		$zones = timezone_identifiers_list();
		foreach ($zones as $zone)
		{
		    $zone = explode('/', $zone); // 0 => Continent, 1 => City
		   
		    // Only use "friendly" continent names
		    if ($zone[0] == 'Africa' || $zone[0] == 'America' || $zone[0] == 'Antarctica' || $zone[0] == 'Arctic' || $zone[0] == 'Asia' || $zone[0] == 'Atlantic' || $zone[0] == 'Australia' || $zone[0] == 'Europe' || $zone[0] == 'Indian' || $zone[0] == 'Pacific')
		    {       
			if (isset($zone[1]) != '')
			{
			    $locations[$zone[0]][$zone[0]. '/' . $zone[1]] = str_replace('_', ' ', $zone[1]); // Creates array(DateTimeZone => 'Friendly name')
			}
		    }
		}								 
		$manual_offset=array('UTC'=>array('UTC'=>'UTC'),
				'Manual Offsets'=>array('UTC-12'=>'UTC-12',
							'UTC-11.5'=>'UTC-11:30',
							'UTC-11'=>'UTC-11',
							'UTC-10.5'=>'UTC-10:30',
							'UTC-10'=>'UTC-10',
							'UTC-9.5'=>'UTC-9:30',
							'UTC-9'=>'UTC-9',
							'UTC-8.5'=>'UTC-8:30',
							'UTC-8'=>'UTC-8',
							'UTC-7.5'=>'UTC-7:30',
							'UTC-7'=>'UTC-7',
							'UTC-6.5'=>'UTC-6:30',
							'UTC-6'=>'UTC-6',
							'UTC-5.5'=>'UTC-5:30',
							'UTC-5'=>'UTC-5',
							'UTC-4.5'=>'UTC-4:30',
							'UTC-4'=>'UTC-4',
							'UTC-3.5'=>'UTC-3:30',
							'UTC-3'=>'UTC-3',
							'UTC-2.5'=>'UTC-2:30',
							'UTC-2'=>'UTC-2',
							'UTC-1.5'=>'UTC-1:30',
							'UTC-1'=>'UTC-1',
							'UTC-0.5'=>'UTC-0:30',
							'UTC+0'=>'UTC+0',
							'UTC+0.5'=>'UTC+0:30',
							'UTC+1'=>'UTC+1',
							'UTC+1.5'=>'UTC+1:30',
							'UTC+2'=>'UTC+2',
							'UTC+2.5'=>'UTC+2:30',
							'UTC+3'=>'UTC+3',
							'UTC+3.5'=>'UTC+3:30',
							'UTC+4'=>'UTC+4',
							'UTC+4.5'=>'UTC+4:30',
							'UTC+5'=>'UTC+5',
							'UTC+5.5'=>'UTC+5:30',
							'UTC+5.75'=>'UTC+5:45',
							'UTC+6'=>'UTC+6',
							'UTC+6.5'=>'UTC+6:30',
							'UTC+7'=>'UTC+7',
							'UTC+7.5'=>'UTC+7:30',
							'UTC+8'=>'UTC+8',
							'UTC+8.5'=>'UTC+8:30',
							'UTC+8.75'=>'UTC+8:45',
							'UTC+9'=>'UTC+9',
							'UTC+9.5'=>'UTC+9:30',
							'UTC+10'=>'UTC+10',
							'UTC+10.5'=>'UTC+10:30',
							'UTC+11'=>'UTC+11',
							'UTC+11.5'=>'UTC+11:30',
							'UTC+12'=>'UTC+12',
							'UTC+12.75'=>'UTC+12:45',
							'UTC+13'=>'UTC+13',
							'UTC+13.75'=>'UTC+13:45',
							'UTC+14'=>'UTC+14'));
		$locations=array_merge($locations,$manual_offset);
		return $locations;
	}
	/**
	 * Get setuped timezone and display it into select option group
	 * 
	 * 
	 * @since 1.0.0
	 * @param string $selected_timezone Choosen timezone. 
	 * @return string Return select option group with timezone information and also the choosen timezone.
	 */
	function get_timezone($selected_timezone){
		$timezone='';
		foreach(timezone() as $key_group=>$val_group){
			$timezone.="<optgroup label=\"$key_group\">";
			if(is_array($val_group)){
				foreach($val_group as $key=>$val){
					
					if($key==$selected_timezone){
						
						$timezone.="<option value=\"$key\" selected=\"selected\">$val</option>";
					}else{
						$timezone.="<option value=\"$key\" >$val</option>";
					}
				}
			}
			$timezone.="</optgroup>";
		}	
		
		return $timezone;
	}
	
	
	/**
	 * Set system timezone depending on the timezone that selected by the user
	 * 
	 * 
	 * @since 1.0.0
	 *  
	 * @return boolean Return true if the setting process is successful and false if failed.
	 */
	function set_timezone($timezone){
		if(substr($timezone,0,4)=='UTC-' || substr($timezone,0,4)=='UTC+'){
			$offset=str_replace('UTC','',$timezone);
			return set_tz_by_offset($offset);
		}
		
		if(function_exists('date_default_timezone_set')){
			date_default_timezone_set($timezone);
			return true;
		}
		else{
			putenv("TZ=".$timezone);
			return true;
		}
		
		return false;
	}
	/**
	 * If the timezone is using manual offset that will contain UTC- or UTC+ character, then use set_tz_by_offset($offset) function
	 * to set the timezone
	 * 
	 * @since 1.0.0
	 * @param string $offset The manual offset that using UTC- or UTC+ 
	 * @return bolean Return true if the setting process is successful and false if failed.
	 */
	function set_tz_by_offset($offset) {
		$offset = $offset*60*60;
		$abbrarray = timezone_abbreviations_list();
		foreach ($abbrarray as $abbr) {
			//echo $abbr."<br>";
			foreach ($abbr as $city) {
			    //echo $city['offset']." $offset<br>";
				if ($city['offset'] == $offset) { // remember to multiply $offset by -1 if you're getting it from js
				       date_default_timezone_set($city['timezone_id']);
				       return true;
				}
			}
		}
	    date_default_timezone_set("UTC");
	    return false;
	}
	
	/**
	 * This function is used to create a button as the argumentation that you pass through it. The arguments could be:
	 * - button Is used what kind of button that you want to select (save_changes,add_new,edit,delete,publish,upload,cancel,insert,unpublish,save_draft)
	 * - type: Is the button type(submit or button). The default value is submit
	 * - id: The id button attribut in the input HTML tag
	 * - index: If the button is in array, then you can specify it in this index
	 * - label: Is the text of the button that you want to create
	 * - enable: Is the button enable function. if you set enable=false, then the button that you create will be disabled. Default value is true
	 * - display: If you set display=false it mean that the button that you created will not be displayed until you set it true. Default value is true
	 * - name: Is the name of the button that you want to create
	 * - link: is the spesific link that you want to add on the button when people click it.
	 * When you want to pass the arguments you need to define which arguments is usefull. 
	 * If you need to call Edit button with the default settings just pass "button=edit" on $args, 
	 * But If you want to change the edit button label you can pass the $args like this "button=edit&label=New Label"
	 * 
	 * @uses button("button=edit&type=button&label=New Label",$link='');
	 * 
	 * @since 1.0.0
	 * @param string $args Argumentation to select what type of button and also the label of the button
	 * @param string $link If the button has a spesific link when people click it.
	 *   
	 * @return string Return the HTML tag of button that you define.
	 */
	function button($args='',$link=''){
			
		$var_name['button']='';
		$var_name['type']='submit';
		$var_name['id']='';
		$var_name['index']='';
		$var_name['label']='';
		$var_name['enable']='true';
		$var_name['display']='true';
		$var_name['link']=$link;
		$var_name['name']='';
		$id='';
		
		if(!empty($args)){
		    $args=explode('&',$args);
		    foreach($args as $val){
			list($variable,$value)=explode('=',$val);
			if($variable=='button' || $variable=='type' || $variable=='id' || $variable=='index' || $variable=='label' || $variable=='enable' || $variable=='display' || $variable=='link' || $variable=='name')
			$var_name[$variable]=$value;
			
		    }
		}
		
		
		if($var_name['display']=='false' || empty($var_name['button']))
			return;
		
		switch($var_name['button']){
			case "save_changes":
				
				if(!empty($var_name['name']))
					$name="name=\"".$var_name['name']."\"";
				else
					$name="name=\"save_changes\"";
				
				if(empty($var_name['label']))
					$var_name['label']="Save Changes";
					
				if(!empty($var_name['id']))
					$id="id=\"".$var_name['id']."\"";
				
					
				if($var_name['enable']=='true')
					return "<input type=\"".$var_name['type']."\" class=\"btn_save_changes_enable\" $name $id value=\"".$var_name['label']."\" />";
				else
					return "<input type=\"".$var_name['type']."\" class=\"btn_save_changes_disable\" $name $id value=\"".$var_name['label']."\" disabled=\"disabled\" />";
				break;
			case "add_new":
				if(!empty($var_name['name']))
					$name="name=\"".$var_name['name']."\"";
				else
					$name="name=\"add_new\"";
					
				if(empty($var_name['label']))
					$var_name['label']="Add New";
					
				if(!empty($var_name['id']))
					$id="id=\"".$var_name['id']."\"";
				
					
				if(!empty($var_name['link'])){
					if($var_name['enable']=='true')
						return "<input type=\"button\" class=\"btn_add_new_enable\" $name $id value=\"".$var_name['label']."\" onclick=\"location='".$var_name['link']."';\" />";
					else
						return "<input type=\"button\" class=\"btn_add_new_disable\" $name $id value=\"".$var_name['label']."\" />";
				}
				
				if($var_name['enable']=='true')
					return "<input type=\"".$var_name['type']."\" class=\"btn_add_new_enable\" $name $id value=\"".$var_name['label']."\" >";
				else
					return "<input type=\"".$var_name['type']."\" class=\"btn_add_new_disable\" $name $id  value=\"".$var_name['label']."\" disabled=\"disabled\" />";
				break;
			case "edit":
				
				if(!empty($var_name['name']))
					$name="name=\"".$var_name['name']."\"";
				else
					$name="name=\"edit\"";
					
				if(empty($var_name['label']))
					$var_name['label']="Edit";
					
				if(!empty($var_name['id']))
					$id="id=\"".$var_name['id']."\"";
				
					
				if($var_name['enable']=='true')
					return "<input type=\"".$var_name['type']."\" class=\"btn_edit_enable\" $name $id  value=\"".$var_name['label']."\" />";
				else
					return "<input type=\"".$var_name['type']."\" class=\"btn_edit_disable\" $name $id  value=\"".$var_name['label']."\" disabled=\"disabled\" />";
				
				break;
			case "delete":
				if(!empty($var_name['name']))
					$name="name=\"".$var_name['name']."\"";
				else
					$name="name=\"delete\"";
					
				if(empty($var_name['label']))
					$var_name['label']="Delete";
					
				if(!empty($var_name['id']))
					$id="id=\"".$var_name['id']."\"";
					
				if($var_name['enable']=='true')
					return "<input type=\"".$var_name['type']."\" class=\"btn_delete_enable\" $name $id value=\"".$var_name['label']."\" />";
				else
					return "<input type=\"".$var_name['type']."\" class=\"btn_delete_disable\" $name $id  value=\"".$var_name['label']."\" disabled=\"disabled\" />";
				break;
			
			case "publish":
				if(!empty($var_name['name']))
					$name="name=\"".$var_name['name']."\"";
				else
					$name="name=\"publish\"";
					
				if(empty($var_name['label']))
					$var_name['label']="Publish";
					
				if(!empty($var_name['id']))
					$id="id=\"".$var_name['id']."\"";
					
				if($var_name['enable']=='true')
					return "<input type=\"".$var_name['type']."\" class=\"btn_publish_enable\" $name $id  value=\"".$var_name['label']."\" />";
				else
					return "<input type=\"".$var_name['type']."\" class=\"btn_publish_disable\" $name $id  value=\"".$var_name['label']."\" disabled=\"disabled\" />";
				break;
			case "approved":
				if(!empty($var_name['name']))
					$name="name=\"".$var_name['name']."\"";
				else
					$name="name=\"approved\"";
					
				if(empty($var_name['label']))
					$var_name['label']="Approved";
					
				if(!empty($var_name['id']))
					$id="id=\"".$var_name['id']."\"";
					
				if($var_name['enable']=='true')
					return "<input type=\"".$var_name['type']."\" class=\"btn_publish_enable\" $name $id  value=\"".$var_name['label']."\" />";
				else
					return "<input type=\"".$var_name['type']."\" class=\"btn_publish_disable\" $name $id  value=\"".$var_name['label']."\" disabled=\"disabled\" />";
				break;
			case "upload":
				if(!empty($var_name['name']))
					$name="name=\"".$var_name['name']."\"";
				else
					$name="name=\"upload\"";
					
				if(empty($var_name['label']))
					$var_name['label']="Upload";
					
				if(!empty($var_name['id']))
					$id="id=\"".$var_name['id']."\"";
					
				if($var_name['enable']=='true')
					return "<input type=\"".$var_name['type']."\" class=\"btn_publish_enable\" $name $id  value=\"".$var_name['label']."\" >";
				else
					return "<input type=\"".$var_name['type']."\" class=\"btn_publish_disable\" $name $id  value=\"".$var_name['label']."\" disabled=\"disabled\" />";
				break;
			case "cancel":
				if(!empty($var_name['name']))
					$name="name=\"".$var_name['name']."\"";
				else
					$name="name=\"cancel\"";
					
				if(empty($var_name['label']))
					$var_name['label']="Cancel";
					
				if(!empty($var_name['id']))
					$id="id=\"".$var_name['id']."\"";
				
				if(!empty($var_name['link'])){
					if($var_name['enable']=='true')
						return "<input type=\"button\" class=\"btn_cancel_enable\" $name $id value=\"".$var_name['label']."\" onclick=\"location='".$var_name['link']."';\" />";
					else
						return "<input type=\"button\" class=\"btn_cancel_disable\" $name $id value=\"".$var_name['label']."\" />";
				}
				if($var_name['enable']=='true')
					return "<input type=\"".$var_name['type']."\" class=\"btn_cancel_enable\" $name $id  value=\"".$var_name['label']."\" />";
				else
					return "<input type=\"".$var_name['type']."\" class=\"btn_cancel_disable\" $name $id  value=\"".$var_name['label']."\" disabled=\"disabled\" />";
				break;
			case "insert":
				
				if(!empty($var_name['name']))
					$name="name=\"".$var_name['name']."\"";
				else
					$name="name=\"insert\"";
					
				if(empty($var_name['label']))
					$var_name['label']="Insert Into Article";
					
				if(!empty($var_name['id']))
					$id="id=\"".$var_name['id']."\"";
					
				if($var_name['enable']=='true')
					return "<input type=\"".$var_name['type']."\" class=\"btn_save_changes_enable\" $name $id  value=\"".$var_name['label']."\" />";
				else
					return "<input type=\"".$var_name['type']."\" class=\"btn_save_changes_disable\" $name $id  value=\"".$var_name['label']."\" disabled=\"disabled\" />";
				break;
			case "unpublish":
				if(!empty($var_name['name']))
					$name="name=\"".$var_name['name']."\"";
				else
					$name="name=\"unpublish\"";
					
				if(empty($var_name['label']))
					$var_name['label']="Unpublish";
					
				if(!empty($var_name['id']))
					$id="id=\"".$var_name['id']."\"";
					
				if($var_name['enable']=='true')
					return "<input type=\"".$var_name['type']."\" class=\"btn_save_changes_enable\" $name $id  value=\"".$var_name['label']."\" />";
				else
					return "<input type=\"".$var_name['type']."\" class=\"btn_save_changes_disable\" $name $id  value=\"".$var_name['label']."\" disabled=\"disabled\" />";
				break;
			case "save_draft":
				if(!empty($var_name['name']))
					$name="name=\"".$var_name['name']."\"";
				else
					$name="name=\"save_draft\"";
					
				if(empty($var_name['label']))
					$var_name['label']="Save Draft";
					
				if(!empty($var_name['id']))
					$id="id=\"".$var_name['id']."\"";
					
				if($var_name['enable']=='true')
					return "<input type=\"".$var_name['type']."\" class=\"btn_save_changes_enable\" $name $id  value=\"".$var_name['label']."\" />";
				else
					return "<input type=\"".$var_name['type']."\" class=\"btn_save_changes_enable\" $name $id  value=\"".$var_name['label']."\" disabled=\"disabled\">";
				break;
			
				
		}
	}
	/**
	 * Return the Save Changes Button
	 * 
	 * @since 1.0.0
	 * @param boolean $enable True if the button is enable and false if disabled
	 * @param boolean $display  True if the button is displayed and false if hidden
	 * @return string Return HTML tag of the Save Changes Input Button.
	 */
	function save_changes_botton($enable=true,$display=true){
		if($display==false)
			return;
		if($enable==true)
			return "<input type=\"submit\" class=\"btn_save_changes_enable\" name=\"save_changes\" value=\"Save Changes\" />";
		else
			return "<input type=\"button\" class=\"btn_save_changes_disable\" name=\"save_changes\" value=\"Save Changes\" disabled=\"disabled\" />";
	}
	/**
	 * Return the Add New Button
	 * 
	 * @since 1.0.0
	 * @param boolean $enable True if the button is enable and false if disabled
	 * @param boolean $display  True if the button is displayed and false if hidden
	 * @return string Return HTML tag of the Add New Input Button.
	 */
	function add_new_button($enable=true,$display=true){
		if($display==false)
			return;
		if($enable==true)
			return "<input type=\"submit\" class=\"btn_add_new_enable\" name=\"add_new\" value=\"Add New\" />";
		else
			return "<input type=\"button\" class=\"btn_add_new_disable\" name=\"add_new\"  value=\"Add New\" disabled=\"disabled\" />";
	}
	/**
	 * Return the Edit Button
	 * 
	 * @since 1.0.0
	 * @param boolean $enable True if the button is enable and false if disabled
	 * @param boolean $display  True if the button is displayed and false if hidden
	 * @return string Return HTML tag of the Edit Input Button.
	 */
	function edit_button($enable=true,$display=true){
		if($display==false)
			return;
		if($enable==true)
			return "<input type=\"submit\" class=\"btn_edit_enable\" name=\"edit\"  value=\"Edit\" />";
		else
			return "<input type=\"button\" class=\"btn_edit_disable\" name=\"edit\"  value=\"Edit\" disabled=\"disabled\" />";
	}
	/**
	 * Return the Delete Button
	 * 
	 * @since 1.0.0
	 * @param integer $id If the button is array, you can use the index here
	 * @param boolean $enable True if the button is enable and false if disabled
	 * @param boolean $display  True if the button is displayed and false if hidden
	 * @return string Return HTML tag of the Delete Input Button.
	 */
	function delete_button($id=0,$enable=true,$display=true){
		if($display==false)
			return;
		if($enable==true)
			return "<input type=\"button\" class=\"btn_delete_enable\" name=\"delete[$id]\"  value=\"Delete\" />";
		else
			return "<input type=\"button\" class=\"btn_delete_disable\" name=\"delete[$id]\"  value=\"Delete\" disabled=\"disabled\" />";
	}
	/**
	 * Return the Publish Button
	 * 
	 * @since 1.0.0
	 * @param boolean $enable True if the button is enable and false if disabled
	 * @param boolean $display  True if the button is displayed and false if hidden
	 * @return string Return HTML tag of the Publish Input Button.
	 */
	function publish_button($enable=true,$display=true){
		if($display==false)
			return;
		if($enable==true)
			return "<input type=\"submit\" class=\"btn_publish_enable\" name=\"publish\"  value=\"Publish\" />";
		else
			return "<input type=\"button\" class=\"btn_publish_disable\" name=\"publish\"  value=\"Publish\" disabled=\"disabled\" />";
	}
	/**
	 * Return the Upload Button
	 * 
	 * @since 1.0.0
	 * @param boolean $enable True if the button is enable and false if disabled
	 * @param boolean $display  True if the button is displayed and false if hidden
	 * @return string Return HTML tag of the Upload Input Button.
	 */
	function upload_button($enable=true,$display=true){
		if($display==false)
			return;
		if($enable==true)
			return "<input type=\"submit\" class=\"btn_publish_enable\" name=\"upload\"  value=\"Upload\" />";
		else
			return "<input type=\"button\" class=\"btn_publish_disable\" name=\"upload\"  value=\"Upload\" disabled=\"disabled\" />";
	}
	/**
	 * Return the Cancel Button
	 * 
	 * @since 1.0.0
	 * @param boolean $enable True if the button is enable and false if disabled
	 * @param boolean $display  True if the button is displayed and false if hidden
	 * @return string Return HTML tag of the Cancel Input Button.
	 */
	function cancel_button($enable=true,$display=true){
		if($display==false)
			return;
		if($enable==true)
			return "<input type=\"button\" class=\"btn_cancel_enable\" name=\"cancel\"  value=\"Cancel\" />";
		else
			return "<input type=\"button\" class=\"btn_cancel_disable\" name=\"cancel\"  value=\"Cancel\" disabled=\"disabled\" />";
	}
	/**
	 * Return the Insert Button
	 * 
	 * @since 1.0.0
	 * @param boolean $enable True if the button is enable and false if disabled
	 * @param boolean $display  True if the button is displayed and false if hidden
	 * @return string Return HTML tag of the Insert Input Button.
	 */
	function insert_button($enable=true,$display=true){
		if($display==false)
			return;
		if($enable==true)
			return "<input type=\"submit\" class=\"btn_save_changes_enable\" name=\"insert\"  value=\"Insert\" />";
		else
			return "<input type=\"button\" class=\"btn_save_changes_disable\" name=\"insert\"  value=\"Insert\" disabled=\"disabled\" />";
	}
	/**
	 * Return the Unpublish Button
	 * 
	 * @since 1.0.0
	 * @param boolean $enable True if the button is enable and false if disabled
	 * @param boolean $display  True if the button is displayed and false if hidden
	 * @return string Return HTML tag of the Unpublish Input Button.
	 */
	function unpublish_button($enable=true,$display=true){
		if($display==false)
			return;
		if($enable==true)
			return "<input type=\"submit\" class=\"btn_save_changes_enable\" name=\"unpublish\"  value=\"Unpublish\" />";
		else
			return "<input type=\"button\" class=\"btn_save_changes_disable\" name=\"unpublish\"  value=\"Unpublish\" disabled=\"disabled\" />";
	}
	/**
	 * Return the Save Draft Button
	 * 
	 * @since 1.0.0
	 * @param boolean $enable True if the button is enable and false if disabled
	 * @param boolean $display  True if the button is displayed and false if hidden
	 * @return string Return HTML tag of the Save Draft Input Button.
	 */
	function save_draft_button($enable=true,$display=true){
		if($display==false)
			return;
		if($enable==true)
			return "<input type=\"submit\" class=\"btn_save_changes_enable\" name=\"save_draft\"  value=\"Save Draft\" />";
		else
			return "<input type=\"button\" class=\"btn_save_changes_disable\" name=\"save_draft\"  value=\"Save Draft\" disabled=\"disabled\" />";
	}
	/**
	 * Before deleteing data, you will need a pop up box that ask you confirm the deletion. Use this function to show the pop up box
	 * 
	 * @since 1.0.0
	 * @param integer $id Is the post_id
	 * @param string $msg Is the Message that will shown on the pop up box
	 * @param string $url Is the location of file that execute the deletion process
	 * @param string $close_frameid Is the id of the deleted data that shown in the list.
	 * @param string $var Parameter that you send to the PHP and then will be execute in the deletion process when user click Yes.
	 * @param string $var_no Parameter that you send to the PHP and then will be execute in the deletion process when user click No.
	 * @return string Return deletion popup box.
	 */
	function delete_confirmation_box($id,$msg,$url,$close_frameid,$var='',$var_no=''){
		if(empty($var))
			$var="confirm_delete=yes&delete_id=".$id;
		elseif($var=='url')
			$var='';
		else
			$var=$var;
			
		$delbox="<div id=\"delete_confirmation_wrapper_$id\" style=\"display:none;\">";
			$delbox.="<div class=\"fade\"></div>";
			$delbox.="<div class=\"popup_block\">";
				$delbox.="<div class=\"popup\">";
					$delbox.="<div class=\"alert_yellow\">$msg</div>";
					$delbox.="<div style=\"text-align:right;margin:10px 5px 0 0;\">";
						$delbox.="<button type=\"submit\" name=\"confirm_delete\" value=\"yes\" class=\"button\" id=\"delete_yes_".$id."\">Yes</button>";
						$delbox.="<button type=\"button\" name=\"confirm_delete\" value=\"no\" class=\"button\" id=\"delete_no_".$id."\">No</button>";
						$delbox.="<button type=\"button\" name=\"confirm_delete\" value=\"cancel\" class=\"button\" id=\"cancel_".$id."\">Cancel</button>";
						$delbox.="<input type=\"hidden\" name=\"delete_id\" value=\"$id\" />";
					$delbox.="</div>";
				$delbox.="</div>";
			$delbox.="</div>";
		$delbox.="</div>";
		
		
		$delbox.="<script type=\"text/javascript\">";
		$delbox.="$(function(){
						$('input[id=delete_".$id."]').click(function(){
							$('#delete_confirmation_wrapper_".$id."').show('fast');
							
						});
					});
			
					$(function(){
						$('a[rel=delete_".$id."]').click(function(){
							$('select').hide();
							theWidth=document.body.clientWidth;
							theHeight=document.body.clientHeight;
							$('.fade').css('width',theWidth);
							$('.fade').css('height',theHeight);
							$('#delete_confirmation_wrapper_".$id."').show('fast');

						});
					});
					
					$(function(){
						$('#delete_".$id."').click(function(){
							$('select').hide();
							theWidth=document.body.clientWidth;
							theHeight=document.body.clientHeight;
							$('.fade').css('width',theWidth);
							$('.fade').css('height',theHeight);
							$('#delete_confirmation_wrapper_".$id."').show('fast');

						});
					});
					
					$(function(){
						$('#cancel_".$id."').click(function(){
							$('select').show();
						    $('#delete_confirmation_wrapper_".$id."').hide('fast');
						    
						});
					});
			";
			
		if(empty($var_no)){	
			$delbox.="$(function(){
					$('#delete_no_".$id."').click(function(){
						$('select').show();
					    $('#delete_confirmation_wrapper_".$id."').hide('fast');
					});
				});";
		}else{
			$delbox.="$(function(){
					$('#delete_no_".$id."').click(function(){
						$('select').show();
						$.post('".$url."', '".$var_no."', function(theResponse){
							$('#response').html(theResponse);
						});
					    $('#delete_confirmation_wrapper_".$id."').hide('fast');
					    $('#".$close_frameid."').css('background','#FF6666');
					    $('#".$close_frameid."').delay(500);
					    $('#".$close_frameid."').fadeOut(700);
					    return false;
					});
				});";
		}
		$delbox.="$(function(){
				$('#delete_yes_".$id."').click(function(){
					$('select').show();
				    $.post('".$url."', '".$var."', function(theResponse){
						$('#response').html(theResponse);
					});
				    $('#delete_confirmation_wrapper_".$id."').hide('fast');
				    $('#".$close_frameid."').css('background','#FF6666');
				    $('#".$close_frameid."').delay(500);
				    $('#".$close_frameid."').fadeOut(700);
				    setTimeout(
				    	function(){
				  			location.reload(true);
                    	}, 1500);
                    	
				    return false;
				});
			    });
		";
		$delbox.="</script>";
		
		return $delbox;
	}
	/**
	 * This function is used to setup the TinyMCE text editor
	 * 
	 * @since 1.0.0
	 * @param string $name The name of the text editor
	 * @param string $id ID atrribut of the text editor
	 * @param string $value The value or content of the text editor
	 * @param integer $article_id The ID of the article that use current text editor 
	 * @param boolean $show_media Set false to remove the upload media button from textarea
	 
	 * @return string Return text editor.
	 */
	function textarea($name,$id,$value='',$article_id=0,$show_media=true){
		if($article_id==0){
			$post_id=time();
		}else{
			$post_id=$article_id;
		}
		$tinyMCE="<div class=\"textarea_button clearfix\">";
			$tinyMCE.="<div class=\"button_area\">";
				if($show_media){
					$tinyMCE.="<a href=\"".get_admin_url()."/upload-media.php?tab=from-computer&type=image&textarea_id=$id&post_id=".$post_id."\" title=\"Upload / Insert Image\" alt=\"Upload / Insert Image\"  class=\"upload_image\">&nbsp;</a>";
					$tinyMCE.="<a href=\"".get_admin_url()."/upload-media.php?tab=from-computer&type=flash&textarea_id=$id&post_id=".$post_id."\" class=\"upload_flash\" title=\"Upload / Insert Flash\" alt=\"Upload / Insert Flash\" >&nbsp;</a>";
					$tinyMCE.="<a href=\"".get_admin_url()."/upload-media.php?tab=from-computer&type=video&textarea_id=$id&post_id=".$post_id."\" class=\"upload_video\" title=\"Upload / Insert Video\" alt=\"Upload / Insert Video\">&nbsp;</a>";
					$tinyMCE.="<a href=\"".get_admin_url()."/upload-media.php?tab=from-computer&type=music&textarea_id=$id&post_id=".$post_id."\" class=\"upload_music\" title=\"Upload / Insert Music\" alt=\"Upload / Insert Music\">&nbsp;</a>";
					$tinyMCE.="<a href=\"".get_admin_url()."/upload-media.php?tab=from-computer&type=pdf&textarea_id=$id&post_id=".$post_id."\" class=\"upload_pdf\" title=\"Upload / Insert PDF\" alt=\"Upload / Insert PDF\">&nbsp;</a>";
					$tinyMCE.="<a href=\"".get_admin_url()."/upload-media.php?tab=from-computer&type=doc&textarea_id=$id&post_id=".$post_id."\" class=\"upload_doc\" title=\"Upload / Insert Document\" alt=\"Upload / Insert Document\">&nbsp;</a>";
				}
			$tinyMCE.="</div>";
		$tinyMCE.="<div class=\"visual_view\">";
		if(get_meta_data('text_editor')=='tiny_mce'){
			/*
			$tinyMCE.="<a href=\"javascript:;\" class=\"visual_view_button\" id=\"visual_view_$id\" onmousedown=\"$('#textarea_$id').tinymce().show();\" >Visual View</a>";
			$tinyMCE.="<a href=\"javascript:;\" class=\"html_view_button\" id=\"html_view_$id\"  onmousedown=\"$('#textarea_$id').tinymce().hide();\" >HTML View</a>";
			*/
			$tinyMCE.="<input type=\"button\"  class=\"visual_view_button\" id=\"visual_view_$id\" onclick=\"$('#textarea_$id').tinymce().show();\" value=\"Visual View\" />";
			$tinyMCE.="<input type=\"button\"  class=\"html_view_button\" id=\"html_view_$id\"  onclick=\"$('#textarea_$id').tinymce().hide();\" value=\"HTML View\" />";
		}
		$tinyMCE.="</div>";
		$tinyMCE.="</div>";
		
		if(get_meta_data('text_editor')=='tiny_mce')
		$tinyMCE.="<textarea id=\"textarea_$id\" name=\"$name\" cols=\"95\" rows=\"20\" class=\"tinymce\">".$value."</textarea>";
		else
		$tinyMCE.="<textarea id=\"textarea_$id\" name=\"$name\" cols=\"95\" rows=\"20\" >".$value."</textarea>";
		$tinyMCE.="<input type=\"hidden\" name=\"post_id[$id]\" value=\"".$post_id."\">";	
		
		
		return $tinyMCE;
	}
	/**
	 * This function is used to setup the Search Box on the Administrator Area
	 * 
	 * @since 1.0.0
	 * @param string $keyup_action The file location that you will execute the search process
	 * @param string $results_id ID atrribut where you will display the search results
	 * @param string $param Paramater that you may add in search process. So you can specify another varibale other than keywords
	 * @param string $pos Float position of the search box
	 * @param string $class Is the attribut class name when the search process is execute.
	 * 
	 * @return string Return HTML Search Box.
	 */
	function search_box($keyup_action='',$results_id='',$param='',$pos='left',$class='alert_green',$text='Search'){
		$searchbox="<div class=\"search_box clearfix\" style=\"float:$pos;\">
				<div class=\"textwrap\">
				    <input type=\"text\" name=\"s\" class=\"searchtext\" value=\"".$text."\" />
				</div>
				<div class=\"buttonwrap\">
				    <input type=\"image\" src=\"". get_theme_img() ."/ico-search.png\" name=\"search\" class=\"searchbutton\" value=\"yes\" />
				</div>
                            </div>";
			    
		if(!empty($keyup_action)){
			$searchbox.="<script type=\"text/javascript\">
				$(function(){
					
					$('.searchtext').keyup(function(){
						
						$('#response').html('<div class=".$class.">Searching...</div>');
						var s = $('input[name=s]').val();
						var parameter='".$param."s='+s;
						
						$.post('".$keyup_action."',parameter,function(data){
							 $('#".$results_id."').html(data);
							 //$('#response').delay(100);
							 //$('#response').slideUp();
						});
						$('#response').html('');
						
					});
					
					
				});
				
				$(function(){
					$('.searchtext').focus(function(){
						$('.searchtext').val('');
					});
				});
				$(function(){
					$('.searchtext').blur(function(){
						$('.searchtext').val($(this).val()==''?'Search':$(this).val());
					});
				});
				</script>";
		}	    
		return $searchbox;
	}
	
	/**
	 * Function to validate integer
	 * 
	 * @since 1.0.0
	 * @param string $str String to check
	 * 
	 * @return string Return true if it is Integer and false if it is not.
	 */
	function isInteger($str) {
		return preg_match("/^-?([0-9])+$/", $str);
	}

	/**
	 * Function to validate float
	 * 
	 * @since 1.0.0
	 * @param string $str String to check
	 * 
	 * @return string Return true if it is float and false if it is not.
	 */
	function isFloat($str) {
		return preg_match("/^-?([0-9])+([\.|,]([0-9])*)?$/", $str);
	}
	
	
	/**
	 * Function to validate alphabetic strings
	 * 
	 * @since 1.0.0
	 * @param string $str String to check
	 * 
	 * @return string Return true if it is alphabetic and false if it is not.
	 */
	function isAlpha($str) {
		return preg_match("/^[a-z]+$/i", $str);
	}
	
	
	/**
	 * Function to validate alphanumeric strings
	 * 
	 * @since 1.0.0
	 * @param string $str String to check
	 * 
	 * @return string Return true if it is alphanumeric and false if it is not.
	 */
	function isAlphaNum($str) {
		return preg_match("/^[a-z0-9]*$/i", $str);
	}
	
	/**
	 * Function to validate email format
	 * 
	 * @since 1.0.0
	 * @param string $str String to check
	 * 
	 * @return string Return true if the format is OK and false if it is not.
	 */
	function isEmailAddress($str) {
	   
		if(preg_match("/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/", $str)){
			list($username, $domain) = preg_split("/\@/",$str);			
			if(function_exists("getmxrr") && !getmxrr($domain, $MXHost)) {
			  return false;
			}else return true;
		}else return false;
	}
	/**
	 * Function to validate web address
	 * 
	 * @since 1.0.0
	 * @param string $str String to check
	 * 
	 * @return string Return true if the format is OK and false if it is not.
	 */
	function is_website_address($str){
		return preg_match("/^(http|https|ftp):\/\/([a-z0-9\-]+\.)*[a-z0-9][a-z0-9\-]+[a-z0-9](\.[a-z]{2,4})+$/i", $str);
	}
	/**
	 * Function to validate URL format
	 * 
	 * @since 1.0.0
	 * @param string $str String to check
	 * 
	 * @return string Return true if the format is OK and false if it is not.
	 */
	function isUrl($str) {
		return preg_match("/^(http|https|ftp):\/\/([a-z0-9]([a-z0-9_-]*[a-z0-9])?\.)+[a-z]{2,6}\/?([a-z0-9\?\._-~&#=+%]*)?/", $str);
		
	}
	/**
	 * Function to count rows from spesific Query 
	 * 
	 * @since 1.0.0
	 * @param string $sql Sql query that will be execute
	 * 
	 * @return string Return the number of rows.
	 */
	function count_rows($sql){
		global $db;
		$r=$db->do_query($sql);
		return $db->num_rows($r);
	}
	/**
	 * Function to update the order id from spesific Query 
	 * 
	 * @since 1.0.0
	 * @param array $order The new data of order
	 * @param string $table_name The table name that will be updated
	 * @param string $key_field the name of the field that used as a key in the table
	 * @param integer $start The order id is start from this value
	 * 
	 * @return boolean Return true if the process run well.
	 */
	function update_order_id($order,$table_name,$key_field,$start){
		global $db;
		foreach($order as $key=>$val){
		    $sql=$db->prepare_query("UPDATE $table_name
					     SET lorder=%d
					     WHERE $key_field=%d",$key+$start,$val);
		    if(!$db->do_query($sql))
			return false;
		}
		
		return true;
	}
	/**
	 * This function is used when you adding a new data on spesific table. 
	 * When you adding new data, you will need to update the other order id and the new data will be sort as the smallest order id
	 * 
	 * @since 1.0.0
	 * @param string $table_name Name of the table that will be execute
	 * 
	 * @return boolean Return true if the process run well.
	 */
	function reset_order_id($table_name){
		global $db;
		$sql=$db->prepare_query("UPDATE $table_name SET lorder=lorder+1");
		return $db->do_query($sql);
		
	}
	/**
	 * This function is used to adding an addtional field for each applications that you created. 
	 * 
	 * @since 1.0.0
	 * @param integer $app_id The ID of your application
	 * @param string $key Key or name of your new additional field
	 * @param string $val The key value
	 * @param string $app_name The name of the application 
	 * 
	 * @return boolean Return true if the process run well.
	 */
	function add_additional_field($app_id,$key,$val,$app_name){
		global $db,$allowedposttags;
		$val=kses(rem_slashes($val),$allowedposttags);
		$sql=$db->prepare_query("INSERT INTO
					lumonata_additional_fields
					VALUES(%d,%s,%s,%s)",
					$app_id,$key,$val,$app_name);
		
		return $db->do_query($sql);
	}
	/**
	 * This function is used to edit existing additional field 
	 * 
	 * @since 1.0.0
	 * @param integer $app_id The ID of your application
	 * @param string $key Key or name of your new additional field
	 * @param string $val The key value
	 * @param string $app_name The name of the application 
	 * 
	 * @return boolean Return true if the process run well.
	 */
	function edit_additional_field($app_id,$key,$val,$app_name){
		global $db,$allowedposttags;
		$val=kses(rem_slashes($val),$allowedposttags);
		$field_val=get_additional_field($app_id,$key,$app_name);
		
		if(count_additional_field($app_id,$key,$app_name)==0){
			return add_additional_field($app_id,$key,$val,$app_name);
		}else{
			$sql=$db->prepare_query("UPDATE
						lumonata_additional_fields
						SET lvalue=%s
						WHERE lapp_id=%d AND lkey=%s AND lapp_name=%s",
						$val,$app_id,$key,$app_name);
			
			return $db->do_query($sql);
		}
	}
	/**
	 * This function is used to get the value of existing additional field 
	 * 
	 * @since 1.0.0
	 * @param integer $app_id The ID of your application
	 * @param string $key Key or name of your new additional field
	 * @param string $app_name The name of the application 
	 * 
	 * @return boolean Return true if the process run well.
	 */
	function get_additional_field($app_id,$key,$app_name){
		global $db;
		$sql=$db->prepare_query("SELECT lvalue
					FROM
					lumonata_additional_fields
					WHERE lapp_id=%d AND lkey=%s AND lapp_name=%s",
					$app_id,$key,$app_name);
		
		$r=$db->do_query($sql);
		$d=$db->fetch_array($r);
		return stripslashes($d['lvalue']);
		
	}
	/**
	 * This function is used to delete the existing additional field 
	 * 
	 * @since 1.0.0
	 * @param integer $app_id The ID of your application
	 * @param string $app_name The name of the application 
	 * 
	 * @return boolean Return true if the process run well.
	 */
	function delete_additional_field($app_id,$app_name){
		global $db;
		$sql=$db->prepare_query("DELETE FROM
					lumonata_additional_fields
					WHERE lapp_id=%d AND lapp_name=%s",
					$app_id,$app_name);
		
		return $r=$db->do_query($sql);
	}
	/**
	 * This function is used to count the number of additional field for spesific application name, ID and key 
	 * 
	 * @since 1.0.0
	 * @param integer $app_id The ID of your application
	 * @param string $key Key or name of your new additional field
	 * @param string $app_name The name of the application 
	 * 
	 * @return integer Return the number of mentioned additional field.
	 */
	function count_additional_field($app_id,$key,$app_name){
		global $db;
		$sql=$db->prepare_query("SELECT lvalue
					FROM
					lumonata_additional_fields
					WHERE lapp_id=%d AND lkey=%s AND lapp_name=%s",
					$app_id,$key,$app_name);
		
		$r=$db->do_query($sql);
		return $db->num_rows($r);
	}
	/**
	 * This function is used to remove the slashes from $_POST or $_GET parameter if get_magic_quotes_gpc is set to ON
	 * 
	 * @since 1.0.0
	 * @param string $string Input string to test and removed
	 * 
	 * @return string Return the new string without quotes.
	 */
	function rem_slashes($string){
		if (get_magic_quotes_gpc()) {
			$string = stripslashes($string);
		}else{
			$string = $string;
		}
		return $string;
	}
	/**
	 * Generate the Search Engine Friendly Name and replace space, other special characthers with "-". 
	 * 
	 * @since 1.0.0
	 * @param string $phrase Input string to test and replaced
	 * 
	 * @return string Return the new Search Engine Friendly name without special characters.
	 */
	function generateSefUrl($phrase){
		$result = str_normalize($phrase);
		$result = strtolower($result);
		$result = preg_replace("/[^a-z0-9\s-]/", "", $result);
		$result = trim(preg_replace("/[\s-]+/", " ", $result));
		//$result = trim(substr($result, 0, $maxLength));
		$result = preg_replace("/\s/", "-", $result);
		return $result;
	}
	/**
	 * Normalize the string from special characthers that usually used in European languages 
	 * 
	 * @since 1.0.0
	 * @param string $string Input string to test and replaced
	 * 
	 * @return string Return the new Search Engine Friendly name without special characters.
	 */
	function str_normalize ($string) {
		$table = array(
		    '�'=>'S', '�'=>'s', '?'=>'Dj', '?'=>'dj', '�'=>'Z', '�'=>'z', '?'=>'C', '?'=>'c', '?'=>'C', '?'=>'c',
		    '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'C', '�'=>'E', '�'=>'E',
		    '�'=>'E', '�'=>'E', '�'=>'I', '�'=>'I', '�'=>'I', '�'=>'I', '�'=>'N', '�'=>'O', '�'=>'O', '�'=>'O',
		    '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'U', '�'=>'U', '�'=>'U', '�'=>'U', '�'=>'Y', '�'=>'B', '�'=>'Ss',
		    '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'c', '�'=>'e', '�'=>'e',
		    '�'=>'e', '�'=>'e', '�'=>'i', '�'=>'i', '�'=>'i', '�'=>'i', '�'=>'o', '�'=>'n', '�'=>'o', '�'=>'o',
		    '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'u', '�'=>'u', '�'=>'u', '�'=>'y', '�'=>'y', '�'=>'b',
		    '�'=>'y', '?'=>'R', '?'=>'r',
		);
	       
		return strtr($string, $table);
	}
	/**
	 * Validate the IP address 
	 * 
	 * @since 1.0.0
	 * @param string $ip IP Address
	 * 
	 * @return boolean Return true if valid and false if invalid
	 */
    function validip($ip) {
       	if (!empty($ip) && ip2long($ip)!=-1) {
           $reserved_ips = array (
               array('0.0.0.0','2.255.255.255'),
               array('10.0.0.0','10.255.255.255'),
               array('127.0.0.0','127.255.255.255'),
               array('169.254.0.0','169.254.255.255'),
               array('172.16.0.0','172.31.255.255'),
               array('192.0.2.0','192.0.2.255'),
               array('192.168.0.0','192.168.255.255'),
               array('255.255.255.0','255.255.255.255')
            );

            foreach ($reserved_ips as $r) {
                   $min = ip2long($r[0]);
                   $max = ip2long($r[1]);
                   if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
            }
            	return true;
           }else{
            	return false;
           }
    }
	/**
	 * Get the real IP address if the user using proxy 
	 * 
	 * @since 1.0.0
	 * @param string $ip IP Address
	 * 
	 * @return string Return the real visitor IP address
	 */
     function getip() {
           if(isset($_SERVER["HTTP_CLIENT_IP"])){
               if (validip($_SERVER["HTTP_CLIENT_IP"])) {
                   return $_SERVER["HTTP_CLIENT_IP"];
               }
           }

           if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
               foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
                   if (validip(trim($ip))) {
                       return $ip;
                   }
               }
           }

           if(isset($_SERVER["HTTP_X_FORWARDED"])){
               if (validip($_SERVER["HTTP_X_FORWARDED"])) {
                   return $_SERVER["HTTP_X_FORWARDED"];
               }
           }

           if(isset($_SERVER["HTTP_FORWARDED_FOR"])){
               if (validip($_SERVER["HTTP_FORWARDED_FOR"])) {
                   return $_SERVER["HTTP_FORWARDED_FOR"];
               }
           }

           if(isset($_SERVER["HTTP_FORWARDED"])){
               if (validip($_SERVER["HTTP_FORWARDED"])) {
                   return $_SERVER["HTTP_FORWARDED"];
               }
           }

           if(isset($_SERVER["HTTP_X_FORWARDED"])){
               if (validip($_SERVER["HTTP_X_FORWARDED"])) {
                   return $_SERVER["HTTP_X_FORWARDED"];
               }
           }
           
           return $_SERVER["REMOTE_ADDR"];
           
      }
	/**
	 * Convert the time format into the nice one. It will say 2 hours ago, 1 day ago. 
	 * 
	 * @since 1.0.0
	 * @param string $datefrom String checked datetime format.
	 * @param string $dateto String now datetime format.
	 * 
	 * @return string Return the converted time(nice time).
	 */
      function nicetime($datefrom,$dateto){
            if(empty($datefrom)) {
                return "No date provided";
            }

            $periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
            $lengths         = array("60","60","24","7","4.35","12","10");

            $now             = strtotime($dateto);
            $unix_date         = strtotime($datefrom);

            // check validity of date
            if(empty($unix_date)) {
                return "Bad date";
            }

            // is it future date or past date
            if($now >= $unix_date) {
                $difference     = $now - $unix_date;
                $tense         = "ago";

            } else {
                $difference     = $unix_date - $now;
                $tense         = "from now";
            }

            for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
                $difference /= $lengths[$j];
            }

            $difference = round($difference);

            if($difference != 1) {
                $periods[$j].= "s";
            }
            
            if($j>3)
            return date(get_date_format()." ".get_time_format(),strtotime($datefrom));

            return "$difference $periods[$j] {$tense}";
      }
      /**
	 * Display the date picker in select option format. When you call this function you will need to define the variable on the tamplate
	 * {birthday}= display the date, 
	 * {birhtmonth}=display the month
	 * {birthyear}=display the year
	 * 
	 * @since 1.0.0
	 * @param string $action_tag the location that you want to attemp the javascript of the date picker.
	 * 
	 * 
	 * @return string Return date picker.
	 */
      function get_date_picker($action_tag,$curdate,$curmonth,$curyear,$indexing=false,$index=0){
      		add_actions($action_tag,"get_javascript","birthday");
      		
      		if($indexing) 
      			add_actions($action_tag,"datepc_js",$indexing,$index);
      			
      		//Birthday
      		if($indexing)
      			$birthday="<select name=\"birthday[$index]\" >
						<option value=\"\">Day:</option>";
      		else
				$birthday="<select name=\"birthday\" >
						<option value=\"\">Day:</option>";
			
			for($bd=1;$bd<=31;$bd++){
				if(isset($curdate) && $curdate==$bd)
					$birthday.="<option value=\"".$bd."\" selected=\"selected\">".$bd."</option>";
				else 	
					$birthday.="<option value=\"".$bd."\">".$bd."</option>";
			}
			$birthday.="</select>";
			add_variable('birthday',$birthday);
			
			//Birthmonth
			if($indexing)
				$birthmonth="<select name=\"birthmonth[$index]\" >
						<option value=\"\">Month:</option>";
			else 
				$birthmonth="<select name=\"birthmonth\" >
						<option value=\"\">Month:</option>";
				
			$month_name=array("January","February","March","April","May","June","July","August","September","October","November","December");
			for($bm=0;$bm<12;$bm++){
				$val=$bm+1;
				if(isset($curmonth) && $curmonth==$val)
					$birthmonth.="<option value=\"".$val."\" selected=\"selected\">".$month_name[$bm]."</option>";
				else 	
					$birthmonth.="<option value=\"".$val."\">".$month_name[$bm]."</option>";
			}
			$birthmonth.="</select>";
			add_variable('birthmonth',$birthmonth);
			
			//Birthyear
			if($indexing)
				$theyear="<select name=\"birthyear[$index]\" >
					<option value=\"\">Year:</option>";
			else
				$theyear="<select name=\"birthyear\" >
					<option value=\"\">Year:</option>";
				
			for($year=date("Y");$year>=1900;$year--){
				if(isset($curyear) && $curyear==$year)
					$theyear.="<option value=\"".$year."\" selected=\"selected\">".$year."</option>";
				else 
					$theyear.="<option value=\"$year\">$year</option>";
			}
			$theyear.="</select>";
			add_variable('birthyear',$theyear);
      }
      function datepc_js($indexing,$index){
      	return "<script type=\"text/javascript\">
      				$(document).ready(function(){
						$(\"select[name=birthday[".$index."]]\").change(function(){
							var month=parseInt($(\"select[name=birthmonth[".$index."]]\").val());
							var date=parseInt($(\"select[name=birthday[".$index."]]\").val());
							var year=parseInt($(\"select[name=birthyear][".$index."]\").val());
							configure_date(date,month,year,".$indexing.",".$index.");
						});
						
						$(\"select[name=birthmonth[".$index."]]\").change(function(){
							var month=parseInt($(\"select[name=birthmonth[".$index."]]\").val());
							var date=parseInt($(\"select[name=birthday[".$index."]]\").val());
							var year=parseInt($(\"select[name=birthyear[".$index."]]\").val());
							configure_date(date,month,year,".$indexing.",".$index.");
							
						});
						
						$(\"select[name=birthyear]\").change(function(){
							var month=parseInt($(\"select[name=birthmonth[".$index."]]\").val());
							var date=parseInt($(\"select[name=birthday[".$index."]]\").val());
							var year=parseInt($(\"select[name=birthyear[".$index."]]\").val());
							configure_date(date,month,year,".$indexing.",".$index.");
						});
					});
				</script>";
      }
      function activate_URLs($string){
      	return preg_replace("#[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]#", "<a href=\"\\0\" rel=\"nofollow\">\\0</a>", $string);
      }
      
      function colorpicker($area_id,$preview_id=''){
			add_actions('header_elements','get_javascript','colorpicker/js/colorpicker.js');
			add_actions('header_elements',"color_picker_util",$area_id,$preview_id);
			
			
      }
      function color_picker_util($area_id,$preview_id){
      	if(!empty($preview_id))
      	$preview_action="
      			var color='#'+$('".$area_id."').val();
				$('".$preview_id."').css({'background-color' : color});
			";
      	else 
      	$preview_action="";	
      	
      	
      	return "<link rel=\"stylesheet\" href=\"".get_admin_url()."/javascript/colorpicker/css/colorpicker.css\" type=\"text/css\" />
      			<script type=\"text/javascript\">
      				$(function(){
						$('".$area_id."').ColorPicker({
							onSubmit: function(hsb, hex, rgb, el) {
								$(el).val(hex);
								$(el).ColorPickerHide();
								$preview_action
							},
							onBeforeShow: function () {
								$(this).ColorPickerSetColor(this.value);
							},
							onShow: function (colpkr) {
								$(colpkr).fadeIn(500);
								return false;
							},
							onHide: function (colpkr) {
								$(colpkr).fadeOut(500);
								
								return false;
							},
							
      					});
      					
      					$('".$area_id."').bind('keyup', function(){
							$(this).ColorPickerSetColor(this.value);
						});
      					
      					
					});
				</script>";
      }
     function tooltips($relname,$gravity='n'){
     	add_actions('header_elements','get_javascript','tipsy/javascripts/jquery.tipsy.js');
     	add_actions('header_elements','tooltips_util',$relname,$gravity);
     }
     function tooltips_util($relname,$gravity){
     	return "<link rel=\"stylesheet\" href=\"".get_admin_url()."/javascript/tipsy/stylesheets/tipsy.css\" type=\"text/css\" />
     			<script type=\"text/javascript\">
     				$(function(){
     					$('a[rel=".$relname."]').tipsy({ gravity: '".$gravity."' });
     				});
     			</script>";
     }
     function the_published_pages(){
		global $db;
		$html="";
		$query=$db->prepare_query("SELECT * FROM lumonata_articles 
									WHERE larticle_type='pages' AND larticle_status='publish'");
		return $result=$db->do_query($query);
		
		
		
	}
	function the_published_apps($app_name){
		global $db;
		
		$query=$db->prepare_query("SELECT * FROM lumonata_rules 
									WHERE lrule='categories' AND (lgroup=%s OR lgroup='default')",$app_name);
		
		return $result=$db->do_query($query);
	}
	function the_published_tags(){
		global $db;
		
		$query=$db->prepare_query("SELECT * FROM lumonata_rules 
									WHERE lrule='tags'");
		
		return $result=$db->do_query($query);
	}
	function get_includes_url($url=''){
	    if (!empty($_SERVER["HTTPS"]))
	        return get_admin_url()."/includes/".$url;
	    else 
	        return get_admin_url()."/includes/".$url;
	}
	function get_css_inc($url){
	    return "<link rel=\"stylesheet\" href=\"".get_includes_url($url)."\" type=\"text/css\" media=\"screen\" />";
	}
	function get_javascript_inc($url){
	    return "<script type=\"text/javascript\" src=\"".get_includes_url($url)."\" ></script>";
	}
?>