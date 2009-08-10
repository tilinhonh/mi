<?php
class ClientesController extends Zend_Controller_Action
{
    
    function init()
    {
		$this->clientes = new Clientes();
		$this->view->title = "Cliente";
    }

	function indexAction()
	{
		$this->_makeSelects();
		if ($this->getRequest()->isPost()){
			$db = $this->clientes->getAdapter();
			$data = $this->getRequest()->getPost();
			$this->view->form = $data;
			if ($data['grupo'])
				$where[] = $db->quoteInto('divisaoID = ?', $data['grupo']);
			if ($data['estado'])
				$where[] = $db->quoteInto('estadoID = ?', $data['estado']);
			if ($data['text'])
				$where[] = $db->quoteInto('nome like ?', "%" . $data['text'] . "%");

			$where = count($where) < 1 ? null : implode(' AND ', $where);
			$result = $this->clientes->fetchAll($where, 'nome');

		}else{
			 $result = $this->clientes->fetchAll(null, 'nome');
		}
		$this->view->cliente = $result;
	}

	
    private function _makeSelects(){
        $selects = array(
                'Divisoes',
				'Estados'
        );

        $this->view->select = SelectBoxes::makeSelects($selects);
    }
	
	function delAction()
	{
		if ($this->_request->isPost()){
			$id = $this->_request->getParam('id');
			$del = $this->_request->getPost('del');
			if ($del == 'Sim' && $id > 0 ){
				try{
					$this->clientes->find($id)->current()->delete();
					$this->view->flash('Cliente excluido com sucesso.');
				}catch(Exception $e){
					$msg = 'Não foi possível excluir cliente. Verifique se não há vinculos com o mesmo.';
					$this->view->flashError($msg);
				}
			}
			$this->_redirect('/clientes');
		}else{
			$id = (int) $this->_request->getParam('id');
			if ($cliente = $this->clientes->find($id)->current()){
				$this->view->cliente = $cliente;
			}else{
				$this->view->error('Cliente não encontrado.');
			}
		}
	}
	
	
	function editAction()
	{
		$this->_getScripts();
		$form = new ClientesForm('Alterar');
		$this->view->form =  $form;
		
		if ($this->_request->isPost()){
			$data = $this->_request->getPost();
			$id = $this->_request->getPost('id');
			if ($this->clientes->skipDbUniqueValidation($id)->isValid($data)){
				if ($row = $this->clientes->find($id)->current()){
					$this->_saveRegister($row, $data);
				}
				else {
					$this->view->errors('Cliente nao encontrado.');
				}
			}//not valid
			else{
				$this->view->errors($this->clientes->getValidationMessages());
				$form->populate($data);
			}
		}//not post
		else{
			$id = (int) $this->_request->getParam('id',0);
				if( $cliente = $this->clientes->find($id)->current() ){
					$form->populate($cliente->toArray());
					$this->view->pendente = $this->clientes->getTotalEmPedidos($id);
					$this->view->disponivel = $cliente->limite - $this->view->pendente;
				}
				else {
					$this->view->error('Cliente nao encontrado.');
				}
		}
		
		$this->getSaldo();
	}
	
	
	function newAction()
	{
		$this->_getScripts();
		$form = new ClientesForm();
		$this->view->form = $form;
		if ($this->_request->isPost()){
			$data = $this->_request->getPost();
			if ($this->clientes->disableValidationRulesForField('id')->isValid($data)){
				if($row = $this->clientes->createRow()){
					$this->_saveRegister($row,$data);
				}else{
					$this->view->errors('Cliente não encontrado.');
				}
			}
			else{
				$this->view->errors($this->clientes->getValidationMessages());
				$form->populate($data);
			}
		}
	}
	
	
	private function _saveRegister($row,$data)
	{
		try{
			$filter = new My_Model_Filter('Clientes',$data);
			$row->nome_oficial		= $filter->filter('nome_oficial');
			$row->contato			= $filter->filter('contato');
			$row->nome				= $filter->filter('nome');
			$row->endereco			= $filter->filter('endereco');
			$row->numero			= $filter->filter('numero');
			$row->complemento		= $filter->filter('complemento');
			$row->bairro			= $filter->filter('bairro');
			$row->cidadeID			= $filter->filter('cidadeID');
			$row->cep				= $filter->filter('cep');
			$row->cpfCnpj			= $filter->filter('cpfCnpj');
			$row->inscricaoEstadual	= $filter->filter('inscricaoEstadual');
			$row->telefone			= $filter->filter('telefone');
			$row->cpfCnpj			= $filter->filter('cpfCnpj');
			$row->fax				= $filter->filter('fax');
			$row->email				= $filter->filter('email');
			$row->ativo				= $filter->filter('ativo');
			$row->divisaoID			= $filter->filter('divisaoID');
			$row->notas				= $filter->filter('notas');
			$row->limite			= Dinheiro::toDbFormat($data['limite']);

			if ('new' == $this->_request->action){
				$row->inclusao	= Zend_Date::now()->toString('YYYY-MM-dd');
			}

			$row->save();
			new QueryLogger();
			$this->view->flash('Cliente salvo com sucesso.');
			$this->_redirect('/clientes');
			
		}catch(Exception $e){
			$this->view->errors('Cliente não pode ser salvo.')->addMessage($e->getMessage());
			$this->view->form->populate($data);
		}
	}
	
	private function _getScripts()
	{
		$this->view->script = array();
		$this->view->script[]="jquery.js";
		$this->view->script[]="wait.js";
		$this->view->script[]="Clientes.js";
		$this->view->script[]="jquery.selectboxes.min.js";
		$this->view->script[]="jquery.validate.pt_BR.js";
		$this->view->script[]="jquery.validate.CPFOuCNPJ.js";
		$this->view->script[]="CNPJ.class.js";
		$this->view->script[]="CPF.class.js";
		$this->view->script[]="CPFOuCNPJ.class.js";
		$this->view->script[]="format.dinheiro.js";
		$this->view->script[]="jquery.validate.dinheiro.js";
	}
	
	
	public function getSaldo()
	{
        $saldo=5158;
		
		$this->view->saldo=$saldo;
		if ($saldo > 0){
		    $cssStyle='positivo';
		}
		else{
		    $cssStyle='negativo';
		}
		$this->view->cssStyle=$cssStyle;    
	}
	
}
?>