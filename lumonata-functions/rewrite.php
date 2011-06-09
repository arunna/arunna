<?php
    function get_uri(){
        $url=$_SERVER['REQUEST_URI'];
	// Get rid of the #anchor
	$url_split = explode('#', $url);
	$url = $url_split[0];
       
	// Get rid of URL ?query=string
	$url_split = explode('?', $url);
        
        //If not using permalink then return the passing variables
        if(!is_permalink()){
            if(count($url_split)>1){
                return $url_split[1];
            }
        }
	$url = $url_split[0];
	$site_url="http://".site_url();
	
        
	// Add 'www.' if it is absent and should be there
	if ( false !== strpos($site_url, '://www.') && false === strpos($url, '://www.') )
		$url = str_replace('://', '://www.', $url);
	// Strip 'www.' if it is present and shouldn't be
	if ( false === strpos($site_url, '://www.') )
		$url = str_replace('://www.', '://', $url);
	
	if ( false !== strpos($url, $site_url) ) {
		// Chop off http://domain.com
		$url = str_replace($site_url, '', $url);
	} else {
		// Chop off /path/to/blog
		
		$home_path = parse_url($site_url);
		if(!empty($home_path['path'])){
			$home_path = $home_path['path'];
			$url = str_replace($home_path, '', $url);
		}
		
	}
	// Trim leading and lagging slashes
	 $url = trim($url, '/');
	return $url;
    }
    function post_to_id($app_name='articles'){
		global $db;
		
		$uri=get_uri();
		
		if(is_details($app_name) || is_article_comments_feed()){
		    if(!is_permalink()){
		    	$the_uri=explode("&",$uri);
				$the_uri=explode("=",$the_uri[1]);
				return $the_uri[1];
		    }else{
				$the_uri=explode("/",$uri);
				$theuri=explode(".",$the_uri[2]);
				$sql=$db->prepare_query("SELECT larticle_id
						     FROM lumonata_articles
						     WHERE lsef=%s and larticle_type=%s"
						     ,$theuri[0],$the_uri[0]);
				
				$r=$db->do_query($sql);
				$f=$db->fetch_array($r);
				return $f['larticle_id'];
		    }
		}elseif(is_category()){
		    
		    if(!is_permalink()){
				$the_uri=explode("&",$uri);
				$the_uri=explode("=",$the_uri[1]);
				return $the_uri[1];
		    }	
		    $the_uri=explode("/",$uri);
		    if($the_uri[1]=='uncategorized')
		    $sql=$db->prepare_query("SELECT lrule_id
					     FROM lumonata_rules
					     WHERE lsef=%s and lrule=%s"
					     ,$the_uri[1],'categories');
		    else
		    $sql=$db->prepare_query("SELECT lrule_id
					     FROM lumonata_rules
					     WHERE lsef=%s and lgroup=%s and lrule=%s"
					     ,$the_uri[1],$the_uri[0],'categories');
		    
		    
		    $r=$db->do_query($sql);
		    $f=$db->fetch_array($r);
		    return $f['lrule_id'];
		    
		}elseif(is_tag()){
		    
		    if(!is_permalink()){
			$the_uri=explode("=",$uri);
		    }else{
			$the_uri=explode("/",$uri);
		    }
		    
		    $sql=$db->prepare_query("SELECT lrule_id
					     FROM lumonata_rules
					     WHERE lsef=%s and lrule=%s"
					     ,$the_uri[1],'tags');
		    
		    $r=$db->do_query($sql);
		    $f=$db->fetch_array($r);
		    return $f['lrule_id'];
		}elseif(is_page()){
			if(!is_permalink()){
				$the_uri=explode("=",$uri);
				return $the_uri[1];
		    }else{
		    	
				
				$sql=$db->prepare_query("SELECT larticle_id
						     FROM lumonata_articles
						     WHERE lsef=%s and larticle_type='pages'"
						     ,$uri);
				
				$r=$db->do_query($sql);
				$f=$db->fetch_array($r);
				return $f['larticle_id'];
		    }
		}
    }
    function toxo_id($return_type='categories'){
    	global $db;
		$fr=array();
		$uri=get_uri();
		
		if(is_details() || is_article_comments_feed()){
		    if(!is_permalink()){
		    	$the_uri=explode("&",$uri);
				$the_uri=explode("=",$the_uri[1]);
				$pid=$the_uri[1];
				
				/*if($return_type=='categories'){
					$sql=$db->prepare_query("SELECT lrule_id
							     FROM lumonata_rule_relationship
							     WHERE lapp_id=%d"
							     ,$pid);
				}elseif($return_type='tags'){
					$sql=$db->prepare_query("SELECT b.lrule_id
							     FROM lumonata_rule_relationship a,lumonata_rules b
							     WHERE a.lapp_id=%d AND b.lrule_id=a.lrule_id AND b.lrule=%s"
							     ,$pid,$return_type);
				//}
				
				$r=$db->do_query($sql);
				$f=$db->fetch_array($r);
				return $f['lrule_id'];
				*/
		    }else{
				/*$the_uri=explode("/",$uri);
				$theuri=explode(".",$the_uri[1]);
				$sql=$db->prepare_query("SELECT lrule_id
						     FROM lumonata_rules
						     WHERE lsef=%s and lgroup=%s and lrule=%s"
						     ,$theuri[0],$the_uri[0],$return_type);
				
				$r=$db->do_query($sql);
				$f=$db->fetch_array($r);
				return $f['lrule_id'];*/
		    	$pid=post_to_id();
		    }
		    $sql=$db->prepare_query("SELECT b.lrule_id
							     FROM lumonata_rule_relationship a,lumonata_rules b
							     WHERE a.lapp_id=%d AND b.lrule_id=a.lrule_id AND b.lrule=%s"
							     ,$pid,$return_type);
							     
			$r=$db->do_query($sql);
			while($f=$db->fetch_array($r)){
				$fr[]=$f['lrule_id'];
			}
			return $fr;
		}elseif(is_category()){
		   return post_to_id();
		}elseif(is_tag()){
		   return post_to_id();
		}
    }
    function get_post_title($separator=""){
    	global $db;
    	
    	$separator=(is_home())?"":$separator;
    	
    	if(is_details() || is_page()){
	    	$query=$db->prepare_query("SELECT larticle_title FROM lumonata_articles WHERE larticle_id=%d",post_to_id());
	    	$result=$db->do_query($query);
	    	$data=$db->fetch_array($result);
	    	return $data['larticle_title']." ".$separator." ";
    	}elseif(is_category()){
		    $sql=$db->prepare_query("SELECT lname FROM lumonata_rules WHERE lrule_id=%d AND lrule=%s",post_to_id(),'categories');
		    $r=$db->do_query($sql);
		    $f=$db->fetch_array($r);
		    return $f['lname']." ".$separator." ";
		}elseif(is_tag()){
		    $sql=$db->prepare_query("SELECT lname FROM lumonata_rules WHERE lrule_id=%d AND lrule=%s",post_to_id(),'tags');
		    $r=$db->do_query($sql);
		    $f=$db->fetch_array($r);
		    return $f['lname']." ".$separator." ";
		}
    }
    function get_appname(){
    	$uri=get_uri();
		
		if(is_details()){
			if(!is_permalink()){
				$the_uri=explode("&",$uri);
				$the_uri=explode("=",$the_uri[0]);
				return $the_uri[1];
		    }else{
				$the_uri=explode("/",$uri);
				 return $the_uri[0];
		    }
		   
		}elseif(is_category()){
		    if(!is_permalink()){
				$the_uri=explode("&",$uri);
				$the_uri=explode("=",$the_uri[0]);
				return $the_uri[1];
		    }	
		    $the_uri=explode("/",$uri);
		    return $the_uri[0];
		}elseif(is_page()){
			return "pages";
		}
    }
    function get_feed_section(){
    	$uri=get_uri();
    	if(is_feed()){
    		//the permalink structure of tag is http://domain.com/feed/feed_section/
        	//the original link: http://domain.com/feed=rss&section=feed_section
    		if(!is_permalink()){
    			$the_uri=explode("&",$uri);
				$the_uri=explode("=",$the_uri[1]);
				return $the_uri[1];
    		}else{
    			$the_uri=explode("/",$uri);
				return $the_uri[1];
    		}
    	}elseif(is_article_comments_feed()){
    		//the permalink structure of category is http://domain.com/%application%/%category%/%pagename%/comments-feed/
      		//the original link: http://domain.com/?app_name=appname&pid=10&feed=comments-feed
    		if(!is_permalink()){
    			$the_uri=explode("&",$uri);
				$the_uri=explode("=",$the_uri[2]);
				return $the_uri[1];
    		}else{
    			$the_uri=explode("/",$uri);
				return $the_uri[3];
    		}
    	}
    }
    function is_category($args=''){
      //the permalink structure of category is http://domain.com/%application%/%category%/
      //the original link : http://domain.com/?app_name=articles&cat_id=1
      
      $var['catname']='';
      $var['appname']='articles';
      $var['cat_id']='';
      //echo $args;
      if(!empty($args)){
        $args=explode('&',$args);
        foreach($args as $val){
            list($variable,$value)=explode('=',$val);
            
            if($variable=='catname' || $variable=='appname' || $variable='cat_id'){
                $var[$variable]=$value;
            }
        }
      }
      
      if(is_permalink()){
        if(empty($var['appname'])&& empty($var['catname'])){
          	$regex="#([^/]+)\/([^/]+)#";
        }else{
        	if(!empty($var['cat_id'])&& empty($var['catname'])){
        		$d=fetch_rule("rule_id=".$var['cat_id']."&group=".$var['appname']);
        		$var['catname']=$d['lsef'];
        	}
          	$regex="#(".$var['appname'].")\/(".$var['catname'].")#";
        }
      }else{
      	$uri=get_uri();
        $the_uri=explode("&",$uri);
        
        if(count($the_uri)!=2)
        return;
        
		$the_uri=explode("=",$the_uri[1]);
		if($the_uri[0]=="token"){
			$regex="#app_name=(".$var['appname'].")&token=(.*)#";
		}else{ 	
	        if(empty($var['cat_id']) && empty($var['appname'])){
	        	$regex="#app_name=(.*)&cat_id=(\d+)#";
	        }else{
		        if(empty($var['cat_id'])&& !empty($var['catname'])){
	        		$d=fetch_rule("sef=".$var['catname']."&group=".$var['appname']);
	        		$var['cat_id']=$d['lrule_id'];
	        	}
	          	$regex="#app_name=(".$var['appname'].")&cat_id=(".$var['cat_id'].")#";
	        }
      	}
      }
     
      $is_match=preg_match($regex,get_uri(),$match);
     
      if($is_match && count($match)==3)return true;
      else return false;
      
    }
   
    
    
    function is_tag($tag=''){
      //the permalink structure of tag is http://domain.com/tag/%tag%/
      //the original link : http://domain.com/?tag=the_tag
      
      if(is_permalink()){
        if(empty($tag))
          $regex="#^tag\/([^/]+)#";
        else
          $regex="#^tag\/($tag)$#";
      }else{
        if(empty($tag))
          $regex="#^tag=(.*)#";
        else
          $regex="#^tag=($tag)$#";
      }
      $is_match=preg_match($regex,get_uri(),$match);
      
      if($is_match && count($match)==2)return true;
      else return false;
      
    }

    function is_page($args=''){
    	global $db;
      	//the permalink structure of page is http://domain.com/%pagename%/
      	//the original link: http://domain.com/?page_id=1
      	$var['page_name']='';
      	$var['page_id']='';
      
    	if(!empty($args)){
        	$args=explode('&',$args);
        	foreach($args as $val){
            	list($variable,$value)=explode('=',$val);
            
            	if($variable=='page_name' || $variable=='page_id'){
            	    $var[$variable]=$value;
            	}
        	}
      	}
      
      if(is_permalink()){
	        if(empty($args)){
	          $regex="#([^/]+)#";
	        }else{
	        	if(empty($var['page_name']) && !empty($var['page_id'])){
	        		$query=$db->prepare_query("SELECT *
	        									FROM lumonata_articles
	        									WHERE larticle_id=%d",$var['page_id']);
	        		$rf=$db->do_query($query);
	        		$df=$db->fetch_array($rf);
	        		$var['page_name']=$df['lsef'];
	        	}
	          	$regex="#^(".$var['page_name'].")$#";
	        }
      }else{
      		
	      	$the_uri=explode("=",get_uri());
			if($the_uri[0]=="page_name"){
				if(empty($args))
		          $regex="#^page_name=(\w+)#";
		        elseif(!empty($var['page_name']))
		          $regex="#^page_name=(".$var['page_name'].")#";
		        else return false;
			}else{
		        if(empty($args)){
		          $regex="#^page_id=(\d+)#";
		        }else{
		        	
			        if(!empty($var['page_name']) && empty($var['page_id'])){
			        	
		        		$query=$db->prepare_query("SELECT *
		        									FROM lumonata_articles
		        									WHERE lsef=%s",$var['page_name']);
		        		
		        		$rf=$db->do_query($query);
		        		$df=$db->fetch_array($rf);
		        		$var['page_id']=$df['larticle_id'];
		        		
		        		if($the_uri[0]=='page_id' && $var['page_id']!= post_to_id())
		        		return false;
		        		
		        		$regex="#^page_id=(".$var['page_id'].")#";
		        	}else{
		        		$regex="#^page_id=(\d+)#";
		        	}
		          	
		        }
			}
      }
     
      $is_match= preg_match($regex,get_uri(),$match);
      $xpl=explode('/', get_uri());
      if($is_match && count($xpl)==1 )return true;
      else return false;
      
    }
    
    function is_details($app_name='articles'){
      //the permalink structure of category is http://domain.com/%application%/%category%/%pagename%.html
      //the original link: http://domain.com/?app_name=appname&pid=10
     
      if(is_permalink()){
      	if(!empty($app_name))
      		$regex="#(".$app_name.")\/([^/]+)\/([^/]+)\.html$#";
      	else
        	$regex="#([^/]+)\/([^/]+)\/([^/]+)\.html$#";
        $cnt=4;
      }else{
      	if(!empty($app_name))
      		$regex="#^app_name=(".$app_name.")&pid=(\d+)#";
      	else 
        	$regex="#^app_name=(.*)&pid=(\d+)#";
        	
        $cnt=3;
      }  
      $is_match=preg_match($regex,get_uri(),$match);
     
      if($is_match && count($match)==$cnt)return true;
      else return false;
      
    }
    function permalink($pid,$encode=false){
		global $db;
		
		if(!defined('SITE_URL'))
		define('SITE_URL',get_meta_data('site_url'));
		
		$sql=$db->prepare_query("SELECT a.larticle_id as article_id,a.larticle_type as appname, a.lsef as pagename, c.lsef as category
					    FROM lumonata_articles a
					    LEFT JOIN lumonata_rule_relationship b ON a.larticle_id=b.lapp_id
					    LEFT JOIN lumonata_rules c ON b.lrule_id=c.lrule_id AND c.lrule<>'tags'
					    WHERE  a.larticle_id=%d  ",$pid);
	    $r=$db->do_query($sql);
	    $d=$db->fetch_array($r);
	  	
	    if($d['appname']=='pages'){
	    	if(is_permalink()){
			    return "http://".site_url()."/".$d['pagename']."/";
			}else{
				if($encode)
					return "http://".site_url()."/?".utf8_encode("page_id=".$d['article_id']);
				else
			    	return "http://".site_url()."/?page_id=".$d['article_id'];
			}
	    }elseif($d['appname']=='status'){
	    	return get_admin_url()."?state=status&sub=status&status=".$d['pagename'];
	    }else{
			if(is_permalink()){
			    return "http://".site_url()."/".$d['appname']."/".$d['category']."/".$d['pagename'].".html";
			}else{
				if($encode)
					return "http://".site_url()."/".utf8_encode("?app_name=".$d['appname']."&amp;pid=".$pid);
				else
			    	return "http://".site_url()."/?app_name=".$d['appname']."&pid=".$pid;
			}
	    }
    }
    function is_user($user=''){
      //the permalink structure of tag is http://domain.com/user/%username%/
      //the original link: http://domain.com/?user=username
      if(is_permalink()){
        if(empty($user))
            $regex="#^user\/([^/]+)#";
        else
            $regex="#^user\/($user)#";
      }else{
        if(empty($user))
            $regex="#^user=(.*)#";
        else
            $regex="#^user=($user)#";
      }  
      $is_match=preg_match($regex,get_uri(),$match);
      
      if($is_match && count($match)==2)return true;
      else return false;
    }
    
    function is_feed(){
      //the permalink structure of tag is http://domain.com/feed/feed_section/
      //the original link: http://domain.com/feed=rss&section=feed_section
      
      if(is_permalink())
        $regex="#^feed\/([^/]+)#";
      else
        $regex="#^feed=rss&section=(.*)#";
      
      $is_match=preg_match($regex,get_uri(),$match);
      if($is_match)return true;
      else return false;
    }
    function is_article_comments_feed($app_name=''){
    	if(is_permalink()){
	      	if(!empty($app_name))
	      		$regex="#(".$app_name.")\/([^/]+)\/([^/]+)\.html\/comments-feed$#";
	      	else
	        	$regex="#([^/]+)\/([^/]+)\/([^/]+)\.html\/comments-feed$#";
	        $cnt=5;
	     }else{
	      	if(!empty($app_name))
	      		$regex="#^app_name=(".$app_name.")&pid=(\d+)&feed=comments-feed#";
	      	else 
	        	$regex="#^app_name=(.*)&pid=(\d+)&feed=comments-feed#";
	        	
	        $cnt=3;
	     }  
	      $is_match=preg_match($regex,get_uri(),$match);
	      if($is_match)return true;
	      else return false;
    }
    function is_sitemap(){
      //the permalink structure of tag is http://domain.com/sitemap/
      //the original link: http://domain.com/sitemap=xml
      if(is_permalink())
        $regex="#^sitemap#";
      else
        $regex="#^sitemap=xml#";
        
      $is_match=preg_match($regex,get_uri(),$match);
      if($is_match)return true;
      else return false;
    }
    function is_verify($token=''){
      //the permalink structure of tag is http://domain.com/verify/%token%/
      //the original link: http://domain.com/?verify=token
      if(is_permalink()){
        if(empty($token))
            $regex="#^verify\/([^/]+)#";
        else
            $regex="#^verify\/($token)#";
      }else{
        if(empty($token))
            $regex="#^verify=(.*)#";
        else
            $regex="#^verify=($token)#";
      }  
      $is_match=preg_match($regex,get_uri(),$match);
      
      if($is_match && count($match)==2)return true;
      else return false;
    }
    
    
    function create_htaccess_file(){
        $home_path=get_home_path();
        $open_file=fopen(ROOT_PATH."/.htaccess","w+");
        
        $rules  = "<IfModule mod_rewrite.c>\n";
        $rules .= "RewriteEngine On\n";
        $rules .= "RewriteBase $home_path\n";
        $rules .= "RewriteRule ^index\.php$ - [L]\n"; // Prevent -f checks on index.php.
        $rules .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
	$rules .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
	$rules .= "RewriteRule . {$home_path}index.php [L]\n";
        $rules .= "</IfModule>\n";
        
        $fputs=fputs($open_file,$rules);
        if($fputs){
            fclose($open_file);
            return true;
        }
    }
    
    function get_home_path(){
        $home_root = parse_url('http://'.site_url());
        if ( isset( $home_root['path'] ) )
                $home_root = rtrim($home_root['path'],"/").'/';
        else
                $home_root = '/';
                
        return $home_root;
    }
    
?>