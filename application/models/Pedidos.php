<?php 
class Pedidos extends NewAbstractModelValidator
{
	protected $_name='pedidos';

	protected $_dependentTables = array('ItemPedido');
	
	protected $_referenceMap=array(
		'Corso' => array(
			'columns'	=>	'corsoID',
			'refTableClass'	=> 'Corsos'
		),
        'Cliente' => array(
			'columns'	=>	'clienteID',
			'refTableClass'	=> 'Clientes'
		),
        'Transportadora' => array(
			'columns'	=>	'transportadoraID',
			'refTableClass'	=> 'Transportadoras'
		),
        'Status' => array(
			'columns'	=>	'statusID',
			'refTableClass'	=> 'Status'
		),
        'Representante' => array(
			'columns'	=>	'representanteID',
			'refTableClass'	=> 'Representante'
		),
        'TipoEmbarque' => array(
			'columns'	=>	'tipoEmbarqueID',
			'refTableClass'	=> 'TipoEmbarque'
		)
	);

	public $applyFilters = array('all' => array('StringTrim','StringToUpper','HtmlEntities'));

	protected $_validate = array(
			'pedidoCliente' => array(
				'Db_NoRecordExists'	=> array(
					'options'=>array('pedidos','pedidoCliente'),
					'message' =>'Pedido Cliente já existe'
				),
			)
	);


    public function  init()
    {
		
		//mandatory fields
        $_mandatoryFields = array(
            'corsoID' =>'Corso',
            'statusID' =>'Status',
            'clienteID' =>'Cliente',
            'transportadoraID' =>'Transportadora',
            'tipoEmbarqueID' =>'Tipo de Embarque',
            //'representanteID' =>'Representante',
            //'pedidoCliente' =>'Pedido Cliente',
            'dataCliente' =>'Data do Cliente',
            'dataFabrica' =>'Data de Saída de Fábrica',
            //'dataFabricaReprogramada' =>'Data de Saída de Fábrica Reprogramada',
        );

        foreach($_mandatoryFields as $field => $alias){
			if (!is_array($this->_validate[$field]))
				 $this->_validate[$field] = array();
				 
            $this->_validate[$field] =
				array('NotEmpty'=>array(
					'message'=>"Campo '$alias' é obrigatório."
					));
		}

		//integer values
		$_integers = array('id','corsoID', 'statusID','fabricaID','clienteID','transportadoraID',
            'tipoEmbarqueID','representanteID');

		foreach($_integers as $field){
			if (!is_array($this->_validate[$field]))
				 $this->_validate[$field] = array();

            $this->_validate[$field]['Int'] = array('message'=>"Inteiros apenas.");
		}
		 
    }

}
?>