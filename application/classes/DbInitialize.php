<?php 
class DbInitialize 
{
	public function __construct()
	{
		$db = Zend_Db::factory('PDO_MYSQL', array(
			'host'		=>'localhost',
			'username'	=>'root',
			'password'	=>'porra753',
			'dbname'	=>	'mi_test',
		    'profiler' => true                    
				));
				
		$db->query("SET NAMES 'utf8'");
		
		Zend_Db_Table::setDefaultAdapter($db);
		

		/*
		
		$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
        $profiler->setEnabled(true);
        
        // Attach the profiler to your db adapter
        $db->setProfiler($profiler);

		*/
	}
}
?>