<?php
/**
 * MySQL table combinacoes
 * 
 * @return Zend_Db_Table_Abstract
 */
class GrupoMaterialCor extends My_Db_Table_Abstract
{
    /**
     * Nome da Tabela
     * @var string
     */
	protected $_name='grupo_material_cor';

    /**
     * Simula constraits
     * @var array
     */
	
	protected $_referenceMap=array(
		'Grupo' => array(
			'columns'	=>	'divisaoID',
			'refTableClass'	=> 'Divisoes'
		),
        'Cor' => array(
            'columns' => 'corID',
            'refTableClass' => 'Cores'
        ),
        'Material' => array(
            'columns' => 'materialID',
            'refTableClass' => 'Materiais'
        ),
        'Estacao' => array(
            'columns' => 'estacaoID',
            'refTableClass' => 'Estacoes'
        )
	);

	public $applyFilters = array(
					'all' => array('StringTrim','StringToUpper','HtmlEntities')
				);

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
			'estacaoID'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' =>"O campo 'Estação' deve serpreenchido."
				),
				'Int'	=> array(
					'options'=>array(),
					'message' =>'Inteiro.'
				),
			),
			'divisaoID'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' =>"O campo 'Grupo' deve serpreenchido."
				),
				'Int'	=> array(
					'options'=>array(),
					'message' =>'Cidade deve ser Inteiro.'
				),
			),
			'materialID'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' =>"O campo 'Material' deve serpreenchido."
				),
				'Int'	=> array(
					'options'=>array(),
					'message' =>'Inteiro.'
				),
			),
			'corID'=>array(
				'NotEmpty'	=> array(
					'options'=>array(),
					'message' =>"O campo 'Cor' deve serpreenchido."
				),
				'Int'	=> array(
					'options'=>array(),
					'message' =>'Cidade deve ser Inteiro.'
				),
			),
			'observacoes'=>array(
				'StringLength' => array(
					'options'=>array(0, 255),
					'message'=>"'Observacoes' até 255 carateres"
				),
			),
	);
}
?>