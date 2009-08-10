<?php
class FabricasForm extends Zend_Form
{
	
	function __construct($options = null)
	{
		parent::__construct($options);
		
		
		$this->addElement('hidden','id');
		$this->id->removeDecorator('DtDdWrapper');

		
		$this->addElement('text','fantasia');
		$this->fantasia
				->setLabel('Nome (fantasia):')
				->setAttrib('maxlength','45')
				->setAttrib('class','required')
				->setAttrib('size','50');
	

			
		$this->addElement('text','nome');
		$this->nome
				->setLabel('Nome Oficial:')
				->setAttrib('maxlength','45')
				->setAttrib('class','required')
				->setAttrib('size','63');
				
		$this->addElement('text','contato')
		    ->contato
				->setLabel('Contato:')
				->setAttrib('maxlength','45')
				->setAttrib('size','63');

				
		$this->addElement('text','endereco');
		$this->endereco
				->setLabel('Endereço:')
				->setAttrib('maxlength','80')
				->setAttrib('class','required')
				->setAttrib('size','63');

				
		//TODO: fazer filtro numerico		
		$this->addElement('text','numero');
		$this->numero
				->setLabel('Número:')
				->setAttrib('maxlength','10')
				->setAttrib('class','required')
				->setAttrib('size','17');
		
				
		$this->addElement('text','complemento');
		$this->complemento
				->setLabel('Complemento:')
				->setAttrib('maxlength','45')
				->setAttrib('size','30');
		
				
		$this->addElement('text','bairro');
		$this->bairro
				->setLabel('Bairro:')
				->setRequired(true)
				->setAttrib('maxlength','45')
				->setAttrib('class','required')
				->setAttrib('size','17');
				
		$this->addElement('select','cidadeID');		
		$this->cidadeID
				->setLabel('Cidade:')
				->setAttrib('style','min-width: 200px;')
				->setAttrib('class','required')
				;
		
				
		$this->addElement('select','estadoID');
		$this->estadoID->setLabel('Estado:');
		
		$this->estadoID->addMultiOption("","");
		$this->_popularEstados();
		
				
		$this->addElement('text','cep');
		$this->cep
			->setLabel('Cep:')
			->setAttrib('maxlength','10')
			->setAttrib('class','required')
			->setAttrib('size','10');

		$this->addElement('text','cnpj');
		$this->cnpj
			->setLabel('CNPJ:')
			->setRequired(true)
			->setAttrib('maxlength','18')
			->setAttrib('class','required cnpj')
			->setAttrib('size','18');
		
		$this->addElement('text','inscricaoEstadual');
		$this->inscricaoEstadual
			->setLabel('Inscrição Estadual:')
			->setRequired(true)
			->setAttrib('maxlength','12')
			->setAttrib('class','required')
			->setAttrib('size','12');
		
		$this->addElement('text','telefone');
		$this->telefone
			->setLabel('Telefone:')
			->setAttrib('class','required')
			->setAttrib('maxlength','20')
			->setAttrib('size','17');
			
			
		$this->addElement('text','fax');
		$this->fax
			->setLabel('Fax:')
			->setAttrib('maxlength','20')
			->setAttrib('size','17');
		
			
		$this->addElement('text','email');
		$this->email
			->setLabel('Email:')
			->setAttrib('maxlength','80')
			->setAttrib('class','required email')
			->setAttrib('size','43');

		$this->addElement('checkbox','ativo');
		$this->ativo
			->setLabel('Ativo:')
			->setAttrib('maxlength','80')
			->setAttrib('size','45');
		
		
		$this->addElement('submit','submit');
		$this->submit
				->setValue('Opa')
				->setAttrib('class','button')
				->removeDecorator('Label')
				->removeDecorator('DtDdWrapper')
				;
				
		$location = '/fabricas';
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
		
		//_popular cidades
		$this->_popularCidades($estadoID);
	}
	
	private function _popularCidades($estadoID=null){
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