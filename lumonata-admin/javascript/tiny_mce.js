$().ready(function() {
		$('.tinymce').tinymce({
			// Location of TinyMCE script
			script_url : 'javascript/tiny_mce/tiny_mce.js',

			// General options
			theme : "advanced",
			plugins : "pdw,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

			// Theme options
                        theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,blockquote,|,link,unlink,pagebreak,|,undo,redo,|,cut,copy,paste,pastetext,pasteword,help,pdw_toggle",
                        theme_advanced_buttons2 : "formatselect,forecolor,backcolor,|,search,replace,|,outdent,indent,cleanup,code,tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,charmap,image",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
                        
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
                        theme_advanced_resize_horizontal: false,
                        pdw_toggle_on : 1,
                        pdw_toggle_toolbars : "2",
                        convert_urls : false,
                       
                        
			// Example content CSS (should be your site CSS)
			content_css : "../lumonata-admin/textarea.css",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js",

			// Replace values for the template plugin
			template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}
		});
});

//configure that visual view is selected
$(function(){
        $('.visual_view_button').attr("disabled","disabled");       
        $('.visual_view_button').removeClass('visual_view_button').addClass('visual_view_button_selected');
        
});

$(function(){
	$('.visual_view_button_selected').click(function(){
                                var id = this.id.replace("visual_view_", "");
				$("#html_view_"+id).removeClass('html_view_button_selected').addClass('html_view_button');
                                $("#html_view_"+id).removeAttr("disabled");
                                $("#visual_view_"+id).attr("disabled","disabled");
                                
                                $("#visual_view_"+id).removeClass('visual_view_button').addClass('visual_view_button_selected');
				return false;
				});
	
	
});

$(function(){
	$('.html_view_button').click(function(){
                                var id = this.id.replace("html_view_", "");
				$("#visual_view_"+id).removeClass('visual_view_button_selected').addClass('visual_view_button');
                                $("#visual_view_"+id).removeAttr("disabled");
                                $("#html_view_"+id).attr("disabled","disabled");
                                $("#html_view_"+id).removeClass('html_view_button').addClass('html_view_button_selected');
				return false;
				});
	
	
});