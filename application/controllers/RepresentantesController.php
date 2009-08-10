<?php
class RepresentantesController extends Zend_Controller_Action
{

	function init()
	{
		$this->representantes = new Representantes();
		$this->view->title = "Representantes";
	}
	
	function indexAction()
	{
		$this->view->title = "Representantes";
		$this->view->representantes = $this->representantes->fetchAll(null, 'nome');
	}
	
	function delAction()
	{
		try{
			if ($this->_request->isPost()){
				$id = $this->_request->getPost('id');
				$del = $this->_request->getPost('del');
				if ($del == 'Sim' && $id > 0 ){
					$this->representantes->find($id)->current()->delete();
					$this->view->flash('Representante deletado com sucesso.');
				}
				$this->_redirect('/representantes');
			}else{
				$id = (int) $this->_request->getParam('id');
				$this->view->representante = $this->representantes->find($id)->current();
			}
		}catch(Exception $e){
			
		}
	}
	
	
	function editAction()
	{
		$this->_makeSelects();
		$this->_loadScripts();
		if ($this->_request->isPost()){
			$data = $this->_request->getPost();
			if ($this->representantes->skipDbUniqueValidation($data['id'])->isValid($data)){
				if( $row = $this->representantes->find($data['id'])->current() )
					$this->_saveRegister($row, $data);
				else
					$this->view->errors('Registro não encontrado.');
			}
			else{
				$this->_populateForm($data);
				$this->view->errors( $this->representantes->getValidationMessages() );
			}
		}//not post
		else{
			$id = (int) $this->_request->getParam('id',0);
			if ($id > 0){
				$representante = $this->representantes->find($id)->current();
				$data = $representante->toArray();
				$data['estadoID'] = $representante->findParentRow('Cidades')->estadoID;
				$this->_populateForm($data);
			}
		}
	}
	
	
	function newAction(){
		$this->_loadScripts();
		$this->_makeSelects();
		if ($this->_request->isPost()){
			$data = $this->_request->getPost();
			if ($this->representantes->disableValidationRulesForField('id')->isValid($data)){
				$row = $this->representantes->createRow();
				$this->_saveRegister($row, $data);
			}
			else{
				$this->_populateForm($data);
				$this->view->errors( $this->representantes->getValidationMessages() );
			}
		}
	}


	private function _populateForm($data)
	{
		$this->_populateCidade($data['estadoID']);
		$this->view->form = $data;
	}



	private function _populateCidade($estado = null)
	{
		if (null !== $estado){
			$_cidades = new Cidades();
			$where = $_cidades->getAdapter()->quoteInto('estadoID = ?', $estado );
			$cidades = array();
			foreach($_cidades->fetchAll($where) as $cidade){
				$cidades[$cidade->id] = $cidade->cidade;
			}
			$this->view->select->cidades =  $cidades;
		}
	}

	private function _makeSelects($data)
	{
		$this->view->select = SelectBoxes::makeSelects(array('Estados'));
	}
	
	
	private function _saveRegister($row, $data)
	{
		try{

			$filter = new My_Model_Filter('Representantes', $data);
			$row->nome    	= $filter->filter('nome');
			$row->nome_empresa  = $filter->filter('nome_empresa');
			$row->endereco	= $filter->filter('endereco');
			$row->numero	= $filter->filter('numero');
			$row->bairro	= $filter->filter('bairro');
			$row->cidadeID	= $filter->filter('cidadeID');
			$row->cep    	= $filter->filter('cep');
			$row->cnpj	    = $filter->filter('cnpj');
			$row->telefone	= $filter->filter('telefone');
			$row->cnpj	    = $filter->filter('cnpj');
			$row->fax	    = $filter->filter('fax');
			$row->email	    = $filter->filter('email');
			$row->ativo	    = $data['ativo'] > 0 ? 1 : 0;
			$row->complemento	= $filter->filter('complemento');
			
			/* PERMISSOES */
			$row->comissao	= Dinheiro::toDbFormat($data['comissao']);


			if ('new' == $this->_request->action){
				$row->inclusao	= Zend_Date::now()->toString('YYYY-MM-dd');
			}

			$row->save();
			new QueryLogger();
			$this->view->flash('Representante salvo com sucesso.');
			$this->_redirect('/representantes');

		}catch(Exception $e){
			$this->view->errors('Representante não pode ser salvo.')->addMessage($e->getMessage());
			$this->_populateForm($data);
		}
	}
	
	function _loadScripts()
	{
		$this->view->script=array();
		$this->view->script[]="jquery.js";
		$this->view->script[]="wait.js";
		$this->view->script[]="Representantes.js";
		$this->view->script[]="jquery.selectboxes.min.js";
		$this->view->script[]="jquery.validate.pt_BR.js";
		$this->view->script[]="jquery.validate.cnpj.js";
		$this->view->script[]="CNPJ.class.js";
		$this->view->script[]="format.dinheiro.js";
		$this->view->script[]="jquery.validate.dinheiro.js";
	}
	
}
?>