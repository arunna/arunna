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
    
    
});
