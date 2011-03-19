$(document).ready(function(){
	
	$("select[name=birthday]").change(function(){
		var month=parseInt($("select[name=birthmonth]").val());
		var date=parseInt($("select[name=birthday]").val());
		var year=parseInt($("select[name=birthyear]").val());
		configure_date(date,month,year,false,0);
	});
	
	$("select[name=birthmonth]").change(function(){
		var month=parseInt($("select[name=birthmonth]").val());
		var date=parseInt($("select[name=birthday]").val());
		var year=parseInt($("select[name=birthyear]").val());
		configure_date(date,month,year,false,0);
		
	});
	
	$("select[name=birthyear]").change(function(){
		var month=parseInt($("select[name=birthmonth]").val());
		var date=parseInt($("select[name=birthday]").val());
		var year=parseInt($("select[name=birthyear]").val());
		configure_date(date,month,year,false,0);
	});
});

function configure_date(date,month,year,indexing,index){
	if(indexing==true){
		if($("select[name=birthyear["+index+"]]").val()!==""){
			if(year%2==0){
				var datelong=29;
			}else{
				var datelong=28;
			}
		}
	}else{
		if($("select[name=birthyear]").val()!==""){
			if(year%2==0){
				var datelong=29;
			}else{
				var datelong=28;
			}
		}
	}
	switch(parseInt(month)){
		case 1:
		case 3:
		case 5:
		case 7:
		case 8:
		case 10:
		case 12:
			var datelong=31;
			break;
		case 4:
		case 6:
		case 9:
		case 11:
			var datelong=30;
			break;
		case 2:
			var datelong=29;
			if(indexing==true){
				if($("select[name=birthyear["+index+"]]").val()!==""){
					if(year%2==0){
						var datelong=29;
					}else{
						var datelong=28;
					}
				}
			}else{
				if($("select[name=birthyear]").val()!=''){
					if(year%2==0){
						var datelong=29;
					}else{
						var datelong=28;
					}
				}
			}
			if(date>29)
				var datelong=29;
			
			break;
	}
	if(indexing==true){
		if(date>datelong || $("select[name=birthday["+index+"]]").val()==""){
			var newdate="<option value=''>Date:</option>";
			for(d=1;d<=datelong;d++){
				newdate+="<option value='"+ d +"'>"+ d +"</option>";
			}
			$("select[name=birthday["+index+"]]").html(newdate);
		}
	}else{
		if(date>datelong || $("select[name=birthday]").val()==""){
			var newdate="<option value=''>Date:</option>";
			for(d=1;d<=datelong;d++){
				newdate+="<option value='"+ d +"'>"+ d +"</option>";
			}
			$("select[name=birthday]").html(newdate);
		}
	}
}