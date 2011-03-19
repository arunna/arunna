// JavaScript Document

/*Slide Application Menu*/
$(function(){
	$('a#applications').click(function(){
	$('#applications_list').slideToggle(100);
	return false;
	});
	/*$('#app_list').css('display', 'none');*/
});

/*Slide Plugin Menu*/
$(function(){
	$('a#plugins').click(function(){
	$('#plugins_list').slideToggle(100);
	return false;
	});
	/*$('#plugin_list').css('display', 'none');*/
});




