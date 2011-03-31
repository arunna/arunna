<?php
    function get_admin_page(){
        $post_id=0;
        //Publish or Save Draft Actions
        if(is_save_draft() || is_publish()){
           
            //set status and hook defined actions
            if(is_save_draft()){
                $status='draft';
                run_actions('page_draft');
            }elseif(is_publish()){
                $status='publish';
                run_actions('page_publish');
                
                if(isset($_POST['select'])){
                    foreach($_POST['select'] as $key=>$val){
                        update_articles_status($val,'publish');
                    }
                }
            }
            
            if(is_add_new()){
                
                //Hook Add New Actions
                if(is_save_draft()){
                    run_actions('page_addnew_draft');
                }elseif(is_publish()){
                    run_actions('page_addnew_publish');
                }
                if(isset($_POST['allow_comments'][0]))
                    $comments="allowed";
                else
                    $comments="not_allowed";
                    
                    
                $title=$_POST['title'][0];
                
                if(!is_saved()){
                    //save the article and then syncronize with the attachment data if user do upload files    
                    save_article($title,$_POST['post'][0],$status,"pages",$comments,$_POST['sef_box'][0],$_POST['share_option'][0]);
                    $post_id=mysql_insert_id();
                    attachment_sync($_POST['post_id'][0],$post_id);
                    
                    //insert additional fields
                    if(isset($_POST['additional_fields'])){
                        foreach($_POST['additional_fields'] as $key=>$val){
                           foreach($val as $subkey=>$subval){
                                add_additional_field($post_id,$key,$subval,'pages');
                           }
                        }
                    }
                }else{
                    //Update the article because the data is already saved before
                    update_article($_POST['post_id'][0],$title,$_POST['post'][0],$status,"pages",$comments,$_POST['share_option'][0]);
                    
                    //update additional fields
                    if(isset($_POST['additional_fields'])){
                        foreach($_POST['additional_fields'] as $key=>$val){
                           foreach($val as $subkey=>$subval){
                                edit_additional_field($_POST['post_id'][0],$key,$subval,'pages');
                           }
                        }
                    }
                }
            }elseif(is_edit()){
                //Hook Single Edit Actions
                if(is_save_draft()){
                    run_actions('page_edit_draft');
                }elseif(is_publish()){
                    run_actions('page_edit_publish');
                }
                
                if(isset($_POST['allow_comments'][0]))
                    $comments="allowed";
                else
                    $comments="not_allowed";
                    
                
                $title=$_POST['title'][0];
                
                //Update the article
                update_article($_POST['post_id'][0],$title,$_POST['post'][0],$status,"pages",$comments,$_POST['share_option'][0]);
                
                //update additional fields
                if(isset($_POST['additional_fields'])){
                    
                    foreach($_POST['additional_fields'] as $key=>$val){
                       foreach($val as $subkey=>$subval){
                            edit_additional_field($_POST['post_id'][0],$key,$subval,'pages');
                       }
                    }
                }
            }elseif(is_edit_all()){
                //Hook Edit All Actions
                if(is_save_draft()){
                    run_actions('page_editall_draft');
                }elseif(is_publish()){
                    run_actions('page_editall_publish');
                }
                
                //Update the articles
                foreach($_POST['post_id'] as $index=>$value){
                    if(isset($_POST['allow_comments'][$index]))
                        $comments="allowed";
                    else
                        $comments="not_allowed";
                   
                    
                    $title=$_POST['title'][$index];
                    update_article($_POST['post_id'][$index],$title,$_POST['post'][$index],$status,"pages",$comments,$_POST['share_option'][$index]);
                    
                    //update additional fields
                    if(isset($_POST['additional_fields'])){
                        foreach($_POST['additional_fields'] as $key=>$val){
                            edit_additional_field($_POST['post_id'][$index],$key,$_POST['additional_fields'][$key][$index],'pages');
                        }
                    }
                   
                }
                
                
            }
        }elseif(is_unpublish()){
            if(isset($_POST['select'])){
                foreach($_POST['select'] as $key=>$val){
                    update_articles_status($val,'unpublish');
                }
            }
        }
        
        //Automatic to display add new when there is no records on database
        if(is_num_articles()==0 && !isset($_GET['prc'])){
            header("location:".get_state_url('pages')."&prc=add_new");
        }
                
        
        //Display add new form
        if(is_add_new()){
            return add_new_page($post_id) ;
        }elseif(is_edit()){ 
            if(is_contributor() || is_author()){
                if(is_num_articles("id=".$_GET['id'])>0){
                    return edit_page($_GET['id']);
                }else{
                    return "<div class=\"alert_red_form\">You don't have an authorization to access this page</div>";
                }
            }else{
                return edit_page($_GET['id']);
            }
        }elseif(is_edit_all() && isset($_POST['select'])){
            return edit_page();
        }elseif(is_delete_all()){
                add_actions('section_title','Delete Comments');
                $warning="<form action=\"\" method=\"post\">";
                if(count($_POST['select'])==1)
                        $warning.="<div class=\"alert_red_form\"><strong>Are you sure want to delete this comment:</strong>";
                else
                        $warning.="<div class=\"alert_red_form\"><strong>Are you sure want to delete these comments:</strong>";
                        
                $warning.="<ol>";	
                foreach($_POST['select'] as $key=>$val){
                        $d=fetch_artciles("id=".$val);
                        $warning.="<li>".$d['larticle_title']."</li>";
                        $warning.="<input type=\"hidden\" name=\"id[]\" value=\"".$d['larticle_id']."\">";
                }
                $warning.="</ol></div>";
                $warning.="<div style=\"text-align:right;margin:10px 5px 0 0;\">";
                $warning.="<input type=\"submit\" name=\"confirm_delete\" value=\"Yes\" class=\"button\" />";
                $warning.="<input type=\"button\" name=\"confirm_delete\" value=\"No\" class=\"button\" onclick=\"location='".get_state_url('pages')."'\" />";
                $warning.="</div>";
                $warning.="</form>";
                
                return $warning;
        }elseif(is_confirm_delete()){
                foreach($_POST['id'] as $key=>$val){
                        delete_article($val,'pages');
                }
        }
        
        //Display Users Lists
        if(is_num_articles()>0){
                add_actions('header_elements','get_javascript','jquery_ui');
                add_actions('header_elements','get_javascript','articles_list');
                return get_pages_list();
        }
    }
    
    function get_admin_article($type='articles',$thetitle='Article|Articles',$tabs=array()){
    	
        $post_id=0;
        $articletabtitle=explode("|",$thetitle);
        $tabs=array_merge(array('articles'=>$articletabtitle[1],'categories'=>'Categories','tags'=>'Tags'),$tabs);
        if(is_contributor() || is_author()){
            //$tabs=array_slice($tabs, 0,1);
            foreach($tabs as $key=>$val){
                if(is_grant_app($key)){
                    $thetabs[$key]=$val;
                }
            }
            $tabs=$thetabs;
        }else{
             $tabs=$tabs;
        }
        //print_r($tabs);
        /*
            Configure the tabs
            $the_tab is the selected tab
        */
        $tab_keys=array_keys($tabs);
        $tabb='';
        if(empty($_GET['tab']))
               $the_tab=$tab_keys[0];
        else
                $the_tab=$_GET['tab'];
       
        $articles_tabs=set_tabs($tabs,$the_tab);
        add_variable('tab',$articles_tabs);
        /*
            Filter the tabs. Articles tab can be accessed by all user type.
            But for Categories and Tags tabs only granted for Editor and Administrator.
        */
       
        if($the_tab=='articles'){
            //Publish or Save Draft Actions
            if(is_save_draft() || is_publish()){
                
                //set status and hook actions defined
                if(is_save_draft()){
                    $status='draft';
                    run_actions('article_draft');
                }elseif(is_publish()){
                    $status='publish';
                    run_actions('article_publish');
                    
                    if(isset($_POST['select'])){
                        foreach($_POST['select'] as $key=>$val){
                            update_articles_status($val,'publish');
                        }
                    }
                }
               
                if(is_add_new()){
                    
                    //Hook Add New Actions
                    if(is_save_draft()){
                    	
                        attemp_actions('article_addnew_draft');
                    }elseif(is_publish()){
                    	
                        attemp_actions('article_addnew_publish');
                    }
                    if(isset($_POST['allow_comments'][0]))
                        $comments="allowed";
                    else
                        $comments="not_allowed";
                        
                    
                  	
                    $title=$_POST['title'][0];
                    
                    if(!is_saved()){
                    	
                        //save the article and then syncronize with the attachment data if user do upload files    
                        save_article($title,$_POST['post'][0],$status,$type,$comments,$_POST['sef_box'][0],$_POST['share_option'][0]);
                        $post_id=mysql_insert_id();
                        attachment_sync($_POST['post_id'][0],$post_id);
                        
                        //insert additional fields
                        if(isset($_POST['additional_fields'])){
                            foreach($_POST['additional_fields'] as $key=>$val){
                               foreach($val as $subkey=>$subval){
                                    add_additional_field($post_id,$key,$subval,$type);
                               }
                            }
                        }
                        
                        //insert the categories into the rule_relationship table
                        if(isset($_POST['category'][0])){
                            foreach($_POST['category'][0] as $val){
                                insert_rules_relationship($post_id,$val);
                            }
                        }else{
                             	insert_rules_relationship($post_id,1);
                        }
                        
                        //insert the tags into rules and rules_relationship table
                        if(isset($_POST['tags'][0])){
                            foreach($_POST['tags'][0] as $val){
                                $rule_id=insert_rules(0,$val,'','tags',$type);
                                insert_rules_relationship($post_id,$rule_id);
                            }
                        }
                    }else{
                    	 
                        //Update the article because the data is already saved before
                        update_article($_POST['post_id'][0],$title,$_POST['post'][0],$status,$type,$comments,$_POST['share_option'][0]);
                        
                        //update additional fields
                        if(isset($_POST['additional_fields'])){
                            foreach($_POST['additional_fields'] as $key=>$val){
                               foreach($val as $subkey=>$subval){
                                    edit_additional_field($_POST['post_id'][0],$key,$subval,$type);
                               }
                            }
                        }
                        
                        //update the categories at rule_relationship table
                        if(isset($_POST['category'][0])){
                            delete_rules_relationship("app_id=".$_POST['post_id'][0],'categories');
                            foreach($_POST['category'][0] as $val){
                                insert_rules_relationship($_POST['post_id'][0],$val);
                            }
                        }else{
                            delete_rules_relationship("app_id=".$_POST['post_id'][0],'categories');
                            insert_rules_relationship($post_id,1);
                        }
                        
                        //update the tags at rule_relationship table
                        delete_rules_relationship("app_id=".$_POST['post_id'][0],'tags');
                       
                        if(isset($_POST['tags'][0])){
                            foreach($_POST['tags'][0] as $val){
                                $rule_id=insert_rules(0,$val,'','tags',$type);
                                insert_rules_relationship($_POST['post_id'][0],$rule_id);
                            }
                        }
                    }
                }elseif(is_edit()){
                	
                    //Hook Single Edit Actions
                    if(is_save_draft()){
                        run_actions('article_edit_draft');
                    }elseif(is_publish()){
                        run_actions('article_edit_publish');
                    }
                    
                    if(isset($_POST['allow_comments'][0]))
                        $comments="allowed";
                    else
                        $comments="not_allowed";
                        
                  
                    
                    $title=$_POST['title'][0];
                    
                    //Update the article
                    update_article($_POST['post_id'][0],$title,$_POST['post'][0],$status,$type,$comments,$_POST['share_option'][0]);
                    
                    //update additional fields
                    if(isset($_POST['additional_fields'])){
                        
                        foreach($_POST['additional_fields'] as $key=>$val){
                           foreach($val as $subkey=>$subval){
                                edit_additional_field($_POST['post_id'][0],$key,$subval,$type);
                           }
                        }
                    }
                    
                    //update the categories at rule_relationship table
                    if(isset($_POST['category'][0])){
                    	
                        delete_rules_relationship("app_id=".$_POST['post_id'][0],'categories');
                       
                        foreach($_POST['category'][0] as $key=>$val){
                            insert_rules_relationship($_POST['post_id'][0],$val);
                        }
                       
                    }else{
                        delete_rules_relationship("app_id=".$_POST['post_id'][0],'categories');
                        insert_rules_relationship($_POST['post_id'][0],1);
                    }
                    
                    //update the tags at rule_relationship table
                    delete_rules_relationship("app_id=".$_POST['post_id'][0],'tags');
                    
                    if(isset($_POST['tags'][0])){
                        foreach($_POST['tags'][0] as $val){
                            $rule_id=insert_rules(0,$val,'','tags',$type);
                            insert_rules_relationship($_POST['post_id'][0],$rule_id);
                        }
                    }
                    
                }elseif(is_edit_all()){
                    
                    //Hook Edit All Actions
                    if(is_save_draft()){
                        run_actions('article_editall_draft');
                    }elseif(is_publish()){
                        run_actions('article_editall_publish');
                    }
                    
                    //Update the articles
                    foreach($_POST['post_id'] as $index=>$value){
                        if(isset($_POST['allow_comments'][$index]))
                            $comments="allowed";
                        else
                            $comments="not_allowed";
                            
                       
                        $title=$_POST['title'][$index];
                        update_article($_POST['post_id'][$index],$title,$_POST['post'][$index],$status,$type,$comments,$_POST['share_option'][$index]);
                        
                        //update additional fields
                        if(isset($_POST['additional_fields'])){
                            foreach($_POST['additional_fields'] as $key=>$val){
                                edit_additional_field($_POST['post_id'][$index],$key,$_POST['additional_fields'][$key][$index],$type);
                            }
                        }
                        
                        //update the categories at rule_relationship table
                        if(isset($_POST['category'][$index])){
                            delete_rules_relationship("app_id=".$_POST['post_id'][$index],'categories');
                            foreach($_POST['category'][$index] as $val){
                                insert_rules_relationship($_POST['post_id'][$index],$val);
                            }
                        }else{
                            delete_rules_relationship("app_id=".$_POST['post_id'][$index],'categories');
                            insert_rules_relationship($_POST['post_id'][$index],1);
                        }
                        
                        //update the tags at rule_relationship table
                        delete_rules_relationship("app_id=".$_POST['post_id'][$index],'tags');
                        if(isset($_POST['tags'][$index])){
                            foreach($_POST['tags'][$index] as $val){
                                $rule_id=insert_rules(0,$val,'','tags',$type);
                                insert_rules_relationship($_POST['post_id'][$index],$rule_id);
                            }
                        }
                       
                    }
                    
                    
                }
            }elseif(is_unpublish()){
                if(isset($_POST['select'])){
                    foreach($_POST['select'] as $key=>$val){
                        update_articles_status($val,'unpublish');
                    }
                }
            }
            
           //Automatic to add new when there is no records on database
            if(is_num_articles('type='.$type)==0 && !isset($_GET['prc'])){
                if($_GET['state']!='articles')
                    header("location:".get_state_url($_GET['state'])."&sub=".$_GET['sub']."&prc=add_new");
                else    
                    header("location:".get_state_url($type)."&prc=add_new");
            }
                    
           
            //Is add new Article, View the desain
            if(is_add_new()){
                
                return add_new_article($post_id,$type,$thetitle) ;
            }elseif(is_edit()){
                if(is_contributor() || is_author()){
                    if(is_num_articles("id=".$_GET['id']."&type=".$type)>0){
                        return edit_article($_GET['id'],$type,$thetitle);
                    }else{
                        return "<div class=\"alert_red_form\">You don't have an authorization to access this page</div>";
                    }
                }else{
                    return edit_article($_GET['id'],$type,$thetitle);
                }
            }elseif(is_edit_all() && isset($_POST['select'])){
                return edit_article(0,$type,$thetitle);
            }elseif(is_delete_all()){
                $thetitle=explode("|",$thetitle);
                $warning="<form action=\"\" method=\"post\">";
                if(count($_POST['select'])==1){
                        add_actions('section_title','Delete '.$thetitle[0]);
                        $warning.="<div class=\"alert_red_form\"><strong>Are you sure want to delete this ".$thetitle[0].":</strong>";
                }else{
                        add_actions('section_title','Delete '.$thetitle[1]);
                        $warning.="<div class=\"alert_red_form\"><strong>Are you sure want to delete these ".$thetitle[1].":</strong>";
                }        
                $warning.="<ol>";	
                foreach($_POST['select'] as $key=>$val){
                        $d=fetch_artciles("id=".$val."&type=".$type);
                        $warning.="<li>".$d['larticle_title']."</li>";
                        $warning.="<input type=\"hidden\" name=\"id[]\" value=\"".$d['larticle_id']."\">";
                }
                $warning.="</ol></div>";
                $warning.="<div style=\"text-align:right;margin:10px 5px 0 0;\">";
                $warning.="<input type=\"submit\" name=\"confirm_delete\" value=\"Yes\" class=\"button\" />";
                if(is_admin_application())
                $warning.="<input type=\"button\" name=\"confirm_delete\" value=\"No\" class=\"button\" onclick=\"location='".get_application_url($type)."'\" />";
                else
                $warning.="<input type=\"button\" name=\"confirm_delete\" value=\"No\" class=\"button\" onclick=\"location='".get_state_url($type)."'\" />";
                $warning.="</div>";
                $warning.="</form>";
                
                return $warning;
            }elseif(is_confirm_delete()){
                foreach($_POST['id'] as $key=>$val){
                        delete_article($val,$type);
                }
            }
            
            //Display Users Lists
            if(is_num_articles("type=".$type)>0){
                    add_actions('header_elements','get_javascript','jquery_ui');
                    add_actions('header_elements','get_javascript','articles_list');
                   
                    return get_article_list($type,$thetitle,$articles_tabs);
            }
        }elseif(($the_tab=="categories" || $the_tab=="tags") && (is_administrator() || is_editor())){
            return get_admin_rule($the_tab,$type,$thetitle,$tabs);
        }else{
            return "<div class=\"alert_red_form\">You don't have an authorization to access this page</div>";
        }
    }
    
    function get_pages_list($type='pages'){
        global $db;
        $list='';
        $option_viewed="";
        $data_to_show=array('all'=>'All','publish'=>'Publish','unpublish'=>'Unpublish','draft'=>'Draft');
        
        if(isset($_POST['data_to_show']))
            $show_data=$_POST['data_to_show'];
        elseif(isset($_GET['data_to_show']))
            $show_data=$_GET['data_to_show'];
       
        
        foreach($data_to_show as $key=>$val){
            if(isset($show_data)){
                if($show_data==$key){
                    $option_viewed.="<input type=\"radio\" name=\"data_to_show\" value=\"$key\" checked=\"checked\" />$val";
                }else{
                    $option_viewed.="<input type=\"radio\" name=\"data_to_show\" value=\"$key\"  />$val";
                }
            }elseif($key=='all'){
                $option_viewed.="<input type=\"radio\" name=\"data_to_show\" value=\"$key\" checked=\"checked\"  />$val";
            }else{
                $option_viewed.="<input type=\"radio\" name=\"data_to_show\" value=\"$key\"  />$val";
            }
        }
        
        
        
        if($_COOKIE['user_type']=='contributor' || $_COOKIE['user_type']=='author'){
            $w=" lpost_by=".$_COOKIE['user_id']." AND ";    
        }else{
            $w="";
        }
        
        if(is_search()){
                $sql=$db->prepare_query("select * from lumonata_articles where $w larticle_type=%s and (larticle_title like %s or larticle_content like %s)",$type,"%".$_POST['s']."%","%".$_POST['s']."%");
                $num_rows=count_rows($sql);
                
        }else{
                if((isset($_POST['data_to_show']) && $_POST['data_to_show']!="all") || (isset($_GET['data_to_show']) && $_GET['data_to_show']!="all")){
                    //setup paging system
                    if(isset($_POST['data_to_show']))
                        $show_data=$_POST['data_to_show'];
                    else
                        $show_data=$_GET['data_to_show'];
                        
                    $url=get_state_url('pages')."&data_to_show=$show_data&page=";
                    $where=$db->prepare_query(" WHERE $w larticle_status=%s AND larticle_type=%s",$show_data,$type);
                }else{
                    $where=$db->prepare_query("WHERE $w larticle_type=%s",$type);
                    //setup paging system
                    $url=get_state_url('pages')."&page=";
                }    
                $num_rows=count_rows("select * from lumonata_articles $where");
        }
        
        $viewed=list_viewed();
        if(isset($_GET['page'])){
            $page= $_GET['page'];
        }else{
            $page=1;
        }
        
        $limit=($page-1)*$viewed;
        if(is_search()){
                $sql=$db->prepare_query("select * from lumonata_articles where $w larticle_type=%s and (larticle_title like %s or larticle_content like %s) limit %d, %d",$type,"%".$_POST['s']."%","%".$_POST['s']."%",$limit,$viewed);
               
        }else{
                if(isset($_POST['data_to_show']) && $_POST['data_to_show']!="all")
                    $where=$db->prepare_query(" WHERE $w larticle_status=%s AND larticle_type=%s",$_POST['data_to_show'],$type);
                else
                    $where=$db->prepare_query("WHERE $w larticle_type=%s",$type);
                    
                $sql=$db->prepare_query("select * from lumonata_articles $where order by lorder limit %d, %d",$limit,$viewed);
               
        }
        
        //if($viewed*$page > $num_rows && $num_rows!=0 && $page>1)
        //    header("location:".$url."1");
        
        $result=$db->do_query($sql);
        
        $start_order=($page - 1) * $viewed + 1; //start order number
        if($_COOKIE['user_type']=="contributor"){
            $button="<li>".button("button=add_new",get_state_url("pages")."&prc=add_new")."</li>
                    <li>".button('button=edit&type=submit&enable=false')."</li>
                    <li>".button('button=delete&type=submit&enable=false')."</li>";
        }else{
            $button="<li>".button("button=add_new",get_state_url("pages")."&prc=add_new")."</li>
                    <li>".button('button=edit&type=submit&enable=false')."</li>
                    <li>".button('button=delete&type=submit&enable=false')."</li>
                    <li>".button('button=publish&type=submit&enable=false')."</li>
                    <li>".button('button=unpublish&type=submit&enable=false')."</li>";
        }
        $list.="<h1>Pages</h1>
                <div class=\"tab_container\">
                    <div class=\"single_content\">
                        <div id=\"response\"></div>
                        <form action=\"".get_state_url('pages')."\" method=\"post\" name=\"alist\">
                           <input type=\"hidden\" name=\"start_order\" value=\"$start_order\" />
                           <input type=\"hidden\" name=\"state\" value=\"pages\" />
                            <div class=\"button_wrapper clearfix\">
                                <div class=\"button_left\">
                                        <ul class=\"button_navigation\">
                                                $button
                                        </ul>
                                </div>
                                <div class=\"button_right\">
                                ".search_box('articles.php','list_item','state=pages&prc=search&','right','alert_green_form')."
                                </div>
                            </div>
                            <div class=\"status_to_show\">Show: $option_viewed</div>
                            <div class=\"list\">
                                <div class=\"list_title\">
                                    <input type=\"checkbox\" name=\"select_all\" class=\"title_checkbox\" />
                                    <div class=\"pages_title\">Title</div>
                                    <div class=\"list_author\">Author</div>
                                    <div class=\"avatar\"></div>
                                    <div class=\"list_comments\">Comments</div>
                                    <div class=\"list_date\">Date</div>
                                    <!--div class=\"list_date\">Order</div -->
                                </div>
                                <div id=\"list_item\">";
                                	$list.=pages_list($result,$start_order);
                $list.="		</div>
                			</div>
                        </form>
                        
                        <div class=\"button_wrapper clearfix\">
                                <div class=\"button_left\">
                                    <ul class=\"button_navigation\">
                                         $button
                                    </ul>   
                                </div>
                                <div class=\"paging_right\">
                                    ". paging($url,$num_rows,$page,$viewed,5)."
                                </div>
                        </div>
                    </div>
                </div>
            <script type=\"text/javascript\" language=\"javascript\">
                
                
            </script>";
            
        add_actions('section_title','Pages');
        return $list;
    }
    
    function get_article_list($type,$title,$articles_tabs){
        global $db;
        $list='';
        $option_viewed="";
        $data_to_show=array('all'=>'All','publish'=>'Publish','unpublish'=>'Unpublish','draft'=>'Draft');
        
        if(isset($_POST['data_to_show']))
            $show_data=$_POST['data_to_show'];
        elseif(isset($_GET['data_to_show']))
            $show_data=$_GET['data_to_show'];
        
            
        foreach($data_to_show as $key=>$val){
            if(isset($show_data)){
                if($show_data==$key){
                    $option_viewed.="<input type=\"radio\" name=\"data_to_show\" value=\"$key\" checked=\"checked\" />$val";
                }else{
                    $option_viewed.="<input type=\"radio\" name=\"data_to_show\" value=\"$key\"  />$val";
                }
            }elseif($key=='all'){
                $option_viewed.="<input type=\"radio\" name=\"data_to_show\" value=\"$key\" checked=\"checked\"  />$val";
            }else{
                $option_viewed.="<input type=\"radio\" name=\"data_to_show\" value=\"$key\"  />$val";
            }
        }
        
        
        
        if($_COOKIE['user_type']=='contributor' || $_COOKIE['user_type']=='author'){
            $w=" lpost_by=".$_COOKIE['user_id']." AND ";    
        }else{
            $w="";
        }
        
        if(is_search()){
                $sql=$db->prepare_query("select * from lumonata_articles where $w larticle_type=%s and (larticle_title like %s or larticle_content like %s)",$type,"%".$_POST['s']."%","%".$_POST['s']."%");
                $num_rows=count_rows($sql);
                
        }else{
                if((isset($_POST['data_to_show']) && $_POST['data_to_show']!="all") || (isset($_GET['data_to_show']) && $_GET['data_to_show']!="all")){
                    //setup paging system
                    
                    $where=$db->prepare_query(" WHERE $w larticle_status=%s AND larticle_type=%s",$show_data,$type);
                }else{
                    $where=$db->prepare_query("WHERE $w larticle_type=%s",$type);
                }    
                $num_rows=count_rows("select * from lumonata_articles $where");
        }
        
        $viewed=list_viewed();
        if(isset($_GET['page'])){
            $page= $_GET['page'];
        }else{
            $page=1;
        }
        
        $limit=($page-1)*$viewed;
        if(is_search()){
                $sql=$db->prepare_query("select * from lumonata_articles where $w larticle_type=%s and (larticle_title like %s or larticle_content like %s) limit %d, %d",$type,"%".$_POST['s']."%","%".$_POST['s']."%",$limit,$viewed);
               
        }else{
               if((isset($_POST['data_to_show']) && $_POST['data_to_show']!="all") || (isset($_GET['data_to_show']) && $_GET['data_to_show']!="all")){
                    //setup paging system
                    
                    if(is_admin_application())
                        $url=get_application_url($type)."&data_to_show=$show_data&page=";
                    else
                        $url=get_state_url($type)."&data_to_show=$show_data&page=";
                    
                        
                    $where=$db->prepare_query(" WHERE $w larticle_status=%s AND larticle_type=%s",$show_data,$type);
                }else{
                    //setup paging system
                    if(is_admin_application())
                        $url=get_application_url($type)."&page=";
                    else
                        $url=get_state_url($type)."&page=";
                    $where=$db->prepare_query("WHERE $w larticle_type=%s",$type);
                }    
                $sql=$db->prepare_query("select * from lumonata_articles $where order by lorder limit %d, %d",$limit,$viewed);
               
        }
        //echo $viewed*$page."==".$num_rows;
        
        //if($viewed*$page > $num_rows && $num_rows!=0 && $page>1)
        //    header("location:".$url."1");
        
        $result=$db->do_query($sql);
        
        $start_order=($page - 1) * $viewed + 1; //start order number
        $addnew_url=(is_admin_application())?get_application_url($type):get_state_url($type);
        if($_COOKIE['user_type']=="contributor"){
            $button="<li>".button("button=add_new",$addnew_url."&prc=add_new")."</li>
                    <li>".button('button=edit&type=submit&enable=false')."</li>
                    <li>".button('button=delete&type=submit&enable=false')."</li>";
        }else{
            $button="<li>".button("button=add_new",$addnew_url."&prc=add_new")."</li>
                    <li>".button('button=edit&type=submit&enable=false')."</li>
                    <li>".button('button=delete&type=submit&enable=false')."</li>
                    <li>".button('button=publish&type=submit&enable=false')."</li>
                    <li>".button('button=unpublish&type=submit&enable=false')."</li>";
        }
        
        $title=explode("|",$title);
        if($num_rows>1){
            if(count($title)==2)
                $title=$title[1];
            else
                $title=$title[0];
        }else{
            $title=$title[0];
        }
        $list.="<h1>$title</h1>
                <ul class=\"tabs\">$articles_tabs</ul>
                <div class=\"tab_container\">
                        <div id=\"response\"></div>
                        <form action=\"\" method=\"post\" name=\"alist\">
                           <input type=\"hidden\" name=\"start_order\" value=\"$start_order\" />
                           <input type=\"hidden\" name=\"state\" value=\"".$type."\" />
                            <div class=\"button_wrapper clearfix\">
                                <div class=\"button_left\">
                                        <ul class=\"button_navigation\">
                                                $button
                                        </ul>
                                </div>
                                <div class=\"button_right\">
                                ".search_box('articles.php','list_item','state='.$type.'&prc=search&','right','alert_green_form')."
                                </div>
                            </div>
                            <div class=\"status_to_show\">Show: $option_viewed</div>
                            <div class=\"list\">
                                <div class=\"list_title\">
                                    <input type=\"checkbox\" name=\"select_all\" class=\"title_checkbox\" />
                                    <div class=\"article_title\">Title</div>
                                    <div class=\"list_author\">Author</div><div class=\"avatar\"></div>
                                    <div class=\"list_comments\">Comments</div>
                                    <div class=\"list_date\">Date</div>
                                    <!--div class=\"list_date\">Order</div -->
                                </div>
                                <div id=\"list_item\">";
                                	$list.=article_list($result,$type,$start_order);
                $list.="		</div>
                			</div>
                        </form>
                        
                        <div class=\"button_wrapper clearfix\">
                                <div class=\"button_left\">
                                    <ul class=\"button_navigation\">
                                         $button
                                    </ul>   
                                </div>
                                <div class=\"paging_right\">
                                    ". paging($url,$num_rows,$page,$viewed,5)."
                                </div>
                        </div>
                </div>
            <script type=\"text/javascript\" language=\"javascript\">
                
                
            </script>";
            
        add_actions('section_title',$title);
        return $list;
    }
    
    function pages_list($result,$i=1){
        global $db;
        $list='';
        if($db->num_rows($result)==0)
        return "<div class=\"alert_yellow_form\">No result found for <em>".$_POST['s']."</em>. Check your spellling or try another terms</div>";
        
        while($d=$db->fetch_array($result)){
                if($d['larticle_status']!='publish')
                    $status=" - <strong style=\"color:red;\">".ucfirst($d['larticle_status'])."</strong>";
                else
                    $status="";

                if($d['lshare_to']==0){
                    $share_to="<span style=\"font-size:10px;\">Everyone</span>";
                }else{
                    $share_data=get_friend_list_by_id($d['lshare_to']);
                    $share_to="<span style=\"font-size:10px;\">".$share_data['llist_name']."</span>";
                }   
                
                $user_fetched=fetch_user($d['lpost_by']);
                $list.="<div class=\"list_item clearfix\" id=\"theitem_".$d['larticle_id']."\">
                                <input type=\"checkbox\" name=\"select[]\" class=\"title_checkbox select\" value=\"".$d['larticle_id']."\" />
                                <div class=\"pages_title\" >".$d['larticle_title']."$status</div>
                                <div class=\"avatar\"><img src=\"".get_avatar($user_fetched['luser_id'], 3)."\" /></div>
                                <div class=\"list_author\">".$user_fetched['ldisplay_name']."</div>
                                <div class=\"list_comments\" style=\"text-align:center;\">".number_format($d['lcomment_count'])."</div>
                                <div class=\"list_date\">".date(get_date_format(),strtotime($d['lpost_date']))."</div>
                                <!-- div class=\"list_order\" --><input type=\"hidden\" value=\"$i\" id=\"order_".$d['larticle_id']."\" class=\"small_textbox\" name=\"order[".$i."]\"><!-- /div -->
                                <div class=\"the_navigation_list\">
                                        <div class=\"list_navigation\" style=\"display:none;\" id=\"the_navigation_".$d['larticle_id']."\">
                                                <a href=\"".get_state_url('pages')."&prc=edit&id=".$d['larticle_id']."\">Edit</a> |
                                                <a href=\"javascript:;\" rel=\"delete_".$d['larticle_id']."\">Delete</a> | 
                                                $share_to
                                        </div>
                                </div>
                                <script type=\"text/javascript\" language=\"javascript\">
                                        $('#theitem_".$d['larticle_id']."').mouseover(function(){
                                                $('#the_navigation_".$d['larticle_id']."').show();
                                        });
                                        $('#theitem_".$d['larticle_id']."').mouseout(function(){
                                                $('#the_navigation_".$d['larticle_id']."').hide();
                                        });
                                </script>
                                
                        </div>";
                $msg="Are sure want to delete ".$d['larticle_title']."?";
                add_actions('admin_tail','delete_confirmation_box',$d['larticle_id'],$msg,"articles.php","theitem_".$d['larticle_id'],'state=pages&prc=delete&id='.$d['larticle_id']);
                //delete_confirmation_box($d['larticle_id'],"Are sure want to delete ".$d['larticle_title']."?","articles.php","theitem_".$d['larticle_id'],'state=pages&prc=delete&id='.$d['larticle_id'])
                $i++;
        }
        return $list;
    }
    
    function article_list($result,$type,$i=1){
        global $db;
        $list='';
        
        if(is_admin_application())
            $url=get_application_url($type);
        else
            $url=get_state_url($type);
            
        if($db->num_rows($result)==0)
        return "<div class=\"alert_yellow_form\">No result found for <em>".$_POST['s']."</em>. Check your spellling or try another terms</div>";
        
        while($d=$db->fetch_array($result)){
                if($d['larticle_status']!='publish')
                    $status=" - <strong style=\"color:red;\">".ucfirst($d['larticle_status'])."</strong>";
                else
                    $status="";

                if($d['lshare_to']==0){
                    $share_to="<span style=\"font-size:10px;\">Everyone</span>";
                }else{
                    $share_data=get_friend_list_by_id($d['lshare_to']);

                    $share_to="<span style=\"font-size:10px;\">".$share_data['llist_name']."</span>";
                }    
                $user_fetched=fetch_user($d['lpost_by']);
                $list.="<div class=\"list_item clearfix\" id=\"theitem_".$d['larticle_id']."\">
                                <input type=\"checkbox\" name=\"select[]\" class=\"title_checkbox select\" value=\"".$d['larticle_id']."\" />
                                <div class=\"article_title\" >".$d['larticle_title']."$status </div>
                                <div class=\"avatar\"><img src=\"".get_avatar($user_fetched['luser_id'], 3)."\" /></div>
                                <div class=\"list_author\">".$user_fetched['ldisplay_name']."</div>
                                <div class=\"list_comments\" style=\"text-align:center;\">".number_format($d['lcomment_count'])."</div>
                                <div class=\"list_date\">".date(get_date_format(),strtotime($d['lpost_date']))."</div>
                                <!-- div class=\"list_order\" --><input type=\"hidden\" value=\"$i\" id=\"order_".$d['larticle_id']."\" class=\"small_textbox\" name=\"order[".$i."]\"><!-- /div -->
                                
                                <div class=\"the_navigation_list\">
                                    <div class=\"list_navigation\" style=\"display:none;\" id=\"the_navigation_".$d['larticle_id']."\">
                                            <a href=\"".$url."&prc=edit&id=".$d['larticle_id']."\">Edit</a> |
                                            <a href=\"javascript:;\" rel=\"delete_".$d['larticle_id']."\">Delete</a> |
                                            $share_to 
                                    </div>
                                </div>
                                
                                <script type=\"text/javascript\" language=\"javascript\">
                                        $('#theitem_".$d['larticle_id']."').mouseover(function(){
                                                $('#the_navigation_".$d['larticle_id']."').show();
                                        });
                                        $('#theitem_".$d['larticle_id']."').mouseout(function(){
                                                $('#the_navigation_".$d['larticle_id']."').hide();
                                        });
                                </script>
                           </div>";
                $msg="Are sure want to delete ".$d['larticle_title']."?";
                add_actions('admin_tail','delete_confirmation_box',$d['larticle_id'],$msg,"articles.php","theitem_".$d['larticle_id'],'state='.$type.'&prc=delete&id='.$d['larticle_id']);
                //delete_confirmation_box($d['larticle_id'],"Are sure want to delete ".$d['larticle_title']."?","articles.php","theitem_".$d['larticle_id'],'state='.$type.'&prc=delete&id='.$d['larticle_id'])
                $i++;
        }
        return $list;
    }
    
    function set_page_template(){
        //set template
        set_template(TEMPLATE_PATH."/pages.html",'pages');
        //set block
        add_block('loopPage','lPage','pages');
        add_block('pageAddNew','pAddNew','pages');
    }
    function return_page_template($loop=false){
       
        parse_template('pageAddNew','pAddNew',$loop);
        return return_template('pages');
    }
    function set_article_template(){
        //set template
        set_template(TEMPLATE_PATH."/articles.html",'article');
        //set block
        add_block('loopArticle','lArticle','article');
        add_block('articleAddNew','aAddNew','article');
    }
    function return_article_template($loop=false){
       
        parse_template('articleAddNew','aAddNew',$loop);
        return return_template('article');
    }
    
    function add_new_page($post_id=0){
        $args=array($index=0,$post_id);
        set_page_template();
        
        $thepost->post_id=$post_id;
        $thepost->post_index=$index;
        
        $button="";
        if(!is_contributor())
            $button.="<li>".button("button=add_new",get_state_url('pages')."&prc=add_new")."</li>
            <li>".button("button=save_draft")."</li>
            <li>".button("button=publish")."</li>
            <li>".button("button=cancel",get_state_url('pages'))."</li>";
                        
        else
            $button.="<li>".button("button=add_new",get_state_url('pages')."&prc=add_new")."</li>
            <li>".button("button=save_draft&label=Save")."</li>
            <li>".button("button=cancel",get_state_url('pages'))."</li>";
        
        //set the page Title
        add_actions('section_title','Pages - Add New');
        if(is_save_draft() || is_publish()){
            if(!is_saved())
                $post_id=$post_id;
            else
                $post_id=$_POST['post_id'][0];
            
            //Get The Permalink
            //if(is_permalink()){
                if(isset($_POST['sef_box'][0]))
                    $sef=$_POST['sef_box'][0];
                else 
                    $sef="";
                    
                $_POST['index']=0;
                
                if(strlen($sef)>50)$more="...";else $more="";
                
                $sef_scheme="<div id=\"sef_scheme_0\">";
                $sef_scheme.="<strong>Permalink:</strong> 
                        	  http://".site_url()."/
                        	  <span id=\"the_sef_".$_POST['index']."\">
                    			  <span id=\"the_sef_content_".$_POST['index']."\"  style=\"background:#FFCC66;cursor:pointer;\">".
                                      substr($sef,0,50).$more.
                                  "</span>/
                                  <input type=\"button\" value=\"Edit\" id=\"edit_sef_".$_POST['index']."\" class=\"button_bold\">
                              </span>
                              <span id=\"sef_box_".$_POST['index']."\" style=\"display:none;\">
                              <span>
                              	<input type=\"text\" name=\"sef_box[".$_POST['index']."]\" value=\"".$sef."\" style=\"border:1px solid #CCC;width:300px;font-size:11px;\" />/
                              	<input type=\"button\" value=\"Done\" id=\"done_edit_sef_".$_POST['index']."\" class=\"button_bold\">
                              </span>
                              </span>
                              
                              <script type=\"text/javascript\">
                              		$('#the_sef_".$_POST['index']."').click(function(){
                              			$('#the_sef_".$_POST['index']."').hide();
                              			$('#sef_box_".$_POST['index']."').show();
                              			
                    				});
                    				$('#edit_sef_".$_POST['index']."').click(function(){
                              			$('#the_sef_".$_POST['index']."').hide();
                              			$('#sef_box_".$_POST['index']."').show();
                    				});
                    				$('#done_edit_sef_".$_POST['index']."').click(function(){
                    					                    					
                    					var new_sef=$('input[name=sef_box[".$_POST['index']."]]').val();
                    						
                    					if(new_sef.length>50){
                    						var more='...'
                    						
                    					}else{
                        					var more='';
                        					
                        				}
                        				
                              			$('#the_sef_".$_POST['index']."').show();
                              			$('#sef_box_".$_POST['index']."').hide();
                              			$.post('articles.php',
                              			{ 'update_sef' 	: 'true',
             							  'post_id' 	: ".$post_id.",
             							  'type' 		: 'pages',
             							  'title' 		: $('input[name=title[0]]').val(),
             							  'new_sef'	 	: new_sef },
                              			function(theResponse){
                              				if(theResponse=='BAD'){
                    							$('input[name=sef_box[".$_POST['index']."]]').val('".$sef."');
                    							$('#the_sef_content_".$_POST['index']."').html('".substr($sef, 0,50)."');
                    						}else if(theResponse=='OK'){
                    							$('#the_sef_content_".$_POST['index']."').html(new_sef.substr(0,50)+more);
             								}
             							});
             							
             							
                    				});
                              </script>"; 
                    $sef_scheme.="</div>";
            //}
            add_variable('textarea',textarea('post[0]',0,rem_slashes($_POST['post'][0]),$post_id));
            add_variable('title',rem_slashes($_POST['title'][0]));
            add_variable('is_saved',"<input type=\"hidden\" name=\"article_saved\" value=\"1\" />");
        }else{
            //Get The Permalink
            //if(is_permalink()){
                $sef_scheme="<div id=\"sef_scheme_0\"></div>";
                $sef_scheme.="<script type=\"text/javascript\">
                					$(function(){
                						$('input[name=title[0]]').blur(function(){
                							$.post('articles.php',
                							{ 'get_sef' : 'true',
                							   'type'	: 'pages',
                							   'index'	: 0,
                							   'title'	: $(this).val() 
            								},function(theResponse){
            									$('#sef_scheme_0').html(theResponse);
            								});
                							
            							});
            						});
                			   </script>"; 
            //}
            add_variable('textarea',textarea('post[0]',0));
            add_variable('title','');
        }
        
        add_variable('i',0);
        add_variable("sef_scheme", $sef_scheme);
        add_variable('share_to', additional_data("Share To", "article_share_option", true,$args));
        add_variable('comments_settings',additional_data("Comments","discussion_settings",false,$args));
        add_variable('additional_data',attemp_actions('page_additional_data'));
        add_variable('button',$button);
        parse_template('loopPage','lPage',false);
         
        return return_page_template();
        
    }
    
    function add_new_article($post_id=0,$type,$title){
        global $thepost;
        $args=array($index=0,$post_id);
               
        $thepost->post_id=$post_id;
        $thepost->post_index=$index;
        
        set_article_template();
        
        if(is_admin_application())
            $url=get_application_url($type);
        else
            $url=get_state_url($type);
        
        $thetitle=explode("|",$title);
        $thetitle=$thetitle[0];
        add_variable('app_title',"Add New ".$thetitle);
       
        $button="";
        if(!is_contributor())
            $button.="<li>".button("button=add_new",$url."&prc=add_new")."</li>
            <li>".button("button=save_draft")."</li>
            <li>".button("button=publish")."</li>
            <li>".button("button=cancel",$url)."</li>";
                        
        else
            $button.="<li>".button("button=add_new",$url."&prc=add_new")."</li>
            <li>".button("button=save_draft&label=Save")."</li>
            <li>".button("button=cancel",$url)."</li>";
        
        //set the page Title
        add_actions('section_title',$thetitle.' - Add New');
        if(is_save_draft() || is_publish()){
            if(!is_saved()){
                $post_id=$post_id;
            }else{
                $post_id=$_POST['post_id'][0];
            }
            
            /*
                Configure Categories And Tags
                all_categories($post_id,$index) are defined at taxonomy.php
                $post_id is the article ID and $index is the index array of the post.
                The $index value will be "0" if add new and numberic if for edit proccess
            */
            //Categories
            $selected_categories=find_selected_rules($post_id,'categories',$type);
           
            add_variable('all_categories',all_categories(0,'categories',$type,$selected_categories));
            add_variable('most_used_categories',get_most_used_categories($type));
            if(is_editor() || is_administrator()){
                add_variable('add_new_category',article_new_category(0,'categories',$type));
            }
            
            //Tags
            add_variable('all_tags',get_post_tags($post_id,0,$type));
            add_variable('most_used_tags',get_most_used_tags($type));
            add_variable('add_new_tag',add_new_tag($post_id,0));
            
           
            //Get The Permalink
             //if(is_permalink()){
                if(isset($_POST['sef_box'][0]))
                    $sef=$_POST['sef_box'][0];
                else 
                    $sef="";
                    
                $_POST['index']=0;
                $_POST['type']=$type;
                if(strlen($sef)>50)$more="...";else $more="";
                
                $sef_scheme="<div id=\"sef_scheme_0\">";
                $sef_scheme.="<strong>Permalink:</strong> 
                        	  http://".site_url()."/".$_POST['type']."/category/
                        	  <span id=\"the_sef_".$_POST['index']."\">
                    			  <span id=\"the_sef_content_".$_POST['index']."\"  style=\"background:#FFCC66;cursor:pointer;\">".
                                      substr($sef,0,50).$more.
                                  "
                                  </span>
                                  .html
                                  <input type=\"button\" value=\"Edit\" id=\"edit_sef_".$_POST['index']."\" class=\"button_bold\">
                              </span>
                              <span id=\"sef_box_".$_POST['index']."\" style=\"display:none;\">
                              <span>
                              	<input type=\"text\" name=\"sef_box[".$_POST['index']."]\" value=\"".$sef."\" style=\"border:1px solid #CCC;width:300px;font-size:11px;\" />
                              	.html <input type=\"button\" value=\"Done\" id=\"done_edit_sef_".$_POST['index']."\" class=\"button_bold\">
                              </span>
                              </span>
                              
                              <script type=\"text/javascript\">
                              		$('#the_sef_".$_POST['index']."').click(function(){
                              			$('#the_sef_".$_POST['index']."').hide();
                              			$('#sef_box_".$_POST['index']."').show();
                              			
                    				});
                    				$('#edit_sef_".$_POST['index']."').click(function(){
                              			$('#the_sef_".$_POST['index']."').hide();
                              			$('#sef_box_".$_POST['index']."').show();
                    				});
                    				$('#done_edit_sef_".$_POST['index']."').click(function(){
                    					                   					
                    					var new_sef=$('input[name=sef_box[".$_POST['index']."]]').val();
                    					
                    					if(new_sef.length>50)
                    						var more='...'
                    					else
                        					var more='';
                        					
                    					
                              			$('#the_sef_".$_POST['index']."').show();
                              			$('#sef_box_".$_POST['index']."').hide();
                              			$.post('articles.php',
                              			{ 'update_sef' 	: 'true',
             							  'post_id' 	: ".$post_id.",
             							  'type' 		: '".$type."',
             							  'title' 		: $('input[name=title[0]]').val(),
             							  'new_sef'	 	: new_sef },
                              			function(theResponse){
                              				if(theResponse=='BAD'){
                    							$('input[name=sef_box[".$_POST['index']."]]').val('".$sef."');
                    							$('#the_sef_content_".$_POST['index']."').html('".substr($sef, 0,50)."');
                    						}else if(theResponse=='OK'){
                    							$('#the_sef_content_".$_POST['index']."').html(new_sef.substr(0,50)+more);
             								}
             							});
             							
             							
                    				});
                              </script>"; 
                    $sef_scheme.="</div>";
            //}
            
            add_variable('textarea',textarea('post[0]',0,rem_slashes($_POST['post'][0]),$post_id));
            add_variable('title',rem_slashes($_POST['title'][0]));
            add_variable('is_saved',"<input type=\"hidden\" name=\"article_saved\" value=\"1\" />");
        }else{
            add_variable('textarea',textarea('post[0]',0));
            add_variable('title','');
            
            /*
                Configure Categories And Tags
                all_categories($post_id,$index) are defined at taxonomy.php
                $post_id is the article ID and $index is the index array of the post.
                The $index value will be "0" if add new and numberic if for edit proccess
            */
            
            //Categories
            add_variable('all_categories',all_categories(0,'categories',$type));
            add_variable('most_used_categories',get_most_used_categories($type));
            if(is_editor() || is_administrator()){
                add_variable('add_new_category',article_new_category(0,'categories',$type));
            }
            
            //Tags
            add_variable('all_tags',get_post_tags($post_id,0,$type));
            add_variable('most_used_tags',get_most_used_tags($type));
            add_variable('add_new_tag',add_new_tag($post_id,0));
            
            //Get The Permalink
            
             //if(is_permalink()){
                $sef_scheme="<div id=\"sef_scheme_0\"></div>";
                $sef_scheme.="<script type=\"text/javascript\">
                					$(function(){
                						$('input[name=title[0]]').blur(function(){
                							$.post('articles.php',
                							{ 'get_sef' : 'true',
                							   'type'	: '".$type."',
                							   'index'	: 0,
                							   'title'	: $(this).val() 
            								},function(theResponse){
            									$('#sef_scheme_0').html(theResponse);
            								});
                							
            							});
            						});
                			   </script>"; 
            //}
        }
        
       
        add_variable('i',0);
        add_variable('post_id',$post_id);
        add_variable('group',$type);

        add_variable("sef_scheme", $sef_scheme);
        add_variable('comments_settings',additional_data("Comments","discussion_settings",false,$args));
        add_variable('share_to', additional_data("Share To", "article_share_option", true,$args));
        add_variable('application_additional_data',attemp_actions($type.'_additional_filed'));
        add_variable('additional_data',attemp_actions('articles_additional_data'));
        add_variable('button',$button);
        parse_template('loopArticle','lArticle',false);
         
        return return_article_template();
        
    }
    
    function edit_page($post_id=0){
        global $thepost;
        $index=0;
        $button="";
        $sef_scheme="";
        
        set_page_template();
        
        if(!is_contributor())
            $button.="<li>".button("button=add_new",get_state_url('pages')."&prc=add_new")."</li>
            <li>".button("button=save_draft")."</li>
            <li>".button("button=publish")."</li>
            <li>".button("button=cancel",get_state_url('pages'))."</li>";
                        
        else
            $button.="<li>".button("button=add_new",get_state_url('pages')."&prc=add_new")."</li>
            <li>".button("button=save_draft&label=Save")."</li>
            <li>".button("button=cancel",get_state_url('pages'))."</li>";
        
        //set the page Title
        add_actions('section_title','Pages - Edit');
        
        if(is_edit_all()){
            foreach($_POST['select'] as $index=>$post_id){
                $thepost->post_id=$post_id;
                $thepost->post_index=$index;
                if(is_save_draft() || is_publish()){
                    add_variable('textarea',textarea('post['.$index.']',$index,rem_slashes($_POST['post'][$index]),$post_id));
                    add_variable('title',rem_slashes($_POST['title'][$index]));
                }else{
                    $data_articles=fetch_artciles("id=".$post_id);
                    add_variable('textarea',textarea('post['.$index.']',$index,rem_slashes($data_articles['larticle_content']),$post_id));
                    add_variable('title',rem_slashes($data_articles['larticle_title']));
                }
                
                $args=array($index,$post_id);
                
                //Get The Permalink
                if(is_permalink()){
                    if(isset($_POST['sef_box'][$index]))
                        $sef=$_POST['sef_box'][$index];
                    else 
                        $sef=$data_articles['lsef'];
                        
                    $_POST['index']=$index;
                   
                    if(strlen($sef)>50)$more="...";else $more="";
                    
                    $sef_scheme="<div id=\"sef_scheme_0\">";
                    $sef_scheme.="<strong>Permalink:</strong> 
                            	  http://".site_url()."/
                            	  <span id=\"the_sef_".$_POST['index']."\">
                        			  <span id=\"the_sef_content_".$_POST['index']."\"  style=\"background:#FFCC66;cursor:pointer;\">".
                                          substr($sef,0,50).$more.
                                      "
                                      </span>
                                      /
                                      <input type=\"button\" value=\"Edit\" id=\"edit_sef_".$_POST['index']."\" class=\"button_bold\">
                                  </span>
                                  <span id=\"sef_box_".$_POST['index']."\" style=\"display:none;\">
                                  <span>
                                  	<input type=\"text\" name=\"sef_box[".$_POST['index']."]\" value=\"".$sef."\" style=\"border:1px solid #CCC;width:300px;font-size:11px;\" /> /
                                  	<input type=\"button\" value=\"Done\" id=\"done_edit_sef_".$_POST['index']."\" class=\"button_bold\">
                                  </span>
                                  </span>
                                  
                                  <script type=\"text/javascript\">
                                  		$('#the_sef_".$_POST['index']."').click(function(){
                                  			$('#the_sef_".$_POST['index']."').hide();
                                  			$('#sef_box_".$_POST['index']."').show();
                                  			
                        				});
                        				$('#edit_sef_".$_POST['index']."').click(function(){
                                  			$('#the_sef_".$_POST['index']."').hide();
                                  			$('#sef_box_".$_POST['index']."').show();
                        				});
                        				$('#done_edit_sef_".$_POST['index']."').click(function(){
                        					                   						
                        					var new_sef=$('input[name=sef_box[".$_POST['index']."]]').val();
                        					if(new_sef.length>50)
                        						var more='...'
                        					else
                        						var more='';
                        					
                                  			$('#the_sef_".$_POST['index']."').show();
                                  			$('#sef_box_".$_POST['index']."').hide();
                                  			$.post('articles.php',
                                  			{ 'update_sef' 	: 'true',
                 							  'post_id' 	: ".$post_id.",
                 							  'type' 		: 'pages',
                 							  'title' 		: $('input[name=title[".$index."]]').val(),
                 							  'new_sef'	 	: new_sef },
                                  			function(theResponse){
                                  				if(theResponse=='BAD'){
	                    							$('input[name=sef_box[".$_POST['index']."]]').val('".$sef."');
	                    							$('#the_sef_content_".$_POST['index']."').html('".substr($sef, 0,50)."');
	                    						}else if(theResponse=='OK'){
	                    							$('#the_sef_content_".$_POST['index']."').html(new_sef.substr(0,50)+more);
	             								}
                 							});
                 							
                 							
                        				});
                                  </script>"; 
                        $sef_scheme.="</div>";
                }
                add_variable("sef_scheme", $sef_scheme);
                
                add_variable('share_to', additional_data("Share To", "article_share_option", true,$args));
                add_variable('comments_settings',additional_data("Comments","discussion_settings",false,$args));
                add_variable('i',$index);
                add_variable('is_edit_all',"<input type=\"hidden\" name=\"edit\" value=\"Edit\">");
                
                add_variable('additional_data',attemp_actions('page_additional_data_'.$index));
                parse_template('loopPage','lPage',true);
            }
           
        }else{
                $thepost->post_id=$post_id;
                $thepost->post_index=$index;
                if(is_save_draft() || is_publish()){
                    add_variable('textarea',textarea('post['.$index.']',$index,rem_slashes($_POST['post'][$index]),$post_id));
                    add_variable('title',rem_slashes($_POST['title'][$index]));
                }else{
                    $data_articles=fetch_artciles("id=".$post_id);
                    add_variable('textarea',textarea('post['.$index.']',$index,rem_slashes($data_articles['larticle_content']),$post_id));
                    add_variable('title',rem_slashes($data_articles['larticle_title']));
                    
                }
                
                $args=array($index,$post_id);
                
                //Get The Permalink
                if(is_permalink()){
                    if(isset($_POST['sef_box'][$index]))
                        $sef=$_POST['sef_box'][$index];
                    else 
                        $sef=$data_articles['lsef'];
                        
                    $_POST['index']=$index;
                   
                    if(strlen($sef)>50)$more="...";else $more="";
                    
                    $sef_scheme="<div id=\"sef_scheme_0\">";
                    $sef_scheme.="<strong>Permalink:</strong> 
                            	  http://".site_url()."/
                            	  <span id=\"the_sef_".$_POST['index']."\">
                        			  <span id=\"the_sef_content_".$_POST['index']."\"  style=\"background:#FFCC66;cursor:pointer;\">".
                                          substr($sef,0,50).$more.
                                      "
                                      </span>
                                      /
                                      <input type=\"button\" value=\"Edit\" id=\"edit_sef_".$_POST['index']."\" class=\"button_bold\">
                                  </span>
                                  <span id=\"sef_box_".$_POST['index']."\" style=\"display:none;\">
                                  <span>
                                  	<input type=\"text\" name=\"sef_box[".$_POST['index']."]\" value=\"".$sef."\" style=\"border:1px solid #CCC;width:300px;font-size:11px;\" /> /
                                  	<input type=\"button\" value=\"Done\" id=\"done_edit_sef_".$_POST['index']."\" class=\"button_bold\">
                                  </span>
                                  </span>
                                  
                                  <script type=\"text/javascript\">
                                  		$('#the_sef_".$_POST['index']."').click(function(){
                                  			$('#the_sef_".$_POST['index']."').hide();
                                  			$('#sef_box_".$_POST['index']."').show();
                                  			
                        				});
                        				$('#edit_sef_".$_POST['index']."').click(function(){
                                  			$('#the_sef_".$_POST['index']."').hide();
                                  			$('#sef_box_".$_POST['index']."').show();
                        				});
                        				$('#done_edit_sef_".$_POST['index']."').click(function(){
                        					                   						
                        					var new_sef=$('input[name=sef_box[".$_POST['index']."]]').val();
                        					if(new_sef.length>50)
                        						var more='...'
                        					else
                        						var more='';
                        					
                                  			$('#the_sef_".$_POST['index']."').show();
                                  			$('#sef_box_".$_POST['index']."').hide();
                                  			$.post('articles.php',
                                  			{ 'update_sef' 	: 'true',
                 							  'post_id' 	: ".$post_id.",
                 							  'type' 		: 'pages',
                 							  'title' 		: $('input[name=title[".$index."]]').val(),
                 							  'new_sef'	 	: new_sef },
                                  			function(theResponse){
                                  				if(theResponse=='BAD'){
	                    							$('input[name=sef_box[".$_POST['index']."]]').val('".$sef."');
	                    							$('#the_sef_content_".$_POST['index']."').html('".substr($sef, 0,50)."');
	                    						}else if(theResponse=='OK'){
	                    							$('#the_sef_content_".$_POST['index']."').html(new_sef.substr(0,50)+more);
	             								}
                 							});
                 							
                 							
                        				});
                                  </script>"; 
                        $sef_scheme.="</div>";
                }
                add_variable("sef_scheme", $sef_scheme);
                
                add_variable('share_to', additional_data("Share To", "article_share_option", true,$args));
                add_variable('comments_settings',additional_data("Comments","discussion_settings",false,$args));
                add_variable('i',$index);
                
                add_variable('additional_data',attemp_actions('page_additional_data'));
                parse_template('loopPage','lPage',false);
           
        }
        
       
        add_variable('button',$button);
        return return_page_template();
    }
    
    function edit_article($post_id=0,$type,$title){
        global $thepost;
        $sef_scheme="";
        
        $index=0;
        $button="";
        set_article_template();
        
        if(is_admin_application())
            $url=get_application_url($type);
        else
            $url=get_state_url($type);
        
        $thetitle=explode("|",$title);
        if(isset($_POST['select']) && count($_POST['select'])>1){
            if(count($thetitle)==2)
                $thetitle=$thetitle[1];
            else
                $thetitle=$thetitle[0];
        }else{
            $thetitle=$thetitle[0];
        }
        
        
        if(!is_contributor())
            $button.="<li>".button("button=add_new",$url."&prc=add_new")."</li>
            <li>".button("button=save_draft")."</li>
            <li>".button("button=publish")."</li>
            <li>".button("button=cancel",$url)."</li>";
                        
        else
            $button.="<li>".button("button=add_new",$url."&prc=add_new")."</li>
            <li>".button("button=save_draft&label=Save")."</li>
            <li>".button("button=cancel",$url)."</li>";
        
        //set the page Title
        add_actions('section_title',$thetitle.' - Edit');
        add_variable('app_title',"Edit ".$thetitle);
        
        //Is multiple edit
        if(is_edit_all()){
            foreach($_POST['select'] as $index=>$post_id){
                $thepost->post_id=$post_id;
                $thepost->post_index=$index;
                
                $data_articles=fetch_artciles("id=".$post_id."&type=".$type);
                add_variable('textarea',textarea('post['.$index.']',$index,rem_slashes($data_articles['larticle_content']),$post_id));
                add_variable('title',rem_slashes($data_articles['larticle_title']));
                
                /*
                Configure Categories And Tags
                all_categories($post_id,$index) are defined at taxonomy.php
                $post_id is the article ID and $index is the index array of the post.
                The $index value will be "0" if add new and numberic if for edit proccess
                */
                
                //Categories
                $selected_categories=find_selected_rules($post_id,'categories',$type);
               
                add_variable('all_categories',all_categories($index,'categories',$type,$selected_categories));
                add_variable('most_used_categories',get_most_used_categories($type,$index,$selected_categories));
                if(is_editor() || is_administrator()){
                    add_variable('add_new_category',article_new_category($index,'categories',$type));
                }
                
                //Tags
                add_variable('all_tags',get_post_tags($post_id,$index,$type));
                add_variable('most_used_tags',get_most_used_tags($type,$index));
                add_variable('add_new_tag',add_new_tag($post_id,$index));
             
                //Get The Permalink
                if(is_permalink()){
                    if(isset($_POST['sef_box'][$index]))
                        $sef=$_POST['sef_box'][$index];
                    else 
                        $sef=$data_articles['lsef'];
                        
                    $_POST['index']=$index;
                    $_POST['type']=$type;
                    if(strlen($sef)>50)$more="...";else $more="";
                    
                    $sef_scheme="<div id=\"sef_scheme_0\">";
                    $sef_scheme.="<strong>Permalink:</strong> 
                            	  http://".site_url()."/".$_POST['type']."/category/
                            	  <span id=\"the_sef_".$_POST['index']."\">
                        			  <span id=\"the_sef_content_".$_POST['index']."\"  style=\"background:#FFCC66;cursor:pointer;\">".
                                          substr($sef,0,50).$more.
                                      "
                                      </span>
                                      .html
                                      <input type=\"button\" value=\"Edit\" id=\"edit_sef_".$_POST['index']."\" class=\"button_bold\">
                                  </span>
                                  <span id=\"sef_box_".$_POST['index']."\" style=\"display:none;\">
                                  <span>
                                  	<input type=\"text\" name=\"sef_box[".$_POST['index']."]\" value=\"".$sef."\" style=\"border:1px solid #CCC;width:300px;font-size:11px;\" />
                                  	.html <input type=\"button\" value=\"Done\" id=\"done_edit_sef_".$_POST['index']."\" class=\"button_bold\">
                                  </span>
                                  </span>
                                  
                                  <script type=\"text/javascript\">
                                  		$('#the_sef_".$_POST['index']."').click(function(){
                                  			$('#the_sef_".$_POST['index']."').hide();
                                  			$('#sef_box_".$_POST['index']."').show();
                                  			
                        				});
                        				$('#edit_sef_".$_POST['index']."').click(function(){
                                  			$('#the_sef_".$_POST['index']."').hide();
                                  			$('#sef_box_".$_POST['index']."').show();
                        				});
                        				$('#done_edit_sef_".$_POST['index']."').click(function(){
                        					
                        					var new_sef=$('input[name=sef_box[".$_POST['index']."]]').val();
                        					if(new_sef.length>50)
                        						var more='...'
                        					else
                        						var more='';
                        					
                                  			$('#the_sef_".$_POST['index']."').show();
                                  			$('#sef_box_".$_POST['index']."').hide();
                                  			$.post('articles.php',
                                  			{ 'update_sef' 	: 'true',
                 							  'post_id' 	: ".$post_id.",
                 							  'type' 		: '".$type."',
                 							  'title' 		: $('input[name=title[".$index."]]').val(),
                 							  'new_sef'	 	: new_sef },
                                  			function(theResponse){
                                  				if(theResponse=='BAD'){
	                    							$('input[name=sef_box[".$_POST['index']."]]').val('".$sef."');
	                    							$('#the_sef_content_".$_POST['index']."').html('".substr($sef, 0,50)."');
	                    						}else if(theResponse=='OK'){
	                    							$('#the_sef_content_".$_POST['index']."').html(new_sef.substr(0,50)+more);
	             								}
                 							});
                 							
                 							
                        				});
                                  </script>"; 
                        $sef_scheme.="</div>";
                }
                add_variable("sef_scheme", $sef_scheme);
                $args=array($index,$post_id,$type);
                add_variable('share_to', additional_data("Share To", "article_share_option", true,$args));
                add_variable('comments_settings',additional_data("Comments","discussion_settings",false,$args));
                add_variable('i',$index);
                add_variable('is_edit_all',"<input type=\"hidden\" name=\"edit\" value=\"Edit\">");
                add_variable('application_additional_data',attemp_actions($type.'_additional_filed_'.$index));
                add_variable('additional_data',attemp_actions('articles_additional_data_'.$index));
                parse_template('loopArticle','lArticle',true);
            }
        
        }else{
        //Is single edit    
                $thepost->post_id=$post_id;
                $thepost->post_index=$index;
                if(is_save_draft() || is_publish()){
                    add_variable('textarea',textarea('post['.$index.']',$index,rem_slashes($_POST['post'][$index]),$post_id));
                    add_variable('title',rem_slashes($_POST['title'][$index]));
                    
                    //Selected Categories
                    if(isset($_POST['category'][$index])){
                        $selected_categories=$_POST['category'][$index];
                    }else{
                        $selected_categories=array(1);
                    }
                    
                }else{
                    $data_articles=fetch_artciles("id=".$post_id."&type=".$type);
                    add_variable('textarea',textarea('post['.$index.']',$index,rem_slashes($data_articles['larticle_content']),$post_id));
                    add_variable('title',rem_slashes($data_articles['larticle_title']));
                                        
                    //Selected Categories
                    $selected_categories=find_selected_rules($post_id,'categories',$type);
                    
                 
                }
                
                /*
                Configure Categories And Tags
                all_categories($post_id,$index) are defined at taxonomy.php
                $post_id is the article ID and $index is the index array of the post.
                The $index value will be "0" if add new and numberic if for edit proccess
                */
                
                //Categories
                add_variable('all_categories',all_categories($index,'categories',$type,$selected_categories));
                add_variable('most_used_categories',get_most_used_categories($type,$index,$selected_categories));
                if(is_editor() || is_administrator()){
                    add_variable('add_new_category',article_new_category($index,'categories',$type));
                }
                
                //Tags
                add_variable('all_tags',get_post_tags($post_id,$index,$type));
                add_variable('most_used_tags',get_most_used_tags($type,$index));
                add_variable('add_new_tag',add_new_tag($post_id,$index));
                
                
                //Get The Permalink
                if(is_permalink()){
                    if(isset($_POST['sef_box'][$index]))
                        $sef=$_POST['sef_box'][$index];
                    else 
                        $sef=$data_articles['lsef'];
                        
                    $_POST['index']=$index;
                    $_POST['type']=$type;
                    if(strlen($sef)>50)$more="...";else $more="";
                    
                    $sef_scheme="<div id=\"sef_scheme_0\">";
                    $sef_scheme.="<strong>Permalink:</strong> 
                            	  http://".site_url()."/".$_POST['type']."/category/
                            	  <span id=\"the_sef_".$_POST['index']."\">
                        			  <span id=\"the_sef_content_".$_POST['index']."\"  style=\"background:#FFCC66;cursor:pointer;\">".
                                          substr($sef,0,50).$more.
                                      "
                                      </span>
                                      .html
                                      <input type=\"button\" value=\"Edit\" id=\"edit_sef_".$_POST['index']."\" class=\"button_bold\">
                                  </span>
                                  <span id=\"sef_box_".$_POST['index']."\" style=\"display:none;\">
                                  <span>
                                  	<input type=\"text\" name=\"sef_box[".$_POST['index']."]\" value=\"".$sef."\" style=\"border:1px solid #CCC;width:300px;font-size:11px;\" />
                                  	.html <input type=\"button\" value=\"Done\" id=\"done_edit_sef_".$_POST['index']."\" class=\"button_bold\">
                                  </span>
                                  </span>
                                  
                                  <script type=\"text/javascript\">
                                  		$('#the_sef_".$_POST['index']."').click(function(){
                                  			$('#the_sef_".$_POST['index']."').hide();
                                  			$('#sef_box_".$_POST['index']."').show();
                                  			
                        				});
                        				$('#edit_sef_".$_POST['index']."').click(function(){
                                  			$('#the_sef_".$_POST['index']."').hide();
                                  			$('#sef_box_".$_POST['index']."').show();
                        				});
                        				$('#done_edit_sef_".$_POST['index']."').click(function(){
                        					
                        					var new_sef=$('input[name=sef_box[".$_POST['index']."]]').val();
                        					if(new_sef.length>50)
                        						var more='...'
                        					else
                        						var more='';
                        						
                        					
                                  			$('#the_sef_".$_POST['index']."').show();
                                  			$('#sef_box_".$_POST['index']."').hide();
                                  			$.post('articles.php',
                                  			{ 'update_sef' 	: 'true',
                 							  'post_id' 	: ".$post_id.",
                 							  'type' 		: '".$type."',
                 							  'title' 		: $('input[name=title[".$index."]]').val(),
                 							  'new_sef'	 	: new_sef },
                                  			function(theResponse){
                                  				if(theResponse=='BAD'){
	                    							$('input[name=sef_box[".$_POST['index']."]]').val('".$sef."');
	                    							$('#the_sef_content_".$_POST['index']."').html('".substr($sef, 0,50)."');
	                    						}else if(theResponse=='OK'){
	                    							$('#the_sef_content_".$_POST['index']."').html(new_sef.substr(0,50)+more);
	             								}
                 							});
                        				});
                                  </script>"; 
                        $sef_scheme.="</div>";
                }
                add_variable("sef_scheme", $sef_scheme);
                
                $args=array($index,$post_id,$type);
                
                add_variable('share_to', additional_data("Share To", "article_share_option", true,$args));
                add_variable('comments_settings',additional_data("Comments","discussion_settings",false,$args));
                add_variable('i',$index);
                add_variable('application_additional_data',attemp_actions($type.'_additional_filed'));
                add_variable('additional_data',attemp_actions('articles_additional_data'));
                parse_template('loopArticle','lArticle',false);
           
        }
        
        add_variable('group',$type);
        add_variable('button',$button);
        return return_article_template();
    }
    
    function additional_data($name,$tag,$display=true,$args=array()){
        global $thepost;
        if(function_exists($tag)){
            $details=call_user_func_array($tag,$args);
        }else{
            $details=$tag;
        }
        
        if($display==false)
            $display="style=\"display:none;\"";
        else
            $display="";
            
        $data="<div class=\"additional_data\">
                    <h2 id=\"".$tag."_".$thepost->post_index."\">$name</h2>
                    <div class=\"additional_content\" id=\"".$tag."_details_".$thepost->post_index."\" $display >
                       $details
                    </div>
                </div>
                <script type=\"text/javascript\">
                    $(function(){
                        $('#".$tag."_".$thepost->post_index."').click(function(){
                            $('#".$tag."_details_".$thepost->post_index."').slideToggle(100);
                            return false;
                        });
                    });
                </script>
                ";
        return $data;
        
    }
    function discussion_settings($i,$post_id,$type='pages'){
           
        $result="";
        if(is_save_draft() || is_publish()){
            if(isset($_POST['allow_comments'][$i]))
                $result="<input type=\"checkbox\" name=\"allow_comments[$i]\" checked=\"checked\" value=\"allow\" />Allow Comments";
            else
                $result="<input type=\"checkbox\" name=\"allow_comments[$i]\"  />Allow Comments";
        }else{
            if(is_edit() || is_edit_all()){
                $d=fetch_artciles("id=".$post_id."&type=".$type);
                if($d['lcomment_status']=='allowed'){
                    $result="<input type=\"checkbox\" name=\"allow_comments[$i]\" checked=\"checked\" value=\"allow\" />Allow Comments";
                }else{
                    $result="<input type=\"checkbox\" name=\"allow_comments[$i]\" value=\"allow\" />Allow Comments";
                }
            }else{
                if(get_meta_data('is_allow_comment')==1)
                $result="<input type=\"checkbox\" name=\"allow_comments[$i]\" checked=\"checked\" value=\"allow\" />Allow Comments";
                else
                $result="<input type=\"checkbox\" name=\"allow_comments[$i]\"  value=\"allow\" />Allow Comments";
            }
        }
        return $result;
    }
    function article_share_option($i,$post_id,$type='pages'){
        $result="";
        $myFriendList=get_friend_list($_COOKIE['user_id']);
        
        if(is_save_draft() || is_publish()){
            
            if($_POST['share_option'][$i]==0)
                $result.="<input type=\"radio\" name=\"share_option[$i]\" checked=\"checked\" value=\"0\" />Everyone";
            else 
                 $result.="<input type=\"radio\" name=\"share_option[$i]\" value=\"0\" />Everyone";
                 
            if(count($myFriendList) > 0 && count($myFriendList['friends_list_id'])>0)
                foreach ($myFriendList['friends_list_id'] as $key=>$val){
                    if(isset($_POST['share_option'][$i]) && $_POST['share_option'][$i]==$myFriendList['friends_list_id'][$key])
                        $result.="<input type=\"radio\" name=\"share_option[$i]\" checked=\"checked\" value=\"".$myFriendList['friends_list_id'][$key]."\" />".$myFriendList['list_name'][$key];
                    else
                        $result.="<input type=\"radio\" name=\"share_option[$i]\" value=\"".$myFriendList['friends_list_id'][$key]."\" />".$myFriendList['list_name'][$key];        
                }
            
        }else{
            if(is_edit() || is_edit_all()){
                $d=fetch_artciles("id=".$post_id."&type=".$type);
                
                if($d['lshare_to']==0)
                    $result.="<input type=\"radio\" name=\"share_option[$i]\" checked=\"checked\" value=\"0\" />Everyone";
                else 
                    $result.="<input type=\"radio\" name=\"share_option[$i]\" value=\"0\" />Everyone";
                    
                if(count($myFriendList) > 0 && count($myFriendList['friends_list_id'])>0)
                    foreach ($myFriendList['friends_list_id'] as $key=>$val){
                        if($d['lshare_to']==$myFriendList['friends_list_id'][$key]){
                            $result.="<input type=\"radio\" name=\"share_option[$i]\" checked=\"checked\" value=\"".$myFriendList['friends_list_id'][$key]."\" />".$myFriendList['list_name'][$key];
                        }else{
                            $result.="<input type=\"radio\" name=\"share_option[$i]\" value=\"".$myFriendList['friends_list_id'][$key]."\" />".$myFriendList['list_name'][$key];
                        }
                    }
            }else{
                
                $result.="<input type=\"radio\" name=\"share_option[$i]\" checked=\"checked\" value=\"0\" />Everyone";
                if(count($myFriendList) > 0 && count($myFriendList['friends_list_id'])>0){
                    foreach ($myFriendList['friends_list_id'] as $key=>$val){
                        $result.="<input type=\"radio\" name=\"share_option[$i]\" value=\"".$myFriendList['friends_list_id'][$key]."\" />".$myFriendList['list_name'][$key];
                    }
                }
            }
        }
        return $result;
    }
    
    function save_article($title,$content,$status,$type,$comments,$sef='',$share_to=0){
       global $db,$allowedposttags,$allowedtitletags;
       
        if(empty($title)){
           //$num_untitled=is_num_articles('title=Untitled&type='.$type)+1;
           $title="Untitled";
        }else{
            $title=kses(rem_slashes($title),$allowedtitletags);
        }
        $content=kses(rem_slashes($content),$allowedposttags);
        
        /*Formulating the SEF URL.
         First count the number of same title and applications at article table.
         If same title found then add numberic after the SEF created.
         If there are no same title, then use the original SEF
        */
        
        if(empty($sef)){
            $num_by_title_and_type=is_num_articles('title='.$title.'&type='.$type);
            if($num_by_title_and_type>0){
                for($i=2;$i<=$num_by_title_and_type+1;$i++){
                	$sef=generateSefUrl($title)."-".$i;
                	if(is_num_articles('sef='.$sef.'&type='.$type) < 1){
                		$sef=$sef;
                		break;
                	}
                }
            }else{
                $sef=generateSefUrl($title);
            }
        }
        

       $sql=$db->prepare_query("INSERT INTO lumonata_articles(larticle_title,
                                                                larticle_content,
                                                                larticle_status,
                                                                larticle_type,
                                                                lcomment_status,
                                                                lsef,
                                                                lpost_by,
                                                                lpost_date,
                                                                lupdated_by,
                                                                ldlu,
                                                                lshare_to)
                                VALUES(%s,%s,%s,%s,%s,%s,%d,%s,%d,%s,%d)",
                                                                $title,
                                                                $content,
                                                                $status,
                                                                $type,
                                                                $comments,
                                                                $sef,
                                                                $_COOKIE['user_id'],
                                                                date("Y-m-d H:i:s"),
                                                                $_COOKIE['user_id'],
                                                                date("Y-m-d H:i:s"),
                                                                $share_to);
       
        if(reset_order_id("lumonata_articles"))
            return $db->do_query($sql);
        
        return false;
    }
    function attachment_sync($old_id,$new_id){
        global $db;
        $sql=$db->prepare_query("UPDATE lumonata_attachment
                SET larticle_id=%d
                WHERE larticle_id=%d",
                $new_id,$old_id);
        return $db->do_query($sql);
    }
    function update_article($post_id,$title,$content,$status,$type,$comments,$share_to=0){
        global $db,$allowedposttags,$allowedtitletags;
        
        if(empty($title)){
           $title="Untitled";
        }else{
            $title=kses(rem_slashes($title),$allowedtitletags);
        }
        $content=kses(rem_slashes($content),$allowedposttags);
        
        $sql=$db->prepare_query("UPDATE lumonata_articles
                                 SET larticle_title=%s,
                                    larticle_content=%s,
                                    larticle_status=%s,
                                    larticle_type=%s,
                                    lcomment_status=%s,
                                    lupdated_by=%s,
                                    ldlu=%s,
                                    lshare_to=%d
                                 WHERE larticle_id=%d",
                                    $title,
                                    $content,
                                    $status,
                                    $type,
                                    $comments,
                                    $_COOKIE['user_id'],
                                    date("Y-m-d H:i:s"),
                                    $share_to,
                                    $post_id);
        
        return $db->do_query($sql);
    }
    function update_sef($post_id,$title,$sef,$type){
        global $db,$allowedposttags,$allowedtitletags;
   		$update=false;
   		
        if(empty($title)){
           $title="Untitled";
        }else{
           $title=kses(rem_slashes($title),$allowedtitletags);
        }
        
    	if(empty($sef)){
	    	$update=false;
    	}else{
    		$num=is_num_articles('sef='.$sef.'&type='.$type);
    		if($num>0){
	        	$update=false;
	        }else{
	        	$sef=generateSefUrl($sef);
	        	$update=true;
	        }
    		
    	}
    	
    	
        
        if($update){
	        $sef=kses(rem_slashes($sef),$allowedtitletags);
	        $sql=$db->prepare_query("UPDATE lumonata_articles
	                                 SET lsef=%s
	                                 WHERE larticle_id=%d",
	                                    $sef,
	                                    $post_id);
	        
	        return $db->do_query($sql);
        }
        
        return false;
    }
    function delete_article($artilce_id,$app_name){
        global $db;
        if(delete_additional_field($artilce_id,$app_name)){
            $d=fetch_rule_relationship("app_id=".$artilce_id);
            if(delete_rules_relationship("app_id=".$artilce_id)){
                if(is_array($d)){
                    foreach($d as $key=>$val) {
                        $rule_count=count_rules_relationship("rule_id=".$val);
                        update_rule_count($val,$rule_count);
                    }
                    $sql=$db->prepare_query("DELETE FROM lumonata_articles
                                     WHERE larticle_id=%d",
                                        $artilce_id);
                   
                    return $db->do_query($sql);
                }
            }
        }
    }
    function is_num_articles($args=''){
        global $db;
        $var_name['title']='';
        $var_name['id']='';
        $var_name['sef']='';
        $var_name['type']='pages';
        
        if(!empty($args)){
            $args=explode('&',$args);
            foreach($args as $val){
                list($variable,$value)=explode('=',$val);
                if($variable=='title' || $variable=='id' || $variable=='type' || $variable=='sef')
                $var_name[$variable]=$value;
                
            }
        }
        
        if($_COOKIE['user_type']=='contributor' || $_COOKIE['user_type']=='author'){
            $w=" lpost_by=".$_COOKIE['user_id']." AND ";    
        }else{
            $w="";
        }
        
        if(!empty($var_name['title']) && !empty($var_name['id'])){
            $sql=$db->prepare_query("SELECT * FROM lumonata_articles WHERE $w larticle_type=%s AND larticle_id=%d",$var_name['type'],$var_name['id']);
        }elseif(!empty($var_name['id']) && empty($var_name['title'])){
            $sql=$db->prepare_query("SELECT * FROM lumonata_articles WHERE $w larticle_type=%s AND larticle_id=%d",$var_name['type'],$var_name['id']);
        }elseif(!empty($var_name['title']) && empty($var_name['id'])){
            $sql=$db->prepare_query("SELECT * FROM lumonata_articles WHERE $w larticle_type=%s AND larticle_title=%s",$var_name['type'],$var_name['title']);
            
        }elseif(!empty($var_name['sef'])){
            $sql=$db->prepare_query("SELECT * FROM lumonata_articles WHERE $w larticle_type=%s AND lsef=%s",$var_name['type'],$var_name['sef']);
        }else{
            $sql=$db->prepare_query("SELECT * from lumonata_articles WHERE $w larticle_type=%s",$var_name['type']);    
        }
       
        $r=$db->do_query($sql);
        
        
        return $db->num_rows($r);
        
    }
    function is_have_articles($type='articles'){
        if(is_num_articles("type=$type")>0)
            return true;
        else
            return false;
    }
    function update_articles_order($order,$start,$app_name){
        global $db;
        foreach($order as $key=>$val){
            $sql=$db->prepare_query("UPDATE lumonata_articles
                                     SET lorder=%d,
                                     lupdated_by=%s,
                                     ldlu=%s
                                     WHERE larticle_id=%d AND larticle_type=%s",
                                     $key+$start,
                                     $_COOKIE['user_id'],
                                     date("Y-m-d H:i:s"),
                                     $val,
                                     $app_name);
            $db->do_query($sql);
          
            
        }
    }
    function update_articles_status($id,$status){
        global $db;
       
        $sql=$db->prepare_query("UPDATE lumonata_articles
                                 SET larticle_status=%s,
                                 lupdated_by=%s,
                                 ldlu=%s
                                 WHERE larticle_id=%d",
                                 $status,
                                 $_COOKIE['user_id'],
                                 date("Y-m-d H:i:s"),
                                 $id);
        $db->do_query($sql);
        
           
            
        
    }
    function fetch_artciles($args='',$fetch=true){
        global $db;
        $var_name['title']='';
        $var_name['id']='';
        $var_name['type']='pages';
        
        if(!empty($args)){
            $args=explode('&',$args);
            foreach($args as $val){
                list($variable,$value)=explode('=',$val);
                if($variable=='title' || $variable=='id' || $variable=='type')
                $var_name[$variable]=$value;
                
            }
        }
        $w="";
        if(isset($_COOKIE['user_type']) && isset($_COOKIE['user_type']))
        if($_COOKIE['user_type']=='contributor' || $_COOKIE['user_type']=='author'){
            $w=" lpost_by=".$_COOKIE['user_id']." AND ";    
        }else{
            $w="";
        }
        
        if(!empty($var_name['title']) && !empty($var_name['id'])){
            $sql=$db->prepare_query("SELECT * FROM lumonata_articles WHERE $w larticle_type=%s AND larticle_id=%d",$var_name['type'],$var_name['id']);
        }elseif(!empty($var_name['id']) && empty($var_name['title'])){
            $sql=$db->prepare_query("SELECT * FROM lumonata_articles WHERE $w larticle_type=%s AND larticle_id=%d",$var_name['type'],$var_name['id']);
        }elseif(!empty($var_name['title']) && empty($var_name['id'])){
            $sql=$db->prepare_query("SELECT * FROM lumonata_articles WHERE $w larticle_type=%s AND larticle_title=%d",$var_name['type'],$var_name['title']);
        }else{
        	$sql=$db->prepare_query("SELECT * from lumonata_articles WHERE $w larticle_type=%s",$var_name['type']);
        }
        
        $r=$db->do_query($sql);
        
        if($fetch==true)
            return $db->fetch_array($r);
        
        return $r;
    }
    
    function the_looping_articles($type='articles',$args=''){
        global $db;
         //set template
        set_template(TEMPLATE_PATH."/article-lists.html",'article');
        //set block
        
        add_block('loopArticle','lArticle','article');
        if(!empty($args)){
            $id=post_to_id();
            if($args=='category')
                $sql=$db->prepare_query("SELECT a.*
                                        FROM lumonata_articles a,lumonata_rule_relationship b
                                        WHERE a.larticle_type=%s AND a.larticle_status=%s
                                        AND b.lrule_id=%d AND a.larticle_id=b.lapp_id AND lshare_to=0
                                        ORDER BY a.lorder",$type,'publish',$id);
            else
                $sql=$db->prepare_query("SELECT a.*
                                        FROM lumonata_articles a,lumonata_rule_relationship b
                                        WHERE a.larticle_status=%s
                                        AND b.lrule_id=%d AND a.larticle_id=b.lapp_id AND lshare_to=0
                                        ORDER BY a.lorder",'publish',$id);
                
               
        }else{
            $sql=$db->prepare_query("SELECT * FROM lumonata_articles WHERE larticle_type=%s AND larticle_status=%s AND lshare_to=0 order by lorder",$type,'publish');
        }
       
        $r=$db->do_query($sql);
        $i=1;
        while($data=$db->fetch_array($r)){
            if( $i<=post_viewed() ){
            	add_variable('the_thumbs',the_attachment($data['larticle_id']));
                add_variable('artilce_link',permalink($data['larticle_id']));
                add_variable('the_title',$data['larticle_title']);
                add_variable('the_brief',filter_content($data['larticle_content'],true));
                add_variable('post_date',date(get_date_format(),strtotime($data['lpost_date'])));
                add_variable('the_user',the_user($data['lpost_by']));
                add_variable('the_categories',the_categories($data['larticle_id'],$data['larticle_type']));
                add_variable('the_tags',the_tags($data['larticle_id'],$data['larticle_type']));
                
                if(is_break_comment()){
                	add_variable('comments',comments($data['larticle_id'],$data['lcomment_status'],comment_per_page())); 
                }
                parse_template('loopArticle','lArticle',true);
                $i++;
            }else{
                break;
            }
            
        }
         
        return return_template('article');
    }
    function the_looping_categories($type='articles'){
        $rules=fetch_rule('rule_id='.post_to_id().'&group='.get_appname());
    	$return="<div class=\"category_name\"><h1>".$rules['lname']."</h1></div>";
        $return.=the_looping_articles($type,'category');
        return $return;
    }
    function the_looping_tags(){
        return the_looping_articles('article','tag');
    }
    function article_detail(){
        global $db;
        
         //set template
        set_template(TEMPLATE_PATH."/article-detail.html",'thearticle');
        //set block
       
        add_block('article','bArticle','thearticle');
        $sql=$db->prepare_query("SELECT * from lumonata_articles
                                WHERE larticle_id=%d",post_to_id());
        $r=$db->do_query($sql);
        $data=$db->fetch_array($r);
        $the_post=$data['larticle_content'];
        $the_post=filter_content($the_post);
        $the_post=str_replace("<p>[album_set]</p>", "<div class=\"album_set\">".the_attachment($data['larticle_id'],0)."<br clear=\"both\" /></div>", $the_post,$count_replace);
        $the_post=str_replace("[album_set]", "<div class=\"album_set\">".the_attachment($data['larticle_id'],0)."<br clear=\"both\" /></div>", $the_post,$count_replace2);
        
        if($count_replace>0 || $count_replace2>0){
          add_actions('header','get_css_inc','fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.css');
          add_actions('header','get_javascript_inc','fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.pack.js');
          add_actions('header','get_javascript_inc','fancybox-1.3.4/fancybox/fancybox.js');
        }
        
        add_variable('the_thumbs',the_attachment($data['larticle_id'],0));
        add_variable('the_title',$data['larticle_title']);
        add_variable('the_post',$the_post);
        add_variable('post_date',date(get_date_format(),strtotime($data['lpost_date'])));
        add_variable('the_user',the_user($data['lpost_by']));
        add_variable('the_categories',the_categories($data['larticle_id'],$data['larticle_type']));
        add_variable('the_tags',the_tags($data['larticle_id'],$data['larticle_type']));
        add_variable('comments',comments($data['larticle_id'],$data['lcomment_status'])); 
        add_variable('additional_article_plugins',attemp_actions('additional_article_plugins'));
        parse_template('article','bArticle',false);
         
        return return_template('thearticle');
    }
    function the_user($id){
        $the_user=fetch_user($id);
        return $the_user['ldisplay_name'];
    }
    function the_categories($post_id,$type){
        $the_selected_categories=find_selected_rules($post_id,'categories',$type);
        $the_categories=recursive_taxonomy(0,'categories',$type,'category',$the_selected_categories);
        return trim($the_categories,', ');
    }
    function the_tags($post_id,$type){
        $the_selected_tags=find_selected_rules($post_id,'tags',$type);
        $the_tags=recursive_taxonomy(0,'tags',$type,'tag',$the_selected_tags);
        return trim($the_tags,', ');
    }
    function filter_content($the_content,$splitit=false){
        //If found <!-- pagebreak --> on content, then split it
        if($splitit){
            $match=preg_split("/<!--\s+pagebreak\s+-->/",$the_content);
            $the_content=$match[0];
        }
        
        return $the_content;
    }
    function get_article_title($id){
    	global $db;
    	$query=$db->prepare_query("SELECT larticle_title 
    								FROM lumonata_articles
    								WHERE larticle_id=%d",$id);
    	$result=$db->do_query($query);
    	$dt=$db->fetch_array($result);
    	return $dt['larticle_title'];
    }
?>