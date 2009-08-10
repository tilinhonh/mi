<?php 
class Fabricas extends My_Db_Table_Abstract
{
	protected $_name='fabricas';
	
	protected $_referenceMap=array(
		'Cidade' => array(
			'columns'	=>	'cidadeID',
			'refTableClass'	=> 'Cidades'
		)
	);

	public $applyFilters = array(
					'all' => array('StringTrim','StringToUpper','HtmlEntities')
				);

    public $selectBoxes = array(
            'varName'=>'fabricas',
            'id'=>'id',
            'displayField' => 'fantasia',
            'where'=>'ativo = 1',
            'firstOption'=> "blank");

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
					'message' =>'Cidade não pode estar vazia.'
				),
				'Int'	=> array(
					'options'=>array(),
					'message' =>'Cidade deve ser Inteiro.'
				),
				'Db_RecordExists' => array(
					'options'=>array('cidades', 'id'),
					'message'=>'Cidade não existe.'
				)
			),
			'fantasia'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' =>'Fantasia é campo obrigatório.'
				),
				'Db_NoRecordExists'	=> array(
					'options'=>array('fabricas','fantasia'),
					'message' =>'Fantasia já existe.'
				),
				'StringLength' => array(
					'options'=>array(0, 45),
					'message'=>"'Fantasia' deve até 45 caracteres."
				),
			),
			'nome'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' =>'Cidade não pode estar vazia.'
				),
				'Db_NoRecordExists'	=> array(
					'options'=>array('fabricas','nome'),
					'message' =>'Nome já existe.'
				),
				'StringLength' => array(
					'options'=>array(0, 45),
					'message'=>"'Nome' deve ter até 45 caracteres."
				),
			),
			'cnpj'=>array(
				'CNPJ'	=> array(
					'options'=>array(),
					'message' =>'CNPJ inválido.',
					'namespace' =>array('My_Validate')
				),
				'Db_NoRecordExists'	=> array(
					'options'=>array('fabricas','cnpj'),
					'message' =>'CNPJ já para outra fábrica.'
				),
				'NotEmpty'	=> array(
					'message' =>'O campo CNPJ é obrigatório.'
				)
			),
			'contato'=>array(
				'NotEmpty'	=> array( 'message' =>"O campo 'Contato' é obrigatório."),
				'StringLength' => array(
					'options'=>array(0, 45),
					'message'=>"'Contato' até 45 caracteres."
				),
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
			'inscricaoEstadual'=>array(
				'NotEmpty'	=> array( 'message' =>"O campo 'Inscrição estadual' é obrigatório."),
				'StringLength' => array(
					'options'=>array(0, 18),
					'message'=>"'Cnpj' até 18 caracteres."
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