<?php
    
    function activate_plugin($obj,$section){
            global $db;
            if(!is_array($obj))
                    return;
            if(empty($section))
                    return;
                            
            $active_plugin=get_meta_data('active_'.$section,$section);
            
            //if there are no active plugin or applications yet
            if(empty($active_plugin)){
                $active_plugin=json_encode($obj);
                $data=array('lmeta_name'=>'active_'.$section,
                            'lmeta_value'=>$active_plugin,
                            'lapp_name'=>$section);
                                        
                $insert=$db->insert('lumonata_meta_data', $data);
                if($insert)return true;
                else return false;
            //if there are already active plugin or applications        
            }else{
                //DECODE JSON format into Array	
                $active_plugin=json_decode($active_plugin,true);
                $active_plugin=array_merge($active_plugin,$obj);
                
                /*After the merge proccess, now encode it to JSON and save to database*/
                $active_plugin=json_encode($active_plugin);	
                
                $data=array('lmeta_name'=>'active_'.$section,
                            'lmeta_value'=>$active_plugin,
                            'lapp_name'=>$section);
                                                
                $where=array('lmeta_name'=>'active_'.$section,
                             'lapp_name'=>$section);
                                                                
                $update=$db->update('lumonata_meta_data', $data, $where);
                if($update)return true;
                else return false;
            }
    }
    function deactivate_plugin($plugin_key,$section){
        global $db;
        if(empty($plugin_key) || !is_string($plugin_key)) return;
        
        if(empty($section)) return;
        
        $active_plugin=get_meta_data('active_'.$section,$section);
        if(!empty($active_plugin)){
            //DECODE JSON format into Array	
            $active_plugin=json_decode($active_plugin,true);
            
            foreach($active_plugin as $key=>$value){
                    if($key==$plugin_key){
                            unset($active_plugin[$key]);
                            break;
                    }
            }
            
            
            $active_plugin=json_encode($active_plugin);
            
            $data=array('lmeta_name'=>'active_'.$section,
                        'lmeta_value'=>$active_plugin,
                        'lapp_name'=>$section);
                                            
            $where=array('lmeta_name'=>'active_'.$section,
                         'lapp_name'=>$section);
                                                            
            $update=$db->update('lumonata_meta_data', $data, $where);
            if($update)return true;
            else return false;
        }
        
    }
    function get_active_plugin($section){
        $active_plugin=get_meta_data('active_'.$section,$section);
        return json_decode($active_plugin,true);
    }
    function get_plugins(){
        //activate plugin execute
        if(isset($_GET['prc'])){
            if($_GET['prc']=='activate'){
                $activate_plugin[rem_slashes($_GET['id'])]=rem_slashes($_GET['plugin_path']);
                activate_plugin($activate_plugin,'plugins');
            }elseif($_GET['prc']=='deactivate'){
                deactivate_plugin(rem_slashes($_GET['id']),'plugins');
            }
        }
        if($_GET['sub']=='installed'){
            $the_plugins=scan_dir('plugins');
            $title="Installed Plugins";
        }elseif($_GET['sub']=='active'){
            $the_plugins=scan_dir('plugins');
            $the_active_plugins=array();
            $active_plugin=get_meta_data('active_plugins','plugins');
            if(!empty($active_plugin)){
                $active_plugin=json_decode($active_plugin,true);
                
                foreach($the_plugins as $key=>$param){
                    foreach($active_plugin as $key_active=>$val_active){
                        if($key==$key_active){
                            
                            $the_active_plugins[$key]=$param;
                        }
                    }
                }
               $the_plugins=$the_active_plugins;
            }else{
                $the_plugins=array();
            }
            $title="Active Plugins";
           
        }elseif($_GET['sub']=='inactive'){
            $the_plugins=scan_dir('plugins');
            $the_active_plugins=array();
            $active_plugin=get_meta_data('active_plugins','plugins');
            $active_plugin=json_decode($active_plugin,true);
           
            if((!empty($active_plugin) || count($active_plugin)!=0 )&& count($active_plugin) != count($the_plugins)){
              
                foreach($the_plugins as $key=>$param){
                    foreach($active_plugin as $key_active=>$val_active){
                        if($key!=$key_active){
                            $the_active_plugins[$key]=$param;
                        }
                    }
                }
               
                $the_plugins=$the_active_plugins;
                
            }else{
               
                $the_plugins=array();
            }
            $title="Inactive Plugins";
            
        }else{
            $content=run_actions($_GET['sub']);
			
            if(!empty($content))
				return $content;
            else
        		return "<div class=\"alert_red_form\">You don't have an authorization to access this page</div>";
        }
       
        if(is_array($the_plugins))
            $plugin_keys=array_keys($the_plugins);
        else
            $plugin_keys=array(0=>null);
            
        $list='';
        add_actions('section_title',$title);
        
        //start configuring the pagging system
        $url=get_state_url('plugins')."&sub=".$_GET['sub']."&page=";
        $viewed=list_viewed();
        if(isset($_GET['page'])){
            $page=$_GET['page'];
        }else{
            $page=1;
        }
        $num_plugins=count($the_plugins);
        $start=($page-1)* $viewed;
        $end=$start + $viewed - 1;
        
        if($num_plugins-1<$end)
        $end=$num_plugins-1;
       
        $list.="<h1>$title</h1>
                <div class=\"tab_container\">
                <div class=\"single_content\">
                <div id=\"response\"></div>
                <form action=\"".get_state_url('plugins')."&sub=".$_GET['sub']."\" method=\"post\">
                    <div class=\"button_wrapper clearfix\">
                        <div class=\"button_left\"></div>
                        <div class=\"button_right\">
                        ".search_box('plugin_search.php','list_item','plugin_search=search&start='.$start.'&end='.$end.'&','right','alert_green_form')."
                        </div>
                        
                    </div>
                    <div class=\"list\">
                        <div class=\"list_title\">
                            <div class=\"plugin_name\">Plugin Name</div>
                            <div class=\"plugin_description\">Description</div>
                        </div>
                        <div id=\"list_item\">";
        if(isset($_POST['s']) && $_POST['s']!='Search')
        $list.=search_plugin($start,$end);
        else
        $list.=plugin_list($start,$end,$the_plugins,$plugin_keys);
        $list.="</div>
                </form>
                </div>
                <div class=\"button_wrapper clearfix\">
                        <div class=\"button_left\"></div>
                        <div class=\"paging_right\">".
                          paging($url,$num_plugins,$page,$viewed,5)  
                        ."</div>
                </div>
                </div>
            </div>";
        return $list;
        
    }
    
    function plugin_list($start,$end,$the_plugins,$plugin_keys){
        $i=0;$list='';
        
        for($j=$start;$j<=$end;$j++){
            
            $plugin_name=$plugin_keys[$j];
            $theparam=$the_plugins[$plugin_name];
            $author='';
            $action_button="<a href=\"".get_state_url('plugins')."&sub=".$_GET['sub']."&prc=activate&plugin_path=".$theparam['Path']."&id=".$plugin_name."\">Activate</a> |";
            
            $active_plugin=get_meta_data('active_plugins','plugins');
            if(!empty($active_plugin))
                $active_plugin=json_decode($active_plugin,true);
            else
                $active_plugin=$the_plugins;
            
            $active_plugins=array_keys($active_plugin);
            if(in_array($plugin_name,$active_plugins)){
                $action_button="<a href=\"".get_state_url('plugins')."&sub=".$_GET['sub']."&prc=deactivate&plugin_path=".$theparam['Path']."&id=".$plugin_name."\">Deactivate</a> |";
                //break;
            }else{
               $action_button="<a href=\"".get_state_url('plugins')."&sub=".$_GET['sub']."&prc=activate&plugin_path=".$theparam['Path']."&id=".$plugin_name."\">Activate</a> |";
               //break;
            }
            
            
            if(!empty($theparam['Author']) && !empty($theparam['AuthorURL']))
                $author="<a href=\"".$theparam['AuthorURL']."\" >Author: ".$theparam['Author']."</a> |";
            elseif(empty($theparam['Author']) && !empty($theparam['AuthorURL']))
                $author="<a href=\"".$theparam['AuthorURL']."\" >Author: ".$theparam['AuthorURL']."</a> |";
            elseif(!empty($theparam['Author']) && empty($theparam['AuthorURL']))
                $author="<span style=\"font-size:10px;\">Author: ".$theparam['Author']."</span> |";
            
            
            $list.="<div class=\"list_item clearfix\" id=\"the_item_$i\">
                            <div class=\"plugin_name\" >".$theparam['Name']."</div>
                            <div class=\"plugin_description\">".$theparam['Description']."</div>
                            <div class=\"the_navigation_list\">
                                    <div class=\"list_navigation\" style=\"margin:40px 0;\"  id=\"the_navigation_".$i."\">
                                            $action_button
                                            <a href=\"".$theparam['URL']."\" >Plugin URL</a> |
                                            $author
                                            <span style=\"font-size:10px;\">Version: ".$theparam['Version']."</span>
                                    </div>
                            </div>
                    </div>";
            
            $i++;
            
        }
        return $list;
    }
    function search_plugin($start,$end){
        $i=0;$list='';
        $the_plugins=scan_dir('plugins');
        
        if(is_array($the_plugins))
            $plugin_keys=array_keys($the_plugins);
        else
            $plugin_keys=array(0=>null);
            
        for($j=$start;$j<=$end;$j++){
            
            $plugin_name=$plugin_keys[$j];
            $theparam=$the_plugins[$plugin_name];
           
            if(preg_match('/'.strtolower($_POST['s']).'/',strtolower($theparam['Author'])) || preg_match('/'.strtolower($_POST['s']).'/',strtolower($theparam['Name'])) || preg_match('/'.strtolower($_POST['s']).'/',strtolower($theparam['Description'])) ){
                $author='';
                $action_button="<a href=\"".get_state_url('plugins')."&sub=".$_GET['sub']."&prc=activate&plugin_path=".$theparam['Path']."&id=".$plugin_name."\">Activate</a> |";
                
                $active_plugin=get_meta_data('active_plugins','plugins');
                if(!empty($active_plugin))
                    $active_plugin=json_decode($active_plugin,true);
                else
                    $active_plugin=$the_plugins;
                
                $active_plugins=array_keys($active_plugin);
                if(in_array($plugin_name,$active_plugins)){
                    $action_button="<a href=\"".get_state_url('plugins')."&sub=".$_GET['sub']."&prc=deactivate&plugin_path=".$theparam['Path']."&id=".$plugin_name."\">Deactivate</a> |";
                    //break;
                }else{
                   $action_button="<a href=\"".get_state_url('plugins')."&sub=".$_GET['sub']."&prc=activate&plugin_path=".$theparam['Path']."&id=".$plugin_name."\">Activate</a> |";
                   //break;
                }
                
                
                if(!empty($theparam['Author']) && !empty($theparam['AuthorURL']))
                    $author="<a href=\"".$theparam['AuthorURL']."\" >Author: ".$theparam['Author']."</a> |";
                elseif(empty($theparam['Author']) && !empty($theparam['AuthorURL']))
                    $author="<a href=\"".$theparam['AuthorURL']."\" >Author: ".$theparam['AuthorURL']."</a> |";
                elseif(!empty($theparam['Author']) && empty($theparam['AuthorURL']))
                    $author="<span style=\"font-size:10px;\">Author: ".$theparam['Author']."</span> |";
                
                
                $list.="<div class=\"list_item clearfix\" id=\"the_item_$i\">
                                <div class=\"plugin_name\" >".$theparam['Name']."</div>
                                <div class=\"plugin_description\">".$theparam['Description']."</div>
                                <div class=\"the_navigation_list\">
                                        <div class=\"list_navigation\" style=\"margin:40px 0;\"  id=\"the_navigation_".$i."\">
                                                $action_button
                                                <a href=\"".$theparam['URL']."\" >Plugin URL</a> |
                                                $author
                                                <span style=\"font-size:10px;\">Version: ".$theparam['Version']."</span>
                                        </div>
                                </div>
                        </div>";
                
                $i++;
            }
        }
        if(empty($list))
        return "<div class=\"alert_yellow_form\">No result found for <em>".$_POST['s']."</em>. Check your spellling or try another terms</div>";
        else
        return $list;
    }
    if(!isset($_POST['plugin_search'])){
        $active_plugins=(is_array(get_active_plugin('plugins')))?get_active_plugin('plugins'):array();
        foreach($active_plugins as $key=>$val){
            require_once(PLUGINS_PATH.$val);
        }
    }
?>