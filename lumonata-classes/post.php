<?php
    class post{
        function post(){
            $this->post_index=0;
            $this->post_id=0;
            $this->post_title='';
            $this->post_content='';
            $this->post_status='';
            $this->post_type='';
            $this->post_comments=''; 
            $this->comment_id=0;
        }
    }
    
    $thepost=new post();
?>