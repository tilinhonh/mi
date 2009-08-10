<?php 
class MyTasks extends Zend_Db_Table_Abstract
{
	protected $_name='my_tasks';
	
	protected $_referenceMap=array(
		'Status' => array(
			'columns'	=>	'my_tasks_stat_id',
			'refTableClass'	=> 'MyTasksStatus'
		),
        'Priority' => array(
			'columns'	=>	'my_tasks_priority_id',
			'refTableClass'	=> 'MyTasksPriorities'
		)
	);
}
?>