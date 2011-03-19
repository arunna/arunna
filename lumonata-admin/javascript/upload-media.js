$(function(){
    $('input[name=cancel]').click(function(){
        window.parent.$('.upload_image').colorbox.close();
    });
});


$(function(){
    $("input[name=url]").keyup(function(){
        $("input[name=link_to]").val($("input[name=url]").val());
        $("#link_to_file").val($("input[name=url]").val());
    });
    
    $("input[name=url]").blur(function(){
        $("input[name=link_to]").val($("input[name=url]").val());
        $("#link_to_file").val($("input[name=url]").val());
    });
    
});

$(function(){
    $('#my_link').click(function(){
        $('#link_to').val($('#link_to_file').val());
    });
 });
 
 $(function(){
    $('#link_none').click(function(){

         $('#link_to').val('');
    });
 });

$(function(){
   $("input[name=insert]").click(function(){
        var type= $("input[name=type]").val();
        var textarea_id=$("input[name=textarea_id]").val();
        var link_to= $("input[name=link_to]").val();
        var title = $("input[name=title]").val();
        var src = $("input[name=url]").val();
        
        if(type=="image"){
            var alt_text= $("input[name=alt_text]").val();
            var caption= $("input[name=caption]").val();
            var alignment= $("input[name=alignment]:checked").val();
            
            
            if(alt_text.length!=0){
                alt="alt=\""+alt_text+"\"";
            }else{
                alt="";
            }
            
            if(title.length!=0){
                title="title=\""+title+"\"";
            }else{
                title="";
            }
           
            if(alignment!="center"){
                the_float="float:"+alignment;
                the_center_div="";
                end_center_div="";
               
            }else{
                
                the_center_div="<p style=\"text-align:center;\">";
                the_float="";
                end_center_div="</p>";
            }
            
            if(link_to.length!=0){
                the_link="<a href=\""+link_to+"\">";
                end_link="</a>";
            }else{
                the_link="";
                end_link="";
            }
            
            the_content=the_center_div;
            the_content+=the_link;
            the_content+="<img src=\""+src+"\" "+alt+" "+title+" style=\""+the_float+"\" />";
            the_content+=end_link;
            the_content+=end_center_div;
            
        }else{
            the_content="";
            src=link_to;
            if(link_to.length!=0){
                the_link="<a href=\""+src+"\">";
                end_link="</a>";
            }else{
                the_link="";
                end_link="";
            }
            
            the_content+=the_link;
            the_content+=title;
            the_content+=end_link;
        }
        
        window.parent.$("#textarea_"+textarea_id).tinymce().execCommand("mceInsertContent",false,the_content);
	window.parent.$('.upload_image').colorbox.close();
       
    });
});

