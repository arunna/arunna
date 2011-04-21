<?php
    if(!defined('FILES_PATH'));
       define('FILES_PATH',ROOT_PATH.'/lumonata-content/files');
	
	if(isset($_POST['eduwork'])){
		require_once('../lumonata_config.php');
		if(is_user_logged()){
		    require_once('../lumonata_settings.php');
		    require_once('kses.php');
		    require_once('settings.php');
		    require_once('../lumonata-classes/actions.php');
		    $edit=false;
		    
			if(isset($_POST['delete_key'])){
	    		$cur=get_additional_field($_COOKIE['user_id'], $_POST['eduwork'], 'user');
	    		$cur=json_decode($cur,true);
	    		unset($cur[$_POST['delete_key']]);
	    		$cur=json_encode($cur);
	    		return edit_additional_field($_COOKIE['user_id'], $_POST['eduwork'], $cur, 'user');
	    	}
		    
		    if($_POST['eduwork']=='school'){
		    	if(!isset($_POST['school_name']))
		    	return;
		    	$val=array($_POST['school_name']=>$_POST['class_year']);
		    }elseif($_POST['eduwork']=='college'){
		    	if(!isset($_POST['college']))
		    	return;
		    	
		    	$val=array($_POST['college']=>array(
		    								'class_year'=>$_POST['class_year'],
		    								'concentrations'=>$_POST['concentrations'])
		    				);
		    	
		    	
		    }elseif($_POST['eduwork']=='work'){
		    	
		    	if(!isset($_POST['company_name']))
		    	return;
		    	
		    	$from_period=$_POST['from_month_period'].' '.$_POST['from_year_period'];
		    	//$from_period=strtotime($from_period);
		    	
		    	if(isset($_POST['present'])){
		    		$to_period='present';
		    	}else{
		    		$to_period=$_POST['to_month_period'].' '.$_POST['to_year_period'];
			    	//$to_period=strtotime($to_period);
		    	}
		    	$val=array($_POST['company_name']=>array(
		    								'from_period'=>$from_period,
		    								'to_period'=>$to_period,
		    								'position'=>$_POST['position'],
		    								'city'=>$_POST['city'],
		    								'jobdes'=>$_POST['jobdes']
		    								)
		    				);
		    }
		    
		    if(!isset($_POST['delete_key'])){
			    $cur=get_additional_field($_COOKIE['user_id'], $_POST['eduwork'], 'user');
			    
			    if(!empty($cur)){
			    	$cur_arr=json_decode($cur,true);
		    		$result=array_merge($cur_arr,$val);
		    		arsort($result);
		    		$val=json_encode($result);
		    		$r=edit_additional_field($_COOKIE['user_id'], $_POST['eduwork'], $val, 'user');
			    }else{
		    		//($_POST['eduwork']=='work')?krsort($val):ksort($val);
		    		arsort($val);
		    		$val=json_encode($val);
					$r=add_additional_field($_COOKIE['user_id'], $_POST['eduwork'], $val, 'user');
			    }
		    }
		    
		    if($r){
		    	echo get_eduwork($_POST['eduwork'],$_COOKIE['user_id']);
		    }
		}
	}   
	/**
	 * To check if the URI requested is call login form.
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @return boolean 
	 */
	function is_login_form(){
		if(empty($_GET['state']) || $_GET['state']=='login')return true;
		else return false;
	}
	/**
	 * To check if the URI requested is call registration form.
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @return boolean 
	 */
	function is_register_form(){
		if(isset($_GET['state']) && $_GET['state']=='register')return true;
		else return false;
	}
	
	/**
	 * To check if the URI requested is call thanks form after registration.
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @return boolean 
	 */
	
	function is_thanks_page(){
		if(isset($_GET['state']) && $_GET['state']=='thanks')return true;
		else return false;
	}
	
	/**
	 * To check if the URI requested is call email verification form.
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @return boolean 
	 */
	
	function is_verify_account(){
		if(isset($_GET['state']) && $_GET['state']=='verify' && isset($_GET['token']))return true;
		else return false;
	}
	/**
	 * To check if the URI requested is call forget password form.
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @return boolean 
	 */
	function is_forget_password(){
		if(!empty($_GET['state']) && $_GET['state']=='forget_password') return true;
		else return false;
	}
	/**
	 * To check if the URI contain redirection.
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @return boolean 
	 */
	function is_redirect(){
		if(!empty($_GET['redirect']))return true;
		else return false;
	}
	/**
	 * To check if the user already logi or not.
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @return boolean 
	 */
	function is_user_logged(){
		if(isset($_COOKIE['user_id']) && isset($_COOKIE['password']) && isset($_COOKIE['thecookie'])){
			if(md5($_COOKIE['password'].$_COOKIE['user_id'])==$_COOKIE['thecookie'])
				return true;
			else 
				return false;
		}else{
			return false;
		}
	}
	/**
	 * Login POST action and validate the input.
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @return boolean 
	 */
	function post_login(){
		if(count($_POST)>0)
		return validate_login();
	}
	/**
	 * Validate the login input.
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @return boolean 
	 */
	function validate_login(){
		
		if(empty($_POST['username']) || empty($_POST['password'])){
			return "<div class=\"alert_red\">Empty Username or Password.</div>";
		}else{
			if(is_exist_user($_POST['username']) && is_match_password()){
				
				if(isset($_POST['remember_login'])){
					/* Remember username and password for 1 year */
					setcookie('username', $_POST['username'], time()+60*60*24*365,'/');
					setcookie('password', $_POST['password'], time()+60*60*24*365,'/');
					
					
					$d=fetch_user($_POST['username']);
					setcookie('thecookie', md5($_POST['password'].$d['luser_id']), time()+60*60*24*365,'/');
					setcookie('user_id', $d['luser_id'], time()+60*60*24*365,'/');
					setcookie('user_type', $d['luser_type'], time()+60*60*24*365,'/');
					setcookie('user_name', $d['ldisplay_name'], time()+60*60*24*365,'/');
					
				}else{
					/* Cookie expires when browser closes */
					setcookie('username', $_POST['username'], false,'/');
					setcookie('password', $_POST['password'], false,'/');
										
					$d=fetch_user($_POST['username']);
					setcookie('thecookie', md5($_POST['password'].$d['luser_id']),false,'/');
					setcookie('user_id', $d['luser_id'], false,'/');
					setcookie('user_type', $d['luser_type'], false,'/');
					setcookie('user_name', $d['ldisplay_name'], false,'/');
				}
				if(is_redirect())
					header("location:".$_GET['redirect']);
				else
					header("location:".get_admin_url()."/?state=dashboard");
			}else{
				return "<div class=\"alert_red\">Wrong Username or Password.</div>";
			}
		}
	}
	/**
	 * Logout action to destroy the cookie
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @return boolean 
	 */
	function do_logout(){
		
		setcookie('username', "", time()-3600,'/');
		setcookie('password', "", time()-3600,'/');
		setcookie('user_id', "", time()-3600,'/');
		setcookie('user_type', "", time()-3600,'/');
		setcookie('user_name', "", time()-3600,'/');
		setcookie('thecookie', "", time()-3600,'/');
		
		header("location:".get_state_url('login'));
	}
	/**
	 * To check the if the user exist or not.
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @param string $username username
	 * 
	 * @return boolean 
	 */
	function is_exist_user($username){
		global $db;
		$sql=$db->prepare_query("SELECT * FROM lumonata_users WHERE lusername=%s",$username);
		$result=$db->do_query($sql);
		if($db->num_rows($result)>0) return true;
		else return false;
	}
	/**
	 * To check if the email address is exist or not.
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @param string $email email address
	 * 
	 * @return boolean 
	 */
	function is_exist_email($email){
		global $db;
		$sql=$db->prepare_query("SELECT * FROM lumonata_users WHERE lemail=%s",$email);
		$result=$db->do_query($sql);
		if($db->num_rows($result)>0) return true;
		else return false;
	}
	
	/**
	 * To count lumonata_user table on database.
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return Integer number of users 
	 */
	function is_num_users(){
		global $db;
		$sql=$db->prepare_query("SELECT * FROM lumonata_users");
		$result=$db->do_query($sql);
		return $db->num_rows($result);
	}
	/**
	 * To check if the password that sent from the POST variable are match with the mention user name
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return boolean 
	 */
	function is_match_password(){
		global $db;
		$sql=$db->prepare_query("SELECT * FROM lumonata_users WHERE lusername=%s AND lpassword=%s AND lstatus=1",$_POST['username'],md5($_POST['password']));
		$result=$db->do_query($sql);
		if($db->num_rows($result)>0) return true;
		else return false;
	}
	/**
	 * Login Form Design
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string login form html
	 *  
	 */
	function get_login_form(){
		//set template
		set_template(TEMPLATE_PATH."/login.html");
		
		//set block
		add_block('mainBlock','mBlock');
		
		//add variable
		if(isset($_COOKIE['username']))
			add_variable('username',$_COOKIE['username']);
		
		if(isset($_COOKIE['password']))
			add_variable('password',$_COOKIE['password']);
		
		add_variable('web_title',web_title());
		add_variable('alert',post_login());
		add_variable('style_sheet',get_css());
		add_variable('login_action',cur_pageURL());
		
		//print the template
		parse_template('mainBlock','mBlock');
		print_template(); 
	}
	/**
	 * The Sign Up URL
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string Sign up URL 
	 */
	function signup_url(){
		return "http://".site_url()."/lumonata-admin/?state=register";
	}
	/**
	 * The Sign In URL
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string Sign In URL 
	 */
	function signin_url(){		
		return "http://".site_url()."/lumonata-admin/?state=login&redirect=".cur_pageURL();
	}
	/**
	 * URL of user profile when login
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string User  
	 */
	function user_url($id){
		//if(is_permalink())
		//	return "http://".site_url()."/user/".$username."/";
		//else 
			return "http://".site_url()."/lumonata-admin/?state=my-profile&amp;id=".$id;
	}
	/**
	 * Call the Login Form Design
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string Login HTML Design   
	 */
	function sign_in_form(){
		
		set_template(TEMPLATE_PATH."/login.html","sign_in_form");
		
		//set block
		add_block('signin_form','mBlock',"sign_in_form");
		
		if(isset($_SERVER['HTTP_REFERER']))
			add_variable('action', get_admin_url()."/?redirect=".$_SERVER['HTTP_REFERER']);
		else 
			add_variable('action', get_admin_url()."/");

		
		add_variable('signup_url',signup_url());
		parse_template('signin_form','mBlock');
		return return_template("sign_in_form");
	}
	
	/**
	 * Call the sign up form. This function is also checking if the user sign up are invited by other user
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string Sign Up HTML Design  
	 */
	function signup_user(){
		$alert="";
		$tokenmail="";
		
		//set template
		set_template(TEMPLATE_PATH."/signup.html","signup_form");
		
		//set block
		add_block('signupBlock','sgnBlock',"signup_form");
		
		
		//add variable
		if(isset($_GET['token']))			
		$tokenmail=$_GET['token'];
								
		if(isset($_POST['signup'])){
			$validation_user=is_valid_user_input($_POST['username'], $_POST['password'], $_POST['repassword'], $_POST['email'], $_POST['sex'],"");
			if($validation_user=="OK"){
			    			    
				if(!empty($_POST['birthday']) && !empty($_POST['birthmonth']) && !empty($_POST['birthyear'])){
					$thebirthday=$_POST['birthmonth']."/".$_POST['birthday']."/".$_POST['birthyear'];
					$thebirthday=date("Y-m-d",strtotime($thebirthday));
				}else{
					$thebirthday="0000-00-00";
				}	
				if(save_user($_POST['username'],$_POST['password'],$_POST['email'],$_POST['sex'],"standard",$thebirthday)){
                        $user_id=mysql_insert_id(); // must be at first line here
                        
                        //add additional field for invitation limit
				        $invite_limit=get_meta_data("invitation_limit");
				        add_additional_field($user_id, "invite_limit", $invite_limit, "user");
				        
                        if(!empty($_POST['first_name']))
                        add_additional_field($user_id,'first_name',$_POST['first_name'],'user');

                        if(!empty($_POST['last_name']))
                        add_additional_field($user_id,'last_name',$_POST['last_name'],'user');
                        
                        $inviter_id=0;
                        
                        //if invite by friend	
        			    if(isset($_GET['iid']) && isset($_GET['ie'])){
        			        $inviter_id=$_GET['iid'];
                		    $invitr_user=fetch_user($_GET['iid']);
                		    if(isset($invitr_user['luser_id'])){
                		        $rel=add_friendship($_GET['iid'], $user_id,'pending');
                		        $friendship_id=mysql_insert_id();
                		        
                		        if($rel){
                		            $rel=add_friendship($user_id,$_GET['iid'],'onrequest');
                		            if($rel){
                		                //if invite to user friend list
                		                if(isset($_GET['enc_ulid'])){
                		                    $dec_ulid=base64_decode($_GET['enc_ulid']);
                		                    $user_friend_list=get_friend_list($_GET['iid']);
                		                    if(in_array($dec_ulid, $user_friend_list['friends_list_id'])){
                		                        add_friend_list_rel($friendship_id,$dec_ulid);
                		                    }
                		                }
                		               
                		            }
                		        }
                		    }
        			    }	
                        
                        //send activation email
                        $token=md5($_POST['username'].$_POST['email'].$_POST['password']).".".$tokenmail;
                        $uep=array('key'=>array(
                        					'username'=>$_POST['username'],
                        					'email'=>$_POST['email'],
                        					'password'=>$_POST['password'],
                        					'token'=>$token
                        					)
                        			);
                        $uep=base64_encode(json_encode($uep));               
                        send_register_notification($_POST['username'],$_POST['email'],$_POST['password'],$token,$inviter_id);
                        header("location:".get_admin_url()."/?state=thanks&uep=".$uep);
				}
				
			    
			}else{
				$alert=$validation_user;
			}
		}
		
		add_variable('web_title',web_title());
		add_variable('style_sheet',get_css());
		add_variable('jquery',get_javascript('jquery'));
		add_variable('alert',$alert);
		
		//SEX
		$sex="<select name=\"sex\">";
                        $sex.="<option value=\"\">Select Sex:</option>";
                        $sexar=array('1'=>'Male','2'=>'Female');
		foreach($sexar as $key=>$val){
			if(isset($_POST['sex'])){
				if($key==$_POST['sex'])
					$sex.="<option value=\"$key\" selected=\"selected\">$val</option>";
			}
			$sex.="<option value=\"$key\">$val</option>";
		}
		$sex.="</select>";
		add_variable('sex',$sex);
		
		//username
		if(isset($_POST['username']))
		add_variable('username',$_POST['username']);
		
		//First Name
		if(isset($_POST['first_name']))
		add_variable('first_name',$_POST['first_name']);
		
		//Last Name
		if(isset($_POST['last_name']))
		add_variable('last_name',$_POST['last_name']);
		
		//Email
		if(isset($_POST['email']))
		add_variable('email',$_POST['email']);
		
		//Email
		if(isset($_GET['ie']))
		add_variable('email',base64_decode($_GET['ie']));
		
		$birthday=(isset($_POST['birthday']))?$_POST['birthday']:"";
		$birthmonth=(isset($_POST['birthmonth']))?$_POST['birthmonth']:"";
		$birthyear=(isset($_POST['birthyear']))?$_POST['birthyear']:"";
		get_date_picker("tail",$birthday,$birthmonth,$birthyear);
		
		//if invite by friend	
		if(isset($_GET['iid']) && isset($_GET['ie'])){
		    $invitr_user=fetch_user($_GET['iid']);
		    if(isset($invitr_user['luser_id'])){
		        $ihtml="<tr>
		        			<td><img src='".get_avatar($invitr_user['luser_id'],2)."' 
		        				 alt=\"".$invitr_user['ldisplay_name']."\" 
		        				 title=\"".$invitr_user['ldisplay_name']."\" /></td>
		        			<td>
		        				 <h2>You've been invited by ".$invitr_user['ldisplay_name']."</h2>
		        				 Join ".web_name()." and <strong>connect with your friends</strong>,<br /> 
		        				 <strong>share news to others</strong> or <strong>share privately </strong> only to <br /> your friend list
		        			</td>
		        		</tr>";
		        add_variable('inviter',$ihtml);
		    }
		        
		}
		
		add_variable('tail',attemp_actions('tail'));
		
		//print the template
		parse_template('signupBlock','sgnBlock');
		print_template('signup_form');
		
		
		
	}
	/**
	 * Password reseter form. In this function the sending process also executed here 
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string Resend HTML Design  
	 */
	function resendPassword(){
		if(!isset($_GET['uep']))
		header("location:".get_admin_url()."/?state=login"); 
		
		$uep=base64_decode($_GET['uep']);
		$uep=json_decode($uep,true);
		
		if(!is_array($uep))
		header("location:".get_admin_url()."/?state=login");
		
		
		//Resend
		if(isset($_POST['resend'])){
			//send activation email
			
			$token=$uep['key']['token'];
				
			send_register_notification($uep['key']['username'],$uep['key']['email'],$uep['key']['password'],$token);
			add_variable('resendto'," Resent to: ".$uep['key']['email']);
			
			//print the template
			parse_template('thanksPage','thxBlock');
			print_template('signup_form');
		}
		
		//set template
		set_template(TEMPLATE_PATH."/resendPassword.html","resend");
		
		//set block
		add_block('thanksPage','thxBlock',"resend");
		
		add_variable('web_title',web_title());
		add_variable('style_sheet',get_css());
		add_variable('email',$uep['key']['email']);
        add_variable('username',$uep['key']['username']);
        add_variable('password',$uep['key']['password']);

        //print the template
        parse_template('thanksPage','thxBlock');
        print_template('resend');
		
	}
	/**
	 * Function to verify email activation process  
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string The activation process status and the login form when active  
	 */
	function verify_account(){
        global $db;
		set_template(TEMPLATE_PATH."/verifyAccount.html","verify");

		//set block
		add_block('verifyPage','vrfBlock',"verify");

        if(!isset($_GET['token']))		
        header("location:".get_admin_url()."/?state=login");
                
        $themail= explode(".", $_GET['token']);
        
        $sql=$db->prepare_query("SELECT * FROM lumonata_users
                                 WHERE lactivation_key=%s AND lstatus=0",$themail[0]);
        $r=$db->do_query($sql);
        if($db->num_rows($r)>0){
        	$query=$db->prepare_query("UPDATE lumonata_users
        								SET lstatus=1
        								WHERE lactivation_key=%s",$themail[0]);
        	$result=$db->do_query($query);
        	if($result){
        		if(!empty($themail[1])){
        			$query=$db->prepare_query("UPDATE lumonata_comments
        										SET lcomment_status='approved' 
        										WHERE lcomentator_email=%s",base64_decode($themail[1]));
        			$update_comment_status=$db->do_query($query);
        		}
        		$status="<div class=\"alert_yellow\">
        						Activation process succeeded, thanks for doing the activation. 
        						Please sign in using the form below:
        				</div>";
        		$status.="<h2>Sign In</h2>";
        		$status.="<form method=\"post\" action=\"".get_admin_url()."/\">
	        				<table cellspacing=\"0\" >
								<tr>
									<td>Username:</td>
									<td><input type=\"text\" name=\"username\" class=\"inputtext\" style=\"width:300px;\"  /></td>
								</tr>
								<tr>
									<td>Password:</td>
									<td><input type=\"password\" name=\"password\" class=\"inputtext\" style=\"width:300px;\"  /></td>
								</tr>
								<tr>
									<td></td>
									<td><input type=\"submit\" value=\"Sign In\" name=\"login\" class=\"button\" /></td>
								</tr>
							</table>
						</form>";
        		
        		//if invite from friend
        		if(isset($_GET['iid']) && !empty($_GET['iid'])){
        		    $invited_user=$db->fetch_array($r);
        		    $invitr_user=fetch_user($_GET['iid']);
        		    
        		    //update friendship status
        		    $edit_f=edit_friendship($_GET['iid'], $invited_user['luser_id'],'connected',true);
        		    
        		    //send email
        		    if($edit_f)
                    request_approved_mail($invitr_user['lemail'],$invitr_user['ldisplay_name'],$invited_user['luser_id'],$invited_user['lsex']);
        		}
        		
        	}
        }else{
        	$status="<p>You have done the activation process before.</p>";
        } 
        add_variable('web_title',web_title());
		add_variable('style_sheet',get_css());       
        add_variable('status',$status);      
		//print the template
		parse_template('verifyPage','vrfBlock');
		print_template('verify');
	}
	/**
	 * Function to handle forgot password action 
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string Action status  
	 */
	function post_forget_password(){
		if(count($_POST)>0)
			return validate_forget_password();
		else
			return "<div class=\"alert_yellow\">Please enter your username or e-mail address. You will receive a new password via e-mail.</div>";
	}
	/**
	 * Function to check the email input, when request a new password
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string Action status  
	 */
	function validate_forget_password(){
		if(empty($_POST['user_email'])){
			return "<div class=\"alert_yellow\">Please enter your username or e-mail address. You will receive a new password via e-mail.</div>";
		}
	}
	/**
	 * Call Forget Password HTML Design 
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string HTML Design  
	 */
	function get_forget_password_form(){
		//set template
		set_template(TEMPLATE_PATH."/forget_password.html");
		
		//set block
		add_block('mainBlock','mBlock');
		
		//add variable
		add_variable('web_title',web_title());
		add_variable('alert',post_forget_password());
		add_variable('style_sheet',get_css());
		add_variable('login_action',cur_pageURL());
		
		//print the template
		parse_template('mainBlock','mBlock');
		print_template(); 
	}
	/**
	 * Is used in admin area, to get the list of registered user. Navigation button are configured here. 
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string list of registered user in HTML  
	 */
	function get_users_list($tabs=''){
		global $db;
		$list='';
		$url=get_state_url('users')."&page=";		
		//setup paging system
		if(is_search()){
			$sql=$db->prepare_query("select * from lumonata_users where lusername like %s or ldisplay_name like %s or lemail=%s","%".$_POST['s']."%","%".$_POST['s']."%",$_POST['s']);
			$num_rows=count_rows($sql);
		}else{
			$num_rows=count_rows("select * from lumonata_users");
		}
		$viewed=list_viewed();
		if(isset($_GET['page'])){
		    $page= $_GET['page'];
		}else{
		    $page=1;
		}
		
		$limit=($page-1)*$viewed;
		if(is_search()){
			$sql=$db->prepare_query("select * from lumonata_users where lusername like %s or ldisplay_name like %s or lemail=%s limit %d, %d","%".$_POST['s']."%","%".$_POST['s']."%",$_POST['s'],$limit,$viewed);
		}else{
			$sql=$db->prepare_query("select * from lumonata_users limit %d, %d",$limit,$viewed);
		}
		$result=$db->do_query($sql);
		
		
		$list.="<h1>Users</h1>
			<ul class=\"tabs\">
			   $tabs
			</ul>
			<div class=\"tab_container\">
			<div id=\"response\"></div>
			<form action=\"".get_state_url('users')."\" method=\"post\">
			    <div class=\"button_wrapper clearfix\">
				
				<div class=\"button_left\">
					<ul class=\"button_navigation\">
						<li>".button("button=add_new",get_state_url("users")."&prc=add_new")."</li>
						<li>".button('button=edit&type=submit&enable=false')."</li>
						<li>".button('button=delete&type=submit&enable=false')."</li>
					</ul>
				</div>
				<div class=\"button_right\">
				".search_box('user.php','list_item','prc=search&','right','alert_green_form')."
				</div>
				
			    </div>
			    <div class=\"list\">
				<div class=\"list_title\">
				    <input type=\"checkbox\" name=\"select_all\" class=\"title_checkbox\" />
				    <div class=\"title_username\">Username</div>
				    <div class=\"title_name\">Name</div>
				    <div class=\"title_email\">Email</div>
				    <div class=\"title_category\">User Type</div>
				</div>
				<div id=\"list_item\">";
				$list.=users_list($result);
		$list.="	</div>
			</form>
			</div>
			<div class=\"button_wrapper clearfix\">
				<div class=\"button_left\">
				    <ul class=\"button_navigation\">
					<li>".button('button=add_new',get_state_url("users")."&prc=add_new")."</li>
					<li>".button('button=edit&type=submit&enable=false')."</li>
					<li>".button('button=delete&type=submit&enable=false')."</li>
				    </ul>   
				</div>
				<div class=\"paging_right\">
				    ". paging($url,$num_rows,$page,$viewed,5)."
				</div>
			</div>
		    </div>";
		    
		add_actions('header_elements','get_javascript','articles_list');    
		add_actions('section_title','Users');
		return $list;
	}
	
	/**
	 * Is used in admin area, to get the detail list of registered user.  
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return string list of registered user in HTML  
	 */
	function users_list($result){
		global $db;
		$i=0;$list='';
		if($db->num_rows($result)==0)
		return "<div class=\"alert_yellow_form\">No result found for <em>".$_POST['s']."</em>. Check your spellling or try another terms</div>";
		
		while($d=$db->fetch_array($result)){
			$list.="<div class=\"list_item clearfix\" id=\"the_item_$i\">
					<input type=\"checkbox\" name=\"select[]\" class=\"title_checkbox select\" value=\"".$d['luser_id']."\" />
                                        <div class=\"avatar\" ><img src=\"".get_avatar($d['luser_id'], 3)."\" /></div>
					<div class=\"title_username\" >".$d['lusername']."</div>
					<div class=\"title_name\">".$d['ldisplay_name']."</div>
					<div class=\"title_email\">".$d['lemail']."</div>
					<div class=\"title_category\">".ucfirst($d['luser_type'])."</div>
					
					<div class=\"the_navigation_list\">
						<div class=\"list_navigation\" style=\"display:none;\" id=\"the_navigation_".$i."\">
							<a href=\"".get_state_url('users')."&prc=edit&id=".$d['luser_id']."\">Edit</a> |
							<a href=\"javascript:;\" rel=\"delete_".$d['luser_id']."\">Delete</a>
						</div>
					</div>
					<script type=\"text/javascript\" language=\"javascript\">
						$('#the_item_".$i."').mouseover(function(){
							$('#the_navigation_".$i."').show();
						});
						$('#the_item_".$i."').mouseout(function(){
							$('#the_navigation_".$i."').hide();
						});
					</script>
				</div>";
				//delete_confirmation_box($d['luser_id'],"Are sure want to delete ".$d['ldisplay_name']."?","user.php","the_item_$i",'prc=delete&id='.$d['luser_id'])
				add_actions('admin_tail','delete_confirmation_box',$d['luser_id'],"Are sure want to delete ".$d['ldisplay_name']."?","user.php","the_item_$i",'prc=delete&id='.$d['luser_id']);
			$i++;
		}
		return $list;
	}
	
	/**
	 * Will return the user type in array   
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * 
	 * 
	 * @return array type of user  
	 */
	function user_type(){
		return $user_type=array("standard"=>"Standard","contributor"=>"Contributor","author"=>"Author","editor"=>"Editor","administrator"=>"Administrator");
	}
	
	/**
	 * Save user to database   
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @param string $username unique username
	 * @param string $password unique password
	 * @param string $email email address
	 * @param integer $sex 1=Male, 2=Female
	 * @param string $user_type Default user type are: standard, contributor, author, editor, administrator
	 * @param string $birthday The date format is Y-m-d H:i:s
	 * @param string $status the user status(0=pendding activation,1=active, 2=blocked)
	 * 
	 * @return boolean True if the insert process is success  
	 */
	
	function save_user($username,$password,$email,$sex,$user_type,$birthday,$status=0){
		global $db;
		$regdate=date("Y-m-d H:i:s");
		$activation_key=md5($username.$email.$password);
		$sql=$db->prepare_query("INSERT INTO
					lumonata_users(lusername,ldisplay_name,lpassword,lemail,lsex,lregistration_date,luser_type,lactivation_key,lbirthday,lstatus,ldlu)
					VALUES (%s,%s,%s,%s,%d,%s,%s,%s,%s,%d,%s)",$username,$username,md5($password),$email,$sex,$regdate,$user_type,$activation_key,$birthday,$status,$regdate);
		
		return $db->do_query($sql);
		
	}
	
	/**
	 * Automatically add new user as administrator firend   
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @param integer $friend_id The id of the new user 
	 * 
	 * 
	 * @return boolean True if the insert process is success  
	 */
	function add_friend_to_admin($friend_id){
	    global $db;
        $administrator=fetch_user_per_type('administrator');
		//$friend_id=mysql_insert_id();
		foreach ($administrator as $key=>$value){
			$return=add_friendship($value, $friend_id,'connected');
			
			if($return)
			$return=add_friendship($friend_id, $value,'connected');
		}
		return $return;
	}
	
	
	/**
	 * Used to edit user database   
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @param integer $id edited user ID
	 * @param string $display_name User display name
	 * @param string $password New password if any change
	 * @param string $email New email address if applicable
	 * @param integer $sex 1=Male, 2=Female
	 * @param string $user_type Default user type are: standard, contributor, author, editor, administrator
	 * @param string $birthday The date format is Y-m-d H:i:s
	 * @param string $status the user status(0=pendding activation,1=active, 2=blocked)
	 * 
	 * @return boolean True if the insert process is success  
	 */
	function edit_user($id,$display_name,$password,$email,$sex,$user_type,$birthday,$status=0){
		global $db;
		if(empty($password))
		$sql=$db->prepare_query("UPDATE lumonata_users
					SET 
					ldisplay_name=%s,
                    lsex=%d,
					luser_type=%s,
					lbirthday=%s,
					lstatus=%d,
					ldlu=%s
					WHERE luser_id=%d",
					$display_name,
                    $sex,
					$user_type,
					$birthday,
					$status,
					date("Y-m-d H:i:s"),
					$id);
		else
		$sql=$db->prepare_query("UPDATE lumonata_users
					SET 
					ldisplay_name=%s,
                    lsex=%d,
					lpassword=%s,
					luser_type=%s,
					lbirthday=%s,
					lstatus=%d,
					ldlu=%s
					WHERE luser_id=%d",
					$display_name,
                    $sex,
					md5($password),
					$user_type,
					$birthday,
					$status,
					date("Y-m-d H:i:s"),
					$id);
		
		
			return $db->do_query($sql);
		
	}
	
	/**
	 * Delete user from database   
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @param integer $id deleted user ID
	 * 
	 * 
	 * @return boolean True if the insert process is success  
	 */
	function delete_user($id){
		global $db;
		if($id!=1){
			$sql=$db->prepare_query("DELETE FROM lumonata_users
				WHERE luser_id=%d",$id);
			
			if(is_user_logged()){
				if($db->do_query($sql))
					return delete_friendship($id);
			}else{
				return $db->do_query($sql);
			}
		}
	}
	
	/**
	 * Validate the user input   
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 * @param string $username unique username
	 * @param string $password unique password
	 * @param string $re_password must have same value with password
	 * @param string $email Email address
	 * @param integer $sex 1=Male, 2=Female
	 * @param string $website User website address
	 * 
	 * @return string when data are good return OK, if not return the alert text  
	 */
	function is_valid_user_input($username,$password,$re_password,$email,$sex,$website){
		
		if(empty($username)){
			return "<div class=\"alert_red\">Please specifiy your username</div>";
		}
		if(strlen($username) < 5){
			return "<div class=\"alert_red\">Username <em>$username</em> should be at least five characters long</div>";
		}
		if(is_exist_user($username) && (is_add_new()|| is_register_form())){
			return "<div class=\"alert_red\">Username <em>$username</em> is not available, please try another username</div>";
		}
		if(empty($email)){
			return "<div class=\"alert_red\">Please specifiy your email</div>";
		}
		if(is_exist_email($email) && (is_add_new()|| is_register_form()))
			return "<div class=\"alert_red\">This email is already taken. Please choose another email</div>";
			
		if(empty($sex)){
			return "<div class=\"alert_red\">Please select your gender</div>";
		}
		if(!isEmailAddress($email)){
			return "<div class=\"alert_red\">Invalid email format(<em>$email</em>) </div>";
		}
		
		if(!empty($website) && $website!="http://"){
			if(!is_website_address($website))
				return "<div class=\"alert_red\">Invalid website format (<em>$website</em>)</div>";
		}
		if(is_add_new() || is_category("appname=register")){
			if(empty($password) || strlen($password)<7){
				return "<div class=\"alert_red\">The password should be at least seven characters long</div>";
			}
			
			if($password!=$re_password){
				return "<div class=\"alert_red\">Password do not match</div>";
			}
		}elseif(is_edit() || is_edit_all()){
			if(!empty($password)){
				if(strlen($password)<7)
					return "<div class=\"alert_red\">$username's password should be at least seven characters long</div>";
				elseif($password!=$re_password){
					return "<div class=\"alert_red\">$username's password do not match</div>";
				}
			}
			
		}
		return "OK";
	}
	
	/**
	 * Each user can edit his/her user profile when they login. This function will be called when they want to edit the profile.
	 * All process are happen here.    
	 *
	 * @author Wahya Biantara
	 *
	 * @since alpha
	 * 
	 *  
	 * @return string The HTML design of edit profile  
	 */
	function edit_profile(){
		$tabs=array('my-updates'=>'My Updates','my-profile'=>'My Profile','profile-picture'=>'Profile Picture','eduwork'=>'Education & Work');
		$alert='';
        if(isset($_GET['tab']))
			$selected_tab=$_GET['tab'];
		else
            $selected_tab='my-updates';
		
			
		$the_tabs=set_tabs($tabs,$selected_tab);
		
		//set template
		set_template(TEMPLATE_PATH."/users.html",'users');
		
		//set block
		add_block('usersEdit','uEdit','users');
		add_block('usersAddNew','uAddNew','users');
        add_block('profilePicture','pPicture','users');
        add_block('educationWork','eduWork','users');
         
		//set the page Title
		add_actions('section_title','Edit Profile');
		
		//set varibales
		add_variable('i',0);
		add_variable('website','http://');
		add_actions('header_elements','get_javascript','password_strength');
		//add_actions('header_elements','get_javascript','password');
		if(is_save_changes()){
			
			$validation_rs=is_valid_user_input($_POST['username'][0],$_POST['password'][0],$_POST['re_password'][0],$_POST['email'][0],$_POST['sex'][0],$_POST['website'][0]);
			if($validation_rs=="OK"){
				
				$thebirthday="000-00-00";	
				if(!empty($_POST['birthday'][0]) && !empty($_POST['birthmonth'][0]) && !empty($_POST['birthyear'][0])){
					$thebirthday=$_POST['birthmonth'][0]."/".$_POST['birthday'][0]."/".$_POST['birthyear'][0];
					$thebirthday=date("Y-m-d",strtotime($thebirthday));
				}	
				$duser=fetch_user($_COOKIE['user_id']);
				
				if(edit_user($_COOKIE['user_id'],$_POST['display_name'][0],$_POST['password'][0],$_POST['email'][0],$_POST['sex'][0],$duser['luser_type'],$thebirthday,$duser['lstatus'])){
					
					edit_additional_field($_COOKIE['user_id'],'first_name',$_POST['first_name'][0],'user');
					edit_additional_field($_COOKIE['user_id'],'last_name',$_POST['last_name'][0],'user');
					edit_additional_field($_COOKIE['user_id'],'website',$_POST['website'][0],'user');
					edit_additional_field($_COOKIE['user_id'],'bio',$_POST['bio'][0],'user');
					
					
					$alert="<div class=\"alert_green_form\">Your profile has succesfully updated.</div>";
				}
			}else{
				$alert=$validation_rs;
			}
			add_variable('alert',$alert);
			add_variable('username',$_POST['username'][0]);
			add_variable('first_name',$_POST['first_name'][0]);
			add_variable('last_name',$_POST['last_name'][0]);
			add_variable('bio',$_POST['bio'][0]);
			add_variable('email',$_POST['email'][0]);
			add_variable('website',$_POST['website'][0]);
			
						
			//find user display name
			$display_name="<select name=\"display_name[0]\">";
			foreach(opt_display_name($_COOKIE['user_id']) as $key=>$val){
				if($key==$_POST['display_name'][0])
					$display_name.="<option value=\"$key\" selected=\"selected\">$val</option>";
				else
					$display_name.="<option value=\"$key\">$val</option>";
			}
			$display_name.="</select>";

            //SEX
			$sex="<select name=\"sex[0]\">";
                        $sex.="<option value=\"\">Select Sex</option>";
                        $sexar=array('1'=>'Male','2'=>'Female');
			foreach($sexar as $key=>$val){
                            if($key==$_POST['sex'][0])
                                $sex.="<option value=\"$key\" selected=\"selected\">$val</option>";
                            else
                                $sex.="<option value=\"$key\">$val</option>";
			}
			$sex.="</select>";
			add_variable('sex',$sex);
			//birthday
            $birthday=(isset($_POST['birthday'][0]))?$_POST['birthday'][0]:"";
			$birthmonth=(isset($_POST['birthmonth'][0]))?$_POST['birthmonth'][0]:"";
			$birthyear=(isset($_POST['birthyear'][0]))?$_POST['birthyear'][0]:"";
			get_date_picker("admin_tail",$birthday,$birthmonth,$birthyear,true,0);
			
		}else{
			$d=fetch_user($_COOKIE['user_id']);
			add_variable('username',$d['lusername']);
			add_variable('first_name',get_additional_field($_COOKIE['user_id'],'first_name','user'));
			add_variable('last_name',get_additional_field($_COOKIE['user_id'],'last_name','user'));
			add_variable('bio',get_additional_field($_COOKIE['user_id'],'bio','user'));
			add_variable('email',$d['lemail']);
			$website=get_additional_field($_COOKIE['user_id'],'website','user');
			if(empty($website))
				$website='http://';
			else
				$website=get_additional_field($_COOKIE['user_id'],'website','user');
				
			add_variable('website',$website);
			
			
			
			//find user display name
			$display_name="<select name=\"display_name[0]\">";
			foreach(opt_display_name($d['luser_id']) as $key=>$val){
				if($key==$d['ldisplay_name'])
					$display_name.="<option value=\"$key\" selected=\"selected\">$val</option>";
				else
					$display_name.="<option value=\"$key\">$val</option>";
			}
			$display_name.="</select>";

            //SEX
			$sex="<select name=\"sex[0]\">";
                        $sex.="<option value=\"\">Select Sex</option>";
                        $sexar=array('1'=>'Male','2'=>'Female');
			foreach($sexar as $key=>$val){
                            if($key==$d['lsex'])
                                $sex.="<option value=\"$key\" selected=\"selected\">$val</option>";
                            else
                                $sex.="<option value=\"$key\">$val</option>";
			}
			$sex.="</select>";
			add_variable('sex',$sex);
			
			
            //birthday
            $birthday=(!empty($d['lbirthday']))?date("j",strtotime($d['lbirthday'])):"";
			$birthmonth=(!empty($d['lbirthday']))?date("n",strtotime($d['lbirthday'])):"";
			$birthyear=(!empty($d['lbirthday']))?date("Y",strtotime($d['lbirthday'])):"";
			get_date_picker("admin_tail",$birthday,$birthmonth,$birthyear,true,0);
		}
		
		add_variable('prc','Edit Profile');
		
		add_variable('tabs',$the_tabs);
		add_variable('save_user',button("button=save_changes&label=Save User"));
		
		if(is_administrator()){
			add_variable('cancel',button("button=cancel",get_state_url('users')));
			$value="<fieldset>
                		<p><label>Send Details?</label></p>
                		<input type=\"checkbox\" name=\"send[{i}]\" {send_checked} /> Send all details to the new user via email.
        			</fieldset>";
			add_variable('send_option', $value);
		}
		//add_variable('user_type',$user_tpye);
		add_variable('display_name',$display_name);
		
		parse_template('usersEdit','uEdit');
		
		return return_template('users');
	}
	
	/**
	 * This function is used to view the user profile. If you click your friend profile, then this function will be called
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 *  
	 * @return string The profile HTML design   
	 */
	function user_profile($user_id){
		$user=fetch_user($user_id);
		$website=get_additional_field($user_id,'website','user');
		$first_name=get_additional_field($user_id,'first_name','user');
		$last_name=get_additional_field($user_id,'last_name','user');
		$bio=get_additional_field($user_id,'bio','user');
		
		$html="<h2>Basic Information</h2>";
		$html.="<table width='100%' cellpadding='0' cellspacing='0'>";
			
			//First Name
			if(!empty($first_name)){
				$html.="<tr>";
					$html.="<td style='padding:5px 10px;border-bottom:1px solid #f0f0f0;width:100px;' align='right'>";
						$html.="<strong>Frist Name</strong>";
					$html.="</td>";
					$html.="<td style='padding:5px 10px;border-bottom:1px solid #f0f0f0;'>";
						$html.=$first_name;
					$html.="</td>";
				$html.="</tr>";
			}
			//Last Name
			if(!empty($last_name)){
				$html.="<tr>";
					$html.="<td style='padding:5px 10px;border-bottom:1px solid #f0f0f0;width:100px;' align='right'>";
						$html.="<strong>Last Name</strong>";
					$html.="</td>";
					$html.="<td style='padding:5px 10px;border-bottom:1px solid #f0f0f0;'>";
						$html.=$last_name;
					$html.="</td>";
				$html.="</tr>";
			}
			//Sex
			if(!empty($user['lsex'])){
				$html.="<tr>";
					$html.="<td style='padding:5px 10px;border-bottom:1px solid #f0f0f0;width:100px;' align='right'>";
						$html.="<strong>Sex</strong>";
					$html.="</td>";
					$html.="<td style='padding:5px 10px;border-bottom:1px solid #f0f0f0;'>";
						$html.=($user['lsex']==1)?'Male':'Female';
					$html.="</td>";
				$html.="</tr>";
			}
			//Bio
			if(!empty($bio)){
				$html.="<tr>";
					$html.="<td style='padding:5px 10px;border-bottom:1px solid #f0f0f0;width:100px;' align='right'>";
						$html.="<strong>Bio</strong>";
					$html.="</td>";
					$html.="<td style='padding:5px 10px;border-bottom:1px solid #f0f0f0;'>";
						$html.=$bio;
					$html.="</td>";
				$html.="</tr>";
			}
		$html.="</table>";
		$html.="<h2>Contact Information</h2>";
		$html.="<table width='100%' cellpadding='0' cellspacing='0'>";
			if(!empty($user['lemail'])){
				$html.="<tr>";
					$html.="<td style='padding:5px 10px;border-bottom:1px solid #f0f0f0;width:100px;' align='right'>";
						$html.="<strong>Email</strong>";
					$html.="</td>";
					$html.="<td style='padding:5px 10px;border-bottom:1px solid #f0f0f0;'>";
						$html.=$user['lemail'];
					$html.="</td>";
				$html.="</tr>";
			}
			
			if($website!='http://'){
				$html.="<tr>";
					$html.="<td style='padding:5px 10px;border-bottom:1px solid #f0f0f0;width:100px;' align='right'>";
						$html.="<strong>Website</strong>";
					$html.="</td>";
					$html.="<td style='padding:5px 10px;border-bottom:1px solid #f0f0f0;'>";
						$html.="<a href=\"".$website."\" rel=\"nofollow\">".$website."</a>";
					$html.="</td>";
				$html.="</tr>";
			}
			
		$html.="</table>";
		
		$school=get_eduwork('school',$user_id);
		$college=get_eduwork('college',$user_id);
		$work=get_eduwork('work',$user_id);
		if(!empty($school) || !empty($college) || !empty($work)){
			$html.="<h2>Education &amp; Work</h2>";
			$html.="<table width='100%' cellpadding='0' cellspacing='0'>";
					if(!empty($work)){
					$html.="<tr>";
						$html.="<td style='padding:10px 10px;border-bottom:1px solid #f0f0f0;width:100px;' align='right' valign='top'>";
							$html.="<strong>Work</strong>";
						$html.="</td>";
						$html.="<td style='padding:0px 10px;border-bottom:1px solid #f0f0f0;'>";
							$html.=$work;
						$html.="</td>";
					$html.="</tr>";
					}
					if(!empty($college)){
						$html.="<tr>";
							$html.="<td style='padding:10px 10px;border-bottom:1px solid #f0f0f0;width:100px;' align='right' valign='top'>";
								$html.="<strong>Grad School</strong>";
							$html.="</td>";
							$html.="<td style='padding:0px 10px;border-bottom:1px solid #f0f0f0;'>";
								$html.=$college;
							$html.="</td>";
						$html.="</tr>";
					}
					if(!empty($school)){
						$html.="<tr>";
							$html.="<td style='padding:10px 10px;border-bottom:1px solid #f0f0f0;width:100px;' align='right' valign='top'>";
								$html.="<strong>High School</strong>";
							$html.="</td>";
							$html.="<td style='padding:0px 10px;border-bottom:1px solid #f0f0f0;'>";
								$html.=$school;
							$html.="</td>";
						$html.="</tr>";
					}
			$html.="</table>";
		}
		return $html;
	}

	/**
	 * This function is used to show the user feeds update. 
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 *  
	 * @return string Selected user Feeds   
	 */
   	function user_updates(){
   		if(!is_user_logged())
   		return;
   		global $db;
   		if(!empty($_GET['id'])){
   			$id=$_GET['id'];
   			
	   		if(is_my_friend($_COOKIE['user_id'], $_GET['id']) || is_my_friend($_COOKIE['user_id'], $_GET['id'],'unfollow')){
				$query=$db->prepare_query("SELECT * FROM lumonata_articles 
										 WHERE larticle_status='publish' AND
										 lpost_by = %d AND
										 	( lshare_to in (
										 		SELECT a.lfriends_list_id
												FROM lumonata_friends_list_rel a, lumonata_friendship b
												WHERE a.lfriendship_id=b.lfriendship_id AND b.lfriend_id=%d and lstatus='connected'
										 	    )OR lshare_to=0
										 	)
										 ORDER BY lpost_date DESC",$_GET['id'],$_COOKIE['user_id']);
				
				$feed_type='friend_feed';
			}else{
				$query=$db->prepare_query("SELECT * FROM lumonata_articles 
										 WHERE larticle_status='publish' AND
										 lpost_by = %d AND
										 lshare_to=0
										 ORDER BY lpost_date DESC",$_GET['id']);
				$feed_type='everyone_friend_feed';
			}
   			
   		}else{
   			
   			$id=$_COOKIE['user_id'];
   			$query=$db->prepare_query("SELECT * FROM lumonata_articles 
										 WHERE larticle_status='publish' AND
										 lpost_by = %d
										 ORDER BY lpost_date DESC",$id);
   			$feed_type='my_feeds';
   		}
		
   		
		$result=$db->do_query($query);
		$data=$db->fetch_array($result);
				
		$content_left=dashboard_latest_update($query,get_meta_data('comment_per_page'),false,$feed_type);
		
		$user=fetch_user($id);
		add_actions('section_title',$user['ldisplay_name']);
		$add_friend_button="";
		
		if($id==$_COOKIE['user_id']){
			$tabs=array('my-updates'=>'My Updates','my-profile'=>'My Profile','profile-picture'=>'Profile Picture','eduwork'=>'Education & Work');
			if(isset($_GET['tab']))
				$selected_tab=$_GET['tab'];
			else
            	$selected_tab='my-updates';
            	
			$the_tabs=set_tabs($tabs,$selected_tab);
			
		}else{ 
			
			if(isset($_GET['tab']) && $_GET['tab']=='profile'){
				$content_left=user_profile($_GET['id']);
				$the_tabs="<li><a href=\"".get_state_url('my-profile')."&id=".$id."\">".$user['ldisplay_name']." Updates</a></li>";
				$the_tabs.="<li class=\"active\"><a href=\"".get_state_url('my-profile')."&tab=profile&id=".$id."\">Profile</a></li>";
			}else{
				$the_tabs="<li class=\"active\"><a href=\"".get_state_url('my-profile')."&id=".$id."\">".$user['ldisplay_name']." Updates</a></li>";
				$the_tabs.="<li><a href=\"".get_state_url('my-profile')."&tab=profile&id=".$id."\">Profile</a></li>";
			}
			
			if(!is_my_friend($_COOKIE['user_id'], $id,'connected')){
				$add_friend_button=add_friend_button($_GET['id'],0);
			}
			
			if(is_my_friend($_COOKIE['user_id'], $id,'connected')){
				if(!is_administrator($_GET['id'])){
					$friendship=search_friendship($_COOKIE['user_id'],$_GET['id']);
					$add_friend_button=add_friend_button($_GET['id'],$friendship['friendship_id'][0],'unfollow');
				}
			}
			
			if(is_my_friend($_COOKIE['user_id'], $id,'pending'))
			$add_friend_button="<div style='color:#cacaca;margin-bottom:10px;'>Friend request pending.</div>";
			
			if(is_my_friend($_COOKIE['user_id'], $id,'onrequest')){
				$friendship=search_friendship($_COOKIE['user_id'],$_GET['id']);
				$add_friend_button=add_friend_button($_GET['id'],$friendship['friendship_id'][0],'confirm');
			}
			if(is_my_friend($_COOKIE['user_id'], $id,'unfollow')){
				$friendship=search_friendship($_COOKIE['user_id'],$_GET['id']);
				$add_friend_button=add_friend_button($_GET['id'],$friendship['friendship_id'][0],'follow');
			}
		}
		
		
		
		$friends_html=friend_thumb_list($id);
		
		
		$alert='';
        
		
            
        $img='<div style="text-align:left;width:100%;overflow:hidden;margin-bottom:10px;">
        		<img src="'.get_avatar($id,1).'" title="'.$user['ldisplay_name'].'" alt="'.$user['ldisplay_name'].'" style="" />
        		<h2 style="color:#ef451e;">'.ucwords($user['ldisplay_name']).'</h2>
        	</div>';
		
   		
       	$content="<h1>".ucwords($user['ldisplay_name'])."</h1>";
		$content.="<ul class=\"tabs\">
   				$the_tabs
			</ul>";
		$content.="<div class=\"tab_container\">";
   			$content.="<div  style=\"margin:10px 0 10px 5px;\">";			
				$content.="<div id=\"dashboard_left\">";
				$content.="<div class=\"home_plug1\">".$content_left."</div>";
				$content.="</div>";
				$content.="<div id=\"profile_right\" >$img";
				$content.=$add_friend_button;
				$content.=$friends_html;
				$content.=attemp_actions('user_updates_right');
				$content.="</div>";
			$content.="</div>";
		$content.="</div>";
		return $content;
   } 	
   
   /**
	 * Edit the user picture profile 
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 *  
	 * @return string The picture profile HTML Design    
	 */
   function edit_profile_picture(){
         global $db;
         $thealert="";
         //if upload actions
         if(isset($_POST['upload'])){

		     $file_name = $_FILES['theavatar']['name'];
	         $file_size = $_FILES['theavatar']['size'];
	         $file_type = $_FILES['theavatar']['type'];
	         $file_source = $_FILES['theavatar']['tmp_name'];
	                    
	         if(is_allow_file_size($file_size)){
	            if(is_allow_file_type($file_type,'image')){
	                 $fix_file_name=file_name_filter($_COOKIE['username']);
	                 $file_ext=file_name_filter($file_name,true);
	
	                 $file_name_1=$fix_file_name.'-1'. $file_ext;
	                 $file_name_2=$fix_file_name.'-2'. $file_ext;
	                 $file_name_3=$fix_file_name.'-3'. $file_ext;
	                 $thefilename=$file_name_1.'|'.$file_name_2.'|'.$file_name_3;
	
	                 $destination1=FILES_PATH."/users/".$file_name_1;
	                 $destination2=FILES_PATH."/users/".$file_name_2;
	                 $destination3=FILES_PATH."/users/".$file_name_3;
	
	                 //upload_resize($file_source, $destination3, $file_type, 32,38);
	                 //upload_resize($file_source, $destination2, $file_type, 50,60);
	                 //upload_resize($file_source, $destination1, $file_type, 250,300);
	                            
	                 upload_crop($file_source, $destination3, $file_type, 32,32);
	                 upload_crop($file_source, $destination2, $file_type, 50,50);
	                 upload_crop($file_source, $destination1, $file_type, 250,300);
	
	                 $sql=$db->prepare_query("UPDATE lumonata_users
	                                                       SET lavatar=%s
	                                                       WHERE luser_id=%d",$thefilename,$_COOKIE['user_id']);
	                 $r=$db->do_query($sql);
	                             header("location:".cur_pageURL());
	           }else{
	                $thealert="<div class=\"alert_yellow\">The maximum file size is 2MB</div>";
	           }
	       }else{
	                $thealert="<div class=\"alert_yellow\">The maximum file size is 2MB</div>";
	                         
	       }
       }
               
       $tabs=array('my-updates'=>'My Updates','my-profile'=>'My Profile','profile-picture'=>'Profile Picture','eduwork'=>'Education & Work');
		
		if(isset($_GET['tab']))
			$selected_tab=$_GET['tab'];
		else
            $selected_tab='my-profile';


		$the_tabs=set_tabs($tabs,$selected_tab);

		//set template
		set_template(TEMPLATE_PATH."/users.html",'users');

		//set block
		add_block('usersEdit','uEdit','users');
		add_block('usersAddNew','uAddNew','users');
        add_block('profilePicture','pPicture','users');
        add_block('educationWork','eduWork','users');

        //set the page Title
		add_actions('section_title','Edit Profile Picture');

		//set varibales
		//$d=fetch_user($_COOKIE['user_id']);
                $get_avatar=get_avatar($_COOKIE['user_id']);
                $theavatar="";
                if(!empty($get_avatar)){
                    $d=fetch_user($_COOKIE['user_id']);
                    $theavatar="<img src=\"$get_avatar\" alt=\"".$d['ldisplay_name']."\" title=\"".$d['ldisplay_name']."\" />";
                }
                add_variable('the_avatar',$theavatar);
		add_variable('prc','Edit Profile Picture');
		add_variable('tabs',$the_tabs);
		add_variable('upload_button',upload_button());
                add_variable('alert',$thealert);
                

		parse_template('profilePicture','pPicture');

		return return_template('users');
    }
    
    /**
	 * Get the User Education and Work Information 
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 *  
	 * @return string The Education and Work HTML Design    
	 */
    function get_eduwork($eduwork,$user_id){
    	
    	$edw=get_additional_field($user_id, $eduwork, 'user');
    	$edw=json_decode($edw,true);
    	$html='';
    	if($eduwork=='work'){
    		
    		$i=0;
    		if(count($edw)>0)
    		foreach ($edw as $key=>$val){
    			$present=($val['to_period']!='present')?false:true;
    			$from_period=explode(" ", $val['from_period']);
    			$to_period=explode(" ", $val['to_period']);
    			
    			$html.="<div class=\"the_work clearfix\" id='the_work_".$i."'>
				    		<div class=\"comp_name\">
					    		<strong>".$key." - ".$val['position']." (".$val['city'].")</strong><br />
					    		<label>".$val['from_period']." to ".$val['to_period']."</label><br />
					    		<span style='font-size:10px;'>".$val['jobdes']."</span>
				    		</div>";
    				if($user_id==$_COOKIE['user_id'] && isset($_COOKIE['user_id']))
				    $html.="<div class=\"action\"><a href=\"javascript:;\" id=\"work_edit_".$i."\">Edit</a> | <a href=\"javascript:;\" rel=\"delete_work_".$i."\">Delete</a></div>";
				$html.="</div>";
				
    			if($user_id==$_COOKIE['user_id'] && isset($_COOKIE['user_id'])){
	    			$html.="<script type='text/javascript'>
	    						$(function(){
	    							$('#the_work_".$i."').click(function(){
	    								$('input[name=company_name]').val('".$key."');
	    								$('input[name=position]').val('".$val['position']."');
	    								$('input[name=city]').val('".$val['city']."');
	    								$('textarea[name=jobdes]').val('".$val['jobdes']."');
	    								$('select[name=from_month_period]').val('".$from_period[0]."');
	    								$('select[name=from_year_period]').val('".$from_period[1]."');";
	    					if($present){
	    						$html.="$('select[name=to_month_period]').hide();
										$('select[name=to_year_period]').hide();
										$('#present_text').show();
										$('input[name=present]').attr('checked','checked');
										";
	    					}else{
	    						$html.="$('select[name=to_month_period]').val('".$to_period[0]."');
	    								$('select[name=to_year_period]').val('".$to_period[1]."');
	    								$('select[name=to_month_period]').show();
										$('select[name=to_year_period]').show();
										$('#present_text').hide();
										$('input[name=present]').removeAttr('checked');
										";
	    					}
	    					
	    			$html.="	});
	    						});
	    					</script>";
	    			add_actions('admin_tail','delete_confirmation_box','work_'.$i, "Are you sure want to delete ".$key." from your Work Experience list?", '../lumonata-functions/user.php', 'the_work_'.$i,'eduwork='.$eduwork.'&delete_key='.$key);
	    			//$html.=delete_confirmation_box('work_'.$i, "Are you sure want to delete ".$key." from your Work Experience list?", '../lumonata-functions/user.php', 'the_work_'.$i,'eduwork='.$eduwork.'&delete_key='.$key);
    			}
    			$i++;
    		}
    		
    	}elseif($eduwork=='college'){
    		$i=0;
    		if(count($edw)>0)
    		foreach ($edw as $key=>$val){
    			$html.="<div class=\"the_school clearfix\" id=\"college_".$i."\">
				    		<div class=\"shool_name\" >
					    		<strong>".$key."</strong><br />
					    		<label>".$val['concentrations']." - ".$val['class_year']."</label>
				    		</div>";
    				if($user_id==$_COOKIE['user_id'] && isset($_COOKIE['user_id']))
				    $html.="<div class=\"action\"><a href=\"javascript:;\">Edit</a> | <a href=\"javascript:;\" rel=\"delete_college_".$i."\">Delete</a></div>";
				 $html.="</div>";
				 
				if($user_id==$_COOKIE['user_id'] && isset($_COOKIE['user_id'])){ 
	    			$html.="<script type='text/javascript'>
	    					$(function(){
	    						$('#college_".$i."').click(function(){
	    							$('input[name=college]').val('".$key."');
	    							$('select[name=collage_class_year]').val('".$val['class_year']."');
	    							$('input[name=concentrations]').val('".$val['concentrations']."');
	    						});
	    					});
	    				</script>";
	    			$html.=delete_confirmation_box('college_'.$i, "Are you sure want to delete ".$key." from your College/University list?", '../lumonata-functions/user.php', 'college_'.$i,'eduwork='.$eduwork.'&delete_key='.$key);
				}
    			$i++;
    		}
    	}elseif($eduwork=='school'){
    		$i=0;
    		if(count($edw)>0)
    		foreach ($edw as $key=>$val){
    			$html.="<div class=\"the_school clearfix\" id=\"the_school_".$i."\">
				    		<div class=\"shool_name\">
					    		<strong>".$key."</strong><br />
					    		<label>".$val."</label>
				    		</div>";
    			if($user_id==$_COOKIE['user_id'] && isset($_COOKIE['user_id']))
				$html.="<div class=\"action\"><a href=\"javascript:;\">Edit</a> | <a href=\"javascript:;\" rel=\"delete_school_".$i."\">Delete</a></div>";
				$html.="</div>";
				
				if($user_id==$_COOKIE['user_id'] && isset($_COOKIE['user_id'])){
	    			$html.="<script type='text/javascript'>
	    					$(function(){
	    						$('#the_school_".$i."').click(function(){
	    							$('input[name=school_name]').val('".$key."');
	    							$('select[name=school_class_year]').val('".$val."');
	    						});
	    					});
	    				</script>";
	    			$html.=delete_confirmation_box('school_'.$i, "Are you sure want to delete ".$key." from your high school list?", '../lumonata-functions/user.php', 'the_school_'.$i,'eduwork='.$eduwork.'&delete_key='.$key);
				}
    			$i++;
    		}
    	}
    	return $html;
    }
    
    /**
	 * Manage user education and work 
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 *  
	 * @return string The Education and Work HTML Design    
	 */
    function profile_eduwork(){
       global $db;
       $thealert="";
         
               
       $tabs=array('my-updates'=>'My Updates','my-profile'=>'My Profile','profile-picture'=>'Profile Picture','eduwork'=>'Education & Work');
		
		if(isset($_GET['tab']))
			$selected_tab=$_GET['tab'];
		else
            $selected_tab='my-profile';


		$the_tabs=set_tabs($tabs,$selected_tab);

		//set template
		set_template(TEMPLATE_PATH."/users.html",'users');

		//set block
		add_block('usersEdit','uEdit','users');
		add_block('usersAddNew','uAddNew','users');
        add_block('profilePicture','pPicture','users');
        add_block('educationWork','eduWork','users');

        //set the page Title
		add_actions('section_title','Education &amp; Work');

		//set varibales
		$year_to=date("Y")+7;
		$year="";
		$month=array(
			'January'=>'January',
			'February'=>'February',
			'March'=>'March',
			'April'=>'April',
			'May'=>'May',
			'June'=>'June',
			'July'=>'July',
			'Augusts'=>'Augusts',
			'September'=>'September',
			'October'=>'October',
			'November'=>'November',
			'December'=>'December'
		);
		for($y=$year_to;$y>=1910;$y--){
			$year.="<option value=\"".$y."\">".$y."</option>";
		}
    	foreach($month as $key=>$val){
			$month.="<option value=\"".$key."\">".$val."</option>";
		}
		add_variable('year',$year);
		add_variable('month',$month);
        add_variable('school_list',get_eduwork('school', $_COOKIE['user_id']));
        add_variable('collage_list',get_eduwork('college', $_COOKIE['user_id']));
        add_variable('work_list',get_eduwork('work', $_COOKIE['user_id']));
		add_variable('prc','Education &amp; Work');
		add_variable('tabs',$the_tabs);
		add_variable('upload_button',upload_button());
        add_variable('alert',$thealert);
                

		parse_template('educationWork','eduWork');

		return return_template('users');
    }
    
     /**
	 * Grab user data by the user type  
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 *  
	 * @return array user data by user type    
	 */
    function fetch_user_per_type($user_type){
    	global $db;
		
		if(!empty($user_type)){
			$sql=$db->prepare_query("select * from lumonata_users where luser_type=%s",$user_type);
			$r=$db->do_query($sql);
			while($data=$db->fetch_array($r)){
				$user[]=$data['luser_id'];
			}
			return $user;
		}
    }
    
    /**
	 * Grab user data by the user ID or User name
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 *  
	 * @return array user data by ID or Username    
	 */
	function fetch_user($id){
		global $db;		
		
		$sql=$db->prepare_query("select * from lumonata_users where luser_id=%d",$id);
		$r=$db->do_query($sql);
		if($db->num_rows($r) > 0){
			return $d=$db->fetch_array($r);
		}else{
			$sql=$db->prepare_query("select * from lumonata_users where lusername=%s",$id);
			$r1=$db->do_query($sql);
			return $d=$db->fetch_array($r1);
		}
	}
	
	/**
	 * Create the option display name from First and Last Name
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 *  
	 * @return array Display name options    
	 */
	function opt_display_name($id){
		$d=fetch_user($id);
		$display_name[$d['lusername']]=$d['lusername'];
		
		$first_name=get_additional_field($id,'first_name','user');
		if(!empty($first_name))
			$display_name[$first_name]=$first_name;
		
		$last_name=get_additional_field($id,'last_name','user');
		if(!empty($last_name))
			$display_name[$last_name]=$last_name;
			
		if(!empty($first_name) && !empty($last_name)){
			$display_name[$first_name." ".$last_name]=$first_name." ".$last_name;
			$display_name[$last_name." ".$first_name]=$last_name." ".$first_name;
		}
		
		return $display_name;
	}
	/**
	 * Check if the given user_id is a standard user or no 
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $user_id User ID
	 *  
	 * @return boolean     
	 */
	function is_standard_user($user_id=0){
		if(empty($user_id)){
			if($_COOKIE['user_type']=='standard') return true;
			else return false;
		}else{
			$user=fetch_user($user_id);
			if($user['luser_type']=='standard')return true;
			else return false;
		}
	}
	/**
	 * Check if the given user_id is a contributor or no 
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $user_id User ID
	 *  
	 * @return boolean     
	 */
	function is_contributor($user_id=0){
		if(empty($user_id)){
			if($_COOKIE['user_type']=='contributor') return true;
			else return false;
		}else{
			$user=fetch_user($user_id);
			if($user['luser_type']=='contributor')return true;
			else return false;
		}
	}
	/**
	 * Check if the given user_id is an author or no 
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $user_id User ID
	 *  
	 * @return boolean     
	 */
	function is_author($user_id=0){
		if(empty($user_id)){
			if($_COOKIE['user_type']=='author') return true;
			else return false;
		}else{
			$user=fetch_user($user_id);
			if($user['luser_type']=='author')return true;
			else return false;
		}
	}
	/**
	 * Check if the given user_id is an editor or no 
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $user_id User ID
	 *  
	 * @return boolean     
	 */
	function is_editor($user_id=0){
		if(empty($user_id)){
			if($_COOKIE['user_type']=='editor') return true;
			else return false;
		}else{
			$user=fetch_user($user_id);
			if($user['luser_type']=='editor')return true;
			else return false;
		}
	}
	/**
	 * Check if the given user_id is an administrator or no 
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $user_id User ID
	 *  
	 * @return boolean     
	 */
	function is_administrator($user_id=0){
		if(empty($user_id)){
			if($_COOKIE['user_type']=='administrator') return true;
			else return false;
		}else{
			$user=fetch_user($user_id);
			if($user['luser_type']=='administrator')return true;
			else return false;
		}
	}
	
	/**
	 * Get the user avatar image 
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $user_id User ID
	 * @param integer $image_size 1=Large,2=Medium,3=Small 
	 *  
	 * @return boolean     
	 */
    function get_avatar($user_id,$image_size=1){
            $d=fetch_user($user_id);
            if(empty($d['lavatar'])){
                if($d['lsex']==1)
                    return 'http://'.site_url().'/lumonata-content/files/users/man-'.$image_size.'.jpg';
                else
                    return 'http://'.site_url().'/lumonata-content/files/users/woman-'.$image_size.'.jpg';
            }else{
                 $file_name=explode('|',$d['lavatar']);
                 switch($image_size){
                     case 1:
                         $thefile=$file_name[0];
                         break;
                     case 2:
                         $thefile=$file_name[1];
                         break;
                     case 3:
                         $thefile=$file_name[2];
                         break;
                 }
                 if(file_exists(FILES_PATH.'/users/'.$thefile)){
                    return 'http://'.site_url().'/lumonata-content/files/users/'.$thefile;
                 }else{
                    if($d['lsex']==1)
                        return 'http://'.site_url().'/lumonata-content/files/users/man-'.$image_size.'.jpg';
                    else
                        return 'http://'.site_url().'/lumonata-content/files/users/woman-'.$image_size.'.jpg';
                 }
            }
    }
    
    /**
	 * Get the display name of user 
	 *  
	 *     
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param integer $id User ID
	 *  
	 *  
	 * @return boolean     
	 */
    function get_display_name($id){
    	$data=fetch_user($id);
    	return $data['ldisplay_name'];
    }
?>