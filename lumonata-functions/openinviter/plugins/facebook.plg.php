<?php 
/*Import Friends from Facebook
* You can send message to your Friends Inbox
*/
$_pluginInfo=array(
    'name'=>'Facebook',
    'version'=>'1.2.7',
    'description'=>"Get the contacts from a Facebook account",
    'base_version'=>'1.8.0',
    'type'=>'social',
    'check_url'=>'http://apps.facebook.com/resources/',
    'requirement'=>'email',
    'allowed_domains'=>false,
    );
/**
* FaceBook Plugin
*
* Imports user's contacts from FaceBook and sends
* messages using FaceBook's internal system.
*
* @author OpenInviter
* @version 1.0.8
*/
class facebook extends openinviter_base // line 24
    {
    public $login_ok=false;
    public $showContacts=true;
    public $internalError=false;
    protected $timeout=60;

    public $debug_array=array(
                'initial_get'=>'pass',
                'get_friends'=>'payload',
                'update_status'=>'sectitle',
                'url_message'=>'<form method=',
       			'friend_page' => '"error":0',
                'join_fan_page_link' => 'Become a Fan',
                'join_success' => '<b>You are now a fan of',
                'already_joined' => 'Remove Me from Fans',
                'invite_success'=>'"body":"Your invitations have been sent."',
                'post_form_id'=>'name="post_form_id"',
                'initial_get'=>'pass',
				'login_post'=>'javascripts',
				'get_user_id'=>'profile.php?id=',
				'url_friends'=>'fb_dtsg:"',
				'message_elements'=>'composer_id',
				'send_message'=>'"error":0',
                );

    /**
     * Login function
     *
     * Makes all the necessary requests to authenticate
     * the current user to the server.
     *
     * @param string $user The current user.
     * @param string $pass The password for the current user.
     * @return bool TRUE if the current user was authenticated successfully, FALSE otherwise.
     */
    public function login($user, $pass)
    {
        $this->resetDebugger();
        $this->service='facebook';
        $this->service_user=$user;
        $this->service_password=$pass;
        if (!$this->init()) return false;

        $res=$this->get("http://apps.facebook.com/causes/",true);
    //var_dump($res);
    /*echo '<script>stop_request()</script>';exit;*/
        if ($this->checkResponse("initial_get",$res))
            $this->updateDebugBuffer('initial_get',"http://apps.facebook.com/causes/",'GET');
        else
            {
            $this->updateDebugBuffer('initial_get',"http://apps.facebook.com/causes/",'GET',false);
            $this->debugRequest();
            $this->stopPlugin(true);
            return false;
            }

        $form_action="https://login.facebook.com/login.php?login_attempt=1";

		$loginurl = $this->get("http://www.facebook.com/",true);

		$lsd = $this->getElementString($res,'<input type="hidden" id="lsd" name="lsd" value="','"');

		$charset = urldecode('%E2%82%AC%2C%C2%B4%2C%E2%82%AC%2C%C2%B4%2C%E6%B0%B4%2C%D0%94%2C%D0%84');
		$post_elements=array('email'=>$user,
                             'pass'=>$pass,
                             'locale'=>'en_US',
                             'persistent'=>'1',
                             'charset_test'=>$charset,
                             'lsd'=>$lsd
                             );

        $res=$this->post($form_action,$post_elements,true);

        //if ($this->checkResponse("login_post",$res))
		if(!empty($res))
            $this->updateDebugBuffer('login_post',"{$form_action}",'POST',true,$post_elements);
        else
            {
            $this->updateDebugBuffer('login_post',"{$form_action}",'POST',false,$post_elements);
            $this->debugRequest();
            $this->stopPlugin();
            return false;
            }

        $userId=$this->getElementString($res,'Env={user:',',');
        if (empty($userId)) {$this->login_ok=false;}
        else {$this->login_ok="http://www.facebook.com/ajax/social_graph/fetch.php?__a=1";}
        return true;
        }

	public function getMyContacts()
		{
		if (!$this->login_ok)
			{
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		else $url=$this->login_ok;

		$res=$this->get("http://www.facebook.com/ajax/typeahead/friends_page_search.php?u=".$this->userId."&__a=1");

		// echo $res;

		if ($this->checkResponse("friend_page",$res))
		 	$this->updateDebugBuffer('friend_page',"http://www.facebook.com/ajax/typeahead/friends_page_search.php?u=".$this->userId."&__a=1",'GET');
		 else
			{
			$this->updateDebugBuffer('friend_page',"http://www.facebook.com/ajax/typeahead/friends_page_search.php?u=".$this->userId."&__a=1",'GET',false);
			$this->debugRequest();
			$this->stopPlugin();
			return false;
		}

		$contacts=array();
		preg_match_all('#\"t\"\:\"(.+)\"\,\"i\"\:\"(.+)\"#U',$res,$matches);

		// print_r($matches);

			if (!empty($matches[2])) {
				foreach($matches[2] as $key=>$fbId) {
					if (!empty($matches[1][$key])) $contacts[$fbId]=$matches[1][$key];
				}
			}
		return $contacts;
		}


	// SENDMESSAGE FUNCTION
    /**
	 * Send message to contacts
	 * 
	 * Sends a message to the contacts using
	 * the service's inernal messaging system
	 * 
	 * @param string $session_id The OpenInviter user's session ID
	 * @param string $message The message being sent to your contacts
	 * @param array $contacts An array of the contacts that will receive the message
	 * @return mixed FALSE on failure.
	 */
	 public function sendMessage($session_id,$message,$contacts){
		$countMessages=0;							
		$res=$this->get('http://www.facebook.com/?sk=messages',true);
		if ($this->checkResponse("message_elements",$res))
			$this->updateDebugBuffer('message_elements',"http://www.facebook.com/home.php?#!/?sk=messages",'GET');
		else{
			$this->updateDebugBuffer('message_elements',"http://www.facebook.com/home.php?#!/?sk=messages",'GET',false);
			$this->debugRequest();
			$this->stopPlugin();
			echo "here";
			return false;
		}
		$composerId=$this->getElementString($res,'"composer_id\" value=\"','\"');
		$postFormId=$this->getElementString($res,'name="post_form_id" value="','"');
		$userId=$this->getElementString($res,"www.facebook.com\/profile.php?id=",'\"');
		$fbDtsg=$this->getElementString($res,'fb_dtsg:"','"');
		$form_action="http://www.facebook.com/ajax/gigaboxx/endpoint/MessageComposerEndpoint.php?__a=1";
		$post_elements=array();
		foreach($contacts as $fbId=>$name)
			{						
			$countMessages++;
			if ($countMessages>$this->maxMessages) break;			
			$post_elements=array("ids_{$composerId}[0]"=>$fbId,
								  "ids[0]"=>$fbId,
								  'subject'=>$message['subject'],
								  'status'=>$message['body'],
								  'action'=>'send_new',
								  'home_tab_id'=>1,
								  'profile_id'=>$userId,
								  'target_id'=>0,							  
								  'composer_id'=>$composerId,
								  'hey_kid_im_a_composer'=>'true',							  
								  'post_form_id'=>$postFormId,
								  'fb_dtsg'=>$fbDtsg,
								  '_log_action'=>'send_new',							 
								  'ajax_log'=>1,
								  'post_form_id_source'=>'AsyncRequest'								  
								  );				
			$res=$this->post($form_action,$post_elements);
			if ($this->checkResponse("send_message",$res))
				$this->updateDebugBuffer('send_message',"{$form_action}",'POST',true,$post_elements);
			else
				{
				$this->updateDebugBuffer('send_message',"{$form_action}",'POST',false,$post_elements);
				$this->debugRequest();
				$this->stopPlugin();
				return false;
				}
			sleep($this->messageDelay);
			}						
	}

    /**
     * Terminate session
     *
     * Terminates the current user's session,
     * debugs the request and reset's the internal
     * debugger.
     *
     * @return bool TRUE if the session was terminated successfully, FALSE otherwise.
     */
    public function logout()
        {
        if (!$this->checkSession()) return false;
        $res=$this->get("http://www.facebook.com/?ref=home");

        $logoutdata = $this->getElementString($res,'<a href="http://www.facebook.com/logout.php?','"');
        $logouturl = "http://www.facebook.com/logout.php?".$logoutdata;

		$res=$this->get($logouturl);

        $this->debugRequest();
        $this->resetDebugger();
        $this->stopPlugin();
        return true;
        }
    }

?>