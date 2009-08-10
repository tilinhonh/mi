<?php
class ClientesForm extends Zend_Form
{
	function __construct($action = 'Adicionar')
	{
		
		$this->addElement('hidden','id');
		$this->id->removeDecorator('DtDdWrapper');

		
		$this->addElement('text','nome_oficial');
		$this->nome_oficial
				->setLabel('Nome Oficial:')
				->setAttrib('maxlength','45')
				->setAttrib('size','50')
				->setAttrib('class','required');

		$this->addElement('text','nome');
		$this->nome
				->setLabel('Nome (fantasia):')
				->setAttrib('maxlength','45')
				->setAttrib('class','required')
				->setAttrib('size','50');
				
		$this->addElement('text','contato')
		    ->contato
				->setLabel('Contato:')
				->setAttrib('maxlength','45')
				->setAttrib('size','63')
				->setAttrib('class','required');
				
		// TODO: verificar tipos de modeda
		// Validar, cuidar escrita no banco
		
		$this->addElement('text','limite')
		    ->limite
				->setLabel('Limite:')
				->setAttrib('maxlength','12')
				->setAttrib('size','12')
				->setAttrib('class','required dinheiro');

				
				
		$this->addElement('textarea','notas')
		    ->notas
				->setLabel('Observações:')
				->setAttrib('rows',4)
				->setAttrib('cols',30);
				
		$this->addElement('select','divisaoID')
    		->divisaoID
    				->setLabel('Grupo:');
            
    	    $this->_popularDivisoes();			
			
		

				
		$this->addElement('text','endereco')
		    ->endereco
				->setLabel('Endereço:')
				->setAttrib('maxlength','80')
				->setAttrib('size','63')
				->setAttrib('class','required integer');

				
		$this->addElement('text','numero')
		    ->numero
				->setLabel('Número:')
				->setAttrib('maxlength','10')
				->setAttrib('size','17')
				->setAttrib('class','required');
		
				
		$this->addElement('text','complemento')
		    ->complemento
				->setLabel('Complemento:')
				->setAttrib('maxlength','45')
				->setAttrib('size','30');
		
				
		$this->addElement('text','bairro')
		    ->bairro
				->setLabel('Bairro:')
				->setAttrib('maxlength','45')
				->setAttrib('size','17')
				->setAttrib('class','required');
				
		$this->addElement('select','cidadeID')
		    ->cidadeID
				->setLabel('Cidade:')
				->setAttrib('style','min-width: 200px;')
				->setAttrib('class','required');
		
				
		$this->addElement('select','estadoID')
		    ->estadoID->
		        setLabel('Estado:');
		
		$this->estadoID->addMultiOption("","");
		
		$this->_popularEstados();
		
				
		$this->addElement('text','cep')
		    ->cep
			->setLabel('Cep:')
			->setAttrib('maxlength','10')
			->setAttrib('size','10')
			->setAttrib('class','required cep');

		$this->addElement('text','cpfCnpj')
		    ->cpfCnpj
    			->setLabel('CPF/CNPJ:')
    			->setAttrib('maxlength','18')
    			->setAttrib('size','18')
				->setAttrib('class','required CpfOuCnpj');
		
		$this->addElement('text','inscricaoEstadual')
		    ->inscricaoEstadual
    			->setLabel('Inscrição Estadual:')
    			->setAttrib('maxlength','12')
    			->setAttrib('size','12')
				->setAttrib('class','required');
		
		$this->addElement('text','telefone')
		    ->telefone
    			->setLabel('Telefone:')
    			->setAttrib('class','required')
    			->setAttrib('maxlength','20')
    			->setAttrib('size','17');
			
			
		$this->addElement('text','fax')
		    ->fax
    			->setLabel('Fax:')
    			->setAttrib('maxlength','20')
    			->setAttrib('size','17');
        			
			
		$this->addElement('text','email')
		    ->email
			->setLabel('Email:')
    			->setAttrib('maxlength','80')
    			->setAttrib('size','43')
				->setAttrib('class','required');

		$this->addElement('checkbox','ativo')
		    ->ativo
    			->setLabel('Ativo:')
    			->setAttrib('maxlength','80')
    			->setAttrib('size','45')
				->setAttrib('class','required');
		
		
		$this->addElement('submit','submit')
		    ->submit
				->setLabel($action)
				->setAttrib('class','button')
				->removeDecorator('Label')
				->removeDecorator('DtDdWrapper');
				
		$location = '/clientes';
		
		$this->addElement('button','cancelar')
		    ->cancelar
				->setLabel('Cancelar')
				->setAttrib('class','button2')
				->setAttrib('onclick',"javascript:window.location.href='".$location."'")
				->removeDecorator('DtDdWrapper')
				->removeDecorator('Label');
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
		
		$id = (int)$cidadeID;
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
	
	/*
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


	public function populate($data){
		parent::populate($data);
		$this->setCidade($data['cidadeID']);
	}
}
?>