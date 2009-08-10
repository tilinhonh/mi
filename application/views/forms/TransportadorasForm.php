<?php
class TransportadorasForm extends Zend_Form
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


		$this->addElement('text','contato')
		    ->contato
				->setLabel('Contato:')
				->setAttrib('class','required')
				->setAttrib('maxlength','45')
				->setAttrib('size','63');

			
		$this->addElement('text','nome');
		$this->nome
				->setLabel('Nome Oficial:')
				->setAttrib('class','required')
				->setAttrib('maxlength','45')
				->setAttrib('size','63');

				
		$this->addElement('text','endereco');
		$this->endereco
				->setLabel('Endereço:')
				->setAttrib('class','required')
				->setAttrib('maxlength','80')
				->setAttrib('size','63');

				
		//TODO: fazer filtro numerico		
		$this->addElement('text','numero');
		$this->numero
				->setLabel('Número:')
				->setAttrib('class','required')
				->setAttrib('maxlength','10')
				->setAttrib('size','17');
		
				
		$this->addElement('text','complemento');
		$this->complemento
				->setLabel('Complemento:')
				->setAttrib('maxlength','45')
				->setAttrib('size','30');
		
				
		$this->addElement('text','bairro');
		$this->bairro
				->setLabel('Bairro:')
				->setAttrib('class','required')
				->setAttrib('maxlength','45')
				->setAttrib('size','17');
				
		$this->addElement('select','cidadeID');		
		$this->cidadeID
				->setLabel('Cidade:')
				->setAttrib('class','required')
				->setAttrib('style','min-width: 200px;');
		
				
		$this->addElement('select','estadoID');
		$this->estadoID->setLabel('Estado;');
		
		$this->estadoID->addMultiOption("","");
		$this->popularEstados();
		
				
		$this->addElement('text','cep');
		$this->cep
			->setLabel('Cep:')
			->setAttrib('class','required')
			->setAttrib('maxlength','10')
			->setAttrib('size','10');

		$this->addElement('text','cnpj');
		$this->cnpj
			->setLabel('CNPJ:')
			->setAttrib('class','required cnpj')
			->setAttrib('maxlength','18')
			->setAttrib('size','18');
		
		$this->addElement('text','inscricaoEstadual');
		$this->inscricaoEstadual
			->setLabel('Inscrição Estadual:')
			->setAttrib('class','required')
			->setAttrib('maxlength','12')
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
			->setAttrib('class','required email')
			->setAttrib('maxlength','80')
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
	
	private function popularEstados()
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
		$this->popularEstados();
		
		$id=(int)$cidadeID;
		$cidades=new Cidades();
		$cidade=$cidades->find($id)->current();
		$estadoID = $cidade->findParentRow('Estados')->id;
		
		//popular cidades
		$this->popularCidades($estadoID);
	}
	
	private function popularCidades($estadoID=null){
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