<?php
/**
 * MySQL table combinacoes
 * 
 * @return Zend_Db_Table_Abstract
 */
class CombinacaoItem extends Zend_Db_Table_Abstract
{
    /**
     * Nome da Tabela
     * @var string
     */
	protected $_name='combinacao_item';

    /**
     * Simula constraits
     * @var array
     */
	
	protected $_referenceMap=array(
		'Combinacao' => array(
			'columns'	=>	'combinacao_id',
			'refTableClass'	=> 'Combinacoes'
		),
        'GMC' => array(
            'columns' => 'gmc_id',
            'refTableClass' => 'GrupoMaterialCor'
        )
	);
}
?>