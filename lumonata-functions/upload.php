<?php
function upload($source,$destination){
	$upload=move_uploaded_file($source,$destination);
	chmod($destination,0644);
	
	if($upload)
	return true;

	return false;
}

function upload_resize($source,$destination,$file_type,$max_width,$max_height){
	$new_width=0;
	$new_height=0;
	
	// Create an Image from it so we can do the resize
	if ($file_type == "image/jpg"  || $file_type == "image/jpeg"  || $file_type == "image/pjpeg"){ 
		$src = imagecreatefromjpeg($source);
	}else if ($file_type == "image/gif"){
		$src = imagecreatefromgif($source);
	}else if ($file_type == "image/png"){
		$src = imagecreatefrompng($source);
	}else return false;

	// Capture the original size of the uploaded image
	list($width,$height)=getimagesize($source);
	if ($height < $width && $width >= $max_width) { // width lebih besar height
		$new_width=$max_width;
		$new_height=($height/$width)*$max_width;
		if ($new_height > $max_height){
			$new_height = $max_height;
			$new_width = ($width / $height) * $new_height;
		}
	}elseif($height < $width && $width < $max_width){
		$new_width=$width;
		$new_height=($height/$width)*$new_width;
		if ($new_height > $max_height){
			$new_height = $max_height;
			$new_width = $width * $new_height / $height;
		}
	}elseif($height > $width && $height >= $max_height) {
		$new_width=$max_width;
		$new_height=($height/$width) * $max_width;
	}elseif($height > $width && $height < $max_height) {
		$new_height = $height;
		$new_width = ($width / $height) * $new_height;
		if ($new_width > $max_width){
			$new_width = $max_width;
			$new_height = ($height / $width) * $new_width;
		}
	}
	
		
	$tmp=imagecreatetruecolor($new_width,$new_height);

	/* this line actually does the image resizing, copying from the original
	   image into the $tmp image
	 */
	imagecopyresampled($tmp,$src,0,0,0,0,$new_width,$new_height,$width,$height);
	
	/*
	 now write the resized image to disk. I have assumed that you want the
	 resized, uploaded image file to reside in the ./images subdirectory.
	 $filename = "images/". $_FILES['uploadfile']['name'];
	 imagejpeg($tmp,$dest,100);
	*/
	
	if ($file_type == "image/jpg"  || $file_type == "image/jpeg"  || $file_type == "image/pjpeg"){ 
		imagejpeg($tmp,$destination,100);
	}else if ($file_type == "image/gif"){
		imagegif($tmp,$destination);
	}else if ($file_type == "image/png"){
		imagepng($tmp,$destination);
	}

	imagedestroy($src); //clean up the original temporaray file
	imagedestroy($tmp); //clean up the resized temporary file it created when the request		

	chmod($destination, 0644);
	return true;
}
function character_filter($char){
	$escape_char = array ("\\","/",":","*","?","<",">","`","~","!","@","#","$","%","^","&","(",")","_","+","=","|","}","{","[","]",";","\"","'",",","."," ");
	
	$fileext=file_name_filter($char,true);
	$filename=file_name_filter($char);
	
	for ($i=0;$i<count($escape_char);$i++){
		$filename = str_replace($escape_char[$i],"-",$filename);
		$filename = str_replace("--","-",$filename);	
	}
	$filename = str_replace("--","-",$filename);
	
	$strlen = strlen($filename);
	if (substr($filename,-1) == "-") $filename = substr($filename,0,($strlen-1));
	
	$strlen = strlen($filename);
	if (substr($filename,0,1) == "-") { $filename = substr($filename,1,$strlen); }
	
	return strtolower($filename.$fileext);
}

function file_name_filter($file_name,$ext=false){
	$fileext='';
	$filename='';
	
	$fileext=strchr($file_name,".");
	$filename=str_replace($fileext,'',$file_name);
	
	if($ext==true)
		return $fileext;
	
	return generateSefUrl($filename);
}

//rename filename
function rename_file($original_file_name,$new_file_name){
	return $new_file_name.strchr($original_file_name,".");
}
//check filesize
function is_allow_file_size($file_size,$allowed_size=6291456){
	if($file_size<=$allowed_size || $file_size==0){
		return true;
	}
	return false;
}
function is_allow_file_type($filetype,$file_checked){
	switch($file_checked){
		case 'image':
			$allowed_file_type=array('image/jpg','image/jpeg','image/pjpeg','image/gif','image/png');
			break;
		case 'flash':
			$allowed_file_type=array('application/x-shockwave-flash');
			break;
		case 'video':
			$allowed_file_type=array('application/octet-stream');
			break;
		case 'music':
			$allowed_file_type=array('audio/m4a','audio/x-ms-wma','audio/mpeg');
			break;
		case 'pdf':
			$allowed_file_type=array('application/octet','application/pdf');
			break;
		case 'doc':
			$allowed_file_type=array('application/msword','text/plain');
			break;
	}
	
	if(in_array($filetype,$allowed_file_type))
	return true;

	return false;
}
//delete file
function delete_file($destination){
	if(file_exists($destination)){
		unlink($destination);
		return true;
	}
	return false;
	
}

function upload_crop($source,$destination,$file_type,$max_width,$max_height) {  
	
	list($w,$h) = getimagesize($source);

   
	if ($file_type == "image/jpg"  || $file_type == "image/jpeg"  || $file_type == "image/pjpeg"){ 
		$simg = imagecreatefromjpeg($source);
	}else if ($file_type == "image/gif"){
		$simg = imagecreatefromgif($source);
	}else if ($file_type == "image/png"){
		$simg = imagecreatefrompng($source);
	}   
  
	$dimg = imagecreatetruecolor($max_width, $max_height);
	
	$wm = $w / $max_width;
	$hm = $h / $max_height;
	
	$h_height = $max_height / 2;
	$w_height = $max_width / 2;
	
	if($w>$h){
		$adjusted_width = $w / $hm;  
		$half_width = $adjusted_width / 2;
		$int_width = $half_width - $w_height;
		imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$max_height,$w,$h);
	}elseif(($w < $h) || ($w == $h)){
		$adjusted_height = $h / $wm;
		$half_height = $adjusted_height / 2;
		$int_height = $half_height - $h_height;
		imagecopyresampled($dimg,$simg,0,-$int_height,0,0, $max_width ,$adjusted_height,$w,$h);
	}
	
	if ($file_type == "image/jpg"  || $file_type == "image/jpeg"  || $file_type == "image/pjpeg"){ 
		return imagejpeg($dimg,$destination,100);
	}else if ($file_type == "image/gif"){
		return imagegif($dimg,$destination);
	}else if ($file_type == "image/png"){
		return imagepng($dimg,$destination);
	}

}

?>