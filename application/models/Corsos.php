<?php 
class Corsos extends My_Db_Table_Abstract
{

	protected $_name='corsos';
	
	protected $_referenceMap=array(
		'Estacao' => array(
			'columns'	=>	'estacaoID',
			'refTableClass'	=> 'Estacoes'
		)
	);

	public $applyFilters = array(
					'all' => array('StringTrim','StringToUpper','HtmlEntities')
				);

    public $selectBoxes=array(
        'varName'=>'corsos',
        'id'=>'id',
        'displayField' => 'corso',
        //'where'=>'ativo = 1',
        'firstOption'=> "blank",
        'orderBy'=> "corso ASC"
    );

	protected $_validate = array(
			'id'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' =>'Corso não pode ser deixado em branco.'
				),
				'Int'	=> array(
					'options'=>array(),
					'message' =>'Corso Deve ser inteiro!'
				)
			),
			'corso'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' =>'Corso não pode ser deixado em branco.'
				),
				'Db_NoRecordExists'	=> array(
					'options'=>array('corsos','corso'),
					'message' =>'Corso já existe.'
				)
			),
			'estacaoID'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' =>'Corso não pode ser deixado em branco.'
				),
				'Int'	=> array(
					'options'=>array(),
					'message' =>'Estação deve ser um número inteiro.'
				),
				
			)
	);
}
?>