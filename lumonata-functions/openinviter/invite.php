<?php
ob_start();
include('openinviter.php');		
require_once('../../lumonata_config.php');
require_once('../../lumonata_settings.php');
require_once('../kses.php');
require_once('../settings.php');
require_once('../../lumonata-classes/actions.php');
require_once("../../lumonata-classes/vcard.php");
require_once('../vcard.php');
require_once('../user.php');
require_once('../attachment.php'); 
require_once('../upload.php'); 
require_once('../mail.php');

if(!defined('SITE_URL'))
		define('SITE_URL',get_meta_data('site_url'));

$count_limit_mail=1; //number of delivery email
$time_limit_mail=3; //in second
?>
<html>
<head>
        <meta name="generator" content="HTML Tidy, see www.w3.org">
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <script type="text/javascript" src="../../lumonata-admin/javascript/jquery.js"></script>
        <link rel="stylesheet" href="<?php echo 'http://'.get_meta_data('site_url').'/lumonata-admin/themes/'.get_meta_data('admin_theme','themes').'/css/style.css'; ?>" type="text/css" media="screen" />
        
        <title>Invite Friends</title>
         <style type="text/css">
			body{
	
				margin:0;
				padding:0;
				font-family:"Lucida Sans",Arial, Helvetica, sans-serif;
				color:#333333;
				font-size:11px;
				font-weight:normal;
				background:#ffffff;
			}
			label{
				font-size:11px;
			}
			.invite_box{
				padding:10px;	
			}
			.selected_service_box{
				margin:0;
				border-bottom:1px solid #ccc;
				padding:10px;
				background:#f0f0f0;
				
			}
			.service_box{
				margin:0 ;
				border-bottom:1px solid #ccc;
				padding:10px;
				
			}
			.service_account{
				margin:20px;
			}
			.thTextbox{
				border:1px solid #cccccc;
				width:260px;
			}
		</style>
</head>

<body>
     
<?php

if(is_user_logged()){
	$inviter=new OpenInviter();
	$oi_services=$inviter->getPlugins();
	
	if (isset($_POST['provider_box'])) 
	{
		if (isset($oi_services['email'][$_POST['provider_box']])) $plugType='email';
		elseif (isset($oi_services['social'][$_POST['provider_box']])) $plugType='social';
		else $plugType='';
	}
	
	else $plugType = '';
	
	function ers($ers){
		if (!empty($ers)){
			$contents="<div class='alert_red_form' style='margin:5px 0;'><ul>";
			foreach ($ers as $key=>$error)
				$contents.="<li>{$error}</li>";
			
				$contents.="</ul></div>";
				$contents.="<script type='text/javascript'>
							$(function(){
							
								$('.alert_red_form').delay(3000).slideUp(80);
							});
					    </script>";
			return $contents;
		}
	}
		
	function oks($oks){
		if (!empty($oks)){
			$contents="<div class='alert_green_form' style='margin:5px 0;'><ul>";
			foreach ($oks as $key=>$msg)
				$contents.="<li>{$msg}</li>";
			$contents.="</ul></div>";
			$contents.="<script type='text/javascript'>
							$(function(){
								$('.alert_green_form').delay(3000).slideUp(300);
							});
					    </script>";
			return $contents;
		}
	}
	
	function upload_contact_interface($key){
		$contents="<tr>";
			$contents.="<td align='left' colspan='2'>";
				$contents.="<script type='text/javascript'>";
					$contents.="$(function(){
									$('#how_to_export').click(function(){
										$('#export_des').slideToggle(80);
									});
									$('#toutlook').click(function(){
										$('#doutlook').slideToggle(80);
									});
									$('#toutlookexpress').click(function(){
										$('#doutlookexpress').slideToggle(80);
									});
									$('#twinmail').click(function(){
										$('#dwinmail').slideToggle(80);
									});
									$('#twinbook').click(function(){
										$('#dwinbook').slideToggle(80);
									});
									$('#tentourage').click(function(){
										$('#dentourage').slideToggle(80);
									});
									$('#tmacos').click(function(){
										$('#dmacos').slideToggle(80);
									});
									$('#tlinkedin').click(function(){
										$('#dlinkedin').slideToggle(80);
									});
									
									
								});";
				$contents.="</script>";
				$contents.="<p>Import a contact file and invite them to join. You need to export <br />
							  your contact file first. <a href='javascript:;' id='how_to_export'>Here How To Export:</a>
							</p>
							<div style='background:#ffffff;border:1px solid #ccc;padding:5px;display:none;' id='export_des'>
								<ul>
									<li>
										<a href='javascript:;' id='toutlook'>Outlook</a>
										<div id='doutlook' style='display:none;'>
										<p>To export a CSV or tab-delimited text file from Outlook:</p>
											<ol>	
											   	<li>Open Outlook </li>
											    <li>Select \"Import and Export\" from the File menu</li>
											    <li>When the wizard opens, select \"Export to a file\" and click \"Next\"</li>
											   	<li>Select \"Comma separated values (Windows)\" and click \"Next\"</li>
											   	<li>Select the Contacts folder you would like to export and click \"Next\"</li>
											   	<li>Choose a filename and a place to save the file (for instance, \"Contacts.csv\" on the Desktop), then click \"Next\"</li>
											   	<li>Confirm what you are exporting: make sure the checkbox next to \"Export...\" is checked and click \"Finish\"</li>
											</ol>
										<p>To upload your file, simply hit \"Browse\" or \"Choose File\" below and select the contact file you've just created.</p>
										</div>
									</li>
									<li>
										<a href='javascript:;' id='toutlookexpress'>Outlook Express</a>
										<div id='doutlookexpress' style='display:none;'>
											<p>To export a CSV or tab-delimited text file from Outlook Express:</p>
												<ol>	
												   	<li>Open Outlook Express </li>
												    <li>Select \"Export\" from the File menu</li>
												    <li>Click \"Export\", and then click \"Address Book\"</li>
												   	<li>Select \"Text File\" (Comma Separated Values), and then click \"Export\".</li>
												   	<li>Choose a filename and a place to save the file (for instance, \"Contacts.csv\" on the Desktop) and click \"Next\"</li>
												   	<li>Click to select the check boxes for the fields that you want to export, and then click \"Finish\". Please be sure to select the email address and name fields</li>
												   	<li>Click \"OK\" and then click \"Close\"</li>
												</ol>
											<p>To upload your file, simply hit \"Browse\" or \"Choose File\" below and select the contact file you've just created.</p>
										</div>
									</li>
									<li>
										<a href='javascript:;' id='twinmail'>Windows Mail</a>
										<div id='dwinmail' style='display:none;'>
											<p>To export a CSV from Windows Mail:</p>
												<ol>	
												   	<li>Open Windows Mail</li>
												    <li>Select Tools | Windows Contacts... from the menu in Windows Mail</li>
												    <li>Click \"Export\" in the toolbar</li>
												   	<li>Make sure CSV (Comma Separated Values) is highlighted, then click \"Export\"</li>
												   	<li>Now click \"Browse\"</li>
												   	<li>Pick a folder to save the exported contacts.</li>
												   	<li>Choose a filename and a place to save the file (for instance, \"Contacts.csv\" on the Desktop) and click \"Next\" </li>
												   	<li>Click \"Save\" then click \"Next\"</li>
												   	<li>Make sure all address book fields you want included are checked</li>
												   	<li>Note that Windows Mail does not export first and last names separately, even though there are First Name and Last Name fields. Do choose Name instead.</li>
												   	<li>Click \"Finish\"</li>
												   	<li>Click \"OK\" then click \"Close\"</li>
												</ol>
											<p>To upload your file, simply hit \"Browse\" or \"Choose File\" below and select the contact file you've just created.</p>
										</div>
									</li>
									<li>
										<a href='javascript:;' id='twinbook'>Windows Address Book</a>
										<div id='dwinbook' style='display:none;'>
										<p>To export a CSV from Windows Address Book:</p>
											<ol>	
											   	<li>Open Windows Address Book</li>
											    <li>From the \"File\" menu, select \"Export\", then \"Other Address Book...\"</li>
											    <li>When the \"Address Book Export Tool\" dialog opens, select \"Text File (Comma Separated Values)\" and click \"Export\"</li>
											   	<li>Choose a filename and a place to save the file (for instance, \"Contacts.csv\" on the Desktop) and click \"Next\"</li>
											   	<li>Click to select the check boxes for the fields that you want to export, and then click \"Finish\". Please be sure to select the email address and name fields</li>
											   	<li>Click \"OK\" and then click \"Close\"</li>
											</ol>
										<p>To upload your file, simply hit \"Browse\" or \"Choose File\" below and select the contact file you've just created.</p>
										</div>
									</li>
									<li>
										<a href='javascript:;' id='tentourage'>Entourage</a>
										<div id='dentourage' style='display:none;'>
										<p>To export a tab-delimited text file from Entourage:</p>
											<ol>	
											   	<li>Open Entourage</li>
											    <li>Select \"Export\" from the \"File\" menu</li>
											    <li>Select \"Local Contacts to a list (tab-delimited text)\"</li>
											   	<li>Click the \"Next\" arrow</li>
											   	<li>Choose a filename and a place to save the file (for instance, \"Contacts.txt\" on the Desktop), then click \"Save\"</li>
											   	<li>Click \"Done\" in the confirm dialog</li>
											</ol>
										<p>To upload your file, simply hit \"Browse\" or \"Choose File\" below and select the contact file you've just created.</p>
										</div>
									</li>
									<li>
										<a href='javascript:;' id='tmacos'>Mac OS X Address Book</a>
										<div id='dmacos' style='display:none;'>
											<p>To export a vCard file from Mac OS X Address Book:</p>
												<ol>	
												   	<li>Open Mac OS X Address Book</li>
												    <li>Select the contacts you would like to export</li>
												    <li>Select \"Export vCards...\" from the File menu</li>
												   	<li>Choose a filename and a place to save the file (for instance, \"Contacts.vcf\" on the Desktop), then click \"Save\"</li>
												</ol>
											<p>To upload your file, simply hit \"Browse\" or \"Choose File\" below and select the contact file you've just created.</p>
										</div>
									</li>
									<li>
										<a href='javascript:;' id='tlinkedin'>LinkedIn</a>
										<div id='dlinkedin' style='display:none;'>
											<p>To export a CSV file from LinkedIn:</p>
												<ol>	
												   	<li>Sign into LinkedIn</li>
												    <li>Visit the <a href=\"http://www.linkedin.com/addressBookExport\" target='_blank'>Address Book Export</a> page</li>
												    <li>Select \"Microsoft Outlook (.CSV file)\" and click \"Export\"</li>
												   	<li>Choose a filename and a place to save the file (for instance, \"Contacts.csv\" on the Desktop), then click \"Save\"</li>
												</ol>
											<p>To upload your file, simply hit \"Browse\" or \"Choose File\" below and select the contact file you've just created.</p>
										</div>
									</li>
									
								</ul>
							</div>
							";
			$contents.="</td>";
		$contents.="</tr>";
		$contents.="<tr>";
			$contents.="<td align='right'>";
				$contents.="<label>Contact File :</label>";
			$contents.="</td>";
			$contents.="<td>";
				$contents.="<input class='thTextbox' type='file' name='contatc_file' value='' />";
			$contents.="</td>";
		$contents.="</tr>";
		$contents.="<tr>";
			$contents.="<td></td>";
			$contents.="<td>";
				$contents.="<input class='button' type='submit' name='import' id='import_".$key."' value='Upload Contacts'>";
				$contents.="<input type='hidden' name='provider_box' value='".$key."' />";
				$contents.="<input type='hidden' name='step' value='get_contacts'>";
			$contents.="</td>";
		$contents.="</tr>";
		
		return $contents;
	}
	function fetch_contact_interface($key){
		
		if(isset($_POST['email_box']))
			$mail=$_POST['email_box'];
		else 
			$mail='';
			
		$contents="<tr>";
			$contents.="<td align='right'>";
				$contents.="<label for='email_box'>Email :</label>";
			$contents.="</td>";
			$contents.="<td>";
				$contents.="<input class='thTextbox' type='text' name='email_box' value='' />";
			$contents.="</td>";
		$contents.="</tr>";
		$contents.="<tr>";
			$contents.="<td align='right'>";
				$contents.="<label for='password_box'>Password:</label>";
			$contents.="</td>";
			$contents.="<td>";
				$contents.="<input class='thTextbox' type='password' name='password_box' value='' />";
			$contents.="</td>";
		$contents.="</tr>";
		$contents.="<tr>";
			$contents.="<td></td>";
			$contents.="<td>";
				$contents.="<input class='button' type='submit' name='import' id='import_".$key."' value='Fetch Contacts'>";
				$contents.="<input type='hidden' name='provider_box' value='".$key."' />";
				$contents.="<input type='hidden' name='step' value='get_contacts'>";
			$contents.="</td>";
		$contents.="</tr>";
		
		return $contents;
	}
	function fetch_csv_contact($file_source){
		$row = 0;
		$data_count=1;
		$used_col=array('First Name','Middle Name','Last Name','E-mail Address');
		if (($handle = fopen($file_source, "r")) !== FALSE) {
			
			//create the contact matrix
		    while (($data = fgetcsv($handle, 99999, ",")) !== FALSE) {
		        $num_col = count($data);
		    	foreach ($data as $col=>$value) {
		           $data[$col] = trim( $data[$col] );
		           $data[$col] = utf8_encode($data[$col]) ;
		           $data[$col] = str_replace('""', '"', $data[$col]);
		           $data[$col] = preg_replace("/^\"(.*)\"$/sim", "$1", $data[$col]);
		           $contact_matrix[$row][$col]=$value; 	
			       
		        }
		        
		        $row++;
		        $data_count++;
		    }
		   
		    
		    //find the coloumn that used
		    foreach($contact_matrix[0] as $row=>$col_name){
		    	if(in_array($col_name, $used_col))
		    		$used_col_number[]=array($row=>$col_name);
		    }
		    //print_r($used_col_number);
		    //create the contact matrix base on the used coloumn
		    foreach($contact_matrix as $row=>$col){
		    	if($row!=0){
		    		if(is_array($col)){
		    			foreach ($used_col_number as $key=>$val){
		    				if(is_array($val)){
		    					foreach ($val as $col_no=>$col_name){
		    						$contacts_per_col[$col_name][]=$contact_matrix[$row][$col_no];
		    					}
		    				}
		    			}
		    		}
		    	}
		    }
		   
		    //combine as needed
		    foreach($contacts_per_col['E-mail Address'] as $key=>$email){
		    	$middle_name=(empty($contacts_per_col['Middle Name'][$key]))?'':$contacts_per_col['Middle Name'][$key].' ';
		    	$name=$contacts_per_col['First Name'][$key].$middle_name.' '.$contacts_per_col['Last Name'][$key];
		    	$contacts[$email]=$name;
		    	
		    }
		    
		    //print_r($contacts);
		    fclose($handle);
		    return $contacts;
		}
	}
	
	function fetch_vcf_contact($file_source){
		$lines = file($file_source);
	    if (!$lines) {
	        $ers['vCard']= "Can't read the vCard file: $file";
	    }
		$cards = parse_vcards($lines);
		$data_count=0;
		$hide=array();
		foreach ($cards as $card_name => $card) {
		 	$properties = $card->getProperties('EMAIL');
	        if ($properties) {
	            foreach ($properties as $property) {
	                $show = true;
	                $types = $property->params['TYPE'];
	                if ($types) {
	                    foreach ($types as $type) {
	                        if (in_array_case($type, $hide)) {
	                            $show = false;
	                            break;
	                        }
	                    }
	                }
	                if ($show) {
	                   $email=$property->value;
	                }
	            }
	        }
		 	
		 	if(!empty($email))
		 	$contacts[$email]=$card_name;
		 	$data_count++;
		 }
		 return $contacts;
	}
	
	
	
	if (!empty($_POST['step'])) $step=$_POST['step'];
	else $step='get_contacts';
	
	$ers=array();
	$oks=array();
	$import_ok=false;
	$done=false;
	
	
	if ($_SERVER['REQUEST_METHOD']=='POST'){
		if ($step=='get_contacts'){
			$loged_user=fetch_user($_COOKIE['user_id']);
			$user_display_name=$loged_user['ldisplay_name'];
			if($_POST['provider_box']=='csv'){
				if(isset($_FILES['contatc_file'])){
					$file_name = $_FILES['contatc_file']['name'];
		            $file_size = $_FILES['contatc_file']['size'];
		            $file_type = $_FILES['contatc_file']['type'];
		            $file_source = $_FILES['contatc_file']['tmp_name'];
		            
		            if(substr($file_name, -4)=='.csv' || substr($file_name, -4)=='.txt' || substr($file_name, -4)=='.vcf'){
		            	$import_ok=true;
						$step='send_invites';
						$_POST['message_box']='';
						
						$_POST['email_box']=$loged_user['lemail'];
						$_POST['oi_session_id']='';
						$file_ext=substr($file_name, -4);
		            	
		            }else{
		            	$ers['contacts']="Unsupported file format.";
		            }
				}
			}else{
				if (empty($_POST['email_box']))
					$ers['email']="Email missing !";
				if (empty($_POST['password_box']))
					$ers['password']="Password missing !";
				if (empty($_POST['provider_box']))
					$ers['provider']="Provider missing !";
					
				if (count($ers)==0){
					$inviter->startPlugin($_POST['provider_box']);
					$internal=$inviter->getInternalError();
					if ($internal){
						$ers['inviter']=$internal;
					}elseif (!$inviter->login($_POST['email_box'],$_POST['password_box'])){
						$internal=$inviter->getInternalError();
						$ers['login']=($internal?$internal:"Login failed. Please check the email and password you have provided and try again later !");
					}elseif (false===$contacts=$inviter->getMyContacts()){
						$ers['contacts']="Unable to get contacts !";
					}else{
						$import_ok=true;
						$step='send_invites';
						$_POST['oi_session_id']=$inviter->plugin->getSessionID();
						$_POST['message_box']='';
					}
				}
			}
		}elseif ($step=='send_invites'){
			if (empty($_POST['provider_box'])) $ers['provider']='Provider missing !';
			else{
				if($_POST['provider_box']=='csv'){
					
					if(!isset($_POST['check'])){
						echo "<div class='alert_red_form' style='margin:5px 0;'>You haven't selected any contacts to invite !</div>
							  <div style='text-align:left;'><input type='button' value='Back' onclick='history.back();' class='button' /></div>";
						exit;
					}elseif (empty($_POST['email_box'])) {
								echo "<div class='alert_red_form' style='margin:5px 0;'>Inviter information missing !</div>
									  <div style='text-align:left;'><input type='button' value='Back' onclick='history.back();' class='button' /></div>";
								exit;
					}else {
						$_POST['message_box']=strip_tags($_POST['message_box']);
						$checked_contacts=true;
					}
					
					if($checked_contacts){
						$cnt=0;
						$user=fetch_user($_COOKIE['user_id']);
						
						$invite_limit=get_additional_field($_COOKIE['user_id'], "invite_limit", "user");
						$count=$invite_limit;
						foreach ($_POST['check'] as $key=>$val){
							//send email here
							//echo "Name:".$_POST['name'][$key]." - Email:".$_POST['email'][$key].'<br />';
							if($count>0){
    							$invited=invitation_mail($user['lemail'],$user['ldisplay_name'],$_POST['email'][$key],$_POST['message_box']);
    							if($invited){
    							    $sent_to[]=$_POST['email'][$key];
    							    $count--;
    							}
							}else{
							    break;
							}
						}
						
						if($invite_limit!=-1){
						    $invite_limit=$invite_limit-count($sent_to);
						    edit_additional_field($_COOKIE['user_id'], "invite_limit", $invite_limit, "user");
						}
						
						$en_sent_to=json_encode($sent_to);
						$en_sent_to=base64_encode($sent_to);
						header("location:".cur_pageURL()."&sent_to=".$en_sent_to);
						$done=true;
					}
					
					
				}else{
					$inviter->startPlugin($_POST['provider_box']);
					$internal=$inviter->getInternalError();
					if ($internal) $ers['internal']=$internal;
					else{
						if(!isset($_POST['check'])){
							echo "<div class='alert_red_form' style='margin:5px 0;'>You haven't selected any contacts to invite !</div>
								  <div style='text-align:left;'><input type='button' value='Back' onclick='history.back();' class='button' /></div>";
							exit;
						}elseif (empty($_POST['email_box'])) {
								echo "<div class='alert_red_form' style='margin:5px 0;'>Inviter information missing !</div>
									  <div style='text-align:left;'><input type='button' value='Back' onclick='history.back();' class='button' /></div>";
								exit;
						}elseif (empty($_POST['oi_session_id'])){
								echo "<div class='alert_red_form' style='margin:5px 0;'>No active session !</div>";
								exit;
						}else $_POST['message_box']=strip_tags($_POST['message_box']);
						
						
						if ($inviter->showContacts()){
							$cnt=0;
							$user=fetch_user($_COOKIE['user_id']);
							$invite_limit=get_additional_field($_COOKIE['user_id'], "invite_limit", "user");
							$count=$invite_limit;
							
							foreach ($_POST['check'] as $key=>$val){
								//send email here
								if($count>0){
								    //echo "Name:".$_POST['name'][$key]." - Email:".$_POST['email'][$key].'<br />';
								    $invited=invitation_mail($user['lemail'],$user['ldisplay_name'],$_POST['email'][$key],$_POST['message_box']);
								    if($invited){
								        $sent_to[]=$_POST['email'][$key];
								        $count--;
								    }
								}else{
								    break;
								}
								
							}
							
							
							if($invite_limit!=-1){
    						    $invite_limit=$invite_limit-count($sent_to);
    						    edit_additional_field($_COOKIE['user_id'], "invite_limit", $invite_limit, "user");
							}
							
							$en_sent_to=json_encode($sent_to);
    						$en_sent_to=base64_encode($en_sent_to);
    						header("location:".cur_pageURL()."&sent_to=".$en_sent_to);
							$done=true;
						}
					}
				}
			}
			/*
			//send message
			if (count($ers)==0){
				$sendMessage=$inviter->sendMessage($_POST['oi_session_id'],$message,$selected_contacts);
				$inviter->logout();
				if ($sendMessage===-1){
					$message_footer="\r\n\r\nThis invite was sent using OpenInviter technology.";
					$message_subject=$_POST['email_box'].$message['subject'];
					$message_body=$message['body'].$message['attachment'].$message_footer; 
					$headers="From: {$_POST['email_box']}";
					foreach ($selected_contacts as $email=>$name)
						mail($email,$message_subject,$message_body,$headers);
						
						$oks['mails']="Mails sent successfully";
				}elseif($sendMessage===false){
					$internal=$inviter->getInternalError();
					$ers['internal']=($internal?$internal:"There were errors while sending your invites.<br>Please try again later! ");
				}else $oks['internal']="Invites sent successfully!";
				
				$done=true;
			}
			*/
		}
	}else{
		$_POST['email_box']='';
		$_POST['password_box']='';
		$_POST['provider_box']='';
	}
	
	$invite_credit=get_additional_field($_COOKIE['user_id'], "invite_limit", "user");
	$the_credit=$invite_credit;
	
	if($invite_credit==-1)
	$invite_credit="unlimited";
	
	if(isset($_GET['sent_to'])){
	   
	    $de_sent_to=base64_decode($_GET['sent_to']);
	    $de_sent_to=json_decode($de_sent_to,true);
	    if(is_array($de_sent_to)){
    	    $sent_list="<table width=\"98%\">
    	    				<tr>
    	    					<td colspan=\"2\">
    	    						<div style=\"width:100%;background:#f0f0f0;border-bottom:1px solid #ccc;font-weight:bold;padding:5px;margin:0 0 10px 0;\">
    	    							You successfully invited ".count($de_sent_to)." person to join ".web_name().".
    	    						</div>
    	    					</td>
    	    				</tr>";
    	    foreach ($de_sent_to as $key=>$email){
    	        $no=$key+1;
    	        $sent_list.="<tr>
               					<td>".$no.".</td>
               					<td>".$email."
               					<input type=\"hidden\" name=\"sent_email[]\" value=\"".$email."\">
               					</td>
               				</tr>";
    	    }
    	    $sent_list.="</table>";
	    }else{
	         $sent_list="<div class=\"alert_yellow\">Somthing went wrong. Please close this window and try one more time</div>";
	    }
	    echo $sent_list;
	}else{
    	$contents="
    	<script type='text/javascript'>
    		function toggleAll(element) {
    			var form = document.forms.openinviter, z = 0;
    			var the_credit=".$the_credit."
    			
    			if(the_credit==-1){
    				the_credit=form.length;
				}else{
        			if(form.length>the_credit)
        				the_credit=(the_credit)*3; //because 3 coloms
        			else
        				the_credit=form.length;
    			}
    			if(element.checked==true)
    				var invite_credit=(the_credit/3)+1; //plus checkbox on the header
    			else
    				var invite_credit=-1; //minus checkbox on the header
    				
    			for(z=0; z<the_credit;z++){
    				if(form[z].type == 'checkbox'){
    					form[z].checked = element.checked;
						if(element.checked==true)
    						invite_credit--;    	
    					else
    						invite_credit++;
    						
    					$('#invite_credit').html(invite_credit);
    				}
    			}
    			
    		}
    		
    		function checkInviteLimit(checkbox){
    			var form = document.forms.openinviter, z = 0;
    			var the_credit=".$the_credit."
    			
    			if(the_credit==-1)
    			return;
    			
    			var checkbox_checked=0;
    			for(z=0; z<form.length;z++){
    				if(form[z].type == 'checkbox'){
    					if(form[z].checked==true)
    						checkbox_checked++;
					}
				}
				
				if((checkbox_checked) >the_credit){
					checkbox.checked=false;
				}else{
					the_credit=the_credit-(checkbox_checked);
					$('#invite_credit').html(the_credit);
				}
			}
    		
    	</script>";
    	
    	$services=$_GET['ts'];
    	$services=base64_decode($services);
    	$services=json_decode($services,TRUE);
    	
    	
    	
    	if (!$done){
    		if ($step=='get_contacts'){
    			
    			$contents.="<div class=\"invite_box\">";
    			if($invite_credit>1 || $invite_credit==-1)
    			$contents.="<div><h3>You have ".$invite_credit." invitations credit.</h3></div>";
    			else 
    			$contents.="<div><h3>You have ".$invite_credit." invitation credit.</h3></div>";
    			
    			$contents.="<div style=\"background:#f0f0f0;border-bottom:1px solid #ccc;font-weight:bold;padding:5px;margin:0 0 10px 0;\">Invite Your Friends Easily</div>";
    				$contents.=ers($ers).oks($oks);
    				
    				if(!empty($_POST['provider_box']))
    					$selected_provider=$_POST['provider_box'];
    				else 
    					$selected_provider=$_GET['service'];
    				
    						
    				foreach ($services as $key=>$value){
    					if($key==$selected_provider){
    						$cls_services="selected_service_box";
    						$display="";
    					}else{
    						$cls_services="service_box";
    						$display="display:none;";
    					}
    					$contents.="<form action='' method='POST' name='openinviter' style='margin:0;padding;0' enctype='multipart/form-data'>";
    					$contents.="<div class=\"".$cls_services."\" id=\"box_".$key."\">";
    						$contents.="<div id=\"panel_box_".$key."\" style=\"cursor:pointer;font-weight:bold;font-size:12px;background:url('http://".$value['imgurl']."') no-repeat left center;height:22px;padding:10px 0 0 36px;\" >".$value['title']."&nbsp;&nbsp;&nbsp;
    									<img style=\"display:none;\" src=\"http://".get_meta_data('site_url')."/lumonata-admin/themes/".get_meta_data('admin_theme','themes')."/images/loading.gif\" id=\"loading_".$key."\" alt=\"Loaiding...\" title=\"Loading...\" /></div>";
    						$contents.="<div  class='service_account' style='".$display."' id=\"form_box_".$key."\">";
    						$contents.="<table style='font-size:12px;' width='100%' cellppading='5' cellspacing='5'>";
    						if($key=='csv'){
    							$contents.=upload_contact_interface($key);
    						}else{
    							$contents.=fetch_contact_interface($key);
    						}
    							
    						$contents.="</table>";
    						$contents.="</div>";
    					$contents.="</div>";
    					
    					$contents.="<script type=\"text/javascript\">";
    						$contents.="$(function(){
    										$('#panel_box_".$key."').click(function(){
    											$('.service_account').hide();
    											$('.selected_service_box').css({'background-color' : '#ffffff'});
    											$('.service_box').css({'background-color' : '#ffffff'});
    											//$('#box_".$key."').removeAttr('class');
    											$('#box_".$key."').css({'background-color' : '#f0f0f0'});
    											$('#form_box_".$key."').slideDown(80);
    										});
    										$('#import_".$key."').click(function(){
    											$('#loading_".$key."').show();
    										});
    									});";
    					$contents.="</script>";
    					
    					$contents.="</form>";
    			}
    		$contents.="</div>";
    			
    		}/*else
    			$contents.="<table class='thTable' cellspacing='0' cellpadding='0' style='border:none;'>
    					<tr class='thTableRow'><td align='right' valign='top'><label for='message_box'>Message</label></td><td><textarea rows='5' cols='50' name='message_box' class='thTextArea' style='width:300px;'>{$_POST['message_box']}</textarea></td></tr>
    					<tr class='thTableRow'><td align='center' colspan='2'><input type='submit' name='send' value='Send Invites' class='thButton' ></td></tr>
    				</table>";
    		*/
    	}
	
	
    	if (!$done){
    		if ($step=='send_invites'){
    			$show_it=false;
    						
    			$selected_provider=array();
    			foreach ($services as $key=>$val){
    				if($key==$_POST['provider_box']){
    					$selected_provider=$val;
    					break;
    				}
    			}
    			
    			if($_POST['provider_box']=='csv'){
    				if($file_ext=='.csv' || $file_ext=='.txt'){
    					$contacts=fetch_csv_contact($file_source);
    				}elseif($file_ext=='.vcf'){
    				 	$contacts=fetch_vcf_contact($file_source);
    				}
    				$show_it=true;
    			}else{
    				if ($inviter->showContacts()){
    					$show_it=true;
    				}
    			
    			}
    			if($show_it){
    			    if($invite_credit>1 || $invite_credit==-1){
        			    $contents.="<div><h3>You have <span id='invite_credit'>".$invite_credit."</span> invitations credit.</h3></div>";
        			    $contents.="<input type=\"hidden\" name=\"invite_credit\" value=\"".$invite_credit."\" />";
    			    }else{ 
        			    $contents.="<div><h3>You have <span id='invite_credit'>".$invite_credit."</span> invitation credit.</h3></div>";
        			    $contents.="<input type=\"hidden\" name=\"invite_credit\" value=\"".$invite_credit."\" />";
    			    }
    				$contents.="<form action='' method='POST' name='openinviter'>".ers($ers).oks($oks);
    				$contents.="<div style=\"background:#f0f0f0;border-bottom:1px solid #ccc;font-weight:bold;padding:5px;margin:0 0 10px 0;\">Your have ".count($contacts)." contatcs</div>";
    				$contents.="<div><img src=\"http://".$selected_provider['imgico']."\" title=\"".$selected_provider['title']."\" alt=\"".$selected_provider['title']."\" /></div>";		
    				if (count($contacts)==0){
    					$contents.="<div class='alert_red_form' style='margin:5px 0;'>You do not have any contacts in your address book.</div>";
    				}else{
    					$contents.="<div><table cellpadding='5' cellspacing='0' width='100%' >
    									<tr>
    										<th align='left' width='5%'><input type='checkbox' onChange='toggleAll(this)' name='toggle_all' title='Select/Deselect all'></th>
    										<th align='left' width='50%'>Name</th>
    										<th align='left' width='40%'>E-mail</th>
    									</tr>";
    					$odd=true;
    					$counter=0;
    					
    					foreach ($contacts as $email=>$name){
    						$counter++;
    						//if ($odd) $class='thTableOddRow'; else $class='thTableEvenRow';
    						if($counter % 2==0){
    							$background='#f0f0f0';
    						}else{
    							$background='#ffffff';
    						}
    						
    						$contents.="<tr>
    										<td align='left' style='background:".$background.";border-bottom:1px solid #ccc;font-size:10px;'>
    											<input name='check[".$counter."]' value='".$counter."' type='checkbox' class='thCheckbox' onChange='checkInviteLimit(this)' >
    											<input type='hidden' name='email[".$counter."]' value='".$email."' />
    											<input type='hidden' name='name[".$counter."]' value='".$name."' />
    										</td>
    										<td align='left' valign='top' style='background:".$background.";border-bottom:1px solid #ccc;font-size:10px;'>".$name."</td>
    										<td align='left' valign='top' style='background:".$background.";border-bottom:1px solid #ccc;font-size:10px;'>".$email."</td>
    									</tr>";
    					}
    											
    					
    				}
    				
    				$contents.="<tr>
    								<td colspan='3' align='left'>
    									<span style='font-weight:bold;font-size:12px;'>Personal message.</span><br />
    									<textarea name='message_box' class='textarea' stlye='width:98%;'></textarea>
    								</td>
    							</tr>";
    				$contents.="<tr>
    								<td>&nbsp;</td>
    								<td align='left'>&nbsp;</td>
    								<td colspan='3' align='right'>
    									<input type='submit' name='send' value='Send invites' class='button'>
    								</td>
    							</tr>";
    				$contents.="</table></div>";
    				$contents.="<input type='hidden' name='step' value='send_invites'>";
    				$contents.="<input type='hidden' name='provider_box' value='{$_POST['provider_box']}'>";
    				$contents.="<input type='hidden' name='email_box' value='{$_POST['email_box']}'>";
    				$contents.="<input type='hidden' name='oi_session_id' value='{$_POST['oi_session_id']}'>";
    				$contents.="</form>";
    				
    				/*
    				 * 
    				 * 
    				 * 
    				*/
    			}
    		}
	    }    
	    echo $contents;
	}
}
?>
</body>
</html>