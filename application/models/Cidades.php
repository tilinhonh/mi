<?php 
class Cidades extends Zend_Db_Table_Abstract
{
	protected $_name='cidades';
	protected $_primary='id';
	
	//protected $_dependentTables = array('estados');
	
	protected $_referenceMap=array(
		'Estado' => array(
			'columns'	=>	'estadoID',
			'refTableClass'	=> 'Estados'
		)
	);

	public $selectBoxes=array(
			'varName'=>'cidades',
			'displayField' => 'cidade',
			'firstOption'=> "blank",
			'orderBy'=> "cidade ASC"
		);
}
?>