<?php
	/**
	 * This function is used to send email. The email format could be in "html" or "plain_text".
	 *
	 * @author Wahya Biantara
	 *
	 * @since 1.0.0
	 * @param string $subject The subject of the email
	 * @param string $message Content of your email
	 * @param string $from_email Sender email address
	 * @param string $from_name Sender name
	 * @param string $to_email Recipient email adress
	 * @param string $to_name Recipient name
	 * @param string $type Email format that you will send("plain_text" or "html"). Default value is plain_text
	 * @param string $cc If there are Carbon Copy of email
	 * @param string $bcc If there are Blind Carbon Copy of email 
	 * @return boolean Return true if sending process is success and false if not
	 */
	function sendmail($subject,$message,$from_email,$from_name,$to_email,$to_name='',$type='plain_text',$cc='',$bcc=''){
		ini_set("SMTP",get_smtp());
		ini_set("sendmail_from",get_email());
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		if($type=="html")
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		else 
			$headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
			
									
		$headers .= "From:  $from_name <$from_email>\r\nReply-To :'$from_email'\r\n";
		if(!empty($cc))
			$headers .= "Cc: ".$cc."\r\n";
		if(!empty($bcc))	
			$headers .= "Bcc: ".$bcc."\r\n";
		//return true;	
		$send=mail($to_email,$subject,$message,$headers);
		
		if($send)return TRUE;
		else return FALSE;
	} 
	/**
	 * This function is used to send notification email when new member is register
	 *
	 * @author Wahya Biantara
	 *
	 * @since 1.0.0
	 * @param string $username Registred user name
	 * @param string $password Registred user password
	 * @param string $email Registered email
	 * @param string Encrypt string to use when new user verify their email address
	 * @return boolean Return true if sending process is success and false if not
	 */
	function send_register_notification($username,$email,$password,$token,$inviter_id=0){
		if(!empty($inviter_id))
		    $verification_link=get_admin_url()."/?state=verify&token=".$token."&iid=".$inviter_id;
		else 
		    $verification_link=get_admin_url()."/?state=verify&token=".$token;
		    			
		$subject="Your registration details at ".web_name();
		$message="Hi there and welcome to ".web_name().", \n\n";
		$message.="We are here to help you to find an interesting thing that could help you to find an information that you need."; 
		$message.="Being a member, you could share an interesting thing to others or privately only to your friend list \n\n";
		$message.="Before you get started,  let's verify your email address so that we know that you're really you.  Click here to verify: \n";
		$message.=$verification_link."\n\n";
		$message.="Here are your registration details:\n";
		$message.="Login URL: ".get_admin_url()."\n";
		$message.="Username	: ".$username."\n";
		$message.="Password	: ".$password."\n\n";
		$message.="If you have any questions, feel free to contact us.\n\n";
		$message.="Thanks\n\n";
		$message.=trim(web_name())." team\n";
		$message.=site_url();
		
		return sendmail($subject, $message, get_email(), web_name(), $email);
	} 
	/**
	 * This function is used to send comment notification email
	 *
	 * @author Wahya Biantara
	 *
	 * @since 1.0.0
	 * @param string $subject Email subject
	 * @param string $msg Posted comment
	 * @param string $comentator_name Name of the comentator
	 * @param string $to_email Destination email where the alert will be send
	 * 
	 * @return boolean Return true if sending process is success and false if not
	 */
	function send_comment_notification($subject,$msg,$comentator_name,$to_email,$article_link){
		$message=$subject."\n\n";
		$message.=$comentator_name." wrote:\n";
		$message.=$msg."\n\n";
		$message.="To see the comment thread, follow the link below:"."\n";
		$message.=$article_link."\n\n";
		$message.="Thanks\n";
		$message.=trim(web_name())." Team";
		
		return sendmail($subject, $message, get_email(),web_name(), $to_email);
	}
	/**
	 * This function is used to send comment notification email
	 *
	 * @author Wahya Biantara
	 *
	 * @since 1.0.0
	 * @param string $subject Email subject
	 * @param string $msg Posted comment
	 * @param string $comentator_name Name of the comentator
	 * @param string $to_email Destination email where the alert will be send
	 * 
	 * @return boolean Return true if sending process is success and false if not
	 */
	function send_like_notification($subject,$to_email,$article_link){

		$message=$subject."\n\n";
		$message.="To see the comment thread, follow the link below:"."\n";
		$message.=$article_link."\n\n";
		$message.="Thanks\n";
		$message.=trim(web_name())." Team";
		
		return sendmail($subject, $message, get_email(),web_name(), $to_email);
	}
	/**
	 * This function is used to send comment notification to the administrator and editor
	 *
	 * @author Wahya Biantara
	 *
	 * @since 1.0.0
	 * @param string $subject Email subject
	 * @param string $msg Posted comment
	 * @param string $comentator_name Name of the comentator
	 * @param string $to_email Destination email where the alert will be send
	 * 
	 * @return boolean Return true if sending process is success and false if not
	 */
	function send_comment_alert($comment_id,$subject,$msg,$comentator_name,$to_email){
		$message=$subject."\n\n";
		$message.=$comentator_name." wrote:\n";
		$message.=$msg."\n\n";
		$message.="To moderate this comment, please click the link bellow\n";
		$message.="http://".site_url()."/lumonata-admin/?state=comments&prc=edit&id=".$comment_id."\n\n";
		$message.="Thanks\n";
		$message.=trim(web_name())." Team";
		
		return sendmail($subject, $message, get_email(),web_name(), $to_email);
	}
	/**
	 * This function is used to send notification to the comentator that are not register as a member yet
	 *
	 * @author Wahya Biantara
	 *
	 * @since 1.0.0
	 * 
	 * @param string $comentator_name Name of the comentator
	 * @param string $email Destination email where the alert will be send
	 * 
	 * @return boolean Return true if sending process is success and false if not
	 */
	function unreguser_comment_notification($commentator_name,$email){
		if(!defined('SITE_URL'))
			define('SITE_URL',get_meta_data('site_url'));
		
		$subject="Your comment's status at ".web_name();
		$message="Hi ".$commentator_name.",\n\n";
		$message.="Thank you for your comment at ".web_name()." website \n\n";
		$message.="To make your comment immediately approved, please register as our member by clicking the link bellow: \n";
		
		$message.=get_admin_url()."/?state=register&token=".base64_encode($email)." \n\n";
			
		$message.="Being a member, you could share an interesting thing to all of other members and friends.\n\n";
		$message.="Thanks\n";
		$message.=trim(web_name())." Team";
		
		return sendmail($subject, $message, get_email(), web_name(), $email);
	}
	/**
	 * This function is used to send invitation email
	 *
	 * @author Wahya Biantara
	 *
	 * @since 1.0.0
	 * 
	 * @param string $invitr_email Inviter Email
	 * @param string $invitr_name Inviter Name
	 * @param string $invited_email Email who invited
	 * @param string $pm Personal Message
	 * 
	 * @return boolean Return true if sending process is success and false if not
	 */
	function invitation_mail($invitr_email,$invitr_name,$invited_email,$pm='',$enc_ulid=''){
		if(!defined('SITE_URL'))
			define('SITE_URL',get_meta_data('site_url'));
		
		$subject="Have a look my photos,status and articles on ".trim(web_name());
		$message="Hi ".$invited_email.",\n\n";
		$message.=$invitr_name. " is inviting you to join ".trim(web_name())."\n\n";
		
		if(!empty($pm)){
		    $message.=$invitr_name. " said: \n\n";
		    $message.="\"".$pm."\"\n\n";
		}
		
		
		if(!empty($enc_ulid)){
		    $enc_ulid="&enc_ulid=".$enc_ulid;
		}
		$message.="Once you join, you could share photos, your status and other interesting thing to others or privately only to your friend list\n\n";
		
		$message.="Click the link bellow to Join Now: \n";
		$message.=get_admin_url()."/?state=register&iid=".$_COOKIE['user_id']."&ie=".base64_encode($invited_email)."$enc_ulid \n\n";
		$message.="Thanks\n";
		$message.=trim(web_name())." Team";
		
		return sendmail($subject, $message, get_email(), $invitr_name, $invited_email);
	}
	/**
	 * This function is used to send email when friend request is approved
	 *
	 * @author Wahya Biantara
	 *
	 * @since 1.0.0
	 * 
	 * @param string $invitr_email Inviter Email
	 * @param string $invitr_name Inviter Name
	 * @param string $invited_id ID who invited
	 * @param integer $sex sex who invited
	 * 
	 * @return boolean Return true if sending process is success and false if not
	 */
	function request_approved_mail($invitr_email,$invitr_name,$invited_id,$sex){
		if(!defined('SITE_URL'))
			define('SITE_URL',get_meta_data('site_url'));
		
		if($sex==1){
		    $hisher="his";
		}elseif($sex==2){
		    $hisher="her";
		}
		    
		$first_name=get_additional_field($invited_id, 'first_name', 'user');
		$last_name=get_additional_field($invited_id, 'last_name', 'user');
		
		$subject=$first_name." ".$last_name." is now your friend on ".trim(web_name());
		$message="Hi ".$invitr_name.",\n\n";
		$message.=$first_name." ".$last_name. " is now your friend on ".trim(web_name())."\n\n";
		
		$message.="Click the link bellow to see ".$hisher." profile: \n";
		$message.=get_admin_url()."/?state=my-profile&tab=profile&id=".$invited_id." \n\n";
		$message.="Thanks\n";
		$message.=trim(web_name())." Team";
		
		return sendmail($subject, $message, get_email(), web_name(), $invitr_email);
	}
	
	/**
	 * This function is used to send request friend email
	 *
	 * @author Wahya Biantara
	 *
	 * @since 1.0.0
	 * 
	 * @param string $invitr_id Inviter ID
	 * @param string $invited_id ID who invited
	 * 
	 * @return boolean Return true if sending process is success and false if not
	 */
	function friend_request_mail($invitr_id,$invited_id){
		if(!defined('SITE_URL'))
			define('SITE_URL',get_meta_data('site_url'));
		
		$invitr=fetch_user($invitr_id);
		$invited=fetch_user($invited_id);
		
		if($invitr['lsex']==1){
		    $hisher="his";
		}elseif($invitr['lsex']==2){
		    $hisher="her";
		}

		$inviter_first_name=get_additional_field($invitr_id, 'first_name', 'user');
		$inviter_last_name=get_additional_field($invitr_id, 'last_name', 'user');
		
		$invited_first_name=get_additional_field($invited_id, 'first_name', 'user');
		$invited_last_name=get_additional_field($invited_id, 'last_name', 'user');
		
		$subject=$inviter_first_name." ".$inviter_last_name." wants to be your friend on ".trim(web_name());
		$message="Hi ".$invited_first_name." ".$invited_last_name.",\n\n";
		$message.=$inviter_first_name." ".$inviter_last_name. " wants to be your friend on ".trim(web_name())."\n\n";
		
		$message.="Click the link bellow to respond ".$hisher." request: \n";
		$message.=get_admin_url()."/?state=friends&tab=friend-requests \n\n";
		$message.="Thanks\n";
		$message.=trim(web_name())." Team";
		
		return sendmail($subject, $message, get_email(), web_name(), $invited['lemail']);
	}
	
	function reset_password_email($email,$username,$name, $new_password){
		if(!defined('SITE_URL'))
			define('SITE_URL',get_meta_data('site_url'));
		
		
		$subject="Reset Password: Your new password on ".trim(web_name());
		$message="Hi ".$name.",\n\n";
		$message.="As requested, we have reset your password on ".trim(web_name()).". Here are the new details:\n\n";
		
		$message.="Username:".$username."\n";
		$message.="Password:".$new_password."\n\n";
		
		$message.="Click the link bellow to login:\n";
		$message.=get_admin_url()."\n\n";
		
		$message.="Please remember that both your login and password are case sensitive and if you still can't log in, please check your browser, firewall settings, and don't forget to enable cookies.\n\n";
		
		$message.="Thanks\n";
		$message.=trim(web_name())." Team";
		
		return sendmail($subject, $message, get_email(), web_name(), $email);
	}
	
?>