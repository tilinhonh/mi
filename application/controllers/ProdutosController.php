<?php
class ProdutosController extends Zend_Controller_Action
{
    
    function init()
    {
        $this->view->title="Produtos";
        $this->produtos = new Produtos();
    }

	function indexAction()
	{
		$this->view->produtos = $this->produtos->fetchAll(null, 'nome');
	}
	
	function delAction()
	{
		if ($this->_request->isPost()){
			$id=$this->_request->getParam('id');
			$del=$this->_request->getPost('del');
			if ($del == 'Sim' && $id > 0 ){
				$where='id='.$id;
				$this->produtos->delete($where);
			}
            $url= $this->getRequest()->getControllerName();
			$this->_redirect($url);
		}else{
			$id=(int) $this->_request->getParam('id');
			$produto = $this->produtos->find($id)->current();
			$this->view->produto = $produto; 
		}
	}
	
	
	function editAction()
	{
		$this->_makeSelects();
		$this->_loadScripts();
		if ($this->_request->isPost()){
			$data = $this->_request->getPost();
			if ($this->produtos->skipDbUniqueValidation($data['id'])->isValid($data)){
				if( $row = $this->produtos->find($data['id'])->current())
					$this->_saveRegister($row, $data);
				else
					throw new My_Exception_RegisterNotFound();
			}//not valid
			else{
				$this->_populateForm($data);
				$this->view->errors( $this->produtos->getValidationMessages() );
			}
		}//not post
		else{
				if ($produto = $this->produtos->find($this->getRequest()->getParam('id'))->current())
					$this->_populateForm($produto->toArray());
				else
					throw new My_Exception_RegisterNotFound();
			}
	}

	protected function _populateForm($data = array())
	{
		$this->view->formData = $data;

		if($data['pictures']){
			$pics = explode(';',$data['pictures']);
			$this->view->mainPicture = '/images/_produtos/' . $pics[0];
		}
		return $this;
	}

	private function _makeSelects()
	{
		$selects = array('Divisoes' , 'TipoProduto', 'Designers', 'Estacoes');
		$this->view->select = SelectBoxes::makeSelects($selects);
	}
	
	
	function newAction()
	{
        $this->_loadScripts();
		$this->_makeSelects();
		if ($this->_request->isPost()){
			$data = $this->_request->getPost();
			if ($this->produtos->disableValidationRulesForField('id')->isValid($data)){
				$row = $this->produtos->createRow();
				$this->_saveRegister($row, $data);
			}
			else{
				$this->_populateForm($data);
				$this->view->errors( $this->produtos->getValidationMessages() );
			}
		}

	}
	
	
	private function _saveRegister($row, $data)
	{
		try{
			$filter = new My_Model_Filter('Produtos',$data);
			$row->nome			= $filter->filter('nome');
			$row->referencia	= $filter->filter('referencia');
			$row->referenciaCliente = $filter->filter('referenciaCliente');
			$row->estacaoID		= $filter->filter('estacaoID');
			$row->tipoID		= $filter->filter('tipoID');
			$row->divisaoID		= $filter->filter('divisaoID');
			$row->designer_id	= $filter->filter('designer_id');

			#$row->construcaoID = $filter->filter('construcaoID');

			/**
			 * Data inclusão (only for inserts)
			 */
			if ('new' == $this->_request->action)
				$row->inclusao	= Zend_Date::now()->toString('YYYY-MM-dd');

			/**
			 * saves and logs query
			 */
			$row->save();
			new QueryLogger();

			$location = $this->getRequest()->getControllerName();

			$id = 'new' == $this->_request->action ? $this->produtos
					->getAdapter()->lastInsertId() : $data['id'];
			$this->view->flash('Produto salvo com sucesso.');
			$location .=  "/edit/id/" . $id;
			$this->_redirect($location);
			
			

			
		}catch(Exception $e){
			$this->_populateForm($data);
			$this->view->errors('Não foi possível salvar resgistro.');
			$this->view->errors()->addMessage($e->getMessage());
		}
	}
	
	private function _loadScripts()
	{
		$this->view->script=array();
		$this->view->script[]="jquery.js";
		$this->view->script[]="Produtos.js";
		$this->view->script[]="wait.js";
		$this->view->script[]="jquery.autocomplete.js";

		$this->view->script[]="jquery.validate.min.js";
		$this->view->script[]="jquery.validate.pt_BR.js";

        $this->view->script[]="jquery.json-1.3.min.js";
        $this->view->script[]="jquery.popupWindow.js";
        $this->view->script[]="jquery.selectboxes.min.js";
		$this->view->script[]="date.format.js";
		$this->view->script[]="jquery.validate.data.js";
		$this->view->script[]="jquery.validate.referencia.js";
		$this->view->script[]="format.dinheiro.js";
		$this->view->script[]="jquery.validate.dinheiro.js";

	}

	public function uploadPicAction()
	{
		
		$this->view->title = "Adicionar foto em produto";
		if($this->getRequest()->isPost()){

			$produto = new Produtos();
			try{
				$id = $this->_getParam('produto');
				$produto = $produto->find($id)->current();
				$picture = new ProdutoPicture($produto);
				if (!$picture->upload()) {
					$this->view->errors($picture->getMessages());
				}
				$this->_redirect($_SERVER['REQUEST_URI']);
				$this->view->flash('Imagem adicionada sucesso.');
			}catch(Exception $e){
				$this->view->errors($e->getMessage());
			}
		}
	}

}