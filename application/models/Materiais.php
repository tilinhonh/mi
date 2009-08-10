<?php
class Materiais extends My_Db_Table_Abstract
{
	protected $_name='materiais';

	public $applyFilters = array(
				'all' => array('StringTrim','StringToUpper','HtmlEntities')
			);

	protected $_validate=array(
			'material' => array(
				'NotEmpty'	=> array(
					'message' =>'Digite nome para a material.'
				),
				'Db_NoRecordExists'	=> array(
					'options'=>array('materiais','material'),
					'message' =>'Material já existe.'
				)
			)
	);

	public $selectBoxes=array(
			'varName'=>'materiais',
			'displayField' => 'material',
			'firstOption'=> "blank",
			'orderBy'=> "material ASC",
		);
}
?>