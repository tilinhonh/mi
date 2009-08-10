<?php 
class Produtos extends My_Db_Table_Abstract
{
	protected $_name='produtos';
	
	protected $_referenceMap = array(
		'Estacao' => array(
			'columns'	=>	'estacaoID',
			'refTableClass'	=> 'Estacoes'
		),
		'Divisao' => array(
			'columns'	=>	'divisaoID',
			'refTableClass'	=> 'Divisoes'
		),
		'Tipo' => array(
			'columns'	=>	'tipoID',
			'refTableClass'	=> 'TipoProduto'
		)
	);

	public $selectBoxes = array(
            'varName'=>'designers',
            'id'=>'id',
            'displayField' => 'nome',
            'firstOption'=> "blank",
            'where'=> "ativo=1"
     );


	 public $applyFilters = array('all' => array('StringTrim','StringToUpper','HtmlEntities'));


	 protected $_validate = array(
			'id'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' =>'Id empty?.'
				),
				'Int'	=> array(
					'options'=>array(),
					'message' =>'Id not int?'
				),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>'Id deve ter no máximo 10 caracteres.'
				)
			),
			'nome'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' => "O campo 'Nome' deve ser preenchido."
				),
				'StringLength' => array(
					'options'=>array(0, 60),
					'message'=>"'Nome' até 60 caracteres."
				),
				'Db_NoRecordExists' => array(
					'options'=>array('produtos', 'nome'),
					'message'=> "'Nome' já existe."
				)
			),
			'referencia' =>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' =>'Nome é um campo obrigatório.'
				),
				'Db_NoRecordExists'	=> array(
					'options'=>array('produtos','referencia'),
					'message' =>"'Referência' já existe."
				),
				'Regex' => array(
					'options'=>array('/[Ss](\d){5}/'),
					'message'=>"'Referência' inválida."
				),
			),
			'referenciaCliente'=>array(
				'StringLength' => array(
					'options'=>array(0, 25),
					'message'=>"'Referência Cliente' até 25 caracteres."
				),
			),
			'estacaoID'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' => "O campo 'Estação' deve ser preenchido."
				),
				'Int'	=> array(
					'options'=>array(),
					'message' => 'Estação deve ser Inteiro.'
				),
				/*
				'Db_RecordExists' => array(
					'options'=>array('estacoes', 'id'),
					'message'=> ''Estação' não existe.'
				)*/
			),
			'divisaoID'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' => "O campo 'Grupo' deve ser preenchido."
				),
				'Int'	=> array(
					'options'=>array(),
					'message' => 'Grupo deve ser Inteiro.'
				),
			),
			'tipoID'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' => "O campo 'Tipo' deve ser preenchido."
				),
				'Int'	=> array(
					'options'=>array(),
					'message' => 'Tipo deve ser Inteiro.'
				),
			),
			'construcaoID'=>array(
				'Int'	=> array(
					'options'=>array(),
					'message' => 'Construcao deve ser Inteiro.'
				),
			),
			'designer_id'=>array(
				'Int'	=> array(
					'options'=>array(),
					'message' => "'Designer' deve ser Inteiro."
				),
			),
	);


}
?>