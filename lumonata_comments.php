<?php

require_once('lumonata_config.php');
require_once('lumonata_settings.php');
require_once('lumonata-functions/settings.php');
require_once('lumonata-classes/actions.php');
require_once('lumonata-functions/user.php');
require_once('lumonata-functions/kses.php');
require_once('lumonata-functions/rewrite.php');
require_once('lumonata-functions/mail.php');
require_once('lumonata-functions/articles.php');
require_once('lumonata-classes/post.php');
require_once('lumonata-functions/comments.php');

/*SET TIMEZONE*/
set_timezone(get_meta_data('time_zone'));
if(isset($_POST['comment_type'])){
	if($_POST['comment_type']=="comment"){
		$invalid=false;
		if(!is_user_logged ()){
		    if(empty($_POST['name'])){
		        echo "<div id=\"thecommentalert\" style=\"padding:5px;background-color:#FFCC66;color:#333333;font-size:12px;-moz-border-radius:5px;-webkit-border-radius:5px;margin:5px;\">Name required</div>";
		        echo "<script type=\"text/javascript\">
		                 $('#commentarea_".$_POST['article_id']."').focus();
		                 $('#thecommentalert').delay(3000);
		                 $('#thecommentalert').slideUp(500);
		              </script>";
		        $invalid=true;
		    }elseif(empty($_POST['email'])){
		        echo "<div id=\"thecommentalert\" style=\"padding:5px;background-color:#FFCC66;color:#333333;font-size:12px;-moz-border-radius:5px;-webkit-border-radius:5px;margin:5px;\">Email required</div>";
		        echo "<script type=\"text/javascript\">
		                 $('#email_".$_POST['article_id']."').focus();
		                 $('#thecommentalert').delay(3000);
		                 $('#thecommentalert').slideUp(500);
		              </script>";
		        $invalid=true;
		    }elseif(!isEmailAddress($_POST['email'])){
		        echo "<div id=\"thecommentalert\" style=\"padding:5px;background-color:#FFCC66;color:#333333;font-size:12px;-moz-border-radius:5px;-webkit-border-radius:5px;margin:5px;\">Invalid email format</div>";
		        echo "<script type=\"text/javascript\">
		                 $('#email_".$_POST['article_id']."').focus();
		                 $('#thecommentalert').delay(3000);
		                 $('#thecommentalert').slideUp(500);
		              </script>";
		        $invalid=true;
		    }elseif(!empty($_POST['website']) && $_POST['website']!="http://" ){
		        if(!is_website_address($_POST['website'])){
		            echo "<div id=\"thecommentalert\" style=\"padding:5px;background-color:#FFCC66;color:#333333;font-size:12px;-moz-border-radius:5px;-webkit-border-radius:5px;margin:5px;\">Wrong website address. Please make sure to type the http://</div>";
		            echo "<script type=\"text/javascript\">
		                     $('#website_".$_POST['article_id']."').focus();
		                     $('#thecommentalert').delay(3000);
		                     $('#thecommentalert').slideUp(500);
		                  </script>";
		            $invalid=true;
		        }
		    }
		}
		if($invalid==false){
		    if(is_user_logged ()){
		        $ins=insert_comment($_POST['parent_id'], $_POST['article_id'], $_POST['comment']);
		        if($ins){
		        	echo get_last_comment($_POST['article_id'], $thepost->comment_id);
		        }
		    }else{
		        $ins=insert_comment ($_POST['parent_id'], $_POST['article_id'], $_POST['comment'], $_POST['name'], $_POST['email'], $_POST['website']);
		        if($ins){
		        	echo "You comment is awating for moderation.";
		        }
		    }
		    if($ins) {
		       if(is_user_logged ()){
		           echo "<script type=\"text/javascript\">
		                    $(function(){
		                       $('#commentarea_".$_POST['article_id']."').val('');
		                    })
		                 </script>";
		       }else{
		           echo "<script type=\"text/javascript\">
		                    $(function(){
		                       $('#commentarea_".$_POST['article_id']."').val('');
		                       $('#email_".$_POST['article_id']."').val('');
		                       $('#website_".$_POST['article_id']."').val('');
		                       $('#comment_".$_POST['article_id']."').val('');
		                    })
		                 </script>";
		       }
		    }
		
		}
	}else{
		
		if($_POST['comment_type']=="like"){
			$ins=insert_comment($_POST['parent_id'], $_POST['article_id'], 'like_post_'.$_POST['article_id'],'','','','like');
		}elseif($_POST['comment_type']=='unlike'){
			unlike($_POST['article_id'],'like');
		}elseif($_POST['comment_type']=="like_comment"){
			insert_comment($_POST['parent_id'], $_POST['article_id'], 'like_comment_'.$_POST['article_id'],'','','','like_comment');
		}elseif($_POST['comment_type']=="unlike_comment"){
			unlike($_POST['article_id'],'like_comment');
		}
		if($_POST['comment_type']=="like" || $_POST['comment_type']=="unlike" ){
			$sql=$db->prepare_query("SELECT lcount_like FROM lumonata_articles WHERE larticle_id=%d",$_POST['article_id']);
			$result=$db->do_query($sql);
			$dc=$db->fetch_array($result);
			if($dc['lcount_like']>0){
				
				$people_like=get_who_likes($_POST['article_id'],'like');
				$people_like=json_encode($people_like);
				$people_like=base64_encode($people_like);
				
				$liked="<script type=\"text/javascript\">
        						$(function(){
        							$('.peoplelike').colorbox();
        						});
        			        </script>";
		    	$liked.="<a href=\"http://".site_url()."/lumonata-functions/comments.php?people_like=".$people_like."\" class=\"commentview peoplelike\" > - ".$dc['lcount_like']." people like this</a>";
		        
		    }else{
		        $liked="";
		    }
		}elseif($_POST['comment_type']=="like_comment" || $_POST['comment_type']=="unlike_comment"){
			$sql=$db->prepare_query("SELECT lcomment_like,luser_id FROM lumonata_comments WHERE lcomment_id=%d",$_POST['article_id']);
			
			$result=$db->do_query($sql);
			$dc=$db->fetch_array($result);
			if($dc['lcomment_like']>0){
		        if($dc['lcomment_like']>1)
		    	    $liked="<a href=\"javascript:;\" class=\"commentview\"> - ".$dc['lcomment_like']." peoples like this comment</a>";
		        else 
		    	    $liked="<a href=\"javascript:;\" class=\"commentview\"> - ".$dc['lcomment_like']." people like this comment</a>";
		    }else{
		        $liked="";
		    }
		   
		}
		
				
		echo $liked;
	}
}else{
	if(isset($_POST['view_all'])){
		echo fetch_comments_list($_POST['limit'],$_POST['nn'],$_POST['start_row'], $_POST['avatar_thmb'], $_POST['post_id']);
	}
}
function get_last_comment($post_id,$comment_id,$avatar_thmb=3){
	 $comment=fetch_comment($comment_id);
     if($comment['luser_id']!=0){
        $d=fetch_user($comment['luser_id']);
     	$display_name=$d['ldisplay_name'];
     }else{
        $display_name=$comment['lcomentator_name'];
     }
	 if(empty($comment['lcomentator_url']))
        $commentator_url="<div class=\"commentator_name\">".$display_name."</div>";
     else
        $commentator_url="<div class=\"commentator_name\"><a href=\"".$comment['lcomentator_url']."\">".$display_name."</a></div>";

     $likeit=get_like_button($comment_id,$post_id);
     $liked_comment=get_liked_comment($comment['lcomment_like']);
     $manageit=get_manage_comment($post_id,$comment['luser_id'],$comment_id);   
        
	 $comment="
            <div class=\"the_comment comment_wrapper_".$post_id."\" id=\"cmmt_id_".$comment_id."\">
                <div class=\"the_avatar\">
                    <img src=\"".get_avatar($comment['luser_id'],$avatar_thmb)."\" alt=\"".$display_name."\" title=\"".$display_name."\"  />
                </div>
                <div class=\"comment\">
                    ".$commentator_url."
                <div class=\"the_comment_content\"> ".nl2br($comment['lcomment'])."</div>
                    <div><span class=\"comment_date\">".nicetime($comment['lcomment_date'],date("Y-m-d H:i:s"))."</span> $likeit  <span id=\"people_like_comment_".$comment_id."\">$liked_comment</span> $manageit </div>
                </div>
            </div>";
	 
	 return $comment;
}
?>
