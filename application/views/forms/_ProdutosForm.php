<?php
class ProdutosForm extends Zend_Form
{
	function __construct($options = null)
	{
		parent::__construct($options);

        $this->setAttrib('id','formProdutos');
		
		$this->addElement('hidden','id');
		$this->id->removeDecorator('DtDdWrapper');

		
		$this->addElement('text','nome')
		    ->nome
				->setLabel('Nome:')
				->setRequired(true)
				->setAttrib('maxlength','60')
				->setAttrib('size','30')
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,60))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

				
		$this->addElement('text','referencia')
		    ->referencia
				->setLabel('Ref.:')
				->setRequired(true)
				->setAttrib('maxlength','6')
				->setAttrib('size','10')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addFilter('StringToUpper')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,20))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );
				    

		$this->addElement('text','referenciaCliente')
		    ->referenciaCliente
				->setLabel('Ref. Cliente:')
				//->setRequired(true)
				->setAttrib('maxlength','25')
				->setAttrib('size','10')
				->addFilter('StringToUpper')
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,20))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

				    
		/**
		 * Estação
		 */    
		$this->addElement('select','estacaoID')
    		->estacaoID
				->setRequired(true)
				->setLabel('Estação:');
    			//	->setRegisterInArrayValidator(false);

        $this->_popularEstacoes();
          
	    /**
	     * Divisoes
	     */
		$this->addElement('select','divisaoID')
    		->divisaoID
    		    ->setRequired(true)
    			->setLabel('Grupo:');
           
	    $this->_popularDivisoes();
	    

	    /**
	     * Tipos
	     */
		$this->addElement('select','tipoID')
    		->tipoID
    		    ->setRequired(true)
				->setLabel('Tipo:');
            
	    $this->_popularTipos();			
	    

	    /**
	     * Construcoes
	     */
		$this->addElement('select','construcaoID')
    		->construcaoID
    		    //->setRequired(true)
    				->setLabel('Construção:');
            
	    #$this->_popularConstrucoes();			
			
		

		
		/**
		 * submit buttons
		 */
		$this->addElement('submit','submit')
		    ->submit
				->setValue('Opa')
				->setAttrib('class','button')
				->removeDecorator('Label')
				->removeDecorator('DtDdWrapper');
		
		$location= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/produtos';
		$this->addElement('button','cancelar')
		    ->cancelar
				->setValue('Algo')
				->setAttrib('class','button2')
				->setAttrib('onclick',"javascript:window.location.href='".$location."'")
				->removeDecorator('DtDdWrapper')
				->removeDecorator('Label');
	}
	

	/**
	 * popula divisoes;
	 * @return mixed
	 */
    private function _popularDivisoes()
    {
        $divisoes=new Divisoes();
        $this->divisaoID->addMultiOption("","");
        $select=$divisoes->select()->order('divisao');
        foreach($divisoes->fetchAll($select) as $divisao){
            $this->divisaoID->addMultiOption($divisao->id,$divisao->divisao);
        }
	}
	
	/**
	 * Popula estacoes
	 * @return void
	 */
    private function _popularEstacoes()
    {
        $estacoes=new Estacoes();
        $this->estacaoID->addMultiOption("","");
        $select=$estacoes->select()->order('estacao');
        foreach($estacoes->fetchAll($select) as $estacao){
            $this->estacaoID->addMultiOption($estacao->id,$estacao->estacao);
        }
	}
	
	/**
	 * Popula tipos de Produto
	 * @return void
	 */
    private function _popularTipos()
    {
        $tipos=new TipoProduto();
        //$this->tipoID->addMultiOption("","");
        //$select=$tipos->select()->order('tipo');
        foreach($tipos->fetchAll() as $tipo){
            $this->tipoID->addMultiOption($tipo->id,$tipo->tipo);
        }
	}
}
?>