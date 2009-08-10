<?php
class TipoProduto extends Zend_Db_Table_Abstract
{
	protected $_name = 'tiposProduto';

	public $selectBoxes=array(
			'varName'=>'tiposProduto',
			'displayField' => 'tipo',
			'firstOption'=> "blank",
			'orderBy'=> "tipo ASC",
		);
}
?>