<?php
    function get_admin_rule($rule,$group,$thetitle,$tabs){
        
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
                    
                if(insert_rules($parent,$_POST['name'][0],$_POST['description'][0],$rule,$group)){
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
                update_rules($_POST['rule_id'][0],$_POST['parent'][0],$_POST['name'][0],$_POST['description'][0],$rule,$group);
                
                
            }elseif(is_edit_all()){
                run_actions($rule.'_editall');
                //Update the articles
               
                foreach($_POST['rule_id'] as $index=>$value){
                    if($rule=='tags')
                    $_POST['parent'][$index]=0;
                    
                    update_rules($_POST['rule_id'][$index],$_POST['parent'][$index],$_POST['name'][$index],$_POST['description'][$index],$rule,$group);
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
                if($_GET['state']!='articles')
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
    function set_rule_template(){
        //set template
        set_template(TEMPLATE_PATH."/rules.html",'rules');
        //set block
        add_block('loopRule','lRule','rules');
        add_block('ruleAddNew','rAddNew','rules');
    }
    function return_rule_template($loop=false){
        parse_template('ruleAddNew','rAddNew',$loop);
        return return_template('rules');
    }
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
                if($rule=='tags')
                    $sql=$db->prepare_query("select * from lumonata_rules where lrule=%s and (lname like %s or ldescription like %s)",$rule,"%".$_POST['s']."%","%".$_POST['s']."%");
                else
                    $sql=$db->prepare_query("select * from lumonata_rules where lrule=%s and (lname like %s or ldescription like %s) and lgroup=%s",$rule,"%".$_POST['s']."%","%".$_POST['s']."%",$type);
                $num_rows=count_rows($sql);
        }else{
                if($rule=='tags')
                    $where=$db->prepare_query("WHERE lrule=%s",$rule);
                else
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
            if($rule=='tags')
                $sql=$db->prepare_query("select * from lumonata_rules where lrule=%s and (lname like %s or ldescription like %s) limit %d, %d",$rule,"%".$_POST['s']."%","%".$_POST['s']."%",$limit,$viewed);
            else
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
                           <input type=\"hidden\" name=\"start_order\" value=\"$start_order\" />
                           <input type=\"hidden\" name=\"state\" value=\"".$type."\" />
                            <div class=\"button_wrapper clearfix\">
                                <div class=\"button_left\">
                                        <ul class=\"button_navigation\">
                                                $button
                                        </ul>
                                </div>
                                <div class=\"button_right\">
                                ".search_box('taxonomy.php','list_taxonomy','state='.$_GET['state'].'&rule='.$rule.'&group='.$type.'&prc=search&','right','alert_green_form')."
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
    function all_categories($index,$rule_name,$group,$rule_id=array()){
        $xcode=json_encode($rule_id);
        
        $return=recursive_taxonomy($index,$rule_name,$group,'checkbox',$rule_id);
        $return.="<span id=\"selected_category_".$index."\">$xcode</span>";
        return $return;
    }
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
    function get_most_used_tags($group,$index=0){
        global $db;
        $sql=$db->prepare_query("SELECT a.lname, a.lrule_id
                            FROM lumonata_rules a
                            WHERE a.lrule=%s 
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
    function insert_rules($parent,$name,$description,$rule,$group,$insert_default=false){
        global $db;
        $parent=rem_slashes($parent);
        $name=rem_slashes($name);
        $sef=generateSefUrl($name);
        $description=rem_slashes($description);
        $rule=rem_slashes($rule);
        $group=rem_slashes($group);
        
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
            $sql=$db->prepare_query("INSERT INTO lumonata_rules(lrule_id,lparent,
                                                            lname,
                                                            lsef,
                                                            ldescription,
                                                            lrule,
                                                            lgroup)
                                VALUES(%d,%d,%s,%s,%s,%s,%s)",1,
                                                            $parent,
                                                            $name,
                                                            $sef,
                                                            $description,
                                                            $rule,
                                                            $group);
        }else{
            $sql=$db->prepare_query("INSERT INTO lumonata_rules(lparent,
                                                                lname,
                                                                lsef,
                                                                ldescription,
                                                                lrule,
                                                                lgroup)
                                    VALUES(%d,%s,%s,%s,%s,%s)",$parent,
                                                                $name,
                                                                $sef,
                                                                $description,
                                                                $rule,
                                                                $group);
        }
        if(reset_order_id("lumonata_rules"))
           if($db->do_query($sql))
                return mysql_insert_id();

    }
    function update_rules($rule_id,$parent,$name,$description,$rule,$group){
        global $db;
        $parent=rem_slashes($parent);
        $name=rem_slashes($name);
        $sef=generateSefUrl($name);
        $description=rem_slashes($description);
        $rule=rem_slashes($rule);
        $group=rem_slashes($group);
        
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
                                     lgroup=%s
                                WHERE lrule_id=%d",
                                    $parent,
                                    $name,
                                    $sef,
                                    $description,
                                    $rule,
                                    $group,
                                    $rule_id
                                );
        return $db->do_query($sql);
                

    }
    function update_rule_count($rule_id,$rule_count){
        global $db;
        $sql=$db->prepare_query("UPDATE lumonata_rules
                                SET lcount=%d
                                WHERE lrule_id=%d",$rule_count,$rule_id);
        return $db->do_query($sql);
    }
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
    function update_rules_relationship($rule_id,$app_id,$new_rule_id){
        global $db;
        $sql=$db->prepare_query("UPDATE lumonata_rule_relationship SET lrule_id=%d
                                 WHERE lrule_id=%d AND lapp_id=%d",$new_rule_id,$rule_id,$app_id);
        
        if($db->do_query($sql)){
            $rule_count=count_rules_relationship("rule_id=$rule_id");
            return update_rule_count($rule_id,$rule_count);
        }
    }
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
    function delete_rules_relationship($args='',$rule=''){
        global $db;
        $var_name['app_id']='';
        $var_name['rule_id']='';
        $where="";
        
        /*if(empty($rule))
            return;
        */
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
            if(!empty($rule)){
                $where.=" AND b.lrule='".$db->_real_escape($rule)."' AND a.lrule_id=b.lrule_id";  
                $sql=$db->prepare_query("DELETE lumonata_rule_relationship a FROM lumonata_rule_relationship a, lumonata_rules b $where ");
            }else{
                $sql=$db->prepare_query("DELETE lumonata_rule_relationship a FROM lumonata_rule_relationship a $where ");
            }
            return $db->do_query($sql);
               
            
        }
        
    }
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
?>