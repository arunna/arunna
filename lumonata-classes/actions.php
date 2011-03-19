<?php
/*
  This class is used for defined the functions that will be run
  Version 1.0
  
*/
class actions{
    
    function actions(){
        //$this->action=array();
    }
    
    
    function add_actions($args=array()){
   
     
      if(count($args)==0)
      return;
      
      $label= $args[0];
      $func_name= $args[1];
      if(count($args)>2){
        $args_shift=array_shift($args);
        $args_shift=array_shift($args);
      }else{
        $args='';
      }
     
      $this->action[$label]['func_name'][]=$func_name;
      $this->action[$label]['args'][]=$args;
      
    }
    
    function attemp_actions($lbl){
        $result='';
        
        if(empty($this->action[$lbl]['func_name']))
        return;
        
        
                
        for($j=0;$j<count($this->action[$lbl]['func_name']);$j++){
            
            if(function_exists($this->action[$lbl]['func_name'][$j])){
                $result.= call_user_func_array($this->action[$lbl]['func_name'][$j],(is_array($this->action[$lbl]['args'][$j]))?$this->action[$lbl]['args'][$j]:array($this->action[$lbl]['args'][$j]))."\n";
            }else{
                return $this->action[$lbl]['func_name'][$j];
            }
        }
        
        return $result;
    }
    
    function run_actions($lbl){
        $result='';
        
        if(empty($this->action[$lbl]['func_name']))
        return;
       
        for($j=0;$j<count($this->action[$lbl]['func_name']);$j++){
            if(function_exists($this->action[$lbl]['func_name'][$j]))
                return call_user_func_array($this->action[$lbl]['func_name'][$j],(is_array($this->action[$lbl]['args'][$j]))?$this->action[$lbl]['args'][$j]:array($this->action[$lbl]['args'][$j]));
            else
                return $this->action[$lbl]['func_name'][$j];
        }
        
       
    }
    
}

$actions=new actions();

function add_actions($args=NULL){
    global $actions;
    if($args=NULL)
    return;
    
    $args = func_get_args();
    
    $new_args[]= $args[0];
    $new_args[]= $args[1];
    
    if(count($args)>2){
        $args_shift=array_shift($args);
        $args_shift=array_shift($args);
        $new_argument= array_merge($new_args, $args);
    }else{
        $new_argument=$new_args;
    }
    
    $actions->add_actions($new_argument);
   
    
}

function attemp_actions($lbl){
    global $actions;
    return $actions->attemp_actions($lbl);
}

function run_actions($lbl){
    global $actions;
    return $actions->run_actions($lbl);
}



?>