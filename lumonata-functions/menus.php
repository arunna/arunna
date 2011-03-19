<?php
	
	
		if(isset($_POST['app_set_name'])){
			require_once('../lumonata-functions/user.php');
			if(is_user_logged()){
				require_once('../lumonata_config.php');
			    require_once('../lumonata_settings.php');
			    require_once('../lumonata-functions/settings.php');
			    require_once('../lumonata-classes/actions.php');
			    
			    if($_POST['app_set_name']=="pages")
			    	echo get_published_pages();
			    elseif($_POST['app_set_name']=="url")
			    	echo get_custome_url();
			    else 
					echo get_published_apps($_POST['app_set_name']);
			}
		}
		
		if(isset($_POST['theorder'])){
			require_once('../lumonata-functions/user.php');
			if(is_user_logged()){
				require_once('../lumonata_config.php');
			    require_once('../lumonata_settings.php');
			    require_once('../lumonata-functions/settings.php');
			    require_once('../lumonata-classes/actions.php');
			    print_r($_POST['theorder']);
			    update_menu_order($_POST['active_tab'],$_POST['theorder']);
				
				
			}
		}
		if(isset($_POST['create_menu_set'])){
			require_once('../lumonata-functions/user.php');
			if(is_user_logged()){
				if(!defined('SITE_URL'))
				define('SITE_URL',get_meta_data('site_url'));
				
				if(!empty($_POST['setname']))
				add_menu_set();
			}
		}
		
		if(isset($_POST['removed_menu_set'])){
			require_once('../lumonata-functions/user.php');
			if(is_user_logged()){
				require_once('../lumonata_config.php');
			    require_once('../lumonata_settings.php');
			    require_once('../lumonata-functions/settings.php');
			    require_once('../lumonata-classes/actions.php');
			    if(!defined('SITE_URL'))
				define('SITE_URL',get_meta_data('site_url'));
				
			    if(remove_menu_set($_POST['removed_menu_set']))
			    echo "OK";
			}
		}
		
		if(isset($_POST['edit_items'])){
			
			require_once('../lumonata-functions/user.php');
			if(is_user_logged()){
				require_once('../lumonata_config.php');
			    require_once('../lumonata_settings.php');
			    require_once('../lumonata-functions/settings.php');
			    require_once('../lumonata-classes/actions.php');
			    if(!defined('SITE_URL'))
				define('SITE_URL',get_meta_data('site_url'));
				
				if($_POST['edit_items']=='edit'){
					if(edit_menu_items($_POST['id'],$_POST['tab']))
						 echo "OK";
				}elseif($_POST['edit_items']=='remove'){
					
					if(remove_menu_items($_POST['id'],$_POST['tab']))
						echo "OK";
				}
								
			    
			}
		}
		
		if(isset($_POST['add_selected'])){
			require_once('../lumonata_config.php');
		    require_once('../lumonata_settings.php');
		    require_once('../lumonata-functions/user.php');	
		    require_once('../lumonata-functions/settings.php');
			require_once('../lumonata-classes/actions.php');	    
			if(is_user_logged()){
				if(!defined('SITE_URL'))
				define('SITE_URL',get_meta_data('site_url'));
				
				$json_menu_set=get_meta_data('menu_set','menus');
				if(!empty($json_menu_set)){
					$menu_set=json_decode($json_menu_set,true);
					$menu_set_keys=array_keys($menu_set);
					$active_tab="";
					if($_POST['add_selected']=='single'){
						if(empty($_POST['tab'])){
				    		if(count($menu_set_keys)>0)
				    		$active_tab=$menu_set_keys[0];
				    	}else{ 
				    		$active_tab=$_POST['tab'];
				    	}
				    	if(add_menu_set_items($active_tab)){
				    		echo "OK";
				    	}
					}else{
						if(isset($_POST['selected_menu'])){
							if(empty($_GET['tab'])){
					    		if(count($menu_set_keys)>0)
					    		$active_tab=$menu_set_keys[0];
					    	}else{ 
					    		$active_tab=$_GET['tab'];
					    	}
					    	
					    	add_menu_set_items($active_tab);
						}
					}	
					
				}
			}
		}
	
	
	add_actions('menus','set_menus');
	function update_menu_order($menu_set,$new_order=array()){
		return update_meta_data('menu_order_'.$menu_set, json_encode($new_order),'menus');
	}
	function edit_menu_items($id,$menuset){
		if(empty($menuset)){
			$json_menu_set=get_meta_data('menu_set','menus');
			$menu_set=json_decode($json_menu_set,true);
			$menu_set_keys=array_keys($menu_set);
			
	    	if(count($menu_set_keys)>0)
	    		$menuset=$menu_set_keys[0];
	    	else return false;
		}
		
		$menu_items=get_meta_data('menu_items_'.$menuset,'menus');
		$menu_items=json_decode($menu_items,true);
		foreach ($menu_items as $key=>$val){
			if(is_array($val)){
				if($val['id']==$id){
					$new_menu_items[]=array(
						'id'=>$val['id'],
						'label'=>$_POST['label'],
						'target'=>$_POST['target'],
					    'link'=>$_POST['link'],
						'permalink'=>$_POST['permalink']
					);
					
				}else{
					$new_menu_items[]=array(
							'id'=>$val['id'],
							'label'=>$val['label'],
							'target'=>$val['target'],
						    'link'=>$val['link'],
							'permalink'=>$val['permalink']
						);
				}
			}
		}
		//print_r($new_menu_items);
		$edited_menu_items=json_encode($new_menu_items);
		return update_meta_data('menu_items_'.$menuset, $edited_menu_items,'menus');
	}
	function remove_menu_items($id,$menuset){
		if(empty($menuset)){
			$json_menu_set=get_meta_data('menu_set','menus');
			$menu_set=json_decode($json_menu_set,true);
			$menu_set_keys=array_keys($menu_set);
			
	    	if(count($menu_set_keys)>0)
	    		$menuset=$menu_set_keys[0];
	    	else return false;
		}
		
		//REMOVE MENU ITEMS
		$menu_items=get_meta_data('menu_items_'.$menuset,'menus');
		$menu_items=json_decode($menu_items,true);
		
		$menu_order=get_meta_data('menu_order_'.$menuset,'menus');
		$menu_order=json_decode($menu_order,true);
		
		
		foreach ($menu_items as $key=>$val){
			if(is_array($val)){
				if(in_array($val['id'],get_child_items($id, $menu_order))){
					
					unset($menu_items[$key]);
					
				}
			}
		}
		
	
		
		$menu_items=json_encode($menu_items);
		if(update_meta_data('menu_items_'.$menuset, $menu_items,'menus')){
			//REMOVE MENU ORDER
			$new_order=remove_menu_orders($id, $menuset,$menu_order);
			$new_order=json_encode($new_order);
			return update_meta_data('menu_order_'.$menuset, $new_order,'menus');
			
		}
	}
	function get_child_items($root_id,$menu_order=array()){
		$results=array();
		foreach ($menu_order as $key=>$val){
			if(find_is_match_array($root_id, $menu_order[$key])){
				$results=array($root_id);
				if(isset($val['children']) && is_array($val['children'])){
					foreach ( $val['children'] as $keyc=>$valc){
						$results=array_merge($results,get_child_items($valc['id'], $val['children']));
					}
				}
			}
		}
		return $results;
	}
	function remove_menu_orders($id,$menuset,&$menu_order=array()){
		if(empty($menuset) || $id=='' || empty($menu_order))return;
		
		foreach ($menu_order as $key=>&$val){
			if(find_is_match_array($id,$menu_order[$key])){
				unset($menu_order[$key]);
			}else{
				if(isset($val['children']) && is_array($val['children']))
				remove_menu_orders($id, $menuset, $val['children']);	
			}

		}
		
		return filter_order($menu_order);
		
		
	}
	function filter_order(&$array=array()){
		foreach ($array as $key=>&$val){
			if(empty($val['children']))
				unset($val['children']);
			if(isset($val['children']) && is_array($val['children'])){
				
				filter_order($val['children']);
			}
		}
		return $array;
	}
	function find_is_match_array($id,$array=array()){
		if($id==$array['id'])return true;
		else return false;
	}
	function add_menu_set_items($active_tab){
		if(empty($active_tab))return false;
		
		if(!empty($active_tab)){
			$menu_set_items=get_meta_data("menu_items_".$active_tab,'menus');
			$decode_menu_set_items=json_decode($menu_set_items,true);
			
			if(count($decode_menu_set_items)==0){
				$id=0;
			}else{
				$the_array_keys =array_keys($decode_menu_set_items);
				$id = max($the_array_keys)+1 ;
				
			}
			
			if($_POST['add_selected']=='single'){
				$the_menu_items[]=array(
						'id'=>$id,
						'label'=>$_POST['label'],
						'target'=>$_POST['target'],
					    'link'=>$_POST['link'],
						'permalink'=>$_POST['permalink']
					);
					
					$the_menu_order[]=array(
						'id'=>$id
					);
					
			}else{
				foreach ($_POST['selected_menu'] as $key=>$value){
					$the_menu_items[]=array(
						'id'=>$id,
						'label'=>$_POST['label'][$value],
						'target'=>$_POST['target'][$value],
					    'link'=>$_POST['link'][$value],
						'permalink'=>$_POST['permalink'][$value]
					);
					$the_menu_order[]=array(
						'id'=>$id
					);
					$id++;
				}
			}
			$menu_items=get_meta_data('menu_items_'.$active_tab,'menus');
			$menu_order=get_meta_data('menu_order_'.$active_tab,'menus');
			
			if(empty($menu_items)){
				set_meta_data('menu_items_'.$active_tab, json_encode($the_menu_items),'menus');
			}else{
				
				$the_menu_items=array_merge(json_decode($menu_items,true),$the_menu_items);
				update_meta_data('menu_items_'.$active_tab, json_encode($the_menu_items),'menus');
			}
			
			
			if(empty($menu_order)){
				set_meta_data('menu_order_'.$active_tab, json_encode($the_menu_order),'menus');
			}else{
				$the_menu_order=array_merge(json_decode($menu_order,true),$the_menu_order);
				update_meta_data('menu_order_'.$active_tab, json_encode($the_menu_order),'menus');
			}
			return true;
		}

	}
	function add_menu_set(){
		$current_menu_set=get_meta_data('menu_set','menus');
		$menu_key=generateSefUrl(rem_slashes($_POST['setname']));
		
		$menu_set=array( $menu_key=>$_POST['setname'] );
		
		if(empty($current_menu_set)){
			$menu_set=json_encode($menu_set);	
							
			set_meta_data('menu_set', $menu_set,'menus');
		}else{
			$ob_menu_set=json_decode($current_menu_set,true);
			$menu_set=array_merge($ob_menu_set,$menu_set);
			$encode_menu_set=json_encode($menu_set);
			
			update_meta_data('menu_set', $encode_menu_set,'menus');
		}
		
		header("location:".get_tab_url($menu_key));
		
	}
	
	function remove_menu_set($menu_set){
		//remove the menu set
		$json_menu_set=get_meta_data('menu_set','menus');
		$ob_menu_set=json_decode($json_menu_set,true);
		foreach ($ob_menu_set as $key=>$val){
			if($menu_set==$key){
					unset($ob_menu_set[$key]);
					break;
			}
		}
		
		$encode_menu_set=json_encode($ob_menu_set);
		
		//REMOVE THE MENU ITEMS HERE
		delete_meta_data('menu_order_'.$menu_set,'menus');
		delete_meta_data('menu_items_'.$menu_set,'menus');
		
		return update_meta_data('menu_set', $encode_menu_set,'menus');
		
		
		
	}
	
	function set_menus(){
		add_actions('section_title','Menus');
		add_actions('admin_tail','get_javascript','interface-1.2');
		add_actions('admin_tail','get_javascript','inestedsortable');
		add_actions('admin_tail','menus_javascript');
	
		$html="<div class=\"the_apps_set\">";
			$html.="<h2>Applications Set</h2>";
			$html.="<div style=\"padding:2px 5px;\">";
				$html.="Choose Applications: <select name=\"apps_set\" style=\"width:90%;\" >";
					$html.="<option value=\"pages\">Pages</option>";
					$html.="<option value=\"articles\">Articles</option>";
					$html.=attemp_actions('plugin_menu_set');
					$html.="<option value=\"url\">Custome URL</option>";
				$html.="</select>";
			$html.="</div>";
			$html.="<form method=\"post\" action=\"\">";
				
				$html.="<span id=\"error\" ></span>";
				$html.="<div class=\"sets_apps_item\">";
					$active_tab=(isset($_GET['tab']))?$_GET['tab']:'' ;
					$html.="<input type=\"hidden\" name=\"curpage\" id=\"activetab\" value=\"".$active_tab."\">";
					$html.="<input type=\"hidden\" name=\"curpage\" id=\"curpage\" value=\"".cur_pageURL()."\">";
					$html.="<ul id=\"the_apps_lists\">";
					$html.= get_published_pages();				
					$html.="</ul>";
				$html.="</div>";
				$html.="<div style=\"text-align:right;border-top:1px solid #bbb;\">";
					$html.="<input type=\"button\" value=\"Select all\" name=\"select_all\" class=\"button\" />";
					$html.="<input type=\"submit\" value=\"Add to menu set\" name=\"add_selected\" class=\"button\" />";
				$html.="</div>";
			$html.="</form>";
		$html.="</div>";

		$json_menu_set=get_meta_data('menu_set','menus');
		$menu_set=json_decode($json_menu_set,true);
		$html.="<div class=\"the_menus_set\">";
			$html.="<h2>Menus Set</h2>";
			$html.="<form action=\"\" method=\"post\">";
			$html.="<div class=\"setname\">Set Name: <input type=\"text\" value=\"\" name=\"setname\" class=\"medium_textbox\" />
						<input type=\"submit\" name=\"create_menu_set\" value=\"Create Menu Set\" class=\"button\" style=\"font-weight:bold;\" />
					</div>";
			$html.="</form>";
			$html.="<ul class=\"tabs\">";
				$active_tab="the_active_one";
				if(empty($json_menu_set) || count($menu_set)<1){
			    	$html.="";	
			    }else{
			    	$menu_set_keys=array_keys($menu_set);
			    	
			    	$i=0;
			    	if(empty($_GET['tab'])){
			    		if(count($menu_set_keys)>0)
			    		$active_tab=$menu_set_keys[0];
			    	}else{ 
			    		$active_tab=$_GET['tab'];
			    	}	
			    	foreach ($menu_set as $key=>$val){
			    		if($key==$active_tab){
							$html.="<li class=\"active\"><a href=\"".get_tab_url($key)."\">".$val."</a><span id=\"del_set_".$i."\" class=\"delete_tab\">X</span></li>";
			    		}else{ 
							$html.="<li ><a href=\"".get_tab_url($key)."\">".$val."</a><span id=\"del_set_".$i."\" class=\"delete_tab\">X</span></li>";
			    		}
						$html.="<script type=\"text/javascript\">
									$(function(){
										$('#del_set_".$i."').click(function(){
											$.post('../lumonata-functions/menus.php',{ removed_menu_set:'".$key."' },function(data){
												if(data=='OK')location='".get_state_url('menus')."'
			    							});
		    							});
		    						});
								</script>";	
						$i++;
			    	}
			    }
			$html.="</ul>";
				
			$html.="<div class=\"the_sets\">";
			  
			    	json_decode($json_menu_set,true);
			    	if(empty($json_menu_set) || count($menu_set)<1){
			    		$html.="<p>Please create the Menu Set first before you add the Application Items. To add new Menu Set, please fill the Set Name above and then click Create Menu Set.</p>";
			    		$html.="<p>After you create the Menu Set, then choose the Application on the left side to add to the Menu that you created. Click Add To Menu Set to add the items to this Menu</p>";
			    		$html.="<p>When you finish adding the application items, you can sorting the Menu Items by dragg and dropping it.</p>";
			    	}else{
			    		$html.="<div style=\"border:1px solid #ccc;padding:10px;display:none;font-weight:bold;color:red;margin:5px 0;\" id=\"procces_alert\">Saving...</div>";
			    		$html.="<iframe src=\"menus.php?active_tab=".$active_tab."\" width=\"100%\" height=\"400px\" frameborder=\"0\"></iframe>";
			    	  	
			    	}
		    $html.="</div>";					
		$html.="</div>";
		
		return $html;
	}
	
	function get_menu_items($menu_items=array(),$menu_order=array(),$active_tab){
		
		$html="";
		$target=array('_self'=>'Self','_blank'=>'Blank');

		if(is_array($menu_order)){
			foreach ($menu_order as $key=>$val){
				$items_val=array_match($menu_items,'id', $val['id']);
				$html.="<li id=\"ele-".$val['id']."\" class=\"clear-element page-item1\">";
		        	$html.="<div class='sort-handle'>";
		                    	$html.="<div class=\"apps_item clearfix\" style=\"height:30px;background:#ccc;\">";
									$html.="<div class=\"apps_item_text\">".$items_val[0]['label'];
									$html.="</div>";
									$html.="<div class=\"view\"  id=\"view_".$val['id']."\">";
									$html.="";
									$html.="</div>";
								$html.="</div>";
		            $html.="</div>";
		            $html.="<div class=\"menuset_details_item\" id=\"details_".$val['id']."\" style=\"display:none;\">";
						$html.="<p><label>Label</label>: <input type=\"text\" value=\"".$items_val[0]['label']."\" id=\"label_".$val['id']."\" name=\"title[".$val['id']."]\" class=\"medium_textbox\" /></p>";
						$html.="<p><label>Target</label>: <select id=\"target_".$val['id']."\" name=\"target[".$val['id']."]\">";
						
						foreach ($target as $key_items=>$val_items){
							if($key_items==$items_val[0]['target'])
								$html.="<option value=\"".$key_items."\" selected=\"selected\">".$val_items."</option>";
							else 
								$html.="<option value=\"".$key_items."\">".$val_items."</option>";
						}
						
						$html.="</select></p>";
						$html.="<div style=\"text-align:right;\">";
						$html.="<input type=\"button\" value=\"Remove\" id=\"remove_items_".$val['id']."\" name=\"remove_menu[".$val['id']."]\" class=\"button\" />";
						$html.="<input type=\"button\" value=\"Save\" id=\"edit_items_".$val['id']."\" name=\"save_menu[".$val['id']."]\" class=\"button\" />";
						$html.="<input type=\"hidden\" value=\"".$items_val[0]['link']."\" id=\"link_".$val['id']."\" name=\"link[".$val['id']."]\" />";
						$html.="<input type=\"hidden\" value=\"".$items_val[0]['permalink']."\" id=\"permalink_".$val['id']."\" name=\"permalink[".$val['id']."]\" />";
						$html.="</div>";
					$html.="</div>";
					
					if(isset($val['children'])){
						if(is_array($val['children'])){
							
							$html.="<ul class=\"page-list\">".get_menu_items($menu_items,$val['children'],$active_tab)."</ul>";
						}
					}
		        	$html.="</li>";
			}
		
		}
		return $html;
	}
	function looping_js_nav($menu_items=array()){
		$html="";
		
		if(!empty($menu_items)){
			foreach ($menu_items as $key=>$val){
				$html.="<script type=\"text/javascript\">";
					$html.="$(function(){
								$('#view_".$val['id']."').click(function(){
									$('#details_".$val['id']."').slideToggle(100);
									return false;
								});
								
								$('#edit_items_".$val['id']."').click(function(){
									
									$.post('../lumonata-functions/menus.php',{
										id:'".$val['id']."',
										label:$('#label_".$val['id']."').val(),
										target:$('#target_".$val['id']."').val(),
										link:$('#link_".$val['id']."').val(),
										permalink:$('#permalink_".$val['id']."').val(),
										tab:$('#activetab').val(),
										edit_items:'edit'
									},function(data){
										$('#procces_alert').show();
										if(data=='OK'){
											location.reload();
										}
									});
									
								});
								
								$('#remove_items_".$val['id']."').click(function(){
									
									$.post('../lumonata-functions/menus.php',{
										id:'".$val['id']."',
										tab:$('#activetab').val(),
										edit_items:'remove'
									},function(data){
										$('#procces_alert').show();
										//$('#procces_alert').html(data);
										if(data=='OK'){
											location.reload();
										}
									});
									
								});
								
							});
							";
				$html.="</script>";
				
			}
		}
		return $html;
	}
	function array_match($array, $key, $value){
	    $results = array();
		
	    if (is_array($array))
	    {
	    	if(isset($array[$key]))	
	        	if ($array[$key] == $value)
	            	$results[] = $array;
	
	        foreach ($array as $subarray)
	            $results = array_merge($results, array_match($subarray, $key, $value));
	    }
	
	    return $results;
	}
		
	function get_published_pages(){
		global $db;
		$html="";
		$pub_pages=the_published_pages();
		while($data=$db->fetch_array($pub_pages)){
			
			$html.="<li class=\"draggable\">";	
					$html.="<div class=\"apps_item clearfix\">";
						$html.="<div class=\"apps_item_text\">".$data['larticle_title'];
						$html.="</div>";
						$html.="<div class=\"add\" id=\"add_".$data['larticle_id']."\">";
						$html.="<input type=\"checkbox\" name=\"selected_menu[]\" class=\"selected_menu\" value=\"".$data['larticle_id']."\" />";
						$html.="</div>";
						$html.="<div class=\"view\" id=\"view_".$data['larticle_id']."\">";
						$html.="</div>";
					$html.="</div>";
					
					$html.="<div class=\"apps_details_item\" id=\"details_set_".$data['larticle_id']."\" style=\"display:none;\">";
						$html.="<p><label>Label</label>:<br /> <input type=\"text\" value=\"".$data['larticle_title']."\" id=\"label_".$data['larticle_id']."\" name=\"label[".$data['larticle_id']."]\" class=\"medium_textbox\" /></p>";
						$html.="<p><label>Target</label>:<br /> <select id=\"target_".$data['larticle_id']."\" name=\"target[".$data['larticle_id']."]\"><option value=\"_self\">Self</option><option value=\"_blank\">Blank</option></select></p>";
						$html.="<div style=\"text-align:right;\"><input type=\"button\" id=\"add_to_menu_set_".$data['larticle_id']."\" value=\"Add to menu set\" name=\"add[".$data['larticle_id']."]\" class=\"button\" /></div>";
						$html.="<input type=\"hidden\" value=\""."/?page_id=".$data['larticle_id']."\" id=\"link_".$data['larticle_id']."\" name=\"link[".$data['larticle_id']."]\" />";
						$html.="<input type=\"hidden\" value=\"".$data['lsef']."/\" id=\"permalink_".$data['larticle_id']."\" name=\"permalink[".$data['larticle_id']."]\" />";
					$html.="</div>";
			$html.="</li>";
			
			
			$html.="<script type=\"text/javascript\">";
						$html.="$(function(){
									$('#view_".$data['larticle_id']."').click(function(){
										$('#details_set_".$data['larticle_id']."').slideToggle(100);
										return false;
									});
									
									$('#add_to_menu_set_".$data['larticle_id']."').click(function(){
										$('#procces_alert').show();
										$.post('../lumonata-functions/menus.php',{
											label:$('#label_".$data['larticle_id']."').val(),
											target:$('#target_".$data['larticle_id']."').val(),
											link:$('#link_".$data['larticle_id']."').val(),
											permalink:$('#permalink_".$data['larticle_id']."').val(),
											tab:$('#activetab').val(),
											add_selected:'single'
										},function(data){
										
											if(data=='OK'){
												location=$('#curpage').val();
											}
										});
										//$('#procces_alert').hide();
									});
								});
								";
					$html.="</script>";
		}
		
		
		return $html;
	}
	function get_published_apps($app_name=''){
		global $db;
		$html="";
		$pub_apps=the_published_apps($app_name);
		while($data=$db->fetch_array($pub_apps)){
			$html.="<li class=\"draggable\">";	
					$html.="<div class=\"apps_item clearfix\">";
						$html.="<div class=\"apps_item_text\">".$data['lname'];
						$html.="</div>";
						$html.="<div class=\"add\" id=\"add_".$data['lrule_id']."\">";
						$html.="<input type=\"checkbox\" name=\"selected_menu[]\"  class=\"selected_menu\" value=\"".$data['lrule_id']."\" />";
						$html.="</div>";
						$html.="<div class=\"view\" id=\"view_".$data['lrule_id']."\">";
						$html.="</div>";
					$html.="</div>";
					
					$html.="<div class=\"apps_details_item\" id=\"details_set_".$data['lrule_id']."\" style=\"display:none;\">";
						$html.="<p><label>Label</label>:<br /> <input type=\"text\" id=\"label_".$data['lrule_id']."\" value=\"".$data['lname']."\" name=\"label[".$data['lrule_id']."]\" class=\"medium_textbox\" /></p>";
						$html.="<p><label>Target</label>:<br /> <select id=\"target_".$data['lrule_id']."\" name=\"target[".$data['lrule_id']."]\"><option value=\"_self\">Self</option><option value=\"_blank\">Blank</option></select></p>";
						$html.="<div style=\"text-align:right;\"><input id=\"add_to_menu_set_".$data['lrule_id']."\" type=\"button\" value=\"Add to menu set\" name=\"add[".$data['lrule_id']."]\" class=\"button\" /></div>";
						$html.="<input type=\"hidden\" id=\"link_".$data['lrule_id']."\" value=\""."/?app_name=".$data['lgroup']."&amp;cat_id=".$data['lrule_id']."\" name=\"link[".$data['lrule_id']."]\" />";
						$html.="<input type=\"hidden\" id=\"permalink_".$data['lrule_id']."\" value=\"".$data['lgroup']."/".$data['lsef']."/\" name=\"permalink[".$data['lrule_id']."]\" />";
					$html.="</div>";
					$active_tab=(isset($_GET['tab']))?$_GET['tab']:'' ;					
					$html.="<script type=\"text/javascript\">";
						$html.="$(function(){
						
									$('#view_".$data['lrule_id']."').click(function(){
										$('#details_set_".$data['lrule_id']."').slideToggle(100);
										return false;
									});
								
									$('#add_to_menu_set_".$data['lrule_id']."').click(function(){
										$('#procces_alert').show();
										$.post('../lumonata-functions/menus.php',{
											label:$('#label_".$data['lrule_id']."').val(),
											target:$('#target_".$data['lrule_id']."').val(),
											link:$('#link_".$data['lrule_id']."').val(),
											permalink:$('#permalink_".$data['lrule_id']."').val(),
											tab:$('#activetab').val(),
											add_selected:'single'
										},function(data){
											
											if(data=='OK'){
												location=$('#curpage').val();
											}
										});
										//$('#procces_alert').hide();
									});
								});
								
								
								";
					$html.="</script>";
					
			$html.="</li>";
		}
		
		return $html;
	}
	function get_custome_url(){
		$html="<li class=\"draggable\">";
			$html.="<div class=\"apps_details_item\" >";
			$html.="<p><label>Label</label>: <br /><input type=\"text\" value=\"\" id=\"label\" name=\"label\" class=\"medium_textbox\" /></p>";
			$html.="<p><label>URL</label>: <br /><input type=\"text\" value=\"\" id=\"link\" name=\"link\" class=\"medium_textbox\" /> <br />( include the http:// )</p>";
			$html.="<p><label>Target</label>: <br /><select name=\"target\" id=\"target\"><option value=\"_self\">Self</option><option value=\"_blank\">Blank</option></select></p>";
			$html.="<div style=\"text-align:right;\"><input type=\"button\" value=\"Add to menu set\" id=\"add_to_menu_set\" name=\"add\" class=\"button\" /></div>";
			$html.="</div>";
		$html.="</li>";
		$html.="<script type=\"text/javascript\">";
		$html.="$(function(){
					$('#add_to_menu_set').click(function(){
						$('#procces_alert').show();
						$.post('../lumonata-functions/menus.php',{
							label:$('#label').val(),
							target:$('#target').val(),
							link:$('#link').val(),
							permalink:$('#link').val(),
							tab:$('#activetab').val(),
							add_selected:'single'
						},function(data){
							
							if(data=='OK'){
								location=$('#curpage').val();
							}
						});
						//$('#procces_alert').hide();
					});
				});";
		$html.="</script>";
		return  $html;
	}
	
	function menus_javascript(){
		return "<script type=\"text/javascript\">
					$(document).ready( function(){
						//reset all to unchecked
				        $('.selected_menu').each(function(){
				            $('.selected_menu').removeAttr('checked');
				        });
				        
				        $('input[name=select_all]').click(function(){
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
						$('select[name=apps_set]').change(function(){
							
							$('#the_apps_lists').html('<img src=\"".get_theme_img()."/loader.gif\" />');
							$.post('../lumonata-functions/menus.php',
							{ app_set_name:$('select[name=apps_set]').val() },
							function(data){
									$('#the_apps_lists').html(data);
							});
						});
			        });
		        </script>
		        ";
	}
    
	function the_menus($args=''){
		if(empty($args))
		return;
		
		$var['menuset']='';
		$var['stlye']='li';
		$var['show_title']="true";
		
		if(!empty($args)){
	       $args=explode('&',$args);
	       foreach($args as $val){
                list($variable,$value)=explode('=',$val);
                if($variable=='menuset' || $variable=='style' || $variable=='show_title' )
                $var[$variable]=$value;
	       }
	    }
	    $menuset=get_meta_data('menu_set','menus');
	    $menuset=json_decode($menuset,TRUE);
	    if(is_array($menuset)){
    	    foreach($menuset as $key=>$val){
    	    	if(strtolower($val)==strtolower($var['menuset'])){
    	    		$menuset_key=$key;
    	    		$set_name=$val;
    	    		break;
    	    	}
    	    }
    	    if(!isset($menuset_key))
    	    return;
	    }else return;
		$menu_items=get_meta_data('menu_items_'.$menuset_key,'menus');
		$menu_items=json_decode($menu_items,TRUE);
		
		$menu_order=get_meta_data('menu_order_'.$menuset_key,'menus');
		$menu_order=json_decode($menu_order,TRUE);
		
		$return="";
		if($var['show_title']=="true")
		if(!empty($var['set_name']))
			$return="<h2>".$var['set_name']."</h2>";
		else 
			$return="<h2>".$set_name."</h2>";
			
		
		$return.=fetch_menu_set_items($menu_items,$menu_order,$var['stlye']);
		
	    
		return $return;
	}
	function the_page_menu($home=true){
		global $db;
		$query=$db->prepare_query("SELECT * FROM lumonata_articles
									WHERE larticle_type='pages' AND 
									larticle_status='publish' AND lshare_to=0 
									ORDER BY lorder");
		$result=$db->do_query($query);
		$menu="<ul>";
		if($home)
		$menu.="<li><a href=\"http://".site_url()."/\">HOME</a></li>";
		while($data=$db->fetch_array($result)){
			if(is_permalink())
				$menu.="<li><a href=\"http://".site_url()."/".$data['lsef']."/\">".$data['larticle_title']."</a></li>";
			else 
				$menu.="<li><a href=\"http://".site_url()."/?page_id=".$data['larticle_id']."\">".$data['larticle_title']."</a></li>";
		}
		$menu.="</ul>";
		return $menu;
	}	
	function fetch_menu_set_items($menu_items=array(),$menu_order=array(),$style){
		$return="";
		if(is_array($menu_order)){
			if($style=='li'){
			 	$return.="<ul>";
			}
			foreach ($menu_order as $key=>$val){
				 $items_val=array_match($menu_items,'id', $val['id']);
				 if(is_permalink())
				 	$link='http://'.site_url().'/'.$items_val[0]['permalink'];
				 else 
				 	$link='http://'.site_url().$items_val[0]['link'];
				 
				 if($style=='li'){
				 	$return.="<li><a href=\"".$link."\">".$items_val[0]['label']."</a>";
				 }else{
				 	$return.="<div><a href=\"".$link."\">".$items_val[0]['label']."</a>";
				 }
				 if(isset($val['children'])){
					if(is_array($val['children'])){
						if($style=='li'){
							$return.=fetch_menu_set_items($menu_items,$val['children'],$style)."</li>";
						}else{
							$return.=fetch_menu_set_items($menu_items,$val['children'],$style)."</div>";
						}
					}
				 }
			}
			
			if($style=='li'){
			 	$return.="</ul>";
			}
		}
		
		return $return;
	}
	
	function get_the_categories($args=''){
		global $db;
		$var['app_name']='';
		$var['parent_id']=0;
		$var['type']='li';
		$var['order']='ASC';
		$var['category_name']='';
		$var['sef']='';
		//echo $args;
		
		if(!empty($args)){
	       $args=explode('&',$args);
	       foreach($args as $val){
                list($variable,$value)=explode('=',$val);
                if($variable=='app_name' || $variable=='parent_id' || $variable=='type' || $variable=='order' || $variable=='category_name' || $variable=='sef'){
                	$var[$variable]=$value;
                	
                }
	       }
	    }
		
		if(!empty($var['category_name']) && !empty($var['app_name'])){
			$query=$db->prepare_query("SELECT a.* 
										FROM lumonata_rules a
										WHERE a.lname=%s AND 
												a.lrule='categories' AND 
												a.lgroup=%s AND
												",
										$var['category_name'],$var['app_name']);
			
												
			$result=$db->do_query($query);
			$data=$db->fetch_array($result);
			$var['parent_id']=$data['lrule_id'];
		}elseif(!empty($var['sef']) && !empty($var['app_name'])){
			$query=$db->prepare_query("SELECT a.* 
										FROM lumonata_rules a 
										WHERE lsef=%s AND 
												lrule='categories' AND 
												lgroup=%s AND
												",
										        $var['sef'],$var['app_name']);
			
												
			$result=$db->do_query($query);
			$data=$db->fetch_array($result);
			$var['parent_id']=$data['lrule_id'];
		}
		
		
		return recursive_taxonomy(0, 'categories', $var['app_name'],$var['type'], array(),$var['order'],$var['parent_id'],0,true);
		   
	}
	
	
?>