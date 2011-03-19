$(document).ready(function(){ 	
    $(function() {
            $("#list_item").mousedown(function(){
                $(this).css('cursor','move');
            });
             $("#list_item").mouseup(function(){
                $(this).css('cursor','default');
            });
            $("#list_item").sortable({ axis:'y', cursor: 'move', update: function() {
                    var start_order=$("input[name=start_order]").val();
                    var state=$("input[name=state]").val();
                    var order = $(this).sortable("serialize") + '&update_order=list&start='+start_order+'&state='+state;
                    var order_array=$(this).sortable("toArray");
                   
                    for(i=0;i<order_array.length;i++){
                        var string=order_array[i];
                        
                        string=string.split("_");
                       
                        
                        $("#order_"+string[1]).val(start_order);
                        start_order++;
                        
                    }
                    $.post("articles.php", order,function(data){
                                                    $('#response').html(data);
							 
		    });
                    
            }								  
            });
    });
    
    $(function() {
	        $("#list_taxonomy").mousedown(function(){
	            $(this).css('cursor','move');
	        });
	         $("#list_taxonomy").mouseup(function(){
	            $(this).css('cursor','default');
	        });
	        $("#list_taxonomy").sortable({ axis:'y', cursor: 'move', update: function() {
	                var start_order=$("input[name=start_order]").val();
	                var state=$("input[name=state]").val();
	                var order = $(this).sortable("serialize") + '&update_order=list&start='+start_order+'&state='+state;
	                var order_array=$(this).sortable("toArray");
	               
	                for(i=0;i<order_array.length;i++){
	                    var string=order_array[i];
	                    
	                    string=string.split("_");
	                   
	                    
	                    $("#order_"+string[1]).val(start_order);
	                    start_order++;
	                    
	                }
	                $.post("taxonomy.php", order,function(data){
	                                                $('#response').html(data);
							 
		    });
	                
	        }								  
	        });
	});

});

$(function(){
    $("input[name=data_to_show]").click(function(){
        document.alist.submit();
    });
    
    $("input[name=data_order]").click(function(){
        document.alist.submit();
    });
});

$(document).ready( function(){
        //reset to unchecked
        $('input[name=select_all]').removeAttr('checked');
        $('input[name=select[]]').each(function(){
            $('input[name=select[]]').removeAttr('checked');
        });
        
        $('input[name=select_all]').click(function(){
                var checked_status = this.checked;
                
                $('.select').each(function(){
                        this.checked = checked_status;
                        if(checked_status){ //checked all chekcbox if select all checked
                            $("input[name=edit]").removeClass("btn_edit_disable");
                            $("input[name=edit]").addClass("btn_edit_enable");
                            $("input[name=edit]").removeAttr('disabled');
                            
                            $("input[name=delete]").removeClass("btn_delete_disable");
                            $("input[name=delete]").addClass("btn_delete_enable");
                            $("input[name=delete]").removeAttr('disabled');
                            
                            $("input[name=publish]").removeClass("btn_publish_disable");
                            $("input[name=publish]").addClass("btn_publish_enable");
                            $("input[name=publish]").removeAttr('disabled');
                            
                            $("input[name=unpublish]").removeClass("btn_save_changes_disable");
                            $("input[name=unpublish]").addClass("btn_save_changes_enable");
                            $("input[name=unpublish]").removeAttr('disabled');
                        }else{
                            $("input[name=edit]").removeClass("btn_edit_enable");
                            $("input[name=edit]").addClass("btn_edit_disable");
                            $("input[name=edit]").attr('disabled', 'disabled');
                            
                            $("input[name=delete]").removeClass("btn_delete_enable");
                            $("input[name=delete]").addClass("btn_delete_disable");
                            $("input[name=delete]").attr('disabled', 'disabled');
                            
                            $("input[name=publish]").removeClass("btn_publish_enable");
                            $("input[name=publish]").addClass("btn_publish_disable");
                            $("input[name=publish]").attr('disabled', 'disabled');
                            
                            $("input[name=unpublish]").removeClass("btn_save_changes_enable");
                            $("input[name=unpublish]").addClass("btn_save_changes_disable");
                            $("input[name=unpublish]").attr('disabled', 'disabled');
                            
                        }
                });
        });
        
        $('.select').click(function(){
            //count how many checkbox are checked, if more then 0 than enable the edit and delete button
           if($('.select:checked').length > 0){
                $("input[name=edit]").removeClass("btn_edit_disable");
                $("input[name=edit]").addClass("btn_edit_enable");
                $("input[name=edit]").removeAttr('disabled');
                
                $("input[name=delete]").removeClass("btn_delete_disable");
                $("input[name=delete]").addClass("btn_delete_enable");
                $("input[name=delete]").removeAttr('disabled');
                
                $("input[name=publish]").removeClass("btn_publish_disable");
                $("input[name=publish]").addClass("btn_publish_enable");
                $("input[name=publish]").removeAttr('disabled');
                
                $("input[name=unpublish]").removeClass("btn_save_changes_disable");
                $("input[name=unpublish]").addClass("btn_save_changes_enable");
                $("input[name=unpublish]").removeAttr('disabled');
           }else{
                $("input[name=edit]").removeClass("btn_edit_enable");
                $("input[name=edit]").addClass("btn_edit_disable");
                $("input[name=edit]").attr('disabled', 'disabled');
                
                $("input[name=delete]").removeClass("btn_delete_enable");
                $("input[name=delete]").addClass("btn_delete_disable");
                $("input[name=delete]").attr('disabled', 'disabled');
                
                $("input[name=publish]").removeClass("btn_publish_enable");
                $("input[name=publish]").addClass("btn_publish_disable");
                $("input[name=publish]").attr('disabled', 'disabled');
                
                $("input[name=unpublish]").removeClass("btn_save_changes_enable");
                $("input[name=unpublish]").addClass("btn_save_changes_disable");
                $("input[name=unpublish]").attr('disabled', 'disabled');
           }
        });
});
