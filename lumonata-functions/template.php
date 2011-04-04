<?
	require_once(ROOT_PATH.'/lumonata-classes/template.inc');
	$t=new Template(TEMPLATE_PATH);
	
	/**
	 * Set the tamplate name and template location    
	 *   
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param string $file_name Template path location
	 * @param string $template_name Name of the template
	 * 
	 * @return void
	 *      
	 */
	function set_template($file_name,$template_name='template'){
		global $t;
		$t->set_file($template_name, $file_name);
	}
	
	/**
	 * Add block on selected template    
	 *   
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param string $block_name Name of the block, the name must be match with the block name in template file 
	 * @param string $block_alias The alias nam
	 * @param string $template_name name of the template. Must be the same name when you set the tempalte
	 *   
	 * @return void     
	 */
	function add_block($block_name,$block_alias,$template_name='template'){
		global $t;
		$t->set_block($template_name, $block_name, $block_alias);
	}
	
	/**
	 * This functions set the value of a variable. 
	 * It may be called with either a varname and a value as two strings or an
	 * an associative array with the key being the varname and the value being
	 * the new variable value.
	 *
	 * The function inserts the new value of the variable into the $varkeys and
	 * $varvals hashes. It is not necessary for a variable to exist in these hashes
	 * before calling this function.
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param string $name Name of variable 
	 * @param string $value Value that set to the name
	 * 
	 *   
	 * @return void     
	 */
	function add_variable($name,$value){
		global $t;
		$t->set_var($name, $value);
	}
	
	/**
	 * The function substitutes the values of all defined variables in the variable
     * named $block_name and stores or appends the result in the variable named $block_alias.
	 * 
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param string $block_name Template block name that will be parsed  
	 * @param string $block_alias Must be the same name when you set/add the block 
	 * @param boolean $loop If $loop set to TRUE it mean that the block are contain looping variable
	 *   
	 * @return void     
	 */
	
	function parse_template($block_name,$block_alias,$loop=false){
		global $t;
		$t->Parse($block_alias, $block_name, $loop); 
	}
	
	/**
	 * Print the mention $template_name after the template is parse
	 * 
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param string $template_name this variabale should match with the $template_name in set_template() function   
	 * 
	 *   
	 * @return void     
	 */
	function print_template($template_name='template'){
		global $t;
		$t->pparse('Output', $template_name);  
	}
	/**
	 * Return the mention $template_name after the template is parse
	 * 
	 *
	 * @author Wahya Biantara
	 * 
	 * @since alpha
	 * 
	 * @param string $template_name this variabale should match with the $template_name in set_template() function   
	 * 
	 *   
	 * @return void     
	 */
	function return_template($template_name='template'){
		global $t;
		return $t->rparse('Output', $template_name);  
	}
?>