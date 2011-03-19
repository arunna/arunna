//Tabs
/*
$(document).ready(function() {
	//When page loads...
	$(".tabcontent").hide(); //Hide all content
        $(".catcontent").hide(); //Hide all content
	$("ul.cat_tab li:first").addClass("active").show(); //Activate first category tab
        $("ul.tag_tab li:first").addClass("active").show(); //Activate first tag tab
        $(".catcontent:first").show(); //Show first category tab content
	$(".tabcontent:first").show(); //Show first tag tab content
	
	//On Click Category Tab Event
	$("ul.cat_tab li").click(function() {

		$("ul.cat_tab li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".catcontent").hide(); //Hide all tab content

		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
        
        $("ul.tag_tab li").click(function() {

		$("ul.tag_tab li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tabcontent").hide(); //Hide all tab content

		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});

});
*/
