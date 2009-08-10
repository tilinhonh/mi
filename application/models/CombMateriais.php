<?php 
class CombMateriais extends Zend_Db_Table_Abstract
{
	protected $_name='combMateriais';
	
	protected $_referenceMap=array(
		'Item' => array(
			'columns'	=>	'combinacaoID',
			'refTableClass'	=> 'Combinacoes'
		),
		'Material' => array(
			'columns'	=>	'materialID',
			'refTableClass'	=> 'Materiais'
		)
	);
}
?>