<?php
/*
    Plugin Name: Lumonata Meta Data
    Plugin URL: http://lumonata.com/
    Description: This plugin is use for adding Meta Data in each post like Meta Title, Meta Keywords and Meta Description.
    Author: Wahya Biantara
    Author URL: http://wahya.biantara.com/
    Version: 1.0.1
    
*/

/*
 * This function is used to add meta data additional fields on each articles and pages applications.
 * 
 * @since 1.0.0
 * 
 * @return string Will return the input type of meta title, meta keywords and meta description on each time you try to update or insert artilces
 * */
function meta_data(){
    global $thepost;
    $i=$thepost->post_index;
    $post_id=$thepost->post_id;
    if(isset($_POST['additional_fields']['meta_title'][$i])){
        return "<p>Title:<br />
                <input type=\"text\" class=\"textbox\" name=\"additional_fields[meta_title][$i]\" value=\"".rem_slashes($_POST['additional_fields']['meta_title'][$i])."\"  />
                 </p>
                 <p>Description:<br />
                <textarea class=\"textarea\" type=\"text\" name=\"additional_fields[meta_description][$i]\">".rem_slashes($_POST['additional_fields']['meta_description'][$i])."</textarea>
                 </p>
                 <p>Keywords:<br />
                <input type=\"text\" class=\"textbox\" name=\"additional_fields[meta_keywords][$i]\" value=\"".rem_slashes($_POST['additional_fields']['meta_keywords'][$i])."\"  />
                 </p>";
    }else{
        $mtitle="";
        $mkey="";
        $mdes="";
        
        if(is_edit() || is_edit_all()){
            $mtitle=get_additional_field($post_id,'meta_title',$_GET['state']);
            $mkey=get_additional_field($post_id,'meta_keywords',$_GET['state']);
            $mdes=get_additional_field($post_id,'meta_description',$_GET['state']);
            
            return "<p>Title:<br />
                    <input type=\"text\" class=\"textbox\" name=\"additional_fields[meta_title][$i]\" value=\"$mtitle\"  />
                     </p>
                     <p>Description:<br />
                    <textarea class=\"textarea\" type=\"text\" name=\"additional_fields[meta_description][$i]\">$mdes</textarea>
                     </p>
                     <p>Keywords:<br />
                    <input type=\"text\" class=\"textbox\" name=\"additional_fields[meta_keywords][$i]\" value=\"$mkey\"  />
                     </p>";
            
        }else{
            return "<p>Title:<br />
                    <input type=\"text\" class=\"textbox\" name=\"additional_fields[meta_title][$i]\"  />
                     </p>
                     <p>Description:<br />
                    <textarea class=\"textarea\" type=\"text\" name=\"additional_fields[meta_description][$i]\"></textarea>
                     </p>
                     <p>Keywords:<br />
                    <input type=\"text\" class=\"textbox\" name=\"additional_fields[meta_keywords][$i]\"  />
                     </p>";
        }
    }
}

/*
 * This function is used to get the Meta Title on each articles that exist on lumonata_additional_fileds table
 * 
 * @since 1.0.0
 * 
 * @return string Return the meta title on each article
 * */
function get_meta_title(){
	$id=post_to_id();
	
	if(!empty($id)){
		return strip_tags(get_additional_field($id,'meta_title',get_appname()));
	}
}
/*
 * This function is used to get the Meta Keywords on each articles that exist on lumonata_additional_fileds table
 * 
 * @since 1.0.0
 * 
 * @return string Return the meta keywords on each article
 * */
function get_meta_keywords(){
	$id=post_to_id();
	if(!empty($id)){
		$meta_keywords=strip_tags(get_additional_field($id,'meta_keywords',get_appname()));
		if(!empty($meta_keywords)){
			return "<meta name=\"keywords\" value=\"".$meta_keywords."\" />";
		}
	}
}
/*
 * This function is used to get the Meta Description on each articles that exist on lumonata_additional_fileds table
 * 
 * @since 1.0.0
 * 
 * @return string Return the meta description on each article
 * */
function get_meta_description(){
	$id=post_to_id();
	if(!empty($id)){
		$meta_description=strip_tags(get_additional_field($id,'meta_description',get_appname()));
		if(!empty($meta_description)){
			return "<meta name=\"description\" value=\"".$meta_description."\" />";
		}
	}
}
/*
 * This function is used to set meta data that will shown in home page. This application will be appear in Applications Sub Menu
 * 
 * @since 1.0.0
 * 
 * @return string Return the input area of meta data and will save it into lumonata_meta_data table
 * */
function set_home_meta_data(){
	
	$alert="";
	add_actions('section_title','Meta Data');
	
	if(isset($_POST['save_changes'])){
		foreach ($_POST as $key=>$val){
			if($key!="save_changes"){
				if(find_meta_data($key))
					$update=update_meta_data($key,$val);
				else 
					$update=set_meta_data($key,$val);
			}
			
		}
		if($update)
			$alert="<div class=\"alert_green_form\">Meta data has been updated.</div>";
		else
			$alert="<div class=\"alert_green_form\">Meta data failed to update.</div>";
	}
	
	$meta_title=get_meta_data("meta_title");
	$meta_keywords=get_meta_data("meta_keywords");
	$meta_description=get_meta_data("meta_description");
	
	$meta_title=(!empty($meta_title))?$meta_title:"";
	$meta_keywords=(!empty($meta_keywords))?$meta_keywords:"";
	$meta_description=(!empty($meta_description))?$meta_description:"";
	
	$return="<h1>Meta Data</h1>
			<p>This settings will change your meta data on your home page</p>
				<div class=\"tab_container\">
    				<div class=\"single_content\">
						$alert
        				<form method=\"post\" action=\"#\">
        					<fieldset>
								<p><label>Meta Title:</label></p>
								<input type=\"text\" id=\"meta_title\" name=\"meta_title\" value=\"$meta_title\" class=\"textbox\" />
						    </fieldset>
						    <fieldset>
								<p><label>Meta Keywords:</label></p>
								<input type=\"text\" id=\"meta_keywords\" name=\"meta_keywords\" value=\"$meta_keywords\" class=\"textbox\" />
						    </fieldset>
						    <fieldset>
								<p><label>Meta Description:</label></p>
								<textarea class=\"textarea\" type=\"text\" name=\"meta_description\">".$meta_description."</textarea>
						    </fieldset>
						    <div class=\"button_wrapper clearfix\">
						       <ul class=\"button_navigation\">
							   <li>".save_changes_botton()."</li>
						       </ul>
						    </div>
        				</form>
        			</div>
        		</div>";
	return $return;
}
/*
 * Add the meta data input interface into each proccess in administrator area.  
 * */

if(is_edit_all() && !(is_save_draft() || is_publish())){
    foreach($_POST['select'] as $index=>$post_id){
        add_actions('articles_additional_data_'.$index,'additional_data','Meta Data','meta_data');
        add_actions('page_additional_data_'.$index,'additional_data','Meta Data','meta_data');
    }
}else{
        add_actions('articles_additional_data','additional_data','Meta Data','meta_data');
        add_actions('page_additional_data','additional_data','Meta Data','meta_data');
}

/* Select the conditions where to set the meta data. */

if(is_home()){
	$meta_title=get_meta_data("meta_title");
	$meta_keywords=get_meta_data("meta_keywords");
	$meta_description=get_meta_data("meta_description");
	
	$meta_title=(!empty($meta_title))?$meta_title:"";
	$meta_keywords=(!empty($meta_keywords))?$meta_keywords:"";
	$meta_description=(!empty($meta_description))?$meta_description:"";
	
	add_actions("meta_title",$meta_title);
	add_actions("meta_keywords","<meta name=\"keywords\" value=\"".$meta_keywords."\" />");
	add_actions("meta_description","<meta name=\"description\" value=\"".$meta_description."\" />");
}elseif(is_details()){
	
	add_actions("meta_title",get_meta_title());
	add_actions("meta_keywords",get_meta_keywords());
	add_actions("meta_description",get_meta_description());
}elseif(is_page()){
	add_actions("meta_title",get_meta_title());
	add_actions("meta_keywords",get_meta_keywords());
	add_actions("meta_description",get_meta_description());
}
/* 
 * Add sub menu under applications menu
 *  
 * */
add_apps_menu(array('metadata'=>'Meta Data'));
/*
 * After adding sub menu, Now we have to add actions that will taking the process to set the meta data for the home page
 * Please keep in mind that the name of the menu variable(metadata), must be the same with the name of actions varibale bellow
 * */
add_actions("metadata","set_home_meta_data");

/* Add the Plugin at Applications Set  
// add_actions("plugin_menu_set","<option value=\"metadata\">Meta Data</option>");
*/

/* Add Plugin at Main Menu

// Add the array to main menu
add_main_menu(array('metadata'=>'Meta Data'));

//If the Menu has sumenu
add_sub_menu('metadata',array('meta_title'=>'Meta Title','meta_desc'=>'Meta Description'));

//Don't Forget to set the previlage for each user type
add_privileges('administrator','metadata','insert');

// Configure the CSS so the menu will appear at main menu
function menu_css(){
    return "<style type=\"text/css\">.lumonata_menu ul li.meta_data{
                                background:url('../lumonata-plugins/metadata/images/ico-themes.png') no-repeat left top;
                                }</style>";
}

//Function executed when sub menu Meta Title hit
function meta_title($tes){
    add_actions('section_title','Meta');
    return $tes;
}

//Attemp the CSS to header
add_actions('header_elements','menu_css');

//Add actions when Meta Title sub menu were hit. In this action, it wil call meta_title function above
add_actions('meta_title','meta_title','tes');
*/


/*
 * 	Here are how to add plugin that call the get_admin_article function with additional tabs
	
	//$tabs=array('meta_settings'=>'Settings');
	//add_actions('metadata','get_admin_article','metadata','Hotel|Hotels',$tabs);
 * 
 */
?>