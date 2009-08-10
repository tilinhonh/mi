<?php 
class ItemPedido extends My_Db_Table_Abstract
{
	protected $_name='itemPedido';
	
	protected $_referenceMap=array(
		'Pedido' => array(
			'columns'	=>	'pedidoID',
			'refTableClass'	=> 'Pedidos'
		),
        'Item' => array(
			'columns'	=>	'itemID',
			'refTableClass'	=> 'Combinacoes'
		),
	);

	 public $applyFilters = array('all' => array('StringTrim','StringToUpper','HtmlEntities'));

	 protected $_validate = array(
			'id'=>array(
				'NotEmpty'	=> array( 'options'=>array(),'message' =>'ID empty.'),
				'Int'	=> array('options'=>array(), 'message' =>'Id not int?'),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>'Id deve ter no máximo 10 caracteres.'
				)
			),
			'pedidoID'=>array(
				'NotEmpty'	=> array( 'options'=>array(),'message' =>'pedidoID empty.'),
				'Int'	=> array('options'=>array(), 'message' =>'Id not int?'),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>'Id deve ter no máximo 10 caracteres.'
				)
			),
			'itemID'=>array(
				'NotEmpty'	=> array( 'options'=>array(),'message' =>'itemID empty.'),
				'Int'	=> array('options'=>array(), 'message' =>'Id not int?'),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>'Id deve ter no máximo 10 caracteres.'
				)
			),
			'fabricaID'=>array(
				'NotEmpty'	=> array( 'options'=>array(),'message' =>'itemID empty.'),
				'Int'	=> array('options'=>array(), 'message' =>'Id not int?'),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>'Id deve ter no máximo 10 caracteres.'
				)
			),
			'cancelado'=>array(
				'Int'	=> array('options'=>array(), 'message' =>'Id not int?'),
				'StringLength' => array('options'=>array(0, 1),
					'message'=>'cancelado com valor invalido.'
				)
			),
			'precoFabrica'=>array(
				'Float'	=> array(
					'options'=>array(),
					'message' => 'Valor inválido pra preço Fábrica.'
				),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>'Preço deve ter no máximo 10 caracteres.'
				)
			),
			'precoCliente'=>array(
				'Float'	=> array(
					'options'=>array(),
					'message' =>'Valor inválido pra preço Cliente.'
				),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>'Preço deve ter no máximo 10 caracteres.'
				)
			),
	);
}
?>