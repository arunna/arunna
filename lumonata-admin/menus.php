<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta name="generator" content="HTML Tidy, see www.w3.org">
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

		<script type="text/javascript" src="javascript/jquery-1.1.4.js"></script>
		<script type="text/javascript" src="javascript/interface-1.2.js"></script>
		<script type="text/javascript" src="javascript/inestedsortable.js"></script>
		
		<style type="text/css">
			body{
	
				margin:0;
				padding:0;
				font-family:"Lucida Sans",Arial, Helvetica, sans-serif;
				color:#333333;
				font-size:12px;
				font-weight:normal;
			}
			div.wrap {
				border:1px solid #BBBBBB;
				padding: 1em 1em 1em 1em;
			}
			
			.page-list {
				list-style: none;
				margin: 0;
				padding: 0;
				display: block;
			}
			
			.clear-element {
				clear: both;
			}
			
			.page-item1 > div,
			.page-item2 > div,
			.page-item3 > div,
			.page-item4 > div {
				background: #f8f8f8;
				margin: 0.25em 0 0 0;
			}

			.left {
				text-align: left;
			}
			
			.right {
				text-align: right;
			}

			.sort-handle {
				cursor:move;
			}
			
			.helper {
				border:2px dashed #777777;
			}
			
			.current-nesting {
				background-color: yellow;
			}
			
			.bold {
				color: red;
				font-weight: bold;
			}
			
			.apps_item{
				width:300px;
				height: auto;
				border:1px solid #bbb;
				background: #fff;
				margin:3px 0;
				cursor:move;
			}
			.apps_item .apps_item_text{
				width:86%;
				height:auto;
				background: #ccc;
				float:left;
				padding: 5px 5px;
			}
			.apps_item .view{
				background:#fff url(includes/media/ico-arrow.png) center no-repeat;
				width:10%;
				height:30px;
				float: left;
				cursor: pointer;
			}
			.medium_textbox{
				font-family:"Lucida Sans",Arial, Helvetica, sans-serif;
				width:40%;	
				height:20px !important;
				height:30px;/*IE6 Hack*/
				min-width:200px;
				font-size:14px;
				border:1px solid #bbbbbb;
				padding:5px !important;
				padding:0;/*IE6 Hack*/
				background:#fff;
				-moz-border-radius:5px;
				-webkit-border-radius:5px;
				
			}
			select{
				width:auto;
				font-size:12px;
			    height: 30px;
				margin:5px 0;
			}
			.button{
				height:25px;
				border:1px solid #bbbbbb;
				cursor:pointer;
				margin:1px;
				font-size:11px;
				font-size: bold;
				background:url(../images/panel_bg.jpg) repeat-x;
			}
			
			.apps_details_item{
				width:94% !important;
				width:93%;
				min-width:200px !important;
				min-width:150px;
				height:auto;
				background: #fff;
				border: 1px dashed #bbb;
				margin: 0;
				padding: 5px;
				
			}
			.menuset_details_item{
				width:290px !important;
				width:290px;
				min-width:200px !important;
				min-width:150px;
				height:auto;
				background: #fff;
				border: 1px dashed #bbb;
				margin: 0;
				padding: 5px;
				
			}
			.apps_details_item label{
				
				font-weight: bold;
				
			}
			
		</style>
    <title>Menus</title>
    </head>

    <body>
		<?php 
			
			require_once('../lumonata_config.php');
			require_once('../lumonata_settings.php');
			require_once('../lumonata-functions/settings.php');
			require_once('../lumonata-classes/actions.php');
			require_once('../lumonata-functions/menus.php');
			require_once('../lumonata-functions/user.php');
		?>
		<?php if(is_user_logged()){ ?>
		<div id="procces_alert" style="border:1px solid #ccc;padding:10px;display:none;font-weight:bold;color:red;margin:5px 0;">Saving...</div>
		<input type="hidden" value="<?php echo $_GET['active_tab']; ?>" id="activetab" />
        <div class="wrap">
            <?php 
				$menu_items=json_decode(get_meta_data('menu_items_'.$_GET['active_tab'],'menus'),true);	
				$menu_order=json_decode(get_meta_data('menu_order_'.$_GET['active_tab'],'menus'),true);
				echo "<ul id=\"theorder\" class=\"page-list\">";
				echo get_menu_items($menu_items,$menu_order,'theorder');
				echo "</ul>";
				echo looping_js_nav($menu_items);
			?>
        </div>
		<br/>
		
		
		
<script type="text/javascript">
jQuery( function($) {

$('#theorder').NestedSortable(
	{
		accept: 'page-item1',
		noNestingClass: "no-nesting",
		currentNestingClass: 'current-nesting',
		opacity: 0.8,
		helperclass: 'helper',
		onChange: function(serialized) {
		//alert('../lumonata-functions/menus.php?'+serialized[0].hash);
		$.ajax( {type: 'POST',
				 url: '../lumonata-functions/menus.php',
				 data: 'active_tab=<?php echo $_GET['active_tab'];?>&'+serialized[0].hash,
				 //success:function(data){ $('#left-to-right-ser').html(data); }
				}
			  );
		},
		autoScroll: true,
		handle: '.sort-handle'
	}
);


	
});
</script>
	<?php }?>
    </body>
</html>

