<?php 
class CombCores extends Zend_Db_Table_Abstract
{
	protected $_name='combCores';
	
	protected $_referenceMap=array(
		'Item' => array(
			'columns'	=>	'combinacaoID',
			'refTableClass'	=> 'Combinacoes'
		),
		'Cor' => array(
			'columns'	=>	'corID',
			'refTableClass'	=> 'Cores'
		)
	);
}
?>