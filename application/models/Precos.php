<?php 
class Precos extends My_Db_Table_Abstract
{
	protected $_name='precos';
	
	protected $_referenceMap=array(
		'fabrica' => array(
			'columns'	=>	'fabricaID',
			'refTableClass'	=> 'Fabricas'
		),
		'combinacao' => array(
			'columns'	=>	'combinacaoID',
			'refTableClass'	=> 'Combinacoes'
		)
	);
}
?>