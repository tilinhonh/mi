<?php 
class ItemTamanhoQuantidade extends My_Db_Table_Abstract
{
	protected $_name='item_tamanho_quantidade';
	
	protected $_referenceMap=array(
		'ItemPedido' => array(
			'columns'	=>	'item_pedido_id',
			'refTableClass'	=> 'ItemPedido'
		),
        'Tamanho' => array(
			'columns'	=>	'tamanho_id',
			'refTableClass'	=> 'Tamanhos'
		),
	);

   
	public $applyFilters = array('all' => array('StringTrim','StringToUpper','HtmlEntities'));

	protected $_validate = array(
			'id'=>array(
				'Int'	=> array('options'=>array(), 'message' =>'Id not int?'),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>'Id deve ter no máximo 10 caracteres.'
				)
			),
			'tamanho_id'=>array(
				'NotEmpty'	=> array( 'options'=>array(),'message' =>'Tamanho empty.'),
				'Int'	=> array('options'=>array(), 'message' =>'int'),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>'Id deve ter no máximo 10 caracteres.'
				)
			),
			'item_pedido_id'=>array(
				'NotEmpty'	=> array( 'options'=>array(),'message' =>'Item_pedidoID Empty'),
				'Int'	=> array('options'=>array(), 'message' =>'int'),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>'Id deve ter no máximo 10 caracteres.'
				)
			),
			'quantidade'=>array(
				'NotEmpty'	=> array( 'options'=>array(),'message' =>'Quantidade vazia'),
				'Int'	=> array('options'=>array(), 'message' =>'Quantidade deve ser um número inteiro'),
				'GreaterThan'	=> array('options'=>array(0), 'message' =>'Quantidade deve ser maior que zero.'),
				'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>'Quantidade no máximo 10 caracteres.'
				)
			)
	);
}