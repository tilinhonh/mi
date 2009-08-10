<?php
/**
 * MySQL table combinacoes
 * 
 * @return Zend_Db_Table_Abstract
 */
class Combinacoes extends Zend_Db_Table_Abstract
{
    /**
     * Nome da Tabela
     * @var string
     */
	protected $_name='combinacoes';

    /**
     * Simula constraits
     * @var array
     */
	
	protected $_referenceMap=array(
		'Produto' => array(
			'columns'	=>	'produtoID',
			'refTableClass'	=> 'Produtos'
		),
        'Ncm' => array(
            'columns' => 'ncmID',
            'refTableClass' => 'Ncms'
        )
	);
}
?>