<?php 
class Designers extends My_Db_Table_Abstract
{
	protected $_name='designers';
	
	protected $_referenceMap=array(
		'Cidade' => array(
			'columns'	=>	'cidadeID',
			'refTableClass'	=> 'Cidades'
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
			'cidadeID'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' => "O campo 'Cidade' deve ser preenchido."
				),
				'Int'	=> array(
					'options'=>array(),
					'message' => 'Cidade deve ser Inteiro.'
				),
				'Db_RecordExists' => array(
					'options'=>array('cidades', 'id'),
					'message'=> 'Cidade não existe.'
				)
			),
			'nome'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' =>'Nome é um campo obrigatório.'
				),
				'Db_NoRecordExists'	=> array(
					'options'=>array('designers','nome'),
					'message' =>"'Nome' já existe."
				),
				'StringLength' => array(
					'options'=>array(0, 45),
					'message'=>"'Fantasia' deve até 45 caracteres."
				),
			),
			'nome_empresa'=>array(
				'Db_NoRecordExists'	=> array(
					'options'=>array('designers','nome_empresa'),
					'message' =>'Nome da empresa já existe.'
				),
				'StringLength' => array(
					'options'=>array(0, 45),
					'message'=>"'Nome da Empresa' deve ter até 45 caracteres."
				),
			),
			'cnpj'=>array(
				'CNPJ'	=> array(
					'options'=>array(),
					'message' =>'CNPJ inválido.',
					'namespace' =>array('My_Validate')
				),
				'Db_NoRecordExists'	=> array(
					'options'=>array('designers','cnpj'),
					'message' =>'CNPJ foi cadastrado em um outro registro.'
				),
				'NotEmpty'	=> array(
					'message' =>'O campo CNPJ é obrigatório.'
				)
			),
			'endereco'=>array(
				'NotEmpty'	=> array( 'message' =>"O campo 'Endereço' é obrigatório."),
				'StringLength' => array(
					'options'=>array(0, 80),
					'message'=>"'Endereco' até 80 caracteres."
				),
			),
			'numero'=>array(
				'NotEmpty'	=> array( 'message' =>"O campo 'Numero' é obrigatório."),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>"'Numero' até 10 caracteres."
				),
			),
			'bairro'=>array(
				'NotEmpty'	=> array( 'message' =>"O campo 'Bairro' é obrigatório."),
				'StringLength' => array(
					'options'=>array(0, 45),
					'message'=>"'Bairro' até 45 caracteres."
				),
			),
			'telefone'=>array(
				'NotEmpty'	=> array( 'message' =>"O campo 'Telefone' é obrigatório."),
			),
			'cep'=>array(
				'NotEmpty'	=> array( 'message' =>"O campo 'Cep' é obrigatório."),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>"'Cep' até 10 caracteres."
				),
			),
			'complemento'=>array(
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>"'Complemento' até 10 caracteres."
				),
			),
			'email'=>array(
				'NotEmpty'	=> array( 'message' =>"O campo 'Inscrição estadual' é obrigatório."),
				'EmailAddress'	=> array( 'message' =>"Email inválido."),
				'StringLength' => array(
					'options'=>array(0, 80),
					'message'=>"'Email' até 80 caracteres."
				),
			),
	);

	 
}
?>