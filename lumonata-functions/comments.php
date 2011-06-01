<?php
   
    if(isset($_GET['people_like'])){
    	require_once('../lumonata_config.php');
		require_once 'user.php';
		//if(is_user_logged()){
	    	require_once('../lumonata_settings.php');
	    	require_once('settings.php');
	    	if(!defined('SITE_URL'))
				define('SITE_URL',site_url());
				
    		echo get_who_likes_html($_GET['people_like']);
		//}
    }else{
    	add_actions('tail','get_javascript','textarea-expander');
    }	
    //add_actions('tail','hide_comment_onmouseup'); 
    function hide_comment_onmouseup(){
        return "<script type=\"text/javascript\">
                    var mouse_is_inside = false;
                    $(document).ready(function(){
                         
                        $('.commentbox').mouseover(function(){ 
                            mouse_is_inside=true; 
                        }, function(){ 
                            mouse_is_inside=false; 
                        });

                        $('.comment_area_loged').focus(function(){ 
                            mouse_is_inside=true; 
                        }, function(){ 
                            mouse_is_inside=false; 
                        });
                        
                        $('body').mousedown(function(){
                        	
                            if(!mouse_is_inside) {
                            	
        						$('.commentbox').hide();
        						$('.writecomment').show();
        					}
                        });
                    });

                   
                </script>";
    }
    function comments($post_id,$comment_allowed,$limit=0,$avatar_thmb=2){
        global $db;

        $query=$db->prepare_query("SELECT * from lumonata_comments
                                WHERE larticle_id=%d AND lcomment_status='approved' AND lcomment_type='comment' ",$post_id);
        
        $rn=$db->do_query($query);
        $nn=$db->num_rows($rn);
        $start_row=$nn-$limit;

        if($limit!=0 && $nn>$limit){
        	$view_all="View all";
        }else{
        	$view_all="";
        }
        
        $sql=$db->prepare_query("SELECT * FROM lumonata_articles where larticle_id=%d",$post_id);
        $rc=$db->do_query($sql);
        $dc=$db->fetch_array($rc);
        
        //Find seting if the comment is auto close
   		$close_comment=false;
        if(get_meta_data('is_auto_close_comment')==1){
      
        	$post_date=strtotime($dc['lpost_date']);
        	$now=time();
        	
        	$post_old=floor(($now-$post_date)/(60*60*24));
        	if($post_old>=get_meta_data('days_auto_close_comment')){
        		$close_comment=true;
        	}
        	
        }
        
        $comment="<br clear='all' /><div style=\"text-align:right;width:97%;\">";
        if($comment_allowed=='allowed'){
        	if(!$close_comment){
	            $comment.="<a href=\"javascript:;\" class=\"commentview \" id=\"insertcomment_".$post_id."\">Comment</a> - ";
	            $comment.="<script type=\"text/javascript\">
	                        $(function(){
	                            $('#insertcomment_".$post_id."').click(function(){
	                            		
	                                   $('#commentbox_".$post_id."').show();
	                                   $('.write_comment_wrapper_".$post_id."').hide();
	                                   $('#commentarea_".$post_id."').focus();
	                                  
	                                });
	                        
	                            $('#writecomment_".$post_id."').focus(function(){
	                                   $('#commentbox_".$post_id."').show();
	                                   $('#commentarea_".$post_id."').focus();
	                                   $('.write_comment_wrapper_".$post_id."').hide();
	                                  
	                             });
	                           
	                       	 });
	                        
	                      </script>";
        	}
        }
        if(is_allow_post_like()){
        	if(is_user_logged()){
        		if(is_ilike_this_post($post_id)){
        			$comment.="<a href=\"javascript:;\" class=\"commentview\" id=\"unlike_post_".$post_id."\">Unlike</a>";
        			$comment.=" <a href=\"javascript:;\" class=\"commentview\" id=\"like_post_".$post_id."\" style=\"display:none;\">Like</a>";
        		}else{
        			$comment.="<a href=\"javascript:;\" class=\"commentview\" id=\"like_post_".$post_id."\">Like</a>";
        			$comment.="<a href=\"javascript:;\" class=\"commentview\" id=\"unlike_post_".$post_id."\" style=\"display:none;\">Unlike</a>";
        		}
        		$comment.="<script type=\"text/javascript\">
        					 $('#like_post_".$post_id."').click(function(){
        					 		$.post('http://".site_url()."/lumonata_comments.php','comment_type=like&article_id=".$post_id."&parent_id=0&lad=".LUMONATA_ADMIN."',function(data){
                                    	$('#people_like_".$post_id."').html(data);
                                    	$('#unlike_post_".$post_id."').show();
                                    	$('#like_post_".$post_id."').hide();
                                    	$('.comment_wrapper_".$post_id."').show();
                                    });
                             });
                             
                             $('#unlike_post_".$post_id."').click(function(){
        					 		$.post('http://".site_url()."/lumonata_comments.php','comment_type=unlike&article_id=".$post_id."&parent_id=0&lad=".LUMONATA_ADMIN."',function(data){
                                    	$('#people_like_".$post_id."').html(data);
                                    	$('#unlike_post_".$post_id."').hide();
                                    	$('#like_post_".$post_id."').show();
                                    });
                             });
                             
        			   </script>";
        		
        	}else{ 
        		$comment.="<a href=\"".signin_url()."\" class=\"commentview\" id=\"like_post_".$post_id."\">Like</a>";
        		
        	}
        }
        
              
        $comment.="</div>";
        $comment.="<div class=\"comment_box comment_box_".$post_id."\" id=\"comment_box_".$post_id."\">";
        
    	if($dc['lcount_like']>0){
    		$who_like=get_who_likes($post_id,'like');
    		$people_like=json_encode($who_like);
    		$people_like=base64_encode($people_like);
    		
        	if($dc['lcount_like']>1){
        		$count_like_1=count($who_like)-1;
        		
        		if(isset($_COOKIE['user_id']) && $who_like[0]['luser_id']==$_COOKIE['user_id']){
        			$who_like_name_1="You";
        		}else{
        			$who_like_name_1=$who_like[0]['ldisplay_name'];
        		}
        		
        		if(isset($_COOKIE['user_id']) && $who_like[1]['luser_id']==$_COOKIE['user_id']){
        			$who_like_name_2="You";
        		}else{
        			$who_like_name_2=$who_like[1]['ldisplay_name'];
        		}
        		
        		if($count_like_1 > 1){
        			$liked="";
        			$liked.="- <a href=\"".user_url($who_like[0]['luser_id'])."\" class=\"commentview\">".$who_like_name_1."</a> and <a href=\"http://".site_url()."/lumonata-functions/comments.php?people_like=".$people_like."\" class=\"commentview peoplelike\" >".$count_like_1." other </a> people like this";
        			
        		}else{ 
        			$liked="- <a href=\"".user_url($who_like[0]['luser_id'])."\" class=\"commentview\">".$who_like_name_1."</a> and <a href=\"".user_url($who_like[1]['luser_id'])."\" class=\"commentview\">".$who_like_name_2."</a> like this";
        		}
        	}else{
        		if($who_like[0]['luser_id']==$_COOKIE['user_id']){
        			$who_like_name_1="You";
        		}else{
        			$who_like_name_1=$who_like[0]['ldisplay_name'];
        		}
        		$liked="- <a href=\"".user_url($who_like[0]['luser_id'])."\" class=\"commentview\">".$who_like_name_1."</a> like this";
        	}
        }else{
        	$liked="";
        }
        
    	if(!LUMONATA_ADMIN){
        		$link=permalink($post_id)."#comment_box_".$post_id;
        		$loader="";
        		$comment.="<script type=\"text/javascript\">
        						$(function(){
        							$('.peoplelike').fancybox();
        							
        						});
        			        </script>";
        }else{
        	
        	$link="javascript:;";
        	$loader="&nbsp;&nbsp;
        			<span id=\"loading_comments_".$post_id."\" style=\"display:none;\">
        				<img src=\"".get_theme_img()."/loader.gif\" />
        			</span>";
        	$comment.="<script type=\"text/javascript\">
        					$(function(){
	        					$('#view_all_comments_".$post_id."').click(function(){
	        						$('#loading_comments_".$post_id."').show();
	        						
	        						$.post('http://".site_url()."/lumonata_comments.php','view_all=all&post_id=".$post_id."&avatar_thmb=".$avatar_thmb."&limit=".$nn."&nn=".$nn."&start_row=0&lad=".LUMONATA_ADMIN."',function(data){
                                   	 	$('#comments_list_area_".$post_id."').html(data);
                                   	 	$('#loading_comments_".$post_id."').hide();
                                    });
                                    
	        					});
        					});
        				</script>";
        	$comment.="<script type=\"text/javascript\">
        						$(function(){
        							$('.peoplelike').colorbox();
        							
        						});
        			        </script>";
        	
        	
        }
        if($nn>$limit || $dc['lcount_like']>0){
        	
	        $comment.=" <div class=\"the_comment comment_wrapper_".$post_id."\">
	                        <a href=\"".$link."\" class=\"commentview\" id=\"view_all_comments_".$post_id."\">$view_all $nn comments</a>
	                        <span id=\"people_like_".$post_id."\">$liked</span>
	                        $loader
	                    </div>";
        }else{
        	$comment.=" <div class=\"the_comment comment_wrapper_".$post_id."\"  style=\"display:none;\">
        					<a href=\"".$link."\" class=\"commentview\" id=\"view_all_comments_".$post_id."\">$view_all $nn comments</a>
	                        <span id=\"people_like_".$post_id."\">$liked</span>
	                    </div>";
        }

     
        
        $comment.="<div id=\"comments_list_area_".$post_id."\">".fetch_comments_list($limit,$nn,$start_row,$avatar_thmb,$post_id)."</div>";
        
        
        if($comment_allowed=='allowed'){
        	if(!$close_comment){
        		if($nn<1)
        			$style="style=\"display:none;\"";
        		else 
        			$style='';
        			
	        	$comment.="<div class=\"the_comment write_comment_wrapper_".$post_id." writecomment\" ".$style.">
	                       		<input id=\"writecomment_".$post_id."\" type=\"text\" value=\"Write your comment\" readonly=\"readonly\" class=\"inputtext\" style=\"width:98%;color:#666666;border:1px solid #BBB;\" />
	                       </div>
	                       <script type=\"text/javascript\">
	                       	$(function(){
	                       		$('#commentarea_".$post_id."').blur(function(){
		                               var thecomment=$('#commentarea_".$post_id."').val();
		                               var attr_name=$('#commentarea_".$post_id."').attr('name');
		                               if(thecomment=='' && attr_name!='username'){	
		                                   $('#commentbox_".$post_id."').hide();
		                                   $('.write_comment_wrapper_".$post_id."').show();
		                                   $('.write_comment_wrapper_".$post_id."').focus();
		                               }
		                         });
		                     });
	                       </script>
	                       ";
	        	
	            $is_login_to_comment=get_meta_data('is_login_to_comment');
	            if($is_login_to_comment==1){
	                if(is_user_logged ()){
	                    $comment.=logged_comment_interface($post_id,$avatar_thmb);
	                }else{
	                    $comment.=login_comment_interface($post_id,$avatar_thmb);
	                }
	            }else{
	                if(is_user_logged ())
	                    $comment.=logged_comment_interface($post_id,$avatar_thmb);
	                else
	                    $comment.=public_comment_interface($post_id,$avatar_thmb);
	            }
        	}
        }
        
        $comment.="</div>";
        
        return $comment;
    }
    function get_who_likes($post_id,$post_type){
    	global $db;
    	$users=array();
    	$query=$db->prepare_query("SELECT luser_id 
    	                    FROM lumonata_comments
    	                    WHERE larticle_id=%d AND lcomment_type=%s",$post_id,$post_type);
    	
    	$result=$db->do_query($query);
    	while($comment=$db->fetch_array($result)){
    		$users[]=fetch_user($comment['luser_id']);
    	}
    	return $users;
    }
    function get_who_likes_html($string){
		$people=base64_decode($string);
		$people=json_decode($people,true);
		$return="<div style=\"width:400px;height:350px;overflow:auto;\">";
		foreach ($people as $key=>$value){
			$return.="<div class=\"clearfix\" style=\"width:185px;height:60px;border:1px solid #ccc;margin:5px 5px;cursor: pointer;float:left;\" >";
					$return.="<div class=\"clearfix\" >";
						$return.="<div style=\"width:50px;height:50px;overflow:hidden;margin:5px 5px;float:left;\">";
							$return.="<a href=\"".user_url($people[$key]['luser_id'])."\">";
							$return.="<img src=\"".get_avatar($people[$key]['luser_id'],2)."\" />";
							$return.="</a>";
						$return.="</div>";
						$return.="<div style=\"width:110px;height:50px;overflow:hidden;margin:5px 5px;float:left;font-weight:bold;\">";
							$return.="<a href=\"".user_url($people[$key]['luser_id'])."\" style=\"display:block;text-decoration:none;height:inherit;\">";
								$return.=$people[$key]['ldisplay_name'];
							$return.="</a>";
						$return.="</div>";
					$return.="</div>";
				$return.="</div>";
		}
		$return.="</div>";
		
		return $return;
		
	}
    function fetch_comments_list($limit,$nn,$start_row,$avatar_thmb,$post_id){
    	global $db;
    	$comment="";
    	$i=0;
    	
   		$query=$db->prepare_query("SELECT * from lumonata_comments
                                WHERE larticle_id=%d AND lcomment_status='approved' AND lcomment_type='comment' ",$post_id);
        
        if($limit!=0 && $nn>$limit){
        	switch(comment_page_displayed()){
        		case 'last':
        			$query=$db->prepare_query("SELECT * from lumonata_comments
	                                     WHERE larticle_id=%d AND lcomment_status='approved' AND lcomment_type='comment'
	                                     LIMIT %d,%d",$post_id,$start_row,$limit);
        			break;
        		case 'first':
        			$query=$db->prepare_query("SELECT * from lumonata_comments
	                                     WHERE larticle_id=%d AND lcomment_status='approved' AND lcomment_type='comment'
	                                     LIMIT %d",$post_id,$limit);
        			break;
        	}
            $r=$db->do_query($query);
        }else{
            $r=$db->do_query($query);
        }
    	
    	while($data=$db->fetch_array($r)){

            //Display Name of Commentator
            if($data['luser_id']!=0){
               $d=fetch_user($data['luser_id']);
               $display_name=$d['ldisplay_name'];
            }else{
                $display_name=$data['lcomentator_name'];
            }

            //USER URL
            if(empty($data['lcomentator_url'])){
                $commentator_url="<div class=\"commentator_name\">".$display_name."</div>";
                $commentator_url_img="<img src=\"".get_avatar($data['luser_id'],$avatar_thmb)."\" alt=\"".$data['lcomentator_name']."\" title=\"".$data['lcomentator_name']."\" border=\"0\"  />";
            }elseif($data['luser_id']!=0){
            	$commentator_url="<div class=\"commentator_name\"><a href=\"".user_url($data['luser_id'])."\">".$display_name."</a></div>";
            	$commentator_url_img="<a href=\"".user_url($data['luser_id'])."\" ><img src=\"".get_avatar($data['luser_id'],$avatar_thmb)."\" alt=\"".$data['lcomentator_name']."\" title=\"".$data['lcomentator_name']."\" border=\"0\"  /></a>";
            	
            }else{
                $commentator_url="<div class=\"commentator_name\"><a href=\"".$data['lcomentator_url']."\" rel=\"nofollow\">".$display_name."</a></div>";
                $commentator_url_img= "<a href=\"".$data['lcomentator_url']."\" rel=\"nofollow\"><img src=\"".get_avatar($data['luser_id'],$avatar_thmb)."\" alt=\"".$data['lcomentator_name']."\" title=\"".$data['lcomentator_name']."\" border=\"0\"  /></a>";
            }
            
    		if(!LUMONATA_ADMIN){
            	$comment.="<script type=\"text/javascript\">
        						$(function(){
        							$('.peoplelikecomment').fancybox();
        						});
        			        </script>";
            }else{       
            	$comment.="<script type=\"text/javascript\">
	        				$(function(){
	        					$('.peoplelikecomment').colorbox();
	        				});
        			   </script>";
            }
            
            //like comments
	        $likeit=get_like_button($data['lcomment_id'],$post_id);
        	$liked_comment=get_liked_comment($data['lcomment_like'],$data['lcomment_id']);
        	$manageit=get_manage_comment($post_id,$data['luser_id'],$data['lcomment_id']);
	        
	        
            $comment.="
            <div class=\"the_comment comment_wrapper_".$post_id."\" id=\"cmmt_id_".$data['lcomment_id']."\">
                <div class=\"the_avatar\">
                   $commentator_url_img
                </div>
                <div class=\"comment\">
                    ".$commentator_url."
                <div class=\"the_comment_content\"> ".nl2br($data['lcomment'])."</div>
                    <div><span class=\"comment_date\">".nicetime($data['lcomment_date'],date("Y-m-d H:i:s"))."</span> $likeit  <span id=\"people_like_comment_".$data['lcomment_id']."\">$liked_comment</span> $manageit </div>
                </div>
            </div>";

            
            $i++;
           
            
        }
        return $comment;
    }
    function get_liked_comment($liked,$post_id=0){
    	if($liked>0){
    		$people_who_like=get_who_likes($post_id, 'like_comment');
    		$people_like=json_encode($people_who_like);
    		$people_like=base64_encode($people_like);
    		$liked_comment="";
        	$liked_comment.=" - <a href=\"http://".site_url()."/lumonata-functions/comments.php?people_like=".$people_like."\" class=\"commentview peoplelikecomment\">".$liked." people like this</a>";
        	
        }else{
        	$liked_comment="";
        	
        }
        return $liked_comment;
    }
    function get_manage_comment($post_id,$user_id,$comment_id,$hit_manage=true){
    	global $db;
    	$manageit="";
    	$comment=fetch_comment($comment_id);
    	
    	$sql=$db->prepare_query("SELECT * FROM lumonata_articles WHERE larticle_id=%d",$post_id);
    	$r=$db->do_query($sql);
    	$d=$db->fetch_array($r);
    	
    	if(isset($_COOKIE['user_id'])){
	        if(is_administrator() || is_editor() || ($_COOKIE['user_id']==$user_id) || ($_COOKIE['user_id']==$d['lpost_by'])){
	        	$manageit=" - <a href=\"javascript:;\" rel=\"delete_".$comment_id."\" class=\"commentview\">Delete</a>";
	        	//if($hit_manage){
	        		//if(LUMONATA_ADMIN){	
	        			$manageit.=delete_confirmation_box($comment_id,"Are sure want to delete comment from ".$comment['lcomentator_name']." on ".get_article_title($comment['larticle_id'])." ?","http://".site_url()."/lumonata-admin/comments.php","cmmt_id_".$comment_id,'state=comments&prc=delete&id='.$comment_id);
	        		//}else{
	        		//	add_actions('tail','delete_confirmation_box',$comment_id,"Are sure want to delete comment from ".$comment['lcomentator_name']." on ".get_article_title($comment['larticle_id'])." ?","http://".site_url()."/lumonata-admin/comments.php","cmmt_id_".$comment_id,'state=comments&prc=delete&id='.$comment_id);
	        		//}
	        		//add_actions('admin_tail','delete_confirmation_box',$comment_id,"Are sure want to delete comment from ".$comment['lcomentator_name']." on ".get_article_title($comment['larticle_id'])." ?","http://".site_url()."/lumonata-admin/comments.php","cmmt_id_".$comment_id,'state=comments&prc=delete&id='.$comment_id);
	        		
	        		//$manageit.=delete_confirmation_box($comment_id,"Are sure want to delete comment from ".$comment['lcomentator_name']." on ".get_article_title($comment['larticle_id'])." ?","http://".site_url()."/lumonata-admin/comments.php","cmmt_id_".$comment_id,'state=comments&prc=delete&id='.$comment_id);
	        	//}
	        	
	        }
    	}
        return $manageit;
    }
    function get_like_button($comment_id,$post_id,$hit_like=true){
    	if(is_user_logged()){
        	if(is_ilike_this_comment($comment_id)){
        		$likeit="- <a href=\"javascript:;\" class=\"commentview\" id=\"unlike_comment_".$comment_id."\" >Unlike</a>";
        		$likeit.="<a href=\"javascript:;\" class=\"commentview\" id=\"like_comment_".$comment_id."\" style=\"display:none;\" >Like</a>";
        	}else{
        		$likeit="- <a href=\"javascript:;\" class=\"commentview\" id=\"like_comment_".$comment_id."\" >Like</a>";
          		$likeit.="<a href=\"javascript:;\" class=\"commentview\" id=\"unlike_comment_".$comment_id."\" style=\"display:none;\" >Unlike</a>";
        	}
        	if($hit_like)
        	$likeit.=hit_like($comment_id);
        	
        }else{
          	$likeit=" - <a href=\"".signin_url()."\" class=\"commentview\" >Like</a>";
          	
        }
        return $likeit;
    }
    function hit_like($comment_id){
    	$lad=LUMONATA_ADMIN;
    	
    	$likeit="<script type=\"text/javascript\">
        			 $('#like_comment_".$comment_id."').click(function(){
        			 		$.post('http://".site_url()."/lumonata_comments.php','comment_type=like_comment&article_id=".$comment_id."&parent_id=0&lad=".$lad."',function(data){
                                    $('#people_like_comment_".$comment_id."').html(data);
                                    $('#unlike_comment_".$comment_id."').show();
                                    $('#like_comment_".$comment_id."').hide();
                                    });
                             });
                             
                             $('#unlike_comment_".$comment_id."').click(function(){
                             
        			 		$.post('http://".site_url()."/lumonata_comments.php','comment_type=unlike_comment&article_id=".$comment_id."&parent_id=0&lad=".$lad."',function(data){
                                    $('#people_like_comment_".$comment_id."').html(data);
                                    $('#unlike_comment_".$comment_id."').hide();
                                    $('#like_comment_".$comment_id."').show();
                                    });
                             });
                             
        	   </script>";
    	return $likeit;
    }
    function public_comment_interface($post_id,$avatar_thumb){
        $return="<div id=\"commentbox_".$post_id."\" style=\"display:none;\" class=\"commentbox\">
                        <p style=\"text-align:right;padding:5px;font-size:14px;\" >Your email address will not be published. Required fields are marked <span style=\"color:red;\">*</span></p>
                        <div id=\"alert_".$post_id."\"></div>
                        <div class=\"the_comment\">
                            <div class=\"the_avatar\"></div>
                            <div class=\"comment\" style=\"margin-left:-30px;\">
                                <p>Name<span style=\"color:red;\">*</span> :<br />
                                    <input type=\"text\" name=\"name\" id=\"commentarea_".$post_id."\" class=\"inputtext\" style=\"width:98%;\" />
                                </p>
    
                                <p>Email<span style=\"color:red;\">*</span> :<br />
                                    <input type=\"text\" name=\"email\" id=\"email_".$post_id."\" class=\"inputtext\" style=\"width:98%;\" />
                                </p>
                                <p>Website:<br />
                                    <input type=\"text\" name=\"website\" id=\"website_".$post_id."\" class=\"inputtext\" style=\"width:98%;\" value=\"http://\" />
                                 </p>
                                <p>Comment<span style=\"color:red;\">*</span> :<br />
                                <textarea class=\"comment_area expand50-1000\" name=\"comment\" id=\"comment_".$post_id."\"  ></textarea>
                                </p>
                                <div class=\"comment_button\"><input type=\"button\" id=\"send_comment_".$post_id."\" value=\"Comment\" name=\"comment_button\" class=\"button\" /></div>
                            </div>
                        </div>
                </div>
                <input type=\"hidden\" name=\"article_id\" value=\"".$post_id."\" />
                <input type=\"hidden\" name=\"parent_id\" value=\"0\" />
               
                <script type=\"text/javascript\">
                    $(function(){
                    	
                        $('#send_comment_".$post_id."').click(function(){
                            var thename=$('#commentarea_".$post_id."').val();
                            var theemail=$('#email_".$post_id."').val();
                            var thewebsite=$('#website_".$post_id."').val();
                            var thecomment=$('#comment_".$post_id."').val();
                            var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
  							thecomment_show=thecomment.replace(exp,'<a href=\"$1\">$1</a>'); 
                            
                            if(thename==''){
                                $('#alert_".$post_id."').html('<div id=\"thecommentalert\" style=\"padding:5px;background-color:#FFCC66;color:#333333;font-size:12px;-moz-border-radius:5px;-webkit-border-radius:5px;margin:5px;\">Name required</div>');
                                $('#commentarea_".$post_id."').focus();
                                $('#thecommentalert').delay(3000);
                                $('#thecommentalert').slideUp(500);
                                return;
                            }else if(theemail==''){
                                $('#alert_".$post_id."').html('<div id=\"thecommentalert\" style=\"padding:5px;background-color:#FFCC66;color:#333333;font-size:12px;-moz-border-radius:5px;-webkit-border-radius:5px;margin:5px;\">Email address required</div>');
                                $('#email_".$post_id."').focus();
                                $('#thecommentalert').delay(3000);
                                $('#thecommentalert').slideUp(500);
                                return;
                            }
                            
                            if(thecomment!=''){
                                $.post('http://".site_url()."/lumonata_comments.php','comment_type=comment&name='+thename+'&email='+encodeURIComponent(theemail)+'&website='+encodeURIComponent(thewebsite)+'&comment='+encodeURIComponent(thecomment)+'&article_id=".$post_id."&parent_id=0&lad=".LUMONATA_ADMIN."',function(data){
                                    $('#alert_".$post_id."').html(data);
                                });

                                var count_child=$('.comment_wrapper_".$post_id."').size();
                                
                                if(thewebsite=='')
                                    var commentator_url='<div class=\"commentator_name\">'+thename+'</div>';
                                else
                                    var commentator_url='<div class=\"commentator_name\"><a href=\"'+thewebsite+'\">'+thename+'</a></div>';

                                thetag='<div class=\"the_comment comment_wrapper_".$post_id."\">';
                                thetag+='<div class=\"the_avatar\">';
                                thetag+='<img src=\"".get_avatar((is_user_logged())?$_COOKIE['user_id']:0,$avatar_thumb)."\" alt=\"+thename+\" title=\"+thename+\"  />';
                                thetag+='</div>';
                                thetag+='<div class=\"comment\">';
                                thetag+=commentator_url;
                                thetag+=thecomment_show;
                                thetag+='<div class=\"comment_date\">".nicetime(date("Y-m-d H:i:s"),date("Y-m-d H:i:s"))."</div>';
                                thetag+='</div>';
                                thetag+='</div>';

                               
                                if(count_child==0){
                                    $(\".comment_box_".$post_id."\").html('');
                                    $(\".comment_box_".$post_id."\").append(thetag);
                                }else{
                                    $(\".comment_wrapper_".$post_id.":last\").after(thetag);
                                }
                            }
                        });
                    });

                   
                </script>";
        
        return $return;
    }
    function login_comment_interface($post_id){
    	
        return "
                <div class=\"commentbox the_comment\" id=\"commentbox_".$post_id."\" style=\"display:none;\">
                	<form method=\"post\" action=\"".get_admin_url()."/?redirect=".cur_pageURL()."\">
	                    <div class=\"login_comment\">
	                    	<div class=\"signup_text\"><a href=\"".get_admin_url()."/?state=login&redirect=".cur_pageURL()."\" rel=\"nofollow\">Sign In</a> or <a href=\"".get_admin_url()."/?state=register\" rel=\"nofollow\">Sign Up</a> Now</div>
	                        Username: <input type=\"text\" name=\"username\" id=\"commentarea_".$post_id."\" class=\"inputtext\" style=\"width:200px;\" />
	                        Password: <input type=\"password\" name=\"password\" class=\"inputtext\" style=\"width:200px;\" />
	                        <input type=\"submit\" value=\"Sign In\" name=\"login\" class=\"button\" />
	                        <input type=\"button\" value=\"Sign Up\" name=\"signup\" class=\"button\" onclick=\"location='".get_admin_url()."/?state=register'\" />
	                                
	                    </div>
                    </form>
                </div>
              ";
    }
    
    function logged_comment_interface($post_id,$avatar_thmb){
        $d=fetch_user($_COOKIE['user_id']);
        if(is_permalink()){
            $url='http://'.site_url().'/user/'.$d['lusername'].'/';
        }else{
            $url='http://'.site_url().'/?user='.$d['lusername'];
        }
        return "<div id=\"alert_".$post_id."\"></div>
                    <div class=\"the_comment commentbox\" id=\"commentbox_".$post_id."\" style=\"display:none;\">
                    <div class=\"the_avatar\"><img src=\"".get_avatar($_COOKIE['user_id'],$avatar_thmb)."\"  /></div>
                        <div class=\"comment\">
                            <textarea class=\"comment_area_loged expand50-1000\" name=\"comment\"  id=\"commentarea_".$post_id."\"></textarea>
                            <div class=\"comment_button\">
                            	<img src=\"".get_admin_url()."/includes/media/loader.gif\" class=\"comment_loading\" style=\"display:none;\" />
                            	<input type=\"button\" id=\"send_comment_".$post_id."\" value=\"Comment\" name=\"comment_button\" class=\"button\" />
                            </div>
                        </div>
                    </div>
                   
                <script type=\"text/javascript\">
                	   
                         
                        $('#send_comment_".$post_id."').click(function(){
                            var thecomment=$('#commentarea_".$post_id."').val();
                            var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
  							thecomment_show=thecomment.replace(exp,'<a href=\"$1\">$1</a>');
							
                            $('.comment_loading').show();
                            $('#send_comment_".$post_id."').attr('disabled',true);
                            $('#commentarea_".$post_id."').attr('disabled',true);	
                            
                            $.post('http://".site_url()."/lumonata_comments.php','comment_type=comment&comment='+encodeURIComponent(thecomment)+'&article_id=".$post_id."&parent_id=0&lad=".LUMONATA_ADMIN."',function(data){
                            	var count_child=$('.comment_wrapper_".$post_id."').size();
                            	if(count_child==0){
                            		$(\".comment_box_".$post_id."\").html('');
                                	$(\".comment_box_".$post_id."\").append(data);
                            	}else{
                                	$(\".comment_wrapper_".$post_id.":last\").after(data);
                            	}
                            	
                            	$('.comment_loading').hide();
                            	$('#send_comment_".$post_id."').attr('disabled',false);
                                $('#commentarea_".$post_id."').attr('disabled',false);
                            });
                           
                        });
                   

                 
                </script>
                ";
    }
    function is_duplicate_comment($comment){
    	global $db;
    	$query=$db->prepare_query("SELECT * 
    								FROM lumonata_comments 
    								WHERE lcomment=%s AND lcomment_type='comment'",
    								$comment);
    	$result=$db->do_query($query);
    	if($db->num_rows($result)>0)
    	return true;
    }
    function insert_comment($parent_id,$article_id,$comment,$name='',$email='',$url='',$comment_type='comment'){
        global $db,$allowedtags,$allowedtitletags,$thepost;
		
        $article=fetch_artciles_by_id($article_id);
        
        $name=kses(rem_slashes($name),$allowedtitletags);
        $email=kses(rem_slashes($email),$allowedtitletags);
        $url=kses(rem_slashes($url),$allowedtitletags);
        $comment=kses(rem_slashes($comment),$allowedtags);
        
        $writer=get_writer_email($article_id);
        $writer_email=$writer['lemail'];
        $writer_name=$writer['ldisplay_name'];
        $call_name=($writer['lsex']==1)?"his":"her";
        if(is_user_logged ()){
            $status='approved';
            
            //commentator details
            $user_id=$_COOKIE['user_id'];
            $d=fetch_user($_COOKIE['user_id']);
            $name=kses(rem_slashes($d['ldisplay_name']),$allowedtitletags);
            $email=kses(rem_slashes($d['lemail']),$allowedtitletags);
            
            if(is_permalink()){
                $url='http://'.site_url().'/user/'.$d['lusername'].'/';
            }else{
                $url='http://'.site_url().'/?user='.$d['lusername'];
            }
            
            $url=kses(rem_slashes($url),$allowedtitletags);
            
            //send notification to writer if the commentator is not the writer
        	if(alert_on_comment() && $writer_email!=$email && $comment_type=='comment'){
           		send_comment_notification($name." commented on your post", $comment, $name, $writer_email,permalink($article_id)."#comment_box_".$article_id);
           		save_notification($article_id,$writer['luser_id'], $user_id, $writer['luser_id'], 'comment', $article['lshare_to']);
        	}
        	
        	
        	if($comment_type=='comment'){
	        	//send notification to previous existing commentator
	        	$send_notification=false;
	        	$commentTypeIn="";
	        	
	        	if(alert_on_comment_reply()){
	        		$commentTypeIn="'comment'";
	        		$send_notification=true;
	        	}
	        	
	        	if(alert_on_liked_post()){
	        		
	        		if($send_notification==true)
	        			$commentTypeIn=$commentTypeIn.",'like'";
	        		else 
	        			$commentTypeIn="like";
	        				
	        		$send_notification=true;
	        	}
	        	
	        	if(alert_on_liked_comment()){
	        		
	        		if($send_notification==true)
	        			$commentTypeIn=$commentTypeIn.",'like_comment'";
	        		else 
	        			$commentTypeIn="like_comment";
	        				
	        		$send_notification=true;
	        	}
	        	
	        	if($send_notification){	
		            $commentator_result=get_commentator_email($article_id, $writer_email,$email,$commentTypeIn);
		            while($theemail=$db->fetch_array($commentator_result)){
		            	$writer_name=($name==$writer_name)?$call_name:$writer_name;
		            	send_comment_notification($name." also commented on ".$writer_name." post", $comment, $name, $theemail['lemail'],permalink($article_id)."#comment_box_".$article_id);
		            	save_notification($article_id,$writer['luser_id'], $user_id, $theemail['luser_id'], 'comment', $article['lshare_to']);	
		            }
	        	}
	        	
        	}elseif($comment_type=='like'){
        		if($writer['luser_id']!=$user_id){
        			send_like_notification($name." like your post", $writer_email,permalink($article_id)."#comment_box_".$article_id);
        			save_notification($article_id,$writer['luser_id'], $user_id, $writer['luser_id'], 'like', $article['lshare_to']);
        		}
        	}elseif($comment_type=='like_comment'){
        		//Comment data that liked 
        		$theComment=fetch_comment($article_id);
        		$prev_commentator=fetch_user($theComment['luser_id']);
        		
        		
				$prev_commentator_email=$prev_commentator['lemail'];
				$prev_commentator_name=$prev_commentator['ldisplay_name'];
				
					
        		//Writer Data
        		$writer=get_writer_email($theComment['larticle_id']);
        		$writer_email=$writer['lemail'];
		        $writer_name=$writer['ldisplay_name'];

		        //send to writer
		        if($theComment['luser_id']!=$_COOKIE['user_id']){
			        if($prev_commentator_email==$writer_email){
			        	send_like_notification($name." like your comment on your post", $writer_email,permalink($theComment['larticle_id'])."#comment_box_".$theComment['larticle_id']);
			        	save_notification($article_id,$writer['luser_id'], $user_id, $writer['luser_id'], 'like_comment', $article['lshare_to']);
			        }else{
			        	if(trim($name)!=$writer_name){
			        		send_like_notification($name." like ".$prev_commentator_name."'s comment on your post", $writer_email,permalink($theComment['larticle_id'])."#comment_box_".$theComment['larticle_id']);
			        		save_notification($article_id,$writer['luser_id'], $user_id, $writer['luser_id'], 'like_comment', $article['lshare_to']);
			        	}
			        	send_like_notification($name." like your comment on ".$writer_name."'s post", $prev_commentator_email,permalink($theComment['larticle_id'])."#comment_box_".$theComment['larticle_id']);
			        	save_notification($article_id,$writer['luser_id'], $user_id, $theComment['luser_id'], 'like_comment', $article['lshare_to']);
			        } 
		        }
		        	
		      
        	}
        	

        }else{
            $status='moderation';
            $user_id='';
            
            //send notifcation to commentator to register as new member and comment will immedietly approved
            unreguser_comment_notification($name, $email);
        }
        $comment=activate_URLs($comment);        
        $sql=$db->prepare_query("INSERT INTO lumonata_comments(
                                    lcomment_parent,
                                    larticle_id,
                                    lcomentator_name,
                                    lcomentator_email,
                                    lcomentator_url,
                                    lcomentator_ip,
                                    lcomment_date,
                                    lcomment,
                                    lcomment_status,
                                    lcomment_like,
                                    luser_id,
                                    lcomment_type)
                                   VALUES(
                                   %d,
                                   %d,
                                   %s,
                                   %s,
                                   %s,
                                   %s,
                                   %s,
                                   %s,
                                   %s,
                                   %d,
                                   %d,
                                   %s)",
                                   $parent_id
                                   ,$article_id
                                   ,$name
                                   ,$email
                                   ,$url
                                   ,getip()
                                   ,date("Y-m-d H:i:s",time())
                                   ,$comment
                                   ,$status
                                   ,0
                                   ,$user_id
                                   ,$comment_type);
         
         //if(!is_duplicate_comment($comment)){
         	$r=$db->do_query($sql);
         	$thepost->comment_id=mysql_insert_id();
	         if($r){
	         	//send notification to Editor & Administrator when status=moderation
	         	if($status=="moderation" && alert_on_comment()){
		        	
	         		$query="SELECT * FROM lumonata_users
		        			WHERE luser_type IN('administrator','editor')";
		        	$result_query=$db->do_query($query);
		        	
		        	while($result_data=$db->fetch_array($result_query)){
		        		
		        		$the_writer_name=($result_data['ldisplay_name']==$writer_name)?"your":$writer_name;
		        		$subject=$name." commented on ".$the_writer_name." post";
		        		send_comment_alert(mysql_insert_id(), $subject, $comment, $name, $result_data['lemail']);
		        	}
		        	
	         	}
	         	
	         	if($comment_type=="like")
	         		return update_count_like($article_id);
	         	
	         	if($comment_type=="like_comment")
	         		return update_count_comment_like($article_id);
	         		
	         	return update_count_comment($article_id);
	         }
         //}
        
    }
    
    function update_count_comment($article_id){
    	global $db;
    	//find the update number of approved comment
         $query=$db->prepare_query("SELECT * 
         		FROM lumonata_comments 
         		WHERE larticle_id=%d AND lcomment_status='approved' AND lcomment_type='comment'",$article_id);
         $result=$db->do_query($query);
         $thenumber_of_comment=$db->num_rows($result);
         
         //update the number in lumonata_articles table
         $query=$db->prepare_query("UPDATE lumonata_articles
         						   SET lcomment_count=%d 
         						   WHERE larticle_id=%d",$thenumber_of_comment,$article_id);
         return $db->do_query($query);
    }
    function update_count_like($article_id){
    	global $db;
    	//find the update number of approved comment
         $query=$db->prepare_query("SELECT * 
         		FROM lumonata_comments 
         		WHERE larticle_id=%d AND lcomment_status='approved' AND lcomment_type='like'",$article_id);
         $result=$db->do_query($query);
         $thenumber_of_comment=$db->num_rows($result);
         
         //update the number in lumonata_articles table
         $query=$db->prepare_query("UPDATE lumonata_articles
         						   SET lcount_like=%d 
         						   WHERE larticle_id=%d",$thenumber_of_comment,$article_id);
         return $db->do_query($query);
    }
    function update_count_comment_like($id){
    	global $db;
    	    	
    	$query=$db->prepare_query("SELECT lcomment_like FROM lumonata_comments WHERE larticle_id=%d AND lcomment_type='like_comment'",$id);
    	$result1=$db->do_query($query);
    	$n=$db->num_rows($result1);
    		
    	
    	$query=$db->prepare_query("UPDATE lumonata_comments SET lcomment_like=%d WHERE lcomment_id=%d",$n, $id);
    	return $result_u=$db->do_query($query);
    }
    function count_comment_status($status,$comment_type='comment'){
    	global $db;
		$query=$db->prepare_query("SELECT *  FROM lumonata_comments WHERE lcomment_status=%s AND lcomment_type=%s",$status,$comment_type);
		$result=$db->do_query($query);
		return $db->num_rows($result);
    }
    function unlike($article_id,$comment_type){
		global $db;
		$query=$db->prepare_query("DELETE FROM lumonata_comments WHERE larticle_id=%d AND lcomment_type=%s AND luser_id=%s",$article_id,$comment_type,$_COOKIE['user_id']);
		if($db->do_query($query)){
			
			if($comment_type=="like_comment")
         		return update_count_comment_like($article_id);
         	
			return update_count_like($article_id);
		}
	}
	function delete_comment($id){
		global $db;
		$query=$db->prepare_query("DELETE FROM lumonata_comments WHERE lcomment_id=%d",$id);
		if($db->do_query($query)){
			$comment=fetch_comment($id);
			return update_count_comment($comment['larticle_id']);
		}
	}
	function fetch_comment($id){
		global $db;
		$query=$db->prepare_query("SELECT * FROM lumonata_comments where lcomment_id=%d",$id);
		$result=$db->do_query($query);
		return $db->fetch_array($result);
		
	}
	function update_comment_status($id,$status){
		global $db;
		$query=$db->prepare_query("UPDATE lumonata_comments
									SET lcomment_status=%s
									WHERE lcomment_id=%d",$status,$id);
		
		if($db->do_query($query)){
			$comment=fetch_comment($id);
			return update_count_comment($comment['larticle_id']);
		}
	}
	function is_ilike_this_post($artcile_id,$comment_type='like'){
		global $db;
		$query=$db->prepare_query("SELECT * 
									FROM lumonata_comments 
									WHERE larticle_id=%d AND lcomment_type=%s AND luser_id=%d",$artcile_id,$comment_type,$_COOKIE['user_id']);
		$result=$db->do_query($query);
		if($db->num_rows($result)>0)
			return true;
		else 
			return false;
	}
	function is_ilike_this_comment($comment_id){
		return is_ilike_this_post($comment_id,'like_comment');
	}
	function update_comment($name,$email,$url,$status,$comment,$id){
		global $db;
		$query=$db->prepare_query("UPDATE lumonata_comments
									SET lcomment_status=%s,
									lcomentator_name=%s,
									lcomentator_email=%s,
									lcomentator_url=%s,
									lcomment=%s
									WHERE lcomment_id=%d",$status,$name,$email,$url,$comment,$id);
		//echo $query;
		if($db->do_query($query)){
			$comment=fetch_comment($id);
			return update_count_comment($comment['larticle_id']);
		}
	}
    function get_writer_email($article_id){
    	global $db;
       $sql=$db->prepare_query("select * from lumonata_articles where larticle_id=%d",$article_id);
       $query_result=$db->do_query($sql);
       $article=$db->fetch_array($query_result);
       
       return $the_user=fetch_user($article['lpost_by']);
       //return $the_user['lemail'];
    }
    function get_commentator_email($article_id,$writter_email,$commentator_email,$comment_type="comment"){
    	global $db;
    	

    	$query=$db->prepare_query("SELECT distinct(a.lemail),a.luser_id
    								FROM lumonata_users a,lumonata_comments b
    								WHERE a.luser_id=b.luser_id 
    								AND b.larticle_id=%d 
    								AND a.lemail NOT IN(%s,%s)
    								AND b.lcomment_status='approved'
    								AND b.lcomment_type in (".$comment_type.")
    								",$article_id,
    								  $writter_email,
    								  $commentator_email);
    	
    	return $result=$db->do_query($query);
    	//return $db->fetch_array($result);
    }
    function admin_comments(){
    	global $db;
    	
    	if(is_save_changes()){
    		if(empty($_GET['tab']))
	          	$tab='all';
	        else 
	          	$tab=$_GET['tab'];
	        $return=false;  	
    		if(is_edit()){
        		$return=update_comment($_POST['name'][0],$_POST['email'][0], $_POST['url'][0], $_POST['status'][0], $_POST['comment'][0], $_POST['id'][0]);
        		if($return){
        			header("location:".get_tab_url($tab));
        		}
    		}elseif(is_edit_all_comment()){
    			foreach ($_POST['id'] as $key=>$val){
    				$return=update_comment($_POST['name'][$key],$_POST['email'][$key], $_POST['url'][$key], $_POST['status'][$key], $_POST['comment'][$key], $_POST['id'][$key]);
    			}
    			if($return){
        			header("location:".get_tab_url($tab));
        		}
    		}
        		
        }
        
        $the_pending_comment="";
        $pending_count=count_comment_status('moderation');
        if($pending_count>0)
        	$the_pending_comment="<span id=\"comment_pending\">($pending_count)</span>";
        
        $the_approved_comment="";
        $approved_count=count_comment_status('approved');
        if($approved_count>0)
        	$the_approved_comment="<span id=\"comment_approved\">($approved_count)</span>";
        	
    	$tabs=array('all'=>'All','moderation'=>'Pending '.$the_pending_comment,'approved'=>'Approved '.$the_approved_comment);
    	
    	$tab_keys=array_keys($tabs);
        $tabb='';
        if(empty($_GET['tab']))
              $the_tab=$tab_keys[0];
        else
              $the_tab=$_GET['tab'];
       
       	$the_tabs=set_tabs($tabs,$the_tab);
    	
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
      
        if(is_approved()){
        	if(isset($_POST['select'])){
                   foreach($_POST['select'] as $key=>$val){
                       update_comment_status($val,'approved');
                   }
            }
        }elseif(is_disapproved()){
        	if(isset($_POST['select'])){
                   foreach($_POST['select'] as $key=>$val){
                       update_comment_status($val,'moderation');
                   }
            }
        }elseif(is_delete_all()){
                add_actions('section_title','Delete Comments');
                $warning="<form action=\"\" method=\"post\">";
                if(count($_POST['select'])==1)
                        $warning.="<div class=\"alert_red_form\"><strong>Are you sure want to delete this user:</strong>";
                else
                        $warning.="<div class=\"alert_red_form\"><strong>Are you sure want to delete these users:</strong>";
                        
                $warning.="<ol>";	
                foreach($_POST['select'] as $key=>$val){
                       $query=$db->prepare_query("SELECT * FROM lumonata_comments WHERE lcomment_id=%d",$val);
                       $result=$db->do_query($query);
                       $d=$db->fetch_array($result);
                       $warning.="<li>".$d['lcomment']."</li>";
                       $warning.="<input type=\"hidden\" name=\"id[]\" value=\"".$d['lcomment_id']."\">";
                }
                $warning.="</ol></div>";
                $warning.="<div style=\"text-align:right;margin:10px 5px 0 0;\">";
                $warning.="<input type=\"submit\" name=\"confirm_delete\" value=\"Yes\" class=\"button\" />";
                $warning.="<input type=\"button\" name=\"confirm_delete\" value=\"No\" class=\"button\" onclick=\"location='".get_state_url('comments')."'\" />";
                $warning.="</div>";
                $warning.="</form>";
                
                return $warning;
        }elseif(is_confirm_delete()){
                foreach($_POST['id'] as $key=>$val){
                        delete_comment($val);
                }
        }elseif(is_edit()){
        	return edit_comment($the_tabs,$_GET['id']);		
        }elseif(is_edit_all()){
        	return edit_comment($the_tabs);	
        }
        
        
              
       
       if($the_tab=='all')
       		return get_comments_list($the_tabs);
       else 
       		return get_comments_list($the_tabs,$the_tab);
      
    }
    function edit_comment($tabs,$id=0){
    	global $db;
    	$index=0;
    	$loop_time=1;
    	
    	add_actions('section_title','Edit Comments');
    	//set template
        set_template(TEMPLATE_PATH."/comments.html",'comments');
        //set block
        add_block('loopingComments','bLoppComments','comments');
        add_block('thecomments','bComments','comments');
        add_variable("tab", $tabs);
          if(empty($_GET['tab']))
          	$tab='all';
          else 
          	$tab=$_GET['tab'];
          	
          $button="<li>".button('button=save_changes&type=submit&enable=true&label=Save')."</li>
                <li>".button('button=cancel',get_tab_url($tab))."</li>";
        
        add_variable("button", $button);
        if(is_edit_all() && isset($_POST['select'])){
        	foreach($_POST['select'] as $index=>$id){
		        $query=$db->prepare_query("SELECT * FROM lumonata_comments WHERE lcomment_id=%d",$id);
		        $result=$db->do_query($query);
		        $d=$db->fetch_array($result);
		        
		        $sts_ar=array('approved'=>'Approved','moderation'=>'Pending');
		        $thestatus="";
		        
		        foreach ($sts_ar as $key=>$val){
		        	
		        	if($key==$d['lcomment_status']){
		        		$thestatus.="<input type=\"radio\" value=\"$key\" name=\"status[$index]\" checked=\"checked\" /> $val";
		        	}else{
		        		$thestatus.="<input type=\"radio\" value=\"$key\" name=\"status[$index]\" /> $val";
		        	}
		        }
		        add_variable('textarea',textarea('comment['.$index.']',$index,$d['lcomment'],$id,false));
		        add_variable('name', $d['lcomentator_name']);
		        add_variable('email', $d['lcomentator_email']);
		        add_variable('url', $d['lcomentator_url']);
		        add_variable('ip', $d['lcomentator_ip']);
		        add_variable('i', $index);
		        add_variable('id', $id);
		        add_variable('status', $thestatus);
		        parse_template('loopingComments','bLoppComments',true);
		        $index++;
        	}
        	 add_variable('is_edit_all',"<input type=\"hidden\" name=\"edit\" value=\"Edit Comments\">");
        }elseif(is_edit()){
        		$query=$db->prepare_query("SELECT * FROM lumonata_comments WHERE lcomment_id=%d",$id);
		        $result=$db->do_query($query);
		        $d=$db->fetch_array($result);
		        
		        $sts_ar=array('approved'=>'Approved','moderation'=>'Pending');
		        $thestatus="";
		        foreach ($sts_ar as $key=>$val){
		        	if($key==$d['lcomment_status']){
		        		$thestatus.="<input type=\"radio\" value=\"$key\" name=\"status[$index]\" checked=\"checked\" /> $val";
		        	}else{
		        		$thestatus.="<input type=\"radio\" value=\"$key\" name=\"status[$index]\" /> $val";
		        	}
		        }
		        add_variable('textarea',textarea('comment['.$index.']',$index,$d['lcomment'],$id,false));
		        add_variable('name', $d['lcomentator_name']);
		        add_variable('email', $d['lcomentator_email']);
		        add_variable('url', $d['lcomentator_url']);
		        add_variable('ip', $d['lcomentator_ip']);
		        add_variable('i', $index);
		        add_variable('id', $id);
		        add_variable('status', $thestatus);
		        parse_template('loopingComments','bLoppComments',false);
        }
        parse_template('thecomments','bComments',false);
        return return_template('comments');
    }
    function get_comments_list($the_tabs,$status='all'){
       global $db;
       
       //setcookie('comments_opened', true, time()+60*60*24,'/');
       
       if($status=='all')
       		add_actions('section_title','The Comments');
       elseif($status=='moderation') 
       		add_actions('section_title','Pending Comments');
       else
       		add_actions('section_title','Approved Comments');
       		
       $viewed=list_viewed();
       if(isset($_GET['page'])){
            $page= $_GET['page'];
       }else{
            $page=1;
       }
        
       $limit=($page-1)*$viewed;
       if($status=='all'){
	       $query=$db->prepare_query("SELECT a.lcomentator_name,a.luser_id,a.lcomment,b.larticle_title,a.lcomment_date,a.lcomment_id,a.lcomment_status
									FROM lumonata_comments a 
									LEFT JOIN lumonata_articles b ON a.larticle_id=b.larticle_id
									WHERE lcomment_type='comment'
									ORDER BY a.lcomment_date DESC 
									LIMIT %d,%d",$limit,$viewed);
	       
	        $num_rows=count_rows("SELECT a.lcomentator_name,a.luser_id,a.lcomment,b.larticle_title,a.lcomment_date,a.lcomment_status
								FROM lumonata_comments a 
								LEFT JOIN lumonata_articles b ON a.larticle_id=b.larticle_id
								WHERE lcomment_type='comment'
								"); 	
	         $url=get_state_url('comments')."&page=";
       }else{
       		$query=$db->prepare_query("SELECT a.lcomentator_name,a.luser_id,a.lcomment,b.larticle_title,a.lcomment_date,a.lcomment_id,a.lcomment_status
									FROM lumonata_comments a 
									LEFT JOIN lumonata_articles b ON a.larticle_id=b.larticle_id
									WHERE a.lcomment_status=%s AND lcomment_type='comment'
									ORDER BY a.lcomment_date DESC 
									LIMIT %d,%d",$status,$limit,$viewed);
       		 $num_rows=count_rows($db->prepare_query("SELECT a.lcomentator_name,a.luser_id,a.lcomment,b.larticle_title,a.lcomment_date,a.lcomment_status
								FROM lumonata_comments a 
								LEFT JOIN lumonata_articles b ON a.larticle_id=b.larticle_id
								WHERE a.lcomment_status=%s AND lcomment_type='comment'",$status));
       		  $url=get_state_url('comments')."&tab=$status&page=";
       }
       $result=$db->do_query($query);
       
      
         
       if($num_rows>0)
       add_actions('header_elements','get_javascript','articles_list');
       
       $button="<li>".button('button=publish&type=submit&enable=false&label=Approved')."</li>
       			<li>".button('button=unpublish&type=submit&enable=false&label=Disapproved')."</li>
       			<li>".button('button=edit&type=submit&enable=false')."</li>
                <li>".button('button=delete&type=submit&enable=false')."</li>";
       
       $list="<h1>Comments</h1>
       			 <ul class=\"tabs\">$the_tabs</ul>
       			  <div class=\"tab_container\">
       			   	<div id=\"response\"></div>
                        <form action=\"\" method=\"post\" name=\"alist\">
                        	<div class=\"button_wrapper clearfix\">
                                <div class=\"button_left\">
                                        <ul class=\"button_navigation\">
                                                $button
                                        </ul>
                                </div>
                            </div>
                            <div class=\"list\">
                                <div class=\"list_title\">
                                    <input type=\"checkbox\" name=\"select_all\" class=\"title_checkbox\" />
                                    <div class=\"pages_title\" style=\"width:200px;\">Post Title</div>
                                    <div class=\"list_author\">Commentator</div>
                                    <div class=\"thecomments\" style=\"width:240px;\">Comments</div>
                                </div>
                                <div id=\"list_item\">";
                                if($num_rows>0){
                                
	                                while($d=$db->fetch_array($result)){
	                                	$commentator=fetch_user($d['luser_id']);
	                                	if(!empty($commentator['ldisplay_name'])){
	                                		$commentator_name=$commentator['ldisplay_name'];
	                                	}else{
	                                		$commentator_name=$d['lcomentator_name'];
	                                	}
	                                	if($status!='all')
	                                		$hide_list="$('#theitem_".$d['lcomment_id']."').css('background','#FF6666');
					    								$('#theitem_".$d['lcomment_id']."').delay(500);
					    								$('#theitem_".$d['lcomment_id']."').fadeOut(700);";
	                                	else 
	                                		$hide_list="";	
	                                	if($d['lcomment_status']=='approved'){
	                                		$the_approval_button="<a href=\"javascript:;\" rel=\"disapproved_".$d['lcomment_id']."\">Disapproved</a>";
	                                		$the_approval_button.="<a href=\"javascript:;\" rel=\"approved_".$d['lcomment_id']."\" style=\"display:none;\">Approved</a>";
	                                		$higlight="";
	                                	}elseif($d['lcomment_status']=='moderation'){
	                                		$the_approval_button="<a href=\"javascript:;\" rel=\"disapproved_".$d['lcomment_id']."\" style=\"display:none;\">Disapproved</a>";
	                                		$the_approval_button.="<a href=\"javascript:;\" rel=\"approved_".$d['lcomment_id']."\">Approved</a>";
	                                		$higlight="style=\"background-color:#f0f0f0;\"";
	                                	}
	                                	if(empty($_GET['tab'])){
	                                		$tabed='all';
	                                	}else{
	                                		$tabed=$_GET['tab'];
	                                	}
	                                	$list.="<div class=\"list_item clearfix\" id=\"theitem_".$d['lcomment_id']."\" $higlight >";
	                                		$list.="<input type=\"checkbox\" name=\"select[]\" class=\"title_checkbox select\" value=\"".$d['lcomment_id']."\" />";
	                                		$list.="<div class=\"pages_title\" style=\"width:200px;\">".$d['larticle_title']."</div>";
	                                		$list.="<div class=\"list_author\"><div class=\"avatar\"><img src=\"".get_avatar($d['luser_id'], 3)."\" /></div>".$commentator_name."</div>";
	                                		$list.="<div class=\"thecomments\" style=\"width:240px;\">".$d['lcomment']."</div>";
	                                		
	                                		$list.="<div class=\"the_navigation_list\">
				                                        <div class=\"list_navigation\" style=\"display:none;\" id=\"the_navigation_".$d['lcomment_id']."\">
				                                                <a href=\"".get_state_url('comments')."&prc=edit&id=".$d['lcomment_id']."&tab=".$tabed."\">Edit</a> |
				                                                <a href=\"javascript:;\" rel=\"delete_".$d['lcomment_id']."\">Delete</a> | ";
	                                							$list.=$the_approval_button;
				                                 $list.="</div>
	                                				</div>
					                                <script type=\"text/javascript\" language=\"javascript\">
					                                        $('#theitem_".$d['lcomment_id']."').mouseover(function(){
					                                                $('#the_navigation_".$d['lcomment_id']."').show();
					                                        });
					                                        $('#theitem_".$d['lcomment_id']."').mouseout(function(){
					                                                $('#the_navigation_".$d['lcomment_id']."').hide();
					                                        });
					                                        $('a[rel=approved_".$d['lcomment_id']."]').click(function(){
					                                        		$.post('comments.php', 'state=comments&prc=approve&id=".$d['lcomment_id']."', function(theResponse){
																		var the_comment=theResponse.split('|');
																		if(the_comment[0] > 0){
																			$('#comment_pending').html('('+the_comment[0]+')');
																			$('#comments_updates').html(the_comment[0]);
																		}else{
																			$('#comment_pending').html('');
																		}
																		
																		if(the_comment[1] > 0){
																			$('#comment_approved').html('('+the_comment[1]+')');
																		}else{
																			$('#comment_approved').html('');
																		}
																	});
																    $('#theitem_".$d['lcomment_id']."').css('background','#FFFFFF');
																    $('a[rel=disapproved_".$d['lcomment_id']."]').show();
																    $('a[rel=approved_".$d['lcomment_id']."]').hide();
																    ".$hide_list."
																    return false;
	                                						});
	                                						$('a[rel=disapproved_".$d['lcomment_id']."]').click(function(){
					                                        		$.post('comments.php', 'state=comments&prc=disapprove&id=".$d['lcomment_id']."', function(theResponse){
																		var the_comment=theResponse.split('|');
																		if(the_comment[0] > 0){
																			$('#comment_pending').html('('+the_comment[0]+')');
																			$('#comments_updates').html(the_comment[0]);
																		}else{
																			$('#comment_pending').html('');
																		}
																		
																		if(the_comment[1] > 0){
																			$('#comment_approved').html('('+the_comment[1]+')');
																		}else{
																			$('#comment_approved').html('');
																		}
																	});
																    $('#theitem_".$d['lcomment_id']."').css('background','#F0F0F0');
																    $('a[rel=disapproved_".$d['lcomment_id']."]').hide();
																    $('a[rel=approved_".$d['lcomment_id']."]').show();
																    ".$hide_list."
																    return false;
	                                						});
					                                </script>";
	                                		//$list.=delete_confirmation_box($d['lcomment_id'],"Are sure want to delete comment from ".$d['lcomentator_name']." on ".$d['larticle_title']." ?","comments.php","theitem_".$d['lcomment_id'],'state=comments&prc=delete&id='.$d['lcomment_id']);
	                                	$list.="</div>";
	                                	add_actions('admin_tail','delete_confirmation_box',$d['lcomment_id'],"Are sure want to delete comment from ".$d['lcomentator_name']." on ".$d['larticle_title']." ?","comments.php","theitem_".$d['lcomment_id'],'state=comments&prc=delete&id='.$d['lcomment_id']);    
				                        add_actions('tail','delete_confirmation_box',$d['lcomment_id'],"Are sure want to delete comment from ".$d['lcomentator_name']." on ".$d['larticle_title']." ?","comments.php","theitem_".$d['lcomment_id'],'state=comments&prc=delete&id='.$d['lcomment_id']);
	                                }
                                }else{
                                	if($status=='all')
                                		$list.="<p>You currently don't have any comments.</p>";
                                	elseif($status=='moderation') 
                                		$list.="<p>You currently don't have any pending comments. <a href=\"".get_tab_url("all")."\">View all comments</a>.</p>";
                                	elseif($status=='approved') 
                                		$list.="<p>You currently don't have any pending comments. <a href=\"".get_tab_url("all")."\">View all comments</a>.</p>";
                                }	
                $list.="		</div>
                			</div>
                        </form>
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
       			  </div>";
                                         
           return $list;
    }
    
?>