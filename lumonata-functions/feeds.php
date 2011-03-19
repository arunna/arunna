<?php
	
	function feeds($section='articles'){
		global $db;
		if($section=="comments" ){
			
			$query=$db->prepare_query("SELECT a.lcomentator_name,a.lcomment,a.larticle_id,a.lcomment_date
					FROM lumonata_comments a
					LEFT JOIN lumonata_articles b ON a.larticle_id=b.larticle_id
					WHERE  a.lcomment_status='approved' AND a.lcomment_type='comment' AND b.larticle_type<>'status'
					ORDER BY a.lcomment_date DESC
					LIMIT %d",rss_viewed());
			
			$result=$db->do_query($query);
			while($data=$db->fetch_array($result)){
				
				$items[]=array(
								"id"=>$data['larticle_id'],
								"title"=>"Comment on \"".get_article_title($data['larticle_id'])."\"",
								"link"=>permalink($data['larticle_id'],true),
								"comments_link"=>permalink($data['larticle_id'],true).'/#comment_box_'.$data['larticle_id'],
								"description"=>$data['lcomment'],
								"pubDate"=>$data['lcomment_date'],
								"creator"=>$data['lcomentator_name']
							);
			}
		}else{
			$query=$db->prepare_query("SELECT a.larticle_title,a.larticle_id,a.larticle_content,a.ldlu,b.ldisplay_name as creator,a.lcomment_count
										FROM lumonata_articles a, lumonata_users b
										WHERE larticle_type=%s AND larticle_status='publish' AND a.lpost_by=b.luser_id 
										ORDER BY a.ldlu DESC
										LIMIT %d",$section,rss_viewed());
			$result=$db->do_query($query);
			while($data=$db->fetch_array($result)){
				$items[]=array(
								"id"=>$data['larticle_id'],
								"title"=>$data['larticle_title'],
								"link"=>permalink($data['larticle_id'],true),
								"comments_link"=>permalink($data['larticle_id'],true).'/#comment_box_'.$data['larticle_id'],
								"comments_feed"=>permalink($data['larticle_id'],true).'/comments-feed/',
								"description"=>$data['larticle_content'],
								"pubDate"=>$data['ldlu'],
								"creator"=>$data['creator'],
							    "comment_count"=>$data['lcomment_count']
							);
			}
		}
		return feeds_format($items,$section);
	}
	function article_comments_rss($id){
		global $db;
		$query=$db->prepare_query("SELECT a.lcomentator_name,a.lcomment,a.larticle_id,a.lcomment_date
						FROM lumonata_comments a
						WHERE  a.lcomment_status='approved' AND a.lcomment_type='comment' AND a.larticle_id=%d
						ORDER BY a.lcomment_date DESC
						LIMIT %d",$id,rss_viewed());
		
		$result=$db->do_query($query);
		while($data=$db->fetch_array($result)){
			
			$items[]=array(
							"id"=>$data['larticle_id'],
							"title"=>"Comment on \"".get_article_title($data['larticle_id'])."\"",
							"link"=>permalink($data['larticle_id'],true),
							"comments_link"=>permalink($data['larticle_id'],true).'/#comment_box_'.$data['larticle_id'],
							"description"=>$data['lcomment'],
							"pubDate"=>$data['lcomment_date'],
							"creator"=>$data['lcomentator_name']
						);
		}
		
		return feeds_format($items,'comments-feed');
	}
	function feeds_format($items=array(),$section){
		
		$feed="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
		$feed.="<rss version=\"2.0\"
					xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"
					xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\"
					xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
					xmlns:atom=\"http://www.w3.org/2005/Atom\"
					xmlns:sy=\"http://purl.org/rss/1.0/modules/syndication/\"
					xmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\" 
					xmlns:media=\"http://search.yahoo.com/mrss/\"
					xmlns:georss=\"http://www.georss.org/georss\" 
					xmlns:geo=\"http://www.w3.org/2003/01/geo/wgs84_pos#\"
				>\n";
		$feed.="<channel>\n";
			$feed.="<title>".web_name()."</title>\n";
			$feed.="<atom:link href=\"http://".site_url()."/feed/".$section."/\" rel=\"self\" type=\"application/rss+xml\" />\n";
			$feed.="<link>http://".site_url()."/</link>\n";
			$feed.="<description>".web_tagline()."</description> \n";
			$feed.="<lastBuildDate>".date("D, d M Y H:i:s",strtotime(last_build()))." +0000 </lastBuildDate>\n";
			$feed.="<sy:updatePeriod>hourly</sy:updatePeriod>\n";
			$feed.="<sy:updateFrequency>1</sy:updateFrequency>\n";
			$feed.="<generator>http://www.lumonata.com</generator>\n";
			foreach ($items as $key=>$val){
				
			    $feed.="<item>\n";
			         $feed.="<title>".utf8_encode($items[$key]['title'])."</title>\n";
			         $feed.="<link>".utf8_encode($items[$key]['link'])."</link>\n";
			         $feed.="<comments>".utf8_encode($items[$key]['comments_link'])."</comments>\n";
			         $feed.="<description><![CDATA[".utf8_encode(substr(strip_tags($items[$key]['description']),0,300)." [...] ")."]]></description>\n";
			         $feed.="<content:encoded><![CDATA[".utf8_encode($items[$key]['description'])."]]></content:encoded>\n";
			         $feed.="<pubDate>".date("D, d M Y H:i:s",strtotime($items[$key]['pubDate']))." +0000 </pubDate>\n";
			         $feed.="<dc:creator>".utf8_encode($items[$key]['creator'])."</dc:creator>\n";
			         
			         if($section!="comments" && $section!="comments-feed")
			         $feed.=get_categories_rss($items[$key]['id']);
			         
			         $feed.="<guid>".utf8_encode($items[$key]['link'])."</guid>\n";
			         
			         if(!empty($items[$key]['comments_feed']))
			         $feed.="<wfw:commentRss>".utf8_encode($items[$key]['comments_feed'])."</wfw:commentRss>\n";
			         
			         if(!empty($items[$key]['comment_count']))
					 $feed.="<slash:comments>".$items[$key]['comment_count']."</slash:comments>\n";
					 
					 if($section!="comments" && $section!="comments-feed")
					 $feed.=get_enclosure($items[$key]['id']);
					 
			    $feed.="</item>\n";
			}
		    
			
      
			
		$feed.="</channel>\n";
		$feed.="</rss>\n";
		return $feed;
	}
	
	function get_categories_rss($id){
		global $db;
		$query=$db->prepare_query("SELECT a.lname
									FROM lumonata_rules a, lumonata_rule_relationship b
									WHERE a.lrule_id=b.lrule_id AND b.lapp_id=%d",$id);
		$result=$db->do_query($query);
		$feed="";
		while($data=$db->fetch_array($result)){
			 $feed.="<category><![CDATA[".utf8_encode($data['lname'])."]]></category>\n";
		}	
		return $feed;
	}
	function get_enclosure($id){
		global $db;
		$query=$db->prepare_query("SELECT *
									FROM lumonata_attachment 
									WHERE larticle_id=%d",$id);
		$result=$db->do_query($query);
		$feed="";
		
		$audio_video_file_type=array('audio/m4a','audio/x-ms-wma','audio/mpeg','application/octet-stream');
		$image_file_type=array('image/jpg','image/jpeg','image/pjpeg','image/gif','image/png');
		$enclosure=false;
		while($data=$db->fetch_array($result)){
			 if(in_array($data['mime_type'], $audio_video_file_type) && $enclosure==false){
			 	$feed.="<enclosure url=\"http://".site_url().$data['lattach_loc']."\" length=\"".filesize(ROOT_PATH.$data['lattach_loc'])."\" type=\"".$data['mime_type']."\" /> \n";
			 	$enclosure=true;
			 }elseif(in_array($data['mime_type'], $image_file_type)){ 
			 	$feed.="<media:content url=\"http://".site_url().$data['lattach_loc']."\" medium=\"image\">\n";
				$feed.="<media:title type=\"html\">".utf8_encode($data['ltitle'])."</media:title>\n";
				$feed.="</media:content>\n";
			 }	
		}	
		return $feed;
	}
	function pubdate($section='articles'){
		global $db;
		$query=$db->prepare_query("SELECT lpost_date 
									FROM lumonata_articles
									WHERE larticle_status='publish' 
									ORDER BY lpost_date ASC");
		$result=$db->do_query($query);
		$data=$db->fetch_array($result);
		return $data['lpost_date'];
	}
	function last_build($section='articles'){
		global $db;
		$query=$db->prepare_query("SELECT ldlu 
									FROM lumonata_articles
									WHERE larticle_status='publish' 
									ORDER BY ldlu DESC");
		$result=$db->do_query($query);
		$data=$db->fetch_array($result);
		return $data['ldlu'];
	}
	function feeds_link($section="articles",$title="Feed"){
		
		if(is_permalink())
			$link="http://".site_url()."/feed/".$section."/";
		else 
			$link="http://".site_url()."/?feed=rss&section=".$section;
			
		return "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".web_name()." : ".$title."\" href=\"".$link."\" />";
	}
	function article_comments_feed($article_id){
		if(is_permalink())
			$link=permalink($article_id)."/comments-feed/";
		else 
			$link=permalink($article_id)."&feed=comments-feed";
			
		return "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".get_article_title($article_id)." : Comments Feed\" href=\"".$link."\" />";
	}
	add_actions('header','feeds_link','articles');
	add_actions('header','feeds_link','comments','Comments Feed');
	if(is_details()){
		add_actions('header','article_comments_feed',post_to_id());
	}
	
	if(is_feed()){
		echo feeds(get_feed_section());
		exit;
	}elseif(is_article_comments_feed()){
		echo article_comments_rss(post_to_id());
		exit;
	}
?>