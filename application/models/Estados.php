<?php
	class Estados extends Zend_Db_Table_Abstract
	{
		protected $_name='estados';
		#protected $_dependentTables=array('Cidades');

		public $selectBoxes=array(
			'varName'=>'estados',
			'displayField' => 'estado',
			'firstOption'=> "blank",
			'orderBy'=> "estado ASC"
		);
	}
?>