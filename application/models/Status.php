<?php
class Status extends Zend_Db_Table
{
	protected $_name='statusPedido';


    public $selectBoxes = array(
            'varName'=>'status',
            'id'=>'id',
            'displayField' => 'status',
            //'where'=>'ativo = 1',
            'firstOption'=> "blank"
        );
}
?>