<?php
class TipoEmbarque extends Zend_Db_Table_Abstract
{
	protected $_name = 'tipoEmbarque';

    public $selectBoxes =array(
            'varName'=>'tiposEmbarque',
            'id'=>'id',
            'displayField' => 'tipo',
            'orderBy'=>'tipo DESC',
        );
}
?>