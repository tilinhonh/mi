<?php
	class Divisoes extends Zend_Db_Table_Abstract
	{
		protected $_name='divisoes';


		public $selectBoxes=array(
			'varName'=>'divisoes',
			'displayField' => 'divisao',
			'firstOption'=> "blank",
			'orderBy'=> "divisao ASC",
		);
	}
?>