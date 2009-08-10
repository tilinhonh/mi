<?php 
class Estacoes extends Zend_Db_Table 
{ 
    protected $_name = 'estacoes';

	public $selectBoxes = array(
            'varName'=>'estacoes',
            'id'=>'id',
            'displayField' => 'estacao',
			'orderBy'=>'id',
            'firstOption'=> "blank");
} 
?>