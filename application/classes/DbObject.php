<?php 
class DbObject 
{
	public function __construct()
	{
		$db = Zend_Db::factory('PDO_MYSQL', array(
			'host'		=>'localhost',
			'username'	=>'mi_login',
			'password'	=>'ticoticonofuba',
			'dbname'	=>	'mi'		
				));
				
		$db->query("SET NAMES 'utf8'");
		

		

		return $db; 
		
	}
}
?>