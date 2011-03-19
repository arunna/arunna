<?
	require_once(ROOT_PATH.'/lumonata-classes/template.inc');
	$t=new Template(TEMPLATE_PATH);
	
	function set_template($file_name,$template_name='template'){
		global $t;
		$t->set_file($template_name, $file_name);
	}
	
	function add_block($block_name,$block_alias,$template_name='template'){
		global $t;
		$t->set_block($template_name, $block_name, $block_alias);
	}
	
	function add_variable($name,$value){
		global $t;
		$t->set_var($name, $value);
	}
	
	function parse_template($block_name,$block_alias,$loop=false){
		global $t;
		$t->Parse($block_alias, $block_name, $loop); 
	}
	
	function print_template($template_name='template'){
		global $t;
		$t->pparse('Output', $template_name);  
	}
	
	function return_template($template_name='template'){
		global $t;
		return $t->rparse('Output', $template_name);  
	}
?>