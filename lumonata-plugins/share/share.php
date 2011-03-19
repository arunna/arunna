<?php
/*
    Plugin Name: Share Button
    Plugin URL: http://lumonata.com/
    Description: Share your article to Facebook And Twitter
    Author: Wahya Biantara
    Author URL: http://wahya.biantara.com/
    Version: 1.0.1
    
    
*/

function facebook_like_button(){
    return "<div style=\"width:48px;height:65px;text-align:center;overflow:hidden;border:1px solid #f0f0f0;float:right;padding:5px;margin:0 5px;\">
    		<iframe src=\"http://www.facebook.com/plugins/like.php?href=".rawurlencode(cur_pageURL())."&amp;layout=box_count&amp;show_faces=true&amp;width=450&amp;action=like&amp;font&amp;colorscheme=light&amp;height=65\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:50px; height:65px;\" allowTransparency=\"true\"></iframe>
    		</div>";
}

function twitter_share_button($article_title=''){
    return "<div style=\"width:55px;height:65px;text-align:center;overflow:hidden;border:1px solid #f0f0f0;float:right;padding:5px;margin:0 5px;\">
    			<a href=\"http://twitter.com/share\" class=\"twitter-share-button\" data-url=\"http://localhost/arunna/about-us/\" data-text=\"".$article_title."\" data-count=\"vertical\">Tweet</a>
    			<script type=\"text/javascript\" src=\"http://platform.twitter.com/widgets.js\"></script>
    		</div>";
}
$title="";
if(is_details()){
    $article=fetch_artciles("id=".post_to_id()."&type=".get_appname());
    $title=$article['larticle_title'];
}elseif (is_page()){
    $article=fetch_artciles("id=".post_to_id());
    $title=$article['larticle_title'];
}

add_actions('additional_article_plugins','facebook_like_button');
add_actions('additional_article_plugins','twitter_share_button',$title);

?>