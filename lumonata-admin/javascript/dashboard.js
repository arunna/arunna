//Drag And Drop Dashboard
/*
 * Deactiveated, since we want to make it static. Could be activate letter on
 */
/*
$(document).ready(function(){ 	
	$(function() {
		$("#dashboard_left").sortable({ axis:'y',opacity: 0.8, cursor: 'move', update: function() {
			var order = $(this).sortable("serialize") + '&update=left'; 
			$.post("dashboard.php", order, function(theResponse){
				$("#response").html(theResponse);
				$("#response").slideDown('slow')
			}); 															 
		}								  
		});
	});

});	

$(document).ready(function(){ 	
	$(function() {
		$("#dashboard_right").sortable({ axis:'y',opacity: 0.8, cursor: 'move', update: function() {
			var order = $(this).sortable("serialize") + '&update=right'; 
			$.post("dashboard.php", order); 															 
		}								  
		});
	});

});
*/