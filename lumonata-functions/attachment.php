<?php
function insert_attachment($article_id,$title,$mime_type,$attach_loc,$attach_loc_thumb='',$attach_loc_med='',$attach_large='',$alt_text='',$caption=''){
    global $db;
    $upload_date=date('Y-m-d H:i:s',time());
    $date_last_update=date('Y-m-d H:i:s',time());
    
    $title=rem_slashes($title);
    $alt_text=rem_slashes($alt_text);
    $caption=rem_slashes($caption);
    
    $sql=$db->prepare_query("INSERT INTO
                            lumonata_attachment(larticle_id,
                                                lattach_loc,
                                                lattach_loc_thumb,
                                                lattach_loc_medium,
                                                lattach_loc_large,
                                                ltitle,
                                                lalt_text,
                                                lcaption,
                                                upload_date,
                                                date_last_update,
						mime_type,
						lorder)
                            VALUES(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%d)",$article_id,$attach_loc,$attach_loc_thumb,$attach_loc_med,$attach_large,$title,$alt_text,$caption,$upload_date,$date_last_update,$mime_type,1);
    
    if(reset_order_id("lumonata_attachment"))
	if($db->do_query($sql))
	    return true;

    return false;
}

function edit_attachment($attach_id,$title,$order,$alt_text='',$caption=''){
    global $db;
    
    $title=rem_slashes($title);
    $alt_text=rem_slashes($alt_text);
    $caption=rem_slashes($caption);

    $date_last_update=date('Y-m-d H:i:s',time());    
    $sql=$db->prepare_query("UPDATE
                            lumonata_attachment
                            SET ltitle=%s,
                                lalt_text=%s,
                                lcaption=%s,
                                date_last_update=%s,
				lorder=%d
                            WHERE lattach_id=%d",
			    $title,
			    $alt_text,
			    $caption,
			    $date_last_update,
			    $order,
			    $attach_id);
    
    if($db->do_query($sql))
    return true;

    return false;
}

function delete_attachment($attach_id){
    global $db;
    
    //original file
    $loc=attachment_value("lattach_loc",$attach_id);
    if(!empty($loc)){
	$file=ROOT_PATH.$loc;
	
	if(file_exists($file))
		unlink($file);
    }
    
    //thumbanil
    $loc=attachment_value("lattach_loc_thumb",$attach_id);
    if(!empty($loc)){
	$file=ROOT_PATH.$loc;
	if(file_exists($file))
		unlink($file);
    }
    
    //Medium
    $loc=attachment_value("lattach_loc_medium",$attach_id);
    if(!empty($loc)){
	$file=ROOT_PATH.$loc;
	if(file_exists($file))
		unlink($file);
    }
    
    //Large
    $loc=attachment_value("lattach_loc_large",$attach_id);
    if(!empty($loc)){
	$file=ROOT_PATH.$loc;
	if(file_exists($file))
		unlink($file);
    }
    
    $sql=$db->prepare_query("DELETE FROM lumonata_attachment
                            WHERE lattach_id=%d",$attach_id);
    if($db->do_query($sql))
    return true;

    return false;
}

function attachment_value($val,$attach_id){
    global $db;
    $sql=$db->prepare_query("select $val from lumonata_attachment where lattach_id=%d",$attach_id);
    $r=$db->do_query($sql);
    $d=$db->fetch_array($r);
    
    return $d[$val];
}
function upload_image_attachment($source,$file_type,$file_name,$post_id){
    global $db;
    
    $folder_name=upload_folder_name();
    if(!defined('FILES_LOCATION'))
	    define('FILES_LOCATION','/lumonata-content/files');
            
   
    $default_title= file_name_filter($file_name);
    $file_name=character_filter($file_name);
    
    //Thumbnail Image
    $file_name_t='';
    $file_location_t='';
            
    $file_name_t=file_name_filter($file_name).'-thumbnail'.file_name_filter($file_name,true);
    
    $file_location_t=FILES_LOCATION.'/'.$folder_name.'/'.$file_name_t;
    $destination=FILES_PATH.'/'.$folder_name.'/'.$file_name_t;
    add_actions('thumbnail_file_location',$file_location_t);
    
    //create thumbnail image here
    if(upload_resize($source,$destination,$file_type,thumbnail_image_width(),thumbnail_image_height())){
        //Medium Image
        $file_name_m='';
        $file_location_m='';
        
        $file_name_m=file_name_filter($file_name).'-medium'.file_name_filter($file_name,true);
        $file_location_m=FILES_LOCATION.'/'.$folder_name.'/'.$file_name_m;
        
        add_actions('medium_file_location',$file_location_m);
        
        $destination=FILES_PATH.'/'.$folder_name.'/'.$file_name_m;
        //create medium size image here
        if(upload_resize($source,$destination,$file_type,medium_image_width(),medium_image_height())){
            //Large Image
            $file_name_l='';
            $file_location_l='';
            
            $file_name_l=file_name_filter($file_name).'-large'.file_name_filter($file_name,true);
            
            $file_location_l=FILES_LOCATION.'/'.$folder_name.'/'.$file_name_l;
            $destination_l=FILES_PATH.'/'.$folder_name.'/'.$file_name_l;
            
            add_actions('large_file_location',$file_location_l);
            
            //create Large size image here
            if(upload_resize($source,$destination_l,$file_type,large_image_width(),large_image_height())){
                //Original Image
                $destination=FILES_PATH.'/'.$folder_name.'/'.$file_name;
				$file_location=FILES_LOCATION.'/'.$folder_name.'/'.$file_name;
		
                add_actions('original_file_location',FILES_LOCATION.'/'.$folder_name.'/'.$file_name);

                //Upload the original image
                if(upload($source,$destination)){
                    return insert_attachment($post_id,$default_title,$file_type,$file_location,$file_location_t,$file_location_m,$file_location_l);
                }
            }
        }
    }
        
    
    return false;
   
    
}
function the_attachment($article_id,$limit=4){
	global $db;
	
	if(empty($article_id))
	return;
	
	$img="";
	
	if($limit==0)
		$limit="";
	else 
		$limit= "limit ".$limit;
		
	
	$sql=$db->prepare_query("select * from lumonata_attachment where larticle_id=%d ORDER BY lorder ASC $limit ",$article_id);
  	
    $r=$db->do_query($sql);
	while($d=$db->fetch_array($r)){
		$img.="<div class=\"the_thumbs_item\" >
					
					<a rel=\"example_group\" href=\"http://".site_url().$d['lattach_loc_large']."\" title=\"".$d['ltitle']."\" >
					<img id=\"thumbs_".$d['lattach_id']."\" src=\"http://".site_url().$d['lattach_loc_thumb']."\" alt=\"".$d['lalt_text']."\" title=\"".$d['ltitle']."\" border=\"0\" />
					</a>
			   </div>";
		//add_actions('header','jQuery_centering_images',$d['lattach_id']);
	}
		
	return $img;
}
function jQuery_centering_images($id){
	return "<script type=\"text/javascript\">
				$(window).load(function(){
					var parent_height = $('.the_thumbs_item').height();  
					var image_height = $('#thumbs_".$id."').height();
					top_margin = (parent_height - image_height)/2;
					$('#thumbs_".$id."').css( 'margin-top' , top_margin);  
				} );
			</script>";
}
function get_attachment($article_id=0){
    global $db;
    
    $url=get_attachment_tab_url($_GET['tab'])."&page=";
    
    
    $attch="";
    $js='';
        
    $sort_order=" ORDER BY lorder ASC";
    if(isset($_GET['sort_order'])){
	if($_GET['sort_order']=="desc")
	$sort_order=" ORDER BY lorder DESC";
    }
    
    if($article_id!=0)
	$sql=$db->prepare_query("select * from lumonata_attachment where larticle_id=%d $sort_order",$article_id);
    else
	$sql=$db->prepare_query("select * from lumonata_attachment $sort_order");
        
    //setup paging system
    $num_rows=count_rows($sql);
    $viewed=list_viewed();
    if(isset($_GET['page'])){
        $page= $_GET['page'];
    }else{
        $page=1;
    }
    
    $limit=($page-1)*$viewed;
    
    if($article_id!=0)
	$sql=$db->prepare_query("select * from lumonata_attachment where larticle_id=%d $sort_order limit %d, %d",$article_id,$limit,$viewed);
    else
	$sql=$db->prepare_query("select * from lumonata_attachment $sort_order limit %d, %d",$limit,$viewed);
        
    $result=$db->do_query($sql);
    //$result2=$db->do_query($sql);
    
    $i=($page - 1) * $viewed + 1; //start order number
    $y=$i+count_rows($sql)-1; //end order number
    
        
    $attch="<form method=\"post\" action=\"\">
		    <input type=\"hidden\" name=\"start_order\" value=\"$i\" />
		    <input type=\"hidden\" name=\"the_tab\" value=\"".$_GET['tab']."\" />
                    <div class=\"media_navigation\">
                        <div class=\"search_wraper\">".
                            search_box('upload-media.php','media_gallery','textarea_id='.$_GET['textarea_id'].'&tab='.$_GET['tab'].'&article_id='.$article_id.'&')
                        ."</div>
                        <div class=\"paging_media\">".
                            paging($url,$num_rows,$page,$viewed,5)
                        ."</div>
                        <br clear=\"all\" >
                        <div class=\"sort_order\">
                            Sort Order: <a href=\"".get_attachment_tab_url($_GET['tab'])."&sort_order=asc\">Ascending</a> |
                            <a href=\"".get_attachment_tab_url($_GET['tab'])."&sort_order=desc\">Descending </a>
                        </div>
                    </div>
                    <div id=\"response\">{response}</div>
                    <div class=\"media_title clearfix\">
                        <div class=\"media_description\">Media</div>
                         <div class=\"media_action\">Sort / Order</div>
                         <br clear=\"left\" />
                    </div>";
                    
                    $attch.="<div id=\"media_gallery\">";
                        if(isset($_POST['s']) && $_POST['s']!="Search")
                            $attch.=search_attachment_results($_POST['s'],$_GET['tab'],$article_id,$_GET['textarea_id']);
                        else
                            $attch.=gallery_items($result,$_GET['tab'],$_GET['textarea_id'],$i);
                            
                    $attch.="</div>";
                    
                    $attch.="<div style=\"float:left;padding:10px 0;width:50%;\">".
                                button("button=save_changes&label=Save All Changes&name=save_all_changes")
                            ."</div>
                            <div class=\"paging_media\">".
                                paging($url,$num_rows,$page,$viewed,5)
                            ."</div>";
                    
    $attch.="</form>";
    
   
    return $attch;
}
function search_attachment_results($s,$tab,$article_id,$textarea_id){
    global $db;
    if(empty($s)){
        if($tab=='gallery')
            $sql=$db->prepare_query("select * from lumonata_attachment where larticle_id=%d order by lorder",$article_id);
        else
            $sql=$db->prepare_query("select * from lumonata_attachment order by lorder");
            
    }else{
        if($tab=='gallery')
            $sql=$db->prepare_query("select * from lumonata_attachment where larticle_id=%d and (ltitle like %s or lalt_text like %s or lcaption like %s) order by lorder",$article_id,"%".$s."%","%".$s."%","%".$s."%");
        else
            $sql=$db->prepare_query("select * from lumonata_attachment where ltitle like %s or lalt_text like %s or lcaption like %s order by lorder","%".$s."%","%".$s."%","%".$s."%");
    }
    
    $result=$db->do_query($sql);
    if($db->num_rows($result)==0)
    return "<div class=\"alert_yellow\">No results found for <em>$s</em>. Check your spelling or try another term.</div><br />";
    return gallery_items($result,$tab,$textarea_id);

    
}
function gallery_items($result,$tab,$textarea_id,$i=1){
    global $db;
    
    $attch='';
    while($d=$db->fetch_array($result)){
        $attch.="<div class=\"media_gallery_item clearfix\" id=\"attachment_".$d['lattach_id']."\">";
            $attch.="<div class=\"clearfix\">";
                $attch.="<div class=\"media_image_small\">". attachment_icon($d['mime_type'],'60:60',$d['lattach_loc_thumb'])."</div>";
                $attch.="<div class=\"media_item_title\">".stripslashes($d['ltitle'])."</div>";
                $attch.="<div class=\"media_item_action\">";
                    $attch.="<input type=\"text\" value=\"$i\" id=\"order_".$d['lattach_id']."\" class=\"small_textbox\" name=\"order[".$i."]\">";
                    $attch.="&nbsp;<a href=\"#\" id=\"show_attach_".$d['lattach_id']."\">Show</a> | <a href=\"#\" rel=\"delete_".$d['lattach_id']."\">Delete</a>";
                $attch.="</div>";
                $attch.="<br clear=\"all\">";
            $attch.="</div>";
            $attch.="<div class=\"details_item clearfix\" style=\"display:none\" id=\"detail_attach_".$d['lattach_id']."\" >";
                $attch.=attachment_details($d,$i,$textarea_id,$tab);
            $attch.="</div>";
        $attch.="</div>";
        
        $attch.="<script type=\"text/javascript\" language=\"javascript\">
                    $(function(){
                        $('#show_attach_".$d['lattach_id']."').click(function(){
                           
                            $('#detail_attach_".$d['lattach_id']."').slideToggle(100);
                            $(this).text($(this).text()=='Show'?'Hide':'Show');
                            return false;
                        });
                        $('#detail_attach_".$d['lattach_id']."').css('display','none');
                    });
                </script>";
                
        $attch.=delete_confirmation_box($d['lattach_id'],"Are you sure want to delete <code>".$d['ltitle']."</code> from the gallery?","upload-media.php","attachment_".$d['lattach_id']);
        $i++;
    }
    
    return $attch;
}
function count_attachment($article_id=0){
    global $db;
    if($article_id!=0)
	$sql=$db->prepare_query("select * from lumonata_attachment where larticle_id=%d",$article_id);
    else
	$sql=$db->prepare_query("select * from lumonata_attachment");
    
    $r=$db->do_query($sql);
    return $db->num_rows($r);
}
function upload_media_attachment($source,$file_type,$file_name,$post_id){
    $folder_name=upload_folder_name();
    if(!defined('FILES_LOCATION'))
	    define('FILES_LOCATION','/lumonata-content/files');
            
   
    $default_title=file_name_filter($file_name);
    $file_name=character_filter($file_name);
    
    $destination=FILES_PATH.'/'.$folder_name.'/'.$file_name;
    $file_location=FILES_LOCATION.'/'.$folder_name.'/'.$file_name;            

    if(upload($source,$destination)){
	add_actions('original_file_location',$file_location);
	return insert_attachment($post_id,$default_title,$file_type,$file_location);
    }
}
function upload_folder_name(){
    return date("Y",time()).date("m",time());
}
function attachment_icon($file_type,$file_size='',$file_location=''){
    $width="";
    $height="";
    $img="";
    if(!empty($file_size)){
	list($width,$height)=explode(":",$file_size);
	$width="width=\"".$width."px\"";
	$height="width=\"".$height."px\"";
    }
    switch($file_type){
	case "image/jpg":
	case "image/jpeg":
	case "image/pjpeg":
	case "image/gif":
	case "image/png":
	    $img="<img src=\"http://".SITE_URL.$file_location."\" $width $height />";
	    break;
	case "application/x-shockwave-flash":
	    $img="<img src=\"http://".SITE_URL."/lumonata-admin/includes/media/default.png\" $width $height />";
	    break;
	case "application/octet-stream":
	    $img="<img src=\"http://".SITE_URL."/lumonata-admin/includes/media/video.png\" $width $height />";
	    break;
	case "audio/m4a":
	case "audio/x-ms-wma":
	case "audio/mpeg":
	    $img="<img src=\"http://".SITE_URL."/lumonata-admin/includes/media/audio.png\" $width $height />";
	    break;
	case "application/octet":
	case "application/pdf":
	    $img="<img src=\"http://".SITE_URL."/lumonata-admin/includes/media/document.png\" $width $height />";
	    break;
	case "application/msword":
	    $img="<img src=\"http://".SITE_URL."/lumonata-admin/includes/media/document.png\" $width $height />";
	    break;
	case "text/plain":
	    $img="<img src=\"http://".SITE_URL."/lumonata-admin/includes/media/text.png\" $width $height />";
	    break;
    }
    
    return $img;
}
function update_attachment_order($order,$start){
    global $db;
    foreach($order as $key=>$val){
	$sql=$db->prepare_query("UPDATE lumonata_attachment
				 SET lorder=%d
				 WHERE lattach_id=%d",$key+$start,$val);
	$db->do_query($sql);
    }
}

function attachment_details($d,$index,$textarea_id,$tab){
    $mime_type=array('image/jpg','image/jpeg','image/pjpeg','image/gif','image/png');
    $detail="";
    if(in_array($d['mime_type'],$mime_type)){
        
        
        
	$detail="<div class=\"details_attachment_set clearfix\">";
        
            $detail.="<div class=\"media_field_left_image\">";
            $detail.=attachment_icon($d['mime_type'],'100:100',$d['lattach_loc_medium']);
            $detail.="</div>";
            
            $detail.="<div class=\"media_field_right_image\">";
            $detail.="<strong>File Type : </strong>".$d['mime_type']."<br /><br />";
            $detail.="<strong>Upload Date : </strong>".date(get_date_format(),strtotime($d['upload_date']))."<br /><br />";
            $detail.="</div>";
            
        $detail.="</div>";
       
        $detail.="<div class=\"details_attachment_set clearfix\">";
            $detail.="<div class=\"media_field_left\">";
            $detail.="<strong>Title: </strong>";
            $detail.="</div>";
           
            $detail.="<div class=\"media_field_right\">";
            $detail.="<input type=\"text\" class=\"textbox\" name=\"title[$index]\" value=\"".stripslashes($d['ltitle'])."\" />";
            $detail.="</div>";
            
            
        $detail.="</div>";
        
          
        $detail.="<div class=\"details_attachment_set clearfix\">";
            $detail.="<div class=\"media_field_left\">";
            $detail.="<strong>Alternate Text: </strong>";
            $detail.="</div>";
            
            $detail.="<div class=\"media_field_right\">";
            $detail.="<input type=\"text\" class=\"textbox\" name=\"alt_text[$index]\" value=\"".stripslashes($d['lalt_text'])."\" />";
            $detail.="</div>";
            
        $detail.="</div>";
        
        $detail.="<div class=\"details_attachment_set clearfix\">";
            $detail.="<div class=\"media_field_left\">";
            $detail.="<strong>Caption: </strong>";
            $detail.="</div>";
            
            $detail.="<div class=\"media_field_right\">";
            $detail.="<input type=\"text\" class=\"textbox\" name=\"caption[$index]\" value=\"".stripslashes($d['lcaption'])."\" />";
            $detail.="</div>";
            
        $detail.="</div>";
        
        $detail.="<div class=\"details_attachment_set clearfix\">";
            $detail.="<div class=\"media_field_left\">";
            $detail.="<strong>Alignment: </strong>";
            $detail.="</div>";
            
            $detail.="<div class=\"media_field_right\">";
            $detail.="<ul id=\"alignment\">
                        <li id=\"alignment_none\" ><input type=\"radio\" name=\"alignment[$index]\" value=\"none\" checked=\"checked\" />None</li>
                        <li id=\"alignment_left\"><input type=\"radio\" name=\"alignment[$index]\" value=\"left\"   />Left</li>
                        <li id=\"alignment_middle\"> <input type=\"radio\" name=\"alignment[$index]\" value=\"center\"  />Center</li>
                        <li id=\"alignment_right\"><input type=\"radio\" name=\"alignment[$index]\" value=\"right\"  />Right</li>
                    </ul>";
            $detail.="</div>";
            
        $detail.="</div>";
       
        $detail.="<div class=\"details_attachment_set clearfix\">";
            $detail.="<div class=\"media_field_left\">";
            $detail.="<strong>Image Size: </strong>";
            $detail.="</div>";
            
            $detail.="<div class=\"media_field_right\">";
            $detail.="<input type=\"radio\" name=\"image_size[$index]\" value=\"thumbnail\" />Thumbnail (".thumbnail_image_width()." x ".thumbnail_image_height().")
                    <input type=\"radio\" name=\"image_size[$index]\" value=\"medium\" checked=\"checked\" />Medium (".medium_image_width()." x ".medium_image_height().")
                    <input type=\"radio\" name=\"image_size[$index]\" value=\"large\"  />Large (".large_image_width()." x ".large_image_height().")
                    <input type=\"radio\" name=\"image_size[$index]\" value=\"original\"  />Original
                    
                    <input type=\"hidden\" name=\"thumbnail[$index]\" value=\"http://".SITE_URL.$d['lattach_loc_thumb']."\" />
                    <input type=\"hidden\" name=\"medium[$index]\" value=\"http://".SITE_URL.$d['lattach_loc_medium']."\" />
                    <input type=\"hidden\" name=\"large[$index]\" value=\"http://".SITE_URL.$d['lattach_loc_large']."\" />
                    <input type=\"hidden\" name=\"original[$index]\" value=\"http://".SITE_URL.$d['lattach_loc']."\" />";
                    
            $detail.="</div>";
            
        $detail.="</div>";
        
        $detail.="<div class=\"details_attachment_set clearfix\">";
            $detail.="<div class=\"media_field_left\">";
            $detail.="<strong>Link Image to: </strong>";
            $detail.="</div>";
            
            $detail.="<div class=\"media_field_right\">";
            $detail.="<input type=\"text\" class=\"textbox\" name=\"link_to[$index]\" id=\"link_to_".$d['lattach_id']."\" value=\"http://".SITE_URL.$d['lattach_loc']."\" /><br />
                    <button type=\"button\" value=\"\" class=\"button\" id=\"link_none_".$d['lattach_id']."\">None</button>
                    <button type=\"button\" value=\"http://".SITE_URL.$d['lattach_loc']."\" class=\"button\" id=\"the_link_".$d['lattach_id']."\">Link to Image</button><br />
                    <input type=\"hidden\" value=\"http://".SITE_URL.$d['lattach_loc']."\" id=\"link_to_file_".$d['lattach_id']."\" />
                    Enter a link URL or click above for presets.";
                    
            $detail.="</div>";
            
        $detail.="</div>";
        
        $detail.="<div class=\"details_attachment_set clearfix\">";
            $detail.="<ul class=\"button_navigation\" style=\"margin:20px 0 20px 0px;text-align:right;\">";
                    $detail.="<li>".button("button=delete&type=button&id=delete_".$d['lattach_id']."&index=".$index."")."&nbsp</li>";
                    $detail.="<li>".button("button=insert&id=insert_".$d['lattach_id']."&index=".$index."&name=insert[$index]")."&nbsp</li>";
                    $detail.="<li>".button("button=save_changes&id=save_changes_".$d['lattach_id']."&index=".$index."&name=save_changes[$index]")."</li>";
            $detail.="</ul>";
        $detail.="</div>";
        
        $detail.="<input type=\"hidden\" name=\"type[$index]\" value=\"".$d['mime_type']."\" />
                <input type=\"hidden\" name=\"textarea_id[$index]\" value=\"".$textarea_id."\" />
                <input type=\"hidden\" name=\"attachment_id[$index]\" value=\"".$d['lattach_id']."\" />";
       
       
        
    }else{
        
	$detail="<div class=\"details_attachment_set clearfix\">";
        
            $detail.="<div class=\"media_field_left_image\">";
            $detail.=attachment_icon($d['mime_type']);
            $detail.="</div>";
            
            $detail.="<div class=\"media_field_right_image\">";
            $detail.="<strong>File Type : </strong>".$d['mime_type']."<br /><br />";
            $detail.="<strong>Upload Date : </strong>".date(get_date_format(),strtotime($d['upload_date']))."<br /><br />";
            $detail.="</div>";
            
        $detail.="</div>";
        
        $detail.="<div class=\"details_attachment_set clearfix\">";
            $detail.="<div class=\"media_field_left\">";
            $detail.="<strong>Title: </strong>";
            $detail.="</div>";
            
            $detail.="<div class=\"media_field_right\">";
            $detail.="<input type=\"text\" class=\"textbox\" name=\"title[$index]\" value=\"".$d['ltitle']."\" />";
            $detail.="</div>";
            
        $detail.="</div>";
        
        
        $detail.="<div class=\"details_attachment_set clearfix\">";
            $detail.="<div class=\"media_field_left\">";
            $detail.="<strong>Caption: </strong>";
            $detail.="</div>";
            
            $detail.="<div class=\"media_field_right\">";
            $detail.="<input type=\"text\" class=\"textbox\" name=\"caption[$index]\" value=\"".$d['lcaption']."\" />";
            $detail.="</div>";
            
        $detail.="</div>";
        
        $detail.="<div class=\"details_attachment_set clearfix\">";
            $detail.="<div class=\"media_field_left\">";
            $detail.="<strong>Description: </strong>";
            $detail.="</div>";
            
            $detail.="<div class=\"media_field_right\">";
            $detail.="<textarea  name=\"alt_text[$index]\" rows=\"5\" />".$d['lalt_text']."</textarea>";
            $detail.="</div>";
            
        $detail.="</div>";
        
        $detail.="<div class=\"details_attachment_set clearfix\">";
            $detail.="<div class=\"media_field_left\">";
            $detail.="<strong>Link File to: </strong>";
            $detail.="</div>";
            
            $detail.="<div class=\"media_field_right\">";
            $detail.="<input type=\"text\" class=\"textbox\" name=\"link_to[$index]\" id=\"link_to_".$d['lattach_id']."\" value=\"http://".SITE_URL.$d['lattach_loc']."\" /><br />
                    <button type=\"button\" value=\"\" class=\"button\" id=\"link_none_".$d['lattach_id']."\">None</button>
                    <button type=\"button\" value=\"http://".SITE_URL.$d['lattach_loc']."\" class=\"button\" id=\"the_link_".$d['lattach_id']."\">Link to Image</button><br />
                    <input type=\"hidden\" value=\"http://".SITE_URL.$d['lattach_loc']."\" id=\"link_to_file_".$d['lattach_id']."\" />";
                    
            $detail.="</div>";
            
        $detail.="</div>";
        
        $detail.="<div class=\"details_attachment_set clearfix\">";
            $detail.="<ul class=\"button_navigation\" style=\"margin:20px 0 20px 0px;text-align:right;\">";
                   $detail.="<li>".button("button=delete&type=button&id=delete_".$d['lattach_id']."&index=".$index."")."&nbsp</li>";
                    $detail.="<li>".button("button=insert&type=button&id=insert_".$d['lattach_id']."&index=".$index."&name=insert[$index]")."&nbsp </li>";
                    $detail.="<li>".button("button=save_changes&id=save_changes_".$d['lattach_id']."&index=".$index."&name=save_changes[$index]")."</li>";
            $detail.="</ul>";
        $detail.="</div>";
        
        $detail.="<input type=\"hidden\" name=\"type[$index]\" value=\"".$d['mime_type']."\" />
                <input type=\"hidden\" name=\"textarea_id[$index]\" value=\"".$textarea_id."\" />
                <input type=\"hidden\" name=\"attachment_id[$index]\" value=\"".$d['lattach_id']."\" />";
        
    }
    
   
    $detail.="
        <script type=\"text/javascript\" language=\"javascript\">
            
            /*LINK TO FILE*/
            $(function(){
                $('#the_link_".$d['lattach_id']."').click(function(){
                    $('#link_to_".$d['lattach_id']."').val($('#link_to_file_".$d['lattach_id']."').val());
                });
             });
             
             /*LINK TO NONE*/
             $(function(){
                $('#link_none_".$d['lattach_id']."').click(function(){
                     $('#link_to_".$d['lattach_id']."').val('');
                });
             });
             
             /*INSERT INTO ARTICLE*/
             $(function(){
                $('input[name=insert[".$index."]]').click(function(){
                    var type= $('input[name=type[".$index."]]').val();
                    var textarea_id=$('input[name=textarea_id[".$index."]]').val();
                    var link_to= $('input[name=link_to[".$index."]]').val();
                    var title_text = $('input[name=title[".$index."]]').val();
                    var caption= $('input[name=caption[".$index."]]').val();
                    var order= $('input[name=order[".$index."]]').val();

                    if(title_text.length!=0){
                        title='title=\"'+title_text+'\"';
                    }else{
                        title='';
                    }
                    
                    if(type=='image/jpg'|| type=='image/jpeg' || type=='image/pjpeg' || type=='image/gif' || type=='image/png'){
                        var image_size = $('input[name=image_size[".$index."]]:checked').val();
                        if(image_size=='thumbnail'){
                            var src=$('input[name=thumbnail[".$index."]]').val();
                        }else if(image_size=='medium'){
                             var src=$('input[name=medium[".$index."]]').val();
                        }else if(image_size=='large'){
                             var src=$('input[name=large[".$index."]]').val();
                        }else if(image_size=='original'){
                             var src=$('input[name=original[".$index."]]').val();
                        }
                        
                        var alignment= $('input[name=alignment[".$index."]]:checked').val();
                        
                        var alt_text= $('input[name=alt_text[".$index."]]').val();
                        if(alt_text.length!=0){
                            alt='alt=\"'+alt_text+'\"';
                        }else{
                            alt='';
                        }
                        
                        if(alignment!='center'){
                            the_float='float:'+alignment;
                            the_center_div='';
                            end_center_div='';
                           
                        }else{
                            the_center_div=\"<p style='text-align:center;'>\";
                            the_float='';
                            end_center_div=\"</p>\";
                        }
                        
                        if(link_to.length!=0){
                            the_link='<a href=\"'+src+'\" '+title+'>';
                            end_link=\"</a>\";
                        }else{
                            the_link='';
                            end_link='';
                        }
                        
                        the_content=the_center_div;
                        the_content+=the_link;
                        the_content+='<img src=\"'+src+'\" '+alt+' '+title+' style=\"'+the_float+'\" />';
                        the_content+=end_link;
                        the_content+=end_center_div;
                        
                    }else{
                        the_content='';
                        src=link_to;
                        
                        var alt_text= $('textarea[name=alt_text[".$index."]]').val();
                        
                        if(link_to.length!=0){
                            the_link='<a href=\"'+src+'\" '+title+'>';
                            end_link=\"</a>\";
                        }else{
                            the_link='';
                            end_link='';
                        }
                        
                        the_content+=the_link;
                        the_content+=title_text;
                        the_content+=end_link;
                    }
		   
                    $.post('upload-media.php', 'insert=true&attachment_id=".$d['lattach_id']."&title='+title_text+'&alt_text='+alt_text+'&caption='+caption+'&order='+order);
                    window.parent.$('#textarea_'+textarea_id).tinymce().execCommand('mceInsertContent',false,the_content);
                    window.parent.$('.upload_image').colorbox.close();
                    return false;
                    
                });
            });
            
            /*SAVE CHANGES*/
            $(function(){
                $('#save_changes_".$d['lattach_id']."').click(function(){
                    var type= $('input[name=type[".$index."]]').val();
                    var title_text = $('input[name=title[".$index."]]').val();
                    var caption= $('input[name=caption[".$index."]]').val();
                    var order= $('input[name=order[".$index."]]').val();
                    
                    if(type=='image/jpg'|| type=='image/jpeg' || type=='image/pjpeg' || type=='image/gif' || type=='image/png'){
                        var alt_text= $('input[name=alt_text[".$index."]]').val();
                    }else{
                        var alt_text= $('textarea[name=alt_text[".$index."]]').val();    
                    }
                    $.post('upload-media.php', 'save_changes=save_item&attachment_id=".$d['lattach_id']."&title='+title_text+'&alt_text='+alt_text+'&caption='+caption+'&order='+order,function(data){
                        $('#response').html(data);
                    });
                    $('#show_attach_".$d['lattach_id']."').text('Show');
                    ";
                    if($tab=="from-computer"){
                        $detail.="$('#upload_image_detail').slideUp();";
                    }else{
                        $detail.="$('#detail_attach_".$d['lattach_id']."').slideUp();";
                    }
                    $detail.="
                    $('#response').slideDown(500);
                    $('#response').delay(3000);
                    $('#response').slideUp(500);
                    return false;
                    
                })
            }) 
        </script>
    ";
    
    return $detail;
}

?>