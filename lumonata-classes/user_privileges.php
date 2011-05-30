<?php
    class user_privileges{
        var $user_type;
        var $the_privileges;
        var $the_actions;
        
        function user_privileges(){
            /***********Privilege for Administrator***********/
        	//Status
            $this->add_privileges('administrator','status','preview');
            
            //Dashboard
            $this->add_privileges('administrator','dashboard','preview');
            
            //Global Settings
            $this->add_privileges('administrator','global_settings','insert');
            $this->add_privileges('administrator','global_settings','update');
            
             //Menus
            $this->add_privileges('administrator','menus','insert');
            $this->add_privileges('administrator','menus','update');
            $this->add_privileges('administrator','menus','delete');
            
            //Applications
            $this->add_privileges('administrator','applications','insert');
            $this->add_privileges('administrator','applications','update');
            $this->add_privileges('administrator','applications','delete');
            $this->add_privileges('administrator','applications','upload');
            $this->add_privileges('administrator','applications','install');
            
            //Plugins
            $this->add_privileges('administrator','plugins','insert');
            $this->add_privileges('administrator','plugins','update');
            $this->add_privileges('administrator','plugins','delete');
            $this->add_privileges('administrator','plugins','upload');
            $this->add_privileges('administrator','plugins','install');
            
            //Themes
            $this->add_privileges('administrator','themes','insert');
            $this->add_privileges('administrator','themes','update');
             $this->add_privileges('administrator','themes','delete');
            
            //Comments
            $this->add_privileges('administrator','comments','update');
            $this->add_privileges('administrator','comments','approve');
            $this->add_privileges('administrator','comments','delete');
            
            //Articles
            $this->add_privileges('administrator','articles','insert');
            $this->add_privileges('administrator','articles','update');
            $this->add_privileges('administrator','articles','delete');
            $this->add_privileges('administrator','articles','upload');
            
            //Pages
            $this->add_privileges('administrator','pages','insert');
            $this->add_privileges('administrator','pages','update');
            $this->add_privileges('administrator','pages','delete');
            $this->add_privileges('administrator','pages','upload');
            
            //Categories
            $this->add_privileges('administrator','categories','insert');
            $this->add_privileges('administrator','categories','update');
            $this->add_privileges('administrator','categories','delete');
            $this->add_privileges('administrator','categories','upload');
            
            //Tags
            $this->add_privileges('administrator','tags','insert');
            $this->add_privileges('administrator','tags','update');
            $this->add_privileges('administrator','tags','delete');
           
            
            //Users
            $this->add_privileges('administrator','users','insert');
            $this->add_privileges('administrator','users','update');
            $this->add_privileges('administrator','users','delete');
            
            //Friends
            $this->add_privileges('administrator','friends','insert');
            $this->add_privileges('administrator','friends','update');
            $this->add_privileges('administrator','friends','delete');
            
            //Notifications
            $this->add_privileges('administrator','notifications','preview');
            
           
            
            /***********Privilege for Editor***********/
            //Status
            $this->add_privileges('editor','status','preview');
            
            //Dashboard
            $this->add_privileges('editor','dashboard','preview');
            
           
            //Applications
            $this->add_privileges('editor','applications','insert');
            $this->add_privileges('editor','applications','update');
            $this->add_privileges('editor','applications','delete');
            $this->add_privileges('editor','applications','upload');
           
            //Comments
            $this->add_privileges('editor','comments','update');
            $this->add_privileges('editor','comments','approve');
            $this->add_privileges('editor','comments','delete');
            
            //Articles
            $this->add_privileges('editor','articles','insert');
            $this->add_privileges('editor','articles','update');
            $this->add_privileges('editor','articles','delete');
            $this->add_privileges('editor','articles','upload');
            
            //Pages
            $this->add_privileges('editor','pages','insert');
            $this->add_privileges('editor','pages','update');
            $this->add_privileges('editor','pages','delete');
            $this->add_privileges('editor','pages','upload');
            
            //Categories
            $this->add_privileges('editor','categories','insert');
            $this->add_privileges('editor','categories','update');
            $this->add_privileges('editor','categories','delete');
            $this->add_privileges('editor','categories','upload');
            
            //Tags
            $this->add_privileges('editor','tags','insert');
            $this->add_privileges('editor','tags','update');
            $this->add_privileges('editor','tags','delete');
            
            //Profile
            $this->add_privileges('editor','my-profile','update');
            
            //Friends
            $this->add_privileges('editor','friends','insert');
            $this->add_privileges('editor','friends','update');
            $this->add_privileges('editor','friends','delete');
            
            /***********Privilege for Author***********/
            //Status
            $this->add_privileges('author','status','preview');
            
            //Dashboard
            $this->add_privileges('author','dashboard','preview');
            
            //Applications
            $this->add_privileges('author','applications','insert');
            $this->add_privileges('author','applications','update');
            $this->add_privileges('author','applications','delete');
            $this->add_privileges('author','applications','upload');
           
            
            //Articles
            $this->add_privileges('author','articles','insert');
            $this->add_privileges('author','articles','update');
            $this->add_privileges('author','articles','delete');
            $this->add_privileges('author','articles','upload');
            
             //Comments
            $this->add_privileges('editor','comments','approve');
            
            //Pages
            $this->add_privileges('author','pages','insert');
            $this->add_privileges('author','pages','update');
            $this->add_privileges('author','pages','delete');
            $this->add_privileges('author','pages','upload');
            
            //Profile
            $this->add_privileges('author','my-profile','update');
            
            //Friends
            $this->add_privileges('author','friends','insert');
            $this->add_privileges('author','friends','update');
            $this->add_privileges('author','friends','delete');
            
            //Notifications
            $this->add_privileges('author','notifications','preview');
            
            /***********Privilege for Contributor***********/
            //Status
            $this->add_privileges('contributor','status','preview');
            
            //Dashboard
            $this->add_privileges('contributor','dashboard','preview');
            
            //Applications
            $this->add_privileges('contributor','applications','insert');
            $this->add_privileges('contributor','applications','update');
            $this->add_privileges('contributor','applications','delete');
            $this->add_privileges('contributor','applications','upload');
           
            
            //Articles
            $this->add_privileges('contributor','articles','insert');
            $this->add_privileges('contributor','articles','update');
            $this->add_privileges('contributor','articles','delete');
            $this->add_privileges('contributor','articles','upload');
            
             //Comments
            $this->add_privileges('editor','comments','approve');
            
            //Pages
            $this->add_privileges('contributor','pages','insert');
            $this->add_privileges('contributor','pages','update');
            $this->add_privileges('contributor','pages','delete');
            $this->add_privileges('contributor','pages','upload');
            
            //Profile
            $this->add_privileges('contributor','my-profile','update');
            
            //Friends
            $this->add_privileges('contributor','friends','insert');
            $this->add_privileges('contributor','friends','update');
            $this->add_privileges('contributor','friends','delete');
            
            //Notifications
            $this->add_privileges('contributor','notifications','preview');
            
           /***********Privilege for Standard User***********/
            //Status
            $this->add_privileges('standard','status','preview');
            
            //Profile
            $this->add_privileges('standard','my-profile','update');
            
            //Dashboard
            $this->add_privileges('standard','dashboard','preview');
            
            //Friends
            $this->add_privileges('standard','friends','insert');
            $this->add_privileges('standard','friends','update');
            $this->add_privileges('standard','friends','delete');
            
             //Notifications
            $this->add_privileges('standard','notifications','preview');
            
            
        }
          
        function add_privileges($role,$app_name,$action){
            $this->the_privileges[$role][$app_name][]=$action;
        }
        
        function get_privileges(){
            return $this->the_privileges;
        }
        
        
    }
    
    $user_privileges=new user_privileges();
    
    
    function add_privileges($role,$app_name,$action){
       global $user_privileges;
       $user_privileges->add_privileges($role,$app_name,$action);
    }
    function is_grant_app($app_name){
        global $user_privileges;
        $the_privilege=$user_privileges->get_privileges();
        if(!isset($_COOKIE['user_type']))
        return false;
        
        if(isset($the_privilege[$_COOKIE['user_type']])){
            foreach($the_privilege[$_COOKIE['user_type']] as $key=>$val){
               if($key==$app_name)
                return true;
            }
        }
        return false;
    }
    
    function allow_action($app_name,$action){
        global $user_privileges;
        $the_privilege=$user_privileges->get_privileges();
        
        if(!isset($_COOKIE['user_type']))
        return false;
        
        if(isset($the_privilege[$_COOKIE['user_type']][$app_name])){
            foreach($the_privilege[$_COOKIE['user_type']][$app_name] as $key=>$val){
               if($val==$action)
               return true;
            }
        }
        return false;
    }
    
    
?>