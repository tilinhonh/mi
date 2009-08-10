<?php
class Cores extends My_Db_Table_Abstract
{
	protected $_name='cores';

	protected $_validate=array(
			'cor' => array(
				'NotEmpty'	=> array(
					'message' =>'Digite nome para a cor.'
				),
				'Db_NoRecordExists'	=> array(
					'options'=>array('cores','cor'),
					'message' =>'Cor já existe.'
				)
			)
	);
	
	public $applyFilters = array(
					'all' => array('StringTrim','StringToUpper','HtmlEntities')
				);

	public $selectBoxes=array(
			'varName'=>'cores',
			'displayField' => 'cor',
			'firstOption'=> "blank",
			'orderBy'=> "cor ASC",
		);

}
?>