<?php
	/**
	 * This function is called when you click the categories or tags tab on articles application     
	 *   
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param string $rule Rule name: categories or tags
	 * @param string $group The application name, by default is articles
	 * @param string $thetitle The Title that will shown on the admin area
	 * @param string $tabs The list tab
	 * 
	 * @return string
	 *      
	 */
    function get_admin_rule($rule,$group,$thetitle,$tabs,$subsite='arunna'){
        
        //Publish or Save Draft Actions
        if(is_contributor() || is_author())
            $tabs=$tabs[0];
        else
            $tabs=$tabs;
             
        /*
            Configure the tabs
            $the_tab is the selected tab
        */
        $tabb='';
        $tab_keys=array_keys($tabs);
        if(empty($_GET['tab']))
                $the_tab=$tab_keys[0];
        else
                $the_tab=$_GET['tab'];
        
        $tabs=set_tabs($tabs,$the_tab);
        add_variable('tab',$tabs);
        /*
            Filter the tabs. Articles tab can be accessed by all user type.
            But for Categories and Tags tabs only granted for Editor and Administrator.
        */
        
        if(is_publish()){
            //Hook actions defined
            run_actions($rule.'_save');
           
            if(is_add_new()){
                //Hook Add New Actions
                
                //save the taxonomy
                if($rule=="tags")
                    $parent=0;
                else
                    $parent=$_POST['parent'][0];
                    
                if(insert_rules($parent,$_POST['name'][0],$_POST['description'][0],$rule,$group,false,$subsite)){
                    $rule_id=mysql_insert_id();
                    
                    if(is_admin_application())
                        header("location:".get_application_url($group)."&tab=".$rule."&prc=add_new");
                    else    
                        header("location:".get_state_url($group)."&tab=".$rule."&prc=add_new");
                    }    
                
            }elseif(is_edit()){
                //Hook Single Edit Actions
                run_actions($rule.'_edit');
                
                if($rule=='tags')
                $_POST['parent'][0]=0;
                
                //Update the article
                update_rules($_POST['rule_id'][0],$_POST['parent'][0],$_POST['name'][0],$_POST['description'][0],$rule,$group,$subsite);
                
                
            }elseif(is_edit_all()){
                run_actions($rule.'_editall');
                //Update the articles
               
                foreach($_POST['rule_id'] as $index=>$value){
                    if($rule=='tags')
                    $_POST['parent'][$index]=0;
                    
                    update_rules($_POST['rule_id'][$index],$_POST['parent'][$index],$_POST['name'][$index],$_POST['description'][$index],$rule,$group,$subsite);
                }
                
                
            }
        }
       
       //Automatic to add new when there is no records on tag rule database
        if($rule=='tags')
            $count_cond='rule='.$rule;
        else
            $count_cond='rule='.$rule."&group=".$group;
            
        if(count_rules($count_cond)==0 && !isset($_GET['prc'])){
            if($rule=="tags"){
                 if(isset($_GET['state']) && $_GET['state']=='applications')
					header("location:".get_application_url($group)."&tab=tags&prc=add_new");                  
                 else    
                	header("location:".get_state_url($group)."&tab=tags&prc=add_new");
                    
            }elseif($rule=="categories"){
                if(count_rules('rule='.$rule."&group=default")==0)
                    insert_rules(0,"Uncategorized","","category",'default',true);
            }
        }
        
        //Is add new Rule, View the desain
        if(is_add_new()){
            return add_new_rule($rule,$group,$thetitle) ;
        }elseif(is_edit()){
            return edit_rule($rule,$group,$thetitle,$_GET['id']);
        }elseif(is_edit_all() && isset($_POST['select'])){
            return edit_rule($rule,$group,$thetitle);
        }elseif(is_delete_all()){
            $thetitle=explode("|",$thetitle);   
            $warning="<form action=\"\" method=\"post\">";
            if(count($_POST['select'])==1){
                    $rule=($rule=="categories")?"Category":"Tag";
                    add_actions('section_title','Delete '.$thetitle[0]." ".$rule);
                    $warning.="<div class=\"alert_red_form\"><strong>Are you sure want to delete this ".$thetitle[0]." ".$rule.":</strong>";
            }else{
                    $rule=($rule=="categories")?"Categories":"Tags";
                    add_actions('section_title','Delete '.$thetitle[1]." ".$rule);
                    $warning.="<div class=\"alert_red_form\"><strong>Are you sure want to delete these ".$thetitle[0]." ".$rule.":</strong>";
            }        
            $warning.="<ol>";	
            foreach($_POST['select'] as $key=>$val){
                    $d=count_rules("rule_id=".$val,false);
                    $warning.="<li>".$d['lname']."</li>";
                    $warning.="<input type=\"hidden\" name=\"id[]\" value=\"".$d['lrule_id']."\">";
            }
            $warning.="</ol></div>";
            $warning.="<div style=\"text-align:right;margin:10px 5px 0 0;\">";
            $warning.="<input type=\"submit\" name=\"confirm_delete\" value=\"Yes\" class=\"button\" />";
            if(is_admin_application())
            $warning.="<input type=\"button\" name=\"confirm_delete\" value=\"No\" class=\"button\" onclick=\"location='".get_application_url($group)."'\" />";
            else
            $warning.="<input type=\"button\" name=\"confirm_delete\" value=\"No\" class=\"button\" onclick=\"location='".get_state_url($group)."'\" />";
            $warning.="</div>";
            $warning.="</form>";
            return $warning;
        }elseif(is_confirm_delete()){
            foreach($_POST['id'] as $key=>$val){
                delete_rule($val);
            }
        }
        
        //Display Users Lists
        //if(count_rules('rule='.$rule."&group=".$group)>0){
                add_actions('header_elements','get_javascript','jquery_ui');
                add_actions('header_elements','get_javascript','articles_list');
                return get_rule_list($rule,$group,$thetitle,$tabs);
        //}
         
    }
    /**
	 * Set the rules tamplate in administrator    
	 *   
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * 
	 * @return void
	 *      
	 */
    function set_rule_template(){
        //set template
        set_template(TEMPLATE_PATH."/rules.html",'rules');
        //set block
        add_block('loopRule','lRule','rules');
        add_block('ruleAddNew','rAddNew','rules');
    }
     /**
	 * Return the rules tamplate in administrator    
	 *   
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * @param boolean $loop If template block is looping then set it true
	 * 
	 * @return string return rules template
	 *      
	 */
    function return_rule_template($loop=false){
        parse_template('ruleAddNew','rAddNew',$loop);
        return return_template('rules');
    }
    
    /**
	 * Call this function when you want to add new rules (Category or Tags)    
	 *   
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * @param string $rule The rule name. By default it cloud be "categories" or "tags" 
	 * @param string $group The application name. 
	 * @param string $title The rule title that will be shown in admin area 
	 * 
	 * @return string HTML design when you add new category or tags
	 *      
	 */
    function add_new_rule($rule,$group,$title){
        //$args=array($index=0,$post_id);
        set_rule_template();
        
        if(is_admin_application())
            $url=get_application_url($group)."&tab=".$rule;
        else
            $url=get_state_url($group)."&tab=".$rule;
        
        $thetitle=explode("|",$title);
        $thetitle=$thetitle[0];
        add_variable('app_title',"Add New ".$thetitle." ".ucwords($rule));
       
        $button="";
        if(!is_contributor() && !is_author()){
            $button.="<li>".button("button=add_new",$url."&prc=add_new")."</li>
            <li>".button("button=publish&label=Add")."</li>
            <li>".button("button=cancel",$url)."</li>";
                        
        }
        
        //set the page Title
        add_actions('section_title',$thetitle.' - Add New '.ucwords($rule));
        if($rule=="categories"){
            $parent="<fieldset>
                   </p>Parent :<p>
                   <p>
                        <select name=\"parent[0]\" class=\"big\">
                           <option value=\"0\">Parent</option>
                           ".recursive_taxonomy(0,$rule,$group)."
                        </select>
                    </p>
                </fieldset>";
            add_variable('parent',$parent);
        }
        add_variable('button',$button);
        parse_template('loopRule','lRule',false);
        return return_rule_template();
        
    }
    
    /**
	 * Call this function when you want to edit the rules (Category or Tags)    
	 *   
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * @param string $rule The rule name. By default it cloud be "categories" or "tags" 
	 * @param string $group The application name. 
	 * @param string $title The rule title that will be shown in admin area 
	 * @param integer $rule_id The edited rule ID, by default the value is 0
	 * 
	 * @return string HTML design when you edit category or tags
	 *      
	 */
    function edit_rule($rule,$group,$title,$rule_id=0){
        $index=0;
        $button="";
        set_rule_template();
       
        if(is_admin_application())
            $url=get_application_url($group)."&tab=".$rule;
        else
            $url=get_state_url($group)."&tab=".$rule;
        
        $thetitle=explode("|",$title);
        
        if(isset($_POST['select']) && count($_POST['select'])>1){
            if(count($thetitle)==2)
                $thetitle=$thetitle[1];
            else
                $thetitle=$thetitle[0];
        }else{
            $thetitle=$thetitle[0];
        }
        
        
       if(!is_contributor() && !is_author()){
            $button.="<li>".button("button=add_new",$url."&prc=add_new")."</li>
            <li>".button("button=publish&label=Save")."</li>
            <li>".button("button=cancel",$url)."</li>";
                        
        }
        
        //set the page Title
        add_actions('section_title','Edit - '.$thetitle.' '.ucwords($rule));
        add_variable('app_title',"Edit ".$thetitle." ".ucwords($rule));
        
        if(is_edit_all()){
            foreach($_POST['select'] as $index=>$rule_id){
                if(is_publish()){
                    if($rule=="categories"){
                        $parent="<fieldset>
                               </p>Parent :<p>
                               <p>
                                    <select name=\"parent[]\" class=\"big\">
                                       <option value=\"0\">Parent</option>
                                       ".recursive_taxonomy($index,$rule,$group,'select',array($_POST['parent'][$index]))."
                                    </select>
                                </p>
                            </fieldset>";
                            
                        add_variable('parent',$parent);
                    }
                    add_variable('name',$_POST['name'][$index]);
                    add_variable('description',$_POST['description'][$index]);
                    add_variable('rule_id',$rule_id);
                }else{
                    $d=count_rules("rule_id=".$rule_id,false);
                    if($rule=="categories"){
                        $parent="<fieldset>
                               </p>Parent :<p>
                               <p>
                                    <select name=\"parent[]\" class=\"big\">
                                       <option value=\"0\">Parent</option>
                                       ".recursive_taxonomy($index,$rule,$group,'select',array($d['lparent']))."
                                    </select>
                                </p>
                            </fieldset>";
                            
                        add_variable('parent',$parent);
                    }
                    add_variable('name',$d['lname']);
                    add_variable('description',$d['ldescription']);
                    add_variable('rule_id',$rule_id);
                }
                
                add_variable('is_edit_all',"<input type=\"hidden\" name=\"edit\" value=\"Edit\">");
                parse_template('loopRule','lRule',true);
            }
           
        }else{
                if(is_publish()){
                    if($rule=="categories"){
                        $parent="<fieldset>
                               </p>Parent :<p>
                               <p>
                                    <select name=\"parent[]\" class=\"big\">
                                       <option value=\"0\">Parent</option>
                                       ".recursive_taxonomy($index,$rule,$group,'select',array($_POST['parent'][$index]))."
                                    </select>
                                </p>
                            </fieldset>";
                            
                        add_variable('parent',$parent);
                    }
                    add_variable('name',$_POST['name'][$index]);
                    add_variable('description',$_POST['description'][$index]);
                    add_variable('rule_id',$rule_id);
                }else{
                    $d=count_rules("rule_id=".$rule_id,false);
                    if($rule=="categories"){
                        $parent="<fieldset>
                               </p>Parent :<p>
                               <p>
                                    <select name=\"parent[]\" class=\"big\">
                                       <option value=\"0\">Parent</option>
                                       ".recursive_taxonomy($index,$rule,$group,'select',array($d['lparent']))."
                                    </select>
                                </p>
                            </fieldset>";
                            
                        add_variable('parent',$parent);
                    }
                    add_variable('name',$d['lname']);
                    add_variable('description',$d['ldescription']);
                    add_variable('rule_id',$rule_id);
                }
                
                parse_template('loopRule','lRule',false);
           
        }
        
        
        add_variable('button',$button);
        return return_rule_template();
    }
    
     /**
	 * Get the rule lists (Categories or Tags)    
	 *   
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * @param string $rule The rule name. By default it cloud be "categories" or "tags" 
	 * @param string $type The application name. 
	 * @param string $title The rule title that will be shown in admin area 
	 * @param string $tabs Each rule has a different tabs. This variable should contain the list
	 * 
	 * @return string HTML design when you open category or tags complate with the button navigation
	 *      
	 */
    function get_rule_list($rule,$type,$title,$tabs){
        global $db;
        $list='';
        $option_viewed="";
        $data_order=array('asc'=>'Ascending','desc'=>'Descending');
        foreach($data_order as $key=>$val){
            if(isset($_POST['data_order'])){
                if($_POST['data_order']==$key){
                    $option_viewed.="<input type=\"radio\" name=\"data_order\" value=\"$key\" checked=\"checked\" />$val";
                }else{
                    $option_viewed.="<input type=\"radio\" name=\"data_order\" value=\"$key\"  />$val";
                }
            }elseif($key=='asc'){
                $option_viewed.="<input type=\"radio\" name=\"data_order\" value=\"$key\" checked=\"checked\"  />$val";
            }else{
                $option_viewed.="<input type=\"radio\" name=\"data_order\" value=\"$key\"  />$val";
            }
        }
        //setup paging system
        if(is_admin_application())
            $url=get_application_url($type)."&tab=".$rule."&page=";
        else
            $url=get_state_url($type)."&tab=".$rule."&page=";
        
                
        if(is_search()){
                //if($rule=='tags')
                //    $sql=$db->prepare_query("select * from lumonata_rules where lrule=%s and (lname like %s or ldescription like %s)",$rule,"%".$_POST['s']."%","%".$_POST['s']."%");
                //else
                    $sql=$db->prepare_query("select * from lumonata_rules where lrule=%s and (lname like %s or ldescription like %s) and lgroup=%s",$rule,"%".$_POST['s']."%","%".$_POST['s']."%",$type);
                $num_rows=count_rows($sql);
        }else{
                //if($rule=='tags')
                //    $where=$db->prepare_query("WHERE lrule=%s AND lgroup=%s",$rule);
                //else
                   	$where=$db->prepare_query("WHERE lrule=%s AND lgroup=%s",$rule,$type);
                    
                $num_rows=count_rows("select * from lumonata_rules $where ");
        }
        
        $viewed=list_viewed();
        if(isset($_GET['page'])){
            $page= $_GET['page'];
        }else{
            $page=1;
        }
        
        $limit=($page-1)*$viewed;
        if(is_search()){
            //if($rule=='tags')
            //    $sql=$db->prepare_query("select * from lumonata_rules where lrule=%s and (lname like %s or ldescription like %s) limit %d, %d",$rule,"%".$_POST['s']."%","%".$_POST['s']."%",$limit,$viewed);
            //else
                $sql=$db->prepare_query("select * from lumonata_rules where lrule=%s and (lname like %s or ldescription like %s) and lgroup=%s limit %d, %d",$rule,"%".$_POST['s']."%","%".$_POST['s']."%",$type,$limit,$viewed);
        }else{
                if(isset($_POST['data_order'])){
                    $order_by=$_POST['data_order'];
                }else{
                    $order_by="";
                }
                $sql=$db->prepare_query("select * from lumonata_rules $where order by lorder $order_by limit %d, %d",$limit,$viewed);
        }
        $result=$db->do_query($sql);
       
        $start_order=($page - 1) * $viewed + 1; //start order number
        $addnew_url=(is_admin_application())?get_application_url($type)."&tab=".$rule:get_state_url($type)."&tab=".$rule;
        
        $button="<li>".button("button=add_new",$addnew_url."&prc=add_new")."</li>
                <li>".button('button=edit&type=submit&enable=false')."</li>
                <li>".button('button=delete&type=submit&enable=false')."</li>";
        
        
        $title=explode("|",$title);
        if($num_rows>1){
            if(count($title)==2)
                $title=$title[1];
            else
                $title=$title[0];
        }else{
            $title=$title[0];
        }
        $list.="<h1>$title ".ucwords($rule)."</h1>
                <ul class=\"tabs\">$tabs</ul>
                <div class=\"tab_container\">
                        <div id=\"response\"></div>
                        <form action=\"\" method=\"post\" name=\"alist\">
                            <div class=\"button_right\">
                                ".search_box('taxonomy.php','list_taxonomy','state='.$_GET['state'].'&rule='.$rule.'&group='.$type.'&prc=search&','right','alert_green_form')."
                            </div>
                            <br clear=\"all\" />	
                           <input type=\"hidden\" name=\"start_order\" value=\"$start_order\" />
                           <input type=\"hidden\" name=\"state\" value=\"".$type."\" />
                            <div class=\"button_wrapper clearfix\">
                                <div class=\"button_left\">
                                        <ul class=\"button_navigation\">
                                                $button
                                        </ul>
                                </div>
                            </div>
                            <div class=\"status_to_show\">Ordering: $option_viewed</div>
                            <div class=\"list\">
                                <div class=\"list_title\">
                                    <input type=\"checkbox\" name=\"select_all\" class=\"title_checkbox\" />
                                    <div class=\"rule_parent\">Parent</div>
                                    <div class=\"rule_name\">Name</div>
                                    <div class=\"rule_description\">Description</div>
                                    <!--div class=\"list_order\">Order</div -->
                                </div>
                                <div id=\"list_taxonomy\">";
                                $list.=rule_list($result,$type,$rule,$start_order);
                $list.="	</div>
                        </form>
                        </div>
                        <div class=\"button_wrapper clearfix\">
                                <div class=\"button_left\">
                                    <ul class=\"button_navigation\">
                                         $button
                                    </ul>   
                                </div>
                        </div>
                        <div class=\"paging_right\">
                        	". paging($url,$num_rows,$page,$viewed,5)."
                        </div>
                </div>
            <script type=\"text/javascript\" language=\"javascript\">
                
                
            </script>";
            
        add_actions('section_title',$title);
        return $list;
    }
    
     /**
	 * This is function is called in get_rule_list() and search list. This function only return the list without the navigation menu    
	 *   
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * @param object $result Is the result of mysql query 
	 * @param string $type The application name. 
	 * @param string $rule The rule name. By default it cloud be "categories" or "tags" 
	 * @param integer $i Index
	 * 
	 * @return string HTML design list when you open category or tags complate without the button navigation
	 *      
	 */
    function rule_list($result,$type,$rule,$i=1){
        global $db;
        $list='';
        
        if(is_admin_application())
            $url=get_application_url($type)."&tab=".$rule;
        else
            $url=get_state_url($type)."&tab=".$rule;
            
        if($db->num_rows($result)==0 && isset($_POST['s']))
            return "<div class=\"alert_yellow_form\">No result found for <em>".$_POST['s']."</em>. Check your spellling or try another terms</div>";
        elseif($db->num_rows($result)==0)
            return "<div class=\"alert_yellow_form\">No record on database</div>";
        
        while($d=$db->fetch_array($result)){
            $parent=get_rule_structure($d['lrule_id'],$d['lrule_id']);    
            $list.="<div class=\"list_item clearfix\" id=\"theitem_".$d['lrule_id']."\">
                            <input type=\"checkbox\" name=\"select[]\" class=\"title_checkbox select\" value=\"".$d['lrule_id']."\" />
                            <div class=\"rule_parent\" >".$parent."</div>
                            <div class=\"rule_name\">".$d['lname']."</div>
                            <div class=\"rule_description\">".$d['ldescription']."</div>
                            <!--div class=\"list_order\" --><input type=\"hidden\" value=\"$i\" id=\"order_".$d['lrule_id']."\" class=\"small_textbox\" name=\"order[".$i."]\"><!-- /div -->
                            <div class=\"the_navigation_list\">
                                <div class=\"list_navigation\" style=\"display:none;\" id=\"the_navigation_".$d['lrule_id']."\">
                                        <a href=\"".$url."&prc=edit&id=".$d['lrule_id']."\">Edit</a> |
                                        <a href=\"#\" rel=\"delete_".$d['lrule_id']."\">Delete</a>
                                </div>
                            </div>
                            
                            <script type=\"text/javascript\" language=\"javascript\">
                                    $('#theitem_".$d['lrule_id']."').mouseover(function(){
                                            $('#the_navigation_".$d['lrule_id']."').show();
                                    });
                                    $('#theitem_".$d['lrule_id']."').mouseout(function(){
                                            $('#the_navigation_".$d['lrule_id']."').hide();
                                    });
                            </script>
                     </div>";
            //delete_confirmation_box($d['lrule_id'],"Are sure want to delete ".$d['lname']."?","taxonomy.php","theitem_".$d['lrule_id'],'state='.$rule.'&prc=delete&id='.$d['lrule_id'])
            add_actions('admin_tail','delete_confirmation_box',$d['lrule_id'],"Are sure want to delete ".$d['lname']."?","taxonomy.php","theitem_".$d['lrule_id'],'state='.$rule.'&prc=delete&id='.$d['lrule_id']);
            $i++;
        }
        return $list;
    }
    
    /**
	 * Get all categories rules    
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $index Index  
	 * @param string $rule_name The rule name. By default it cloud be "categories" or "tags"
	 * @param string $group The application name. 
	 * @param array $rule_id Selected rule if any. Leave it blank if there are no selected categories
	 * 
	 * @return string All categories with <span>
	 *      
	 */
    function all_categories($index,$rule_name,$group,$rule_id=array()){
        $xcode=json_encode($rule_id);
        
        $return=recursive_taxonomy($index,$rule_name,$group,'checkbox',$rule_id);
        $return.="<span id=\"selected_category_".$index."\">$xcode</span>";
        return $return;
    }
    
     /**
	 * To show the structure of each rule if they has Child.    
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $start_id Is used to identify the root ID. This ID is not change in the recursive and must be the same ID with $rule_id for the first call  
	 * @param integer $rule_id The ID of the rule that you want get the structure
	 * @param integer $level The recursive level. 
	 * 
	 * 
	 * @return string Example output: Root >> Category1 >> Category2
	 *      
	 */
    function get_rule_structure($start_id,$rule_id,$level=0){
        global $db;
        $sql=$db->prepare_query("SELECT *
                                 FROM lumonata_rules 
                                 WHERE lrule_id=%d ",$rule_id);
        $r=$db->do_query($sql);
        $num=$db->num_rows($r);
        
        if($num >0){
            $level+=1;
            $dashed="&raquo;";
        }else{
            $level=$level;
            $dashed="";
        }
        $items="";
        while($d=$db->fetch_array($r)){
            $next_level = get_rule_structure($start_id,$d['lparent'],$level);
           
            if($start_id==$rule_id)
                $items .= "Root ".$dashed." ";
            
            $items .= $next_level;
            
            if($start_id!=$rule_id)
                $items .= $d['lname']." ".$dashed." ";
            
            
            
        }
        
        return $items;
    }
    
    /**
	 * Get all rule from Root to the last child using this recursive function. 
	 * This function is also detected if there any rules are selected.
	 * You also can choose the order type by Descending or Ascending.    
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $index Index
	 * @param integer $rule_name The Rule Name. By default it could be categories or tags  
	 * @param integer $group The application name
	 * @param string $type Output type: select, checkbox, li alse only with link 
	 * @param array $rule_id Selected rule_id if any
	 * @param string $order Ordering type: ASC, DESC
	 * @param integer $parent The rule parent ID
	 * @param integer $level recursive level
	 * @param boolean $related_to_article FALSE=all category will shown, TRUE= Only Category that has correlation with article will shown
	 * 
	 * @return string Example output: Root >> Category1 >> Category2
	 *      
	 */
    function recursive_taxonomy($index,$rule_name,$group,$type='select',$rule_id=array(),$order='ASC',$parent=0,$level=0,$related_to_article=false){
        global $db;
        if(!$related_to_article)
        $sql=$db->prepare_query("SELECT *
                                 FROM lumonata_rules 
                                 WHERE lrule=%s AND (lgroup=%s OR lgroup=%s) AND lparent=%d  order by lorder $order",$rule_name,$group,'default',$parent);
        else 
        $sql=$db->prepare_query("SELECT a.*
                                 FROM lumonata_rules a, lumonata_rule_relationship b, lumonata_articles c 
                                 WHERE a.lrule=%s AND 
                                 (a.lgroup=%s OR a.lgroup=%s) AND 
                                 a.lparent=%d AND
                                 a.lrule_id=b.lrule_id AND
                                 b.lapp_id=c.larticle_id AND
                                 c.lshare_to=0
                                 GROUP BY a.lrule_id
                                 ORDER BY a.lorder $order",$rule_name,$group,'default',$parent);
       
        $r=$db->do_query($sql);
        $num=$db->num_rows($r);
        
        $items="";
        $end_item="";
        $sts="";
        
        if($type=='li' && $num >0){
            $items="<ul>";
            $end_item="</ul>";
        }elseif($type=='checkbox'){
            if($level==0){
                $items="<ul class=\"the_categories\">";
            }else{
                $items="<ul>";
            }
            
            
            $end_item="</ul>";
        }  
        
        if($num >0){
            $level+=1;
        }else{
            $level=$level;
           
        }
        
        $dashed="";
        for($i=0;$i< $level;$i++){
                $x = $level - 1;
                if ($x == $i) 
                        $dashed.="";
                else
                        $dashed.="&nbsp;&nbsp;&nbsp;";
        }
        
       
        
        while($d=$db->fetch_array($r)){
          
            $next_level = recursive_taxonomy($index,$rule_name,$group,$type,$rule_id,$order,$d['lrule_id'],$level,$related_to_article);
            
            if($type=='select'){
                $sts=(in_array($d['lrule_id'],$rule_id))?"selected=\"selected\"":"";
                $items .= "<option value=\"".$d['lrule_id']."\" $sts >".$dashed.$d['lname']."</option>" ;
                $items .= $next_level;
            }elseif($type=='checkbox'){
                if(is_array($rule_id))
                $sts=(in_array($d['lrule_id'],$rule_id))?"checked=\"checked\"":"";
                
                $items .= "<li><input type=\"checkbox\" name=\"category[$index][]\" value=\"".$d['lrule_id']."\" id=\"the_category_".$index."_".$d['lrule_id']."\" $sts />".$d['lname']."</li>";
                $items.="<script type=\"text/javascript\">
							$('#the_category_".$index."_".$d['lrule_id']."').click(function(){
							    var selected_val=$(this).val();
							   
							    var checked_status = this.checked;
							    $('#the_most_category_".$index."_".$d['lrule_id']."').each(function(){
								if($(this).val()==selected_val){
								    this.checked = checked_status;
								}
							    });
							});
                      </script>";
                $items .= $next_level;
            }elseif($type=='li'){
                //if(is_array($rule_id))
                //	if(in_array($d['lrule_id'],$rule_id))
            	if($rule_name=='categories'){
                        if(is_permalink())
                            $the_link="http://".site_url()."/".$group."/".$d['lsef']."/";
                        else
                            $the_link="http://".site_url()."/?app_name=".$group."&cat_id=".$d['lrule_id'];
                            
                }elseif($rule_name=='tags'){
                		
                        if(is_permalink())
                            $the_link="http://".site_url()."/tag/".$d['lsef']."/";
                        else
                            $the_link="http://".site_url()."/?tag=".$d['lsef'];
                        
                }
                $items .= "<li><a href=\"".$the_link."\">".$d['lname']."</a>".$next_level."</li>" ;
            }else{
                if(is_array($rule_id))
                if(in_array($d['lrule_id'],$rule_id)){
                    
                    $sign=", ";
                    if($rule_name=='categories'){
                        if(is_permalink())
                            $the_category_link="http://".site_url()."/".$group."/".$d['lsef']."/";
                        else
                            $the_category_link="http://".site_url()."/?app_name=".$group."&cat_id=".$d['lrule_id'];
                            
                        $items .= "<a href=\"".$the_category_link."\">".$d['lname']."</a>".$sign;
                    }elseif($rule_name=='tags'){
                        
                        if(is_permalink())
                            $the_tag_link="http://".site_url()."/tag/".$d['lsef']."/";
                        else
                            $the_tag_link="http://".site_url()."/?tag=".$d['lsef'];
                        
                        $items .= "<a href=\"".$the_tag_link."\">".$d['lname']."</a>".$sign;
                    }
                }
             	$items .= $next_level;   
            }
            
            $items .= "";
            
        }
        
        
        $items.=$end_item;
            
        return $items;
    }
    
    /**
	 * Get the 15 most used categories.  
	 * 
	 *     
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $group The application name
	 * @param integer $index Index 
	 * @param array $rule_id Selected rule_id if any
	 * 
	 * @return string 15 most used categories
	 *      
	 */
    function get_most_used_categories($group,$index=0,$rule_id=array()){
        global $db;
        
        $sql=$db->prepare_query("SELECT a.lname, a.lrule_id
                            FROM lumonata_rules a
                            WHERE a.lrule=%s AND (a.lgroup=%s OR a.lgroup=%s)
                            ORDER BY a.lcount DESC
                            LIMIT 15",
                            'categories',$group,'default');
        $r=$db->do_query($sql);
        if($db->num_rows($r)==0)
            $notag="<li>No category found, please add new category.</li>";
        else
            $notag="";
            
        $return="<ul class=\"the_categories\">";
        $return.=$notag;
        while($d=$db->fetch_array($r)){
            $sts=(in_array($d['lrule_id'],$rule_id))?"checked=\"checked\"":"";
            $return.="<li><input type=\"checkbox\" name=\"most_used_category[$index][]\" value=\"".$d['lrule_id']."\" id=\"the_most_category_".$index."_".$d['lrule_id']."\" $sts />".$d['lname']."</li>";
            $return.="<script type=\"text/javascript\">
			$(\"#the_most_category_".$index."_".$d['lrule_id']."\").click(function(){
			    var selected_val=$(this).val();
			    var checked_status = this.checked;
			    $(\"#the_category_".$index."_".$d['lrule_id']."\").each(function(){
				if($(this).val()==selected_val){
				    this.checked = checked_status;
				}
			    });
			});
                      </script>";
        }
        $return.="</ul>";
        return $return;
    
    }
    
    /**
	 * This function will display add new category box on add new article  
	 * 
	 *     
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * 
	 * @param integer $index Index 
	 * @param array $rule_name Name of the rule (By default categories or tags).
	 * @param integer $group The application name
	 * 
	 * @return string add new category box
	 *      
	 */
    function article_new_category($index,$rule_name,$group){
        
        $return="<div class=\"add_new_cattags\">
                    <input type=\"text\" class=\"category_textbox\" name=\"new_category[$index]\" value=\"New category name\" />
                    <div style=\"margin:0 0 2px 0;\" id=\"select_parent_$index\">
                        <select name=\"parent[$index]\" >
                            <option value=\"0\">Parent</option>
                            ".recursive_taxonomy($index,$rule_name,$group,'select')."
                        </select>
                    </div>
                    <div class=\"add_cattags_button\">
                        <input type=\"button\" name=\"add_category_btn[$index]\" value=\"Add\" class=\"button\" />
                    </div>
                   
                </div>";
                
        return $return;
    }
    
     /**
	 * Get the tag that been used for an article and show it in add new or edit article  
	 * 
	 *     
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * 
	 * @param integer $post_id article ID 
	 * @param integer $index Index
	 * @param integer $group The application name
	 * 
	 * @return string tags in each post
	 *      
	 */
    function get_post_tags($post_id,$index,$group){
        global $db;
       
        $sql=$db->prepare_query("SELECT a.lname, a.lrule_id
                            FROM lumonata_rules a, lumonata_rule_relationship b
                            WHERE a.lrule_id=b.lrule_id AND a.lrule=%s AND a.lgroup=%s AND b.lapp_id=%d",
                            'tags',$group,$post_id);
        $r=$db->do_query($sql);
        $count_row=$db->num_rows($r);
        if($count_row==0)
            $notag="No tag found, please add new tag or choose from most used tags.";
        else
            $notag="";
            
        $return="<div class=\"the_categories\" id=\"tag_list_".$index."\">";
        $return.=$notag;
        $i=$count_row-1;
        while($d=$db->fetch_array($r)){
            $return.="<div class=\"tag_list tag_index_".$index." clearfix\" id=\"the_tag_list_".$index."_".$i."\">
                            <div class=\"tag_name\">".$d['lname']."</div>
                            <div class=\"tag_action\"><a href=\"javascript:;\" onclick=\"$('#the_tag_list_".$index."_".$i."').remove();\">X</a></div>
                            <input type=\"hidden\" name=\"tags[$index][]\" value=\"".$d['lname']."\" />
                     </div>";
            $i--;    
        }
        $return.="</div>";
        return $return;
    }
    
    /**
	 * 15 tags that used mostly  
	 * 
	 *     
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $group The application name
	 * @param integer $index Index
	 * 
	 * @return string 15 tags that used mostly  
	 *      
	 */
    function get_most_used_tags($group,$index=0){
        global $db;
        $sql=$db->prepare_query("SELECT a.lname, a.lrule_id
                            FROM lumonata_rules a
                            WHERE a.lrule=%s 
                            AND a.lgroup NOT IN ('global_settings', 'profile')
                            ORDER BY a.lcount DESC
                            LIMIT 15",
                            'tags');
        $r=$db->do_query($sql);
        $count_row=$db->num_rows($r);
        if($count_row==0)
            $notag="No tag found, please add new tag.";
        else
            $notag="";
            
        $return="<div class=\"the_categories\">";
        $return.=$notag;
        $i=$count_row-1;
        while($d=$db->fetch_array($r)){
            $return.="<div class=\"most_tag_list most_tag_".$index."_".$i." clearfix\">
                            <div class=\"tag_name\">".$d['lname']."</div>
                            <div class=\"tag_action\"><a href=\"javascript:;\" id=\"append_tags_".$index."_".$i."\">+</a></div>
                      </div>
                      <script type=\"text/javascript\">
                       
                        $(\"#append_tags_".$index."_".$i."\").click(function(){
                            var taglabel='".$d['lname']."';
                            var count_child=$('.tag_index_".$index."').size();
                            thetag='<div class=\"tag_list tag_index_".$index." clearfix\" id=\"the_tag_list_".$index."_'+count_child+'\" >';
                            thetag+='<div class=\"tag_name\">".$d['lname']."</div>';
                            thetag+='<div class=\"tag_action\">';
                            thetag+='<a href=\"javascript:;\" id=\"remove_tag_'+count_child+'\" onclick=\"$(\'#the_tag_list_".$index."_'+count_child+'\').remove();$(\'.most_tag_".$index."_".$i."\').animate({\'backgroundColor\':\'#FFFFFF\' },500);\">X</a>';
                            thetag+='</div>';
                            thetag+='<input type=\"hidden\" name=\"tags[".$index."][]\" value=\"".$d['lname']."\" />';
                            thetag+='</div>';
                           
                            if(count_child==0){
                                $(\"#tag_list_".$index."\").html('');
                                $(\"#tag_list_".$index."\").append(thetag);
                            }else{
                                $(\".tag_index_".$index.":first\").before(thetag);
                            }
                            
                            $('.most_tag_".$index."_".$i."').animate({'backgroundColor':'#FF6666' },500);
                            $('.most_tag_".$index."_".$i."').animate({'backgroundColor':'#cccccc' },500);
                            
                            
                            
                        });
                      </script>";
            $i++;
        }
        $return.="</div>";
        return $return;
    }
    
    /**
	 * Add new tag box when edit or add new article  
	 * 
	 *     
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $post_id article ID
	 * @param integer $index Index
	 * 
	 * @return string Add new tag box when edit or add new article  
	 *      
	 */
    function add_new_tag($post_id,$index){
        $return="<div class=\"add_new_cattags\">
		    <input type=\"text\" class=\"category_textbox\" name=\"new_tag[$index]\" value=\"New tags\" />
                    <em>Separate tags with commas.</em>
                    <div class=\"add_cattags_button\">
                        <input type=\"button\" name=\"add_tag[$index]\" value=\"Add\" class=\"button\" />
                    </div>
                </div>";
                
        return $return;
    }
    
     /**
	 * Insert new rule into lumonata_rules table  
	 * 
	 *     
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $parent parent ID of the new rule, if the new rule are child. If the new rule are Root then the $parent ID must be 0
	 * @param string $name Rule name
	 * @param string $description Rule description
	 * @param string $rule The rule name. "categories" and "tags" are the rule name by default
	 * @param string $group Application name
	 * @param boolean $insert_default If the variable set TRUE means that you insert the default rule. The rule ID, will automatically set to 1
	 * 
	 * @return integer If the rule are 'tags' and then tags name are exist in database then the function will return the exist tag ID.
	 *                 But if it is other that 'tags' it will return the new inserted ID  
	 *      
	 */
    function insert_rules($parent,$name,$description,$rule,$group,$insert_default=false,$subsite='arunna'){
        global $db;
        $parent=rem_slashes($parent);
        $name=rem_slashes($name);
        $sef=generateSefUrl($name);
        $description=rem_slashes($description);
        $rule=rem_slashes($rule);
        $group=rem_slashes($group);
        $subsite=rem_slashes($subsite);
        
        //count rule by sef and group
        $count_rule=count_rules("sef=".$sef."&group=".$group);
        
        if($rule=='tags'){
            //count rule by sef and rule
            $count_rule=count_rules("sef=".$sef."&rule=".$rule."&group=".$group);
            if($count_rule > 0) {
                $fetch_rule=count_rules("sef=".$sef."&rule=".$rule,false);
                return $fetch_rule['lrule_id'];
            }
        }else{
            if($count_rule > 0){
                $count_rule++;
                $sef=$sef."-".$count_rule;
            }
        }
        if($insert_default){
            $sql=$db->prepare_query("INSERT INTO lumonata_rules(lrule_id,
            												lparent,
                                                            lname,
                                                            lsef,
                                                            ldescription,
                                                            lrule,
                                                            lgroup,
                                                            lsubsite)
                                VALUES(%d,%d,%s,%s,%s,%s,%s,%s)",1,
                                                            $parent,
                                                            $name,
                                                            $sef,
                                                            $description,
                                                            $rule,
                                                            $group,
                                                            $subsite);
        }else{
            $sql=$db->prepare_query("INSERT INTO lumonata_rules(lparent,
                                                                lname,
                                                                lsef,
                                                                ldescription,
                                                                lrule,
                                                                lgroup,
                                                                lsubsite)
                                    VALUES(%d,%s,%s,%s,%s,%s,%s)",$parent,
                                                                $name,
                                                                $sef,
                                                                $description,
                                                                $rule,
                                                                $group,
                                                                $subsite);
        }
        if(reset_order_id("lumonata_rules"))
           if($db->do_query($sql))
                return mysql_insert_id();

    }
    
    /**
	 * Edit the rules  
	 * 
	 *     
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $rule_id The rule ID that will be edited
	 * @param string $parent The parent ID of edited rule
	 * @param string $description Rule description
	 * @param string $rule The rule name. "categories" and "tags" are the rule name by default
	 * @param string $group Application name
	 * 
	 * 
	 * @return If the rule are 'tags' and then tags name are exist in database then the function will return the exist tag ID.
	 *         But if it is other that 'tags' it will return TRUE if the edit process success and false if fail  
	 *      
	 */
    function update_rules($rule_id,$parent,$name,$description,$rule,$group,$subsite='arunna'){
        global $db;
        $parent=rem_slashes($parent);
        $name=rem_slashes($name);
        $sef=generateSefUrl($name);
        $description=rem_slashes($description);
        $rule=rem_slashes($rule);
        $group=rem_slashes($group);
        $subsite=rem_slashes($subsite);
        
        //count rule by sef and group
        $count_rule=count_rules("sef=".$sef."&group=".$group);
        
        if($rule=='tags'){
            //count rule by sef and rule
            $count_rule=count_rules("sef=".$sef."&rule=".$rule."&group=".$group);
            if($count_rule > 0) {
                $fetch_rule=count_rules("sef=".$sef."&rule=".$rule,false);
                return $fetch_rule['lrule_id'];
            }
        }else{
            if($count_rule > 0){
                $count_rule++;
                $sef=$sef."-".$count_rule;
            }
        }
        $sql=$db->prepare_query("UPDATE lumonata_rules
                                 SET lparent=%d,
                                     lname=%s,
                                     lsef=%s,
                                     ldescription=%s,
                                     lrule=%s,
                                     lgroup=%s,
                                     lsubsite=%s
                                WHERE lrule_id=%d",
                                    $parent,
                                    $name,
                                    $sef,
                                    $description,
                                    $rule,
                                    $group,
                                    $subsite,
                                    $rule_id
                                );
        return $db->do_query($sql);
                

    }
    
    /**
	 * Update or set the new number of  rule being used by articles or other applications  
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $rule_id The rule ID that will be edited
	 * @param integer $rule_count The number of used
	 
	 * 
	 * 
	 * @return boolean  
	 *      
	 */
    function update_rule_count($rule_id,$rule_count){
        global $db;
        $sql=$db->prepare_query("UPDATE lumonata_rules
                                SET lcount=%d
                                WHERE lrule_id=%d",$rule_count,$rule_id);
        return $db->do_query($sql);
    }
    
     /**
	 * Delete the spesific rule by ID  
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $id The rule ID that will be deleted
	 * 
	 * 
	 * @return boolean  
	 *      
	 */
    function delete_rule($id){
        global $db;
        $d=count_rules("rule_id=".$id,false);
        $theparent=$d['lparent'];
        $rule=$d['lrule'];
        
        //Update child parent into the deleted data parent if any 
        $sql=$db->prepare_query("UPDATE lumonata_rules SET lparent=%d WHERE lparent=%d",$theparent,$id);
        if($db->do_query($sql)){
            //If the rule is categories, then update the post into default Uncategorized category
            if($rule=="categories"){
                //find which post that using deleted category
                $category_in_post=fetch_rule_relationship('rule_id='.$id,'lapp_id');
                
                foreach($category_in_post as $key=>$val){
                    $sql=$db->prepare_query("SELECT a.lrule_id
                                            FROM lumonata_rules a, lumonata_rule_relationship b
                                            WHERE a.lrule_id=b.lrule_id AND b.lapp_id=%d AND a.lrule='categories'",$val);
                    
                    $r=$db->do_query($sql);
                    
                    if($db->num_rows($r)<=1)
                        update_rules_relationship($id,$val,1);
                    else
                        delete_rules_relationship("rule_id=".$id."&app_id=".$val);        
                }
                $sql=$db->prepare_query("DELETE FROM lumonata_rules WHERE lrule_id=%d",$id);
                if($db->do_query($sql)){
                    return true;
                }
                
            }elseif($rule=="tags") {
                //Delete the tag from the post that using deleted tags
                if(delete_rules_relationship("rule_id=".$id)){
                    $sql=$db->prepare_query("DELETE FROM lumonata_rules WHERE lrule_id=%d",$id);
                    if($db->do_query($sql)){
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
     /**
	 * Update rule relationship between article  
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $rule_id The rule ID that will be edited
	 * @param integer $app_id application ID (article ID)
	 * @param integer $new_rule_id New rule ID that replace the existing ID
	 * 
	 * 
	 * @return boolean  
	 *      
	 */
    function update_rules_relationship($rule_id,$app_id,$new_rule_id){
        global $db;
        $sql=$db->prepare_query("UPDATE lumonata_rule_relationship SET lrule_id=%d
                                 WHERE lrule_id=%d AND lapp_id=%d",$new_rule_id,$rule_id,$app_id);
        
        if($db->do_query($sql)){
            $rule_count=count_rules_relationship("rule_id=$rule_id");
            return update_rule_count($rule_id,$rule_count);
        }
    }
    
    /**
	 * Insert rule relationship between article and category or tag  
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $app_id application ID (article ID)
	 * @param integer $rule_id The rule ID that will be edited
	 * 
	 * 
	 * @return boolean  
	 *      
	 */
    function insert_rules_relationship($app_id,$rule_id){
        global $db;
      
        if(count_rules_relationship("app_id=$app_id&rule_id=$rule_id")==0){
            $sql=$db->prepare_query("INSERT INTO lumonata_rule_relationship(lapp_id,lrule_id)
                                      VALUES(%d,%d)",$app_id,$rule_id);
            if($db->do_query($sql)){
                $rule_count=count_rules_relationship("rule_id=$rule_id");
                return update_rule_count($rule_id,$rule_count);
            }
        }
    }
    
    /**
	 * Count how many articles or applications are using the rule, There are two $args that you can use: app_id and rule_id
	 * 
	 * @example count_rules_relationship();
	 * @example count_rules_relationship("app_id=1"); //use this if you only want to count it per application ID
	 * @example count_rules_relationship("app_id=1&rule_id=3"); //use this if you only want to count it per application ID and rule ID
	 *    
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param string $args Argument that can be specify by you. 
	 *  
	 * 
	 * @return boolean  
	 *      
	 */
    function count_rules_relationship($args=''){
         global $db;
        $var_name['app_id']='';
        $var_name['rule_id']='';
        $where="";
        
        if(!empty($args)){
            $args=explode('&',$args);
            
            $where=" WHERE ";
           
            $i=1;
            foreach($args as $val){
                list($variable,$value)=explode('=',$val);
                if($variable=='app_id' || $variable=='rule_id'){
                    $where.="l".$variable."=".$db->_real_escape($value);
                    if($i!=count($args)){
                        $where.=" AND ";
                    }
                    
                }
                $i++;
            }
        }
        
        $sql=$db->prepare_query("SELECT * FROM lumonata_rule_relationship $where ");
       
        return $db->num_rows($db->do_query($sql));
    }
    
    /**
	 * Delete the rule relationship, there are two $args that you can use: app_id and rule_id
	 * 
	 * 
	 * @example delete_rules_relationship("app_id=1"); //use this if you only want to delete the relationship per application ID
	 * @example delete_rules_relationship("app_id=1&rule_id=3"); //use this if you only want to delete the relationship per application ID and rule ID
	 * @example delete_rules_relationship("app_id=1","categories"); //use this if you only want to delete the relationship by the rule name
	 *    
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param string $args Argument that can be specify by you. 
	 * @param string $rule Rule name
	 *  
	 * 
	 * @return boolean  
	 *      
	 */
    function delete_rules_relationship($args='',$rule='',$group=''){
        global $db;
        $var_name['app_id']='';
        $var_name['rule_id']='';
        $where="";
        
        
        if(!empty($args)){
            $args=explode('&',$args);
            
            $where=" WHERE ";
           
            $i=1;
            foreach($args as $val){
                list($variable,$value)=explode('=',$val);
                if($variable=='app_id' || $variable=='rule_id'){
                    $where.="a.l".$variable."=".$db->_real_escape($value);
                    if($i!=count($args)){
                        $where.=" AND ";
                    }
                    
                }
                $i++;
            }
            if(!empty($rule) && empty($group)){
                $where.=" AND b.lrule='".$db->_real_escape($rule)."' AND a.lrule_id=b.lrule_id";  
                $sql=$db->prepare_query("DELETE a FROM lumonata_rule_relationship AS a, lumonata_rules AS b $where ");
            }elseif(!empty($group) && empty($rule)){
            	$where.=" AND b.lgroup='".$db->_real_escape($group)."' AND a.lrule_id=b.lrule_id";  
                $sql=$db->prepare_query("DELETE a FROM lumonata_rule_relationship AS a, lumonata_rules AS b $where ");
            }elseif(!empty($group) && !empty($rule)){
            	$where.=" AND b.lgroup='".$db->_real_escape($group)."' AND b.lrule='".$db->_real_escape($rule)."' AND a.lrule_id=b.lrule_id";  
                $sql=$db->prepare_query("DELETE a FROM lumonata_rule_relationship AS a, lumonata_rules AS b $where ");
            }else{
                $sql=$db->prepare_query("DELETE a FROM lumonata_rule_relationship AS a $where ");
            }
            return $db->do_query($sql);
               
            
        }
        
    }
    
    function fetch_rulerel_by_group_type($app_id,$group="",$rule="",$return_array=false){
    	global $db;
    	
    	if(!empty($rule) && empty($group)){
    		$where=" AND a.lrule='".$db->_real_escape($rule)."'"; 
    	}elseif(empty($rule) && !empty($group)){
    		$where=" AND a.lgroup='".$db->_real_escape($group)."'";
    	}elseif(!empty($rule) && !empty($group)){
    		$where=" AND a.lgroup='".$db->_real_escape($group)."' AND lrule='".$db->_real_escape($rule)."'";
    	}
    	$query=$db->prepare_query("SELECT a.* 
    							FROM lumonata_rules a, lumonata_rule_relationship b 
    							WHERE a.lrule_id=b.lrule_id
    							AND b.lapp_id=%d $where",$app_id);
    	
    	if($return_array){
    		$return=array();
    		$result=$db->do_query($query);
    		while($data=$db->fetch_array($result)){
    			$return[]=$data['lrule_id'];
    			
    		}
    		return $return;	
    	}else{
    		return $result=$db->do_query($query);
    	}
    }
    
    /**
	 * To fetch the rule relationship and there are two $args that you can use: app_id and rule_id
	 *   
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param string $args Argument that can be specify by you. 
	 * @param string $return_value Specify the return value that your want to get, by default the return value is 'lrule_id'
	 * 
	 * @example delete_rules_relationship("app_id=1"); //use this if you only want to fetch the relationship per application ID
	 * @example delete_rules_relationship("app_id=1&rule_id=3"); //use this if you only want to fetch the relationship per application ID and rule ID
	 * @example delete_rules_relationship("app_id=1&rule_id=3","lrule_id"); //use this if you only want to fetch the relationship per application ID and rule ID and return the lrule_id as the result 
	 * 
	 * @return $return_value  
	 *      
	 */
    function fetch_rule_relationship($args='',$return_value='lrule_id'){
        global $db;
        $return=array();
        $var_name['app_id']='';
        $var_name['rule_id']='';
        $where="";
        
        if(!empty($args)){
            $args=explode('&',$args);
            
            $where=" WHERE ";
           
            $i=1;
            foreach($args as $val){
                list($variable,$value)=explode('=',$val);
                if($variable=='app_id' || $variable=='rule_id'){
                    $where.="l".$variable."=".$db->_real_escape($value);
                    if($i!=count($args)){
                        $where.=" AND ";
                    }
                    
                }
                $i++;
            }
            
            $sql=$db->prepare_query("SELECT * FROM lumonata_rule_relationship $where ");
            
            $r=$db->do_query($sql);
            while($d=$db->fetch_array($r)){
                $return[]=$d[$return_value];
            }
            return $return;
        }
    }
    /**
	 * To fetch the rule and there are three $args that you can use: sef, rule_id and group
	 * Default value for 'group' args are 'articles'  
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param string $args Argument that can be specify by you. 
	 * 
	 * @example delete_rules_relationship("sef=category-1"); //use this if you only want to fetch the rule by the given sef
	 * @example delete_rules_relationship("sef=category-1&group=articles"); //use this if you only want to fetch the rule by the given sef in given application name ($group) 
	 * 
	 * @return array Fetch result  
	 *      
	 */
    function fetch_rule($args=''){
    	global $db;
    	$var_name['sef']='';
        $var_name['rule_id']='';
        $var_name['group']='articles';
        $where="";
        
    	if(!empty($args)){
            $args=explode('&',$args);
            
            $where=" WHERE ";
           
            $i=1;
            foreach($args as $val){
                list($variable,$value)=explode('=',$val);
                if($variable=='rule_id'){
                    $where.="l".$variable."=".$db->_real_escape($value);
                }elseif( $variable=='sef' ||  $variable=='group'){
                	$where.="l".$variable."='".$db->_real_escape($value)."'";
                }
            	if($i!=count($args)){
                    $where.=" AND ";
                }
                $i++;
            }
            
            $sql=$db->prepare_query("SELECT * FROM lumonata_rules $where ");
            
            $r=$db->do_query($sql);
            $d=$db->fetch_array($r);
            return $d;
        }
    	
    }
    
    /**
	 * To count the specifiy rule conditions
	 * There are fice $args that you can use: parent,name,rule,group,sef,rule_id
	 *   
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param string $args Argument that can be specify by you. 
	 * @param boolean $count is this variable set to false, the function will return the fetching results 
	 * 
	 * @return the number of count if $count=TRUE and fetch array if $count=FALSE 
	 *      
	 */
    function count_rules($args='',$count=true){
        global $db;
        $var_name['parent']='';
        $var_name['name']='';
        $var_name['rule']='';
        $var_name['group']='';
        $var_name['sef']='';
        $var_name['rule_id']='';
        $where="";
        
        if(!empty($args)){
            $args=explode('&',$args);
            
            $where=" WHERE ";
            $i=1;
            foreach($args as $val){
                
                list($variable,$value)=explode('=',$val);
                if($variable=='parent' || $variable=='name' || $variable=='rule' || $variable=='group' || $variable=='sef' || $variable=='rule_id'){
                  //$var_name[$variable]=$value;
                  if($variable=='parent')
                    $where.="l".$variable."=".$db->_real_escape($value);
                  else
                    $where.="l".$variable."='".$db->_real_escape($value)."'";
                    
                  if($i!=count($args)){
                    $where.=" AND ";
                  }
                }
                
                
                $i++;
            }
        }
        
        
        $sql=$db->prepare_query("SELECT * from lumonata_rules ".$where);
        
        $r=$db->do_query($sql);
        if($count){
            return $db->num_rows($r);
        }else{
            return $db->fetch_array($r);
        }
    }
    /**
	 * This function to the query to select the selected rule in mention article. 
	 * 
	 *   
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $post_id Article ID 
	 * @param string $rule The rule name (categories or tags)
	 * @param string $group The application name 
	 * 
	 * @return array Return a set of Rule ID as per query specify 
	 *      
	 */
    function find_selected_rules($post_id,$rule,$group){
        global $db;
        $result=array();
        $sql=$db->prepare_query("SELECT a.lrule_id
                                 FROM lumonata_rule_relationship a, lumonata_rules b
                                 WHERE a.lrule_id=b.lrule_id AND b.lrule=%s AND (b.lgroup=%s or b.lgroup=%s) AND a.lapp_id=%d",$rule,$group,'default',$post_id);
        
        $r=$db->do_query($sql);
        while($d=$db->fetch_array($r)){
            $result[]=$d['lrule_id'];
        }
        
        return $result;
    }
    
    /**
	 * Update rule order ID of   
	 * 
	 *   
	 * 
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param array $order Set of exisitng data that contain order_id and rule_id. The array data are array(order_id=>rule_id) 
	 * @param integer $start Number where to start to update the order ID
	 * @param string $app_name The application name 
	 * 
	 * @return boolean 
	 *      
	 */
    function update_taxonomy_order($order,$start,$app_name){
        global $db;
        foreach($order as $key=>$val){
            $sql=$db->prepare_query("UPDATE lumonata_rules
                                     SET lorder=%d
                                     WHERE lrule_id=%d",
                                     $key+$start,
                                     $val);
            $db->do_query($sql);
          
            
        }
    }
    
    function get_expertise_categories(){
    	global $db;
    	$query=$db->prepare_query("SELECT * FROM lumonata_rules 
    								WHERE lrule='categories' 
    								AND lgroup='global_settings'
    								AND lsubsite='arunna'
    								ORDER BY lname");
    	return $result=$db->do_query($query);
    	 
    }
    
    function get_expertise_tags(){
    	global $db;
    	$query=$db->prepare_query("SELECT * FROM lumonata_rules 
    								WHERE lrule='tags' 
    								AND lgroup='global_settings'
    								AND lsubsite='arunna'
    								ORDER BY lname");
    	return $result=$db->do_query($query);
    	
    }
?>