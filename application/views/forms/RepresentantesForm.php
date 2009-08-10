<?php
class RepresentantesForm extends Zend_Form
{
	
	function __construct($options = null)
	{
		parent::__construct($options);
		
		
		$this->addElement('hidden','id');
		$this->id->removeDecorator('DtDdWrapper');

		
		$this->addElement('text','representante')
		    ->representante
				->setLabel('Nome:')
				->setAttrib('maxlength','45')
				->setAttrib('size','50')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,45))
				->addValidator('NotEmpty',false,array(
					'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				);
	

			
		$this->addElement('text','nome')
		    ->nome
				->setLabel('Nome Empresa:')
				->setAttrib('maxlength','45')
				->setAttrib('size','63')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,45))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

				
		$this->addElement('text','endereco')
		    ->endereco
				->setLabel('Endereço:')
				->setAttrib('maxlength','80')
				->setAttrib('size','63')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,80))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Preencha o campo Endereço.'))
				    );

				
		//TODO: fazer filtro numerico		
		$this->addElement('text','numero')
		    ->numero
				->setLabel('Número:')
				->setAttrib('maxlength','10')
				->setAttrib('size','17')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,45))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );
		
				
		$this->addElement('text','complemento')
		    ->complemento
				->setLabel('Complemento:')
				->setAttrib('maxlength','45')
				->setAttrib('size','30')
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,45))
				->addValidator('NotEmpty',false,array(
    					'messages'=>array('isEmpty'=>'Campo obrigatório.'))
    				);
		
				
		$this->addElement('text','bairro');
		$this->bairro
				->setLabel('Bairro:')
				->setRequired(true)
				->setAttrib('maxlength','45')
				->setAttrib('size','17')
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,45))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
				;
				
		$this->addElement('select','cidadeID');		
		$this->cidadeID
				->setLabel('Cidade:')
				->setAttrib('style','min-width: 200px;')
				->setRegisterInArrayValidator(false)
				;
		
				
		$this->addElement('select','estadoID');
		$this->estadoID->setLabel('Estado;');
		
		$this->estadoID->addMultiOption("","");
		$this->_popularEstados();
		
				
		$this->addElement('text','cep');
		$this->cep
			->setLabel('Cep:')
			->setRequired(true)
			->setAttrib('maxlength','10')
			->setAttrib('size','10')
			->addFilter('StripTags')
			->addFilter('StringTrim')
            ->addFilter('StringToUpper')
			->addValidator('stringLength',false,array(0,10))
			->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
			;

		$this->addElement('text','cnpj');
		$this->cnpj
			->setLabel('CNPJ:')
			->setRequired(true)
			->setAttrib('maxlength','18')
			->setAttrib('size','18')
			->addFilter('StripTags')
			->addFilter('StringTrim')
            ->addFilter('StringToUpper')
			->addValidator('stringLength',false,array(0,18))
			->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
			;
		
		$this->addElement('text','telefone');
		$this->telefone
			->setLabel('Telefone:')
			->setRequired(true)
			->setAttrib('maxlength','20')
			->setAttrib('size','17')
			->addFilter('StripTags')
			->addFilter('StringTrim')
            ->addFilter('StringToUpper')
			->addValidator('stringLength',false,array(0,20))
			->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
			;
			
			
		$this->addElement('text','fax');
		$this->fax
			->setLabel('Fax:')
			->setAttrib('maxlength','20')
			->setAttrib('size','17')
			->addFilter('StripTags')
			->addFilter('StringTrim')
            ->addFilter('StringToUpper')
			->addValidator('stringLength',false,array(0,20))
			->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
			;
		
			
		$this->addElement('text','email');
		$this->email
			->setLabel('Email:')
			->setRequired(true)
			->setAttrib('maxlength','80')
			->setAttrib('size','43')
			->addFilter('StripTags')
			->addFilter('StringTrim')
            ->addFilter('StringToUpper')
			->addValidator('EmailAddress',false)
			
			->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
			;

		$this->addElement('checkbox','ativo');
		$this->ativo
			->setLabel('Ativo:')
			->setRequired(true)
			->setValue(true)
			->setAttrib('maxlength','80')
			->setAttrib('size','45')
			->addFilter('StripTags')
			->addFilter('StringTrim')
            ->addFilter('StringToUpper')
			->addValidator('stringLength',false,array(0,80))
			->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
			;
		
		
		$this->addElement('submit','submit');
		$this->submit
				->setValue('Opa')
				->setAttrib('class','button')
				->removeDecorator('Label')
				->removeDecorator('DtDdWrapper')
				;
				
		$location= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/transportadoras';
		$this->addElement('button','cancelar');
		$this->cancelar
				->setValue('Algo')
				->setAttrib('class','button2')
				->setAttrib('onclick',"javascript:window.location.href='".$location."'")
				->removeDecorator('DtDdWrapper')
				->removeDecorator('Label')
				;
	}
	
private function _popularEstados()
	{
		$estados=new Estados();
		foreach($estados->fetchAll() as $estado){
			$this->estadoID->addMultiOption($estado->id,$estado->estado);
		}
	}
	
	//pega o id da cidade teta popuar e setar a mesma
	public function setCidade($cidadeID=null)
	{
		//popula estados
		$this->_popularEstados();
		
		$id=(int)$cidadeID;
		$cidades=new Cidades();
		$cidade=$cidades->find($id)->current();
		$estadoID = $cidade->findParentRow('Estados')->id;
		
		//popular cidades
		$this->_popularCidades($estadoID);
	}
	
	private function _popularCidades($estadoID=null)
	{
		if((int)$estadoID>0){
			$cidades=new Cidades();
			$select=$cidades->select()->where('estadoID='.$estadoID);
			foreach($cidades->fetchAll($select) as $cidade){
				$this->cidadeID->addMultiOption($cidade->id,$cidade->cidade);
			}
			$this->estadoID->setValue($estadoID);
		}
	}
}
?>