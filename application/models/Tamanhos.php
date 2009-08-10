<?php
class Tamanhos extends AbstractModelValidator
{
	protected $_name='tamanhos';

	public $selectBoxes = array(
            'varName'=>'tamanhos',
            'displayField' => 'tamanho',
            'firstOption'=> "blank"
     );
}
?>