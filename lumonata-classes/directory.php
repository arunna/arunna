<?php

function get_dir($path){
	$path = opendir($path);
	while ($dir = readdir($path)){
	       if($dir !='..' && $dir !='.' && $dir!='' && $dir!='Thumbs.db'){
		       $dirlist[] = $dir ;
	       }	
	}
       closedir($path);
       return $dirlist;
}

function remove_dir($directory,$empty=false){
	// if the path has a slash at the end we remove it here
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}

	// if the path is not valid or is not a directory ...
	if(!file_exists($directory) || !is_dir($directory))
	{
		// ... we return false and exit the function
		return FALSE;

	// ... if the path is not readable
	}elseif(!is_readable($directory)){
		// ... we return false and exit the function
		return FALSE;

	// ... else if the path is important directories (classes, functions) then return flase and exit function
	}elseif($directory==CLASSES_PATH || $directory==FUNCTIONS_PATH || $directory==ADMIN_PATH || $directory==CONTENT_PATH){
		return FALSE;
	// ... else if the path is readable	
	}else{
		// we open the directory
		$handle = opendir($directory);

		// and scan through the items inside
		while (FALSE !== ($item = readdir($handle)))
		{
			// if the filepointer is not the current directory
			// or the parent directory
			if($item != '.' && $item != '..')
			{
				// we build the new path to delete
				$path = $directory.'/'.$item;

				// if the new path is a directory
				if(is_dir($path)) {
					// we call this function with the new path
					remove_dir($path);
				// if the new path is a file
				}else{
					// we remove the file
					unlink($path);
				}
			}
		}
		// close the directory
		closedir($handle);

		// if the option to empty is not set to true
		if($empty == FALSE)
		{
			// try to delete the now empty directory
			if(!rmdir($directory))
			{
				// return false if not possible
				return FALSE;
			}
		}
		// return success
		return TRUE;
	}
}
function create_dir($path){
	if(!is_dir($path)){
		return mkdir($path,0755);
	}
	return false;
}
function get_file_parameters($file,$dir_type) {
	
	$default_parameters = array( 
		'Name' => 'Name', 
		'URL' => 'URL', 
		'Version' => 'Version', 
		'Description' => 'Description', 
		'Author' => 'Author', 
		'AuthorURL' => 'Author URL', 
		'Path' => 'Path' 
		);

	$parameters = fetch_parameters($file, $default_parameters, $dir_type);
	return $parameters;
}
function fetch_parameters( $file, $parameters, $dir_type='plugins') {
	// We don't need to write to the file, so just open for reading.
	$fp = fopen( $file, 'r' );
	// Pull only the first 1707 of the file in.
	$file_data = fread( $fp, 1707);

	// PHP will close file handle, but we are good citizens.
	fclose( $fp );
	
	$allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());
	foreach ( $parameters as $field => $regex ) {
		if($field=='Path'){
			
			$text_domain=($dir_type=='apps')?APPS_PATH:PLUGINS_PATH;
			$the_params[$field]=str_replace($text_domain,'',$file);
			continue;
		}
		preg_match( '/' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match);
		if ( !empty( $match[1] ) )
			$the_params[$field]= _cleanup_header_comment(kses($match[1],$allowedtags));
		else
			$the_params[$field]='';
		
	}
	return $the_params;
}
function scan_dir($dir_type='apps',$dir=''){
			
	$dirlist = array () ;
	$plugin_files=array();
	
	//set the root if the scan is in applications or plugins folder
	if($dir_type=='apps')
		$root = APPS_PATH;
	elseif($dir_type=='plugins')
		$root = PLUGINS_PATH;
	elseif($dir_type=='themes')
		$root= FRONT_TEMPLATE_PATH.'/'.get_meta_data('front_theme','themes');
		
	//if there is a spesific folder to scan
	if( !empty($dir) )
		$root = $root.'/'.$dir;
	
	/*
		Scan mentioned folder.
		Default for applications is /lumonata-applications
		and /lumonata-plugins for the plugins folder
	*/
	
	if ($handle = opendir ($root)){
		while($file = readdir ( $handle )){
			if ( substr($file, 0, 1) != '.'){
				$file = $root.'/'. $file ;
				
				//if not a directory, read the file to identify which is the master file
				if (!is_dir ($file) ){
					if($dir_type=='themes'){
						
						if (strtolower(substr($file, -4)) == '.jpg' || strtolower(substr($file, -5)) == '.jpeg' || strtolower(substr($file, -4)) == '.gif' || strtolower(substr($file, -4)) == '.png' || strtolower(substr($file, -4)) == '.swf')
							$the_files[] = $file;
							
					}else{
						if (substr($file, -4) == '.php' )
							$the_files[] = $file;
					}
					
				//is there are sub directory, read the sub folder to identify which is the master file
				}else{
					if($subhandle=opendir($file)){
						while($subfile =readdir($subhandle)){
							if (substr($subfile, 0, 1)!= '.'){
								if($dir_type=='themes'){
									if (strtolower(substr($file, -4)) == '.jpg' || strtolower(substr($file, -5)) == '.jpeg' || strtolower(substr($file, -4)) == '.gif' || strtolower(substr($file, -4)) == '.png' || strtolower(substr($file, -4)) == '.swf')
										$the_files[] = $file;
								}else{
									if (substr($subfile, -4) == '.php' )
										$the_files[] = $file.'/'.$subfile;
								}
							}
						}
						closedir ($subhandle) ;
					}
				}
			}
		}
		closedir ($handle);
	}
	
	if($dir_type=='themes'){
		foreach ($the_files as $key=>$val){
			$file=str_replace($root.'/', '', $val);
			if(!preg_match('#(.*)thumb(.*)#', $file)){
				if(!isset($_SERVER["HTTPS"]))
					$master_file['origin'][]='http://'.FRONT_TEMPLATE_URL.'/'.get_meta_data('front_theme','themes').'/images/headers/'.$file;
				else 
					$master_file['origin'][]='https://'.FRONT_TEMPLATE_URL.'/'.get_meta_data('front_theme','themes').'/images/headers/'.$file;

				$tfile=explode(".", $file);
				$thumb_file=$tfile[0].'-thumb.'.$tfile[1];
					
				if(!isset($_SERVER["HTTPS"]))
					$master_file['thumb'][]='http://'.FRONT_TEMPLATE_URL.'/'.get_meta_data('front_theme','themes').'/images/headers/'.$thumb_file;
				else 
					$master_file['thumb'][]='https://'.FRONT_TEMPLATE_URL.'/'.get_meta_data('front_theme','themes').'/images/headers/'.$thumb_file;	
			}
		}	
	}else{
		foreach($the_files as $the_file){
			if (is_readable($the_file)){
				$param=get_file_parameters($the_file,$dir_type);
				if(!empty($param['Name'])){
					$master_file[ generateSefUrl($param['Name']) ]=$param;
				}
			}
		}
	}
	
	return $master_file;

}
?>