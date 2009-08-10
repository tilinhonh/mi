<?php
class ItemPedidoController extends Zend_Controller_Action
{
	protected $_errors = array();
	protected $_response;


	function init()
	{
		$this->item = new ItemPedido();
		$this->itemTamanhoQuantidade = new ItemTamanhoQuantidade();
		$this->view->title = "Item pedido";
	}


	/**
	 *
	 * @param mixed $error
	 * @return object
	 */
	private function _addErrors($error = null){
		if(is_array($error)){
			foreach($error as $e){
				$this->_errors[] = $e;
			}
		}elseif($error){
			$this->_errors[] = $error;
		}
		return $this;
	}


	/**
	 *
	 * @return array
	 */
	private function _getErrors()
	{
		return $this->_errors;
	}


	/**
	 * Busca tamanhos
	 * @return Object
	 */
	protected function _getTamanhos()
	{
		$tamanhos = new Tamanhos();
		$this->view->tamanhos = $tamanhos->fetchAll();
		return $this;
	}


	/**
	 *
	 * @param Zend_Db_Table_Row $item
	 * @param array $data
	 * @return bool
	 */
	private function _saveItem($item, $data)
	{
		if ($item->id){ // update
			$this->item->disableValidationRulesForFields(array('id','pedidoID'));
			$itemID = $item->id;
		}
		else{//insert
			$this->item->disableValidationRulesForFields(array('id'));
			$data['pedidoID'] = $data['pedido'];
			$itemID = 0;
		}
		$filter = new My_Model_Filter('ItemPedido',$data);
		//throw new Exception($filter->filter('itemID'));
		if ($this->item->isValid($data)){
			if ($this->_pedidoHasCombinacao($filter->filter('pedidoID'), $filter->filter('itemID'), $itemID)){
				$this->_addErrors('O item selecionado já está presente neste pedido.');
				throw new Exception();
			}
			if ('add' == $this->_request->action)
				$item->pedidoID = $filter->filter('pedidoID');
			$item->itemID = $filter->filter('itemID');
			$item->precoFabrica = Dinheiro::toDbFormat( $filter->filter('precoFabrica'));
			$item->precoCliente = Dinheiro::toDbFormat( $filter->filter('precoCliente'));
			$item->fabricaID = $filter->filter('fabricaID');
			$item->cancelado = $filter->filter('cancelado');
			$item->save();
		}else{
			$this->_addErrors($this->item->getValidationMessages());
			throw new Exception();
		}
		return true;
	}

	/**
	 *
	 * @param <type> $quantidade
	 * @param <type> $item
	 * @return <type>
	 */
	private function _validaQuantidades($quantidades)
	{
		//valida tamanho e quantidade
		//$k é tamanho e $v quantidade
		$itq = new ItemTamanhoQuantidade();
		$itq->disableValidationRulesForField('item_pedido_id');
		foreach($quantidades as $tamanho => $quantidade){
			$data = array(	'tamanho_id' => $tamanho,
							'quantidade' => $quantidade);
			if($quantidade){
				if (false == $itq->isValid($data)){
					$this->_addErrors($itq->getValidationMessages());
					throw new Exception();
				}
				$peloMenosUmItem = true;
			}
		}//endforeach

		if (false == $peloMenosUmItem)
			throw new Exception('Pedido deve conter pelo menos um par.');

		return $this;
	}


	/**
	 *
	 * @param array $quantidades
	 * @return array
	 */
	private function _getQuantidadesWithValues($quantidades = array())
	{
		foreach($quantidades as $k => $v){
			if(!$v)
				unset($quantidades[$k]);
		}
		return $quantidades;
	}

	/**
	 * Salva as quantidades
	 */
	function _saveQuantidades($quantidades, $item)
	{
		$quantidades = $this->_getQuantidadesWithValues($quantidades);
		$this->_validaQuantidades($quantidades);
		
		//localiza os tamanhos que o item já possue
		$_itqs = $item->findDependentRowset('ItemTamanhoQuantidade');

		//pega todos os valores que exisitam no banco de dados
		//para adicionar o que tem a mais em $quantidade
		$tamanhosNoDB = array();
		
		foreach($_itqs as $_itq){
			$tamanhosNoDB[$_itq->tamanho_id] = $_itq->quantidade;
			//existe nos dois ATUALIZA
			if (array_key_exists($_itq->tamanho_id, $tamanhosNoDB)){
				$_itq->quantidade = $quantidades[$_itq->tamanho_id];
				$_itq->save();
			}else{
				$_itq->delete();
			}
		}//endforeach

		//ADICIONA
		$itq = new ItemTamanhoQuantidade();
		foreach($quantidades as $tamanho => $quantidade){
			//se tem no post mas nao tinha no banco de dados
			if (!array_key_exists($tamanho, $tamanhosNoDB)){
				//insere
				$row = $itq->createRow();
				$row->tamanho_id = $tamanho;
				$row->quantidade = $quantidade;
				$row->item_pedido_id = $item->id;
				$row->save();
			}
		}
	}




	private function _makeSelects()
	{
		$this->view->select = SelectBoxes::makeSelects(array('Fabricas'));
		return $this;
	}



	/*
	 * edita registros
	 */
	public function editAction()
	{
		$this->view->action = 'edit';
		$this->_getTamanhos()->_loadScripts()->_makeSelects();
		//itemPedido.id
		$id = $this->_request->getParam('id');
		try{
			//pega o registro
			$item = $this->item->find($id)->current();
			//busca o grupo em que o pedido pertence
			$grupoDoPedido = $item->findParentRow('Pedidos')->findParentRow('Clientes')->divisaoID;
			$pedido = $item->findParentRow('Pedidos');
			//$estacaoDoPedido = $item->findParentRow('Pedidos')->findParentRow('Corsos')->estacaoID;
			$this->_populateProdutos($pedido, $estacaoDoPedido);
			if ($this->_request->isPost()){
				$this->_response->success = false;
				//try twice
				try{
					$db = $this->item->getAdapter();
					$db->beginTransaction();
					$data = $this->getRequest()->getPost();
					$this->_saveItem($item, $data);
					$this->_saveQuantidades($data['quantidade'],$item);
					$db->commit();
					new QueryLogger();
					$this->_response->success = true;
				}catch(Exception $e){
					$db->rollBack();
					if (($e->getMessage()))
						$this->_addErrors($e->getMessage());
					$this->_response->error->message = $this->_getErrors();
				}
				$this->_helper->json($this->_response);
			}else{//not post
				$this->view->formValue = $item->toArray();
				$this->view->formValue['produtoID'] = $item->findParentRow('Combinacoes')->produtoID;
				$this->view->formValue['pedido'] = $item->pedidoID;
				$this->_getTamanhosQuantidades($item);
			}
		}catch(Exception $e){
			//registro nao existe mais
		}
	}


	/**
	 *
	 * @param ItemPedido $item
	 */
	private function _getTamanhosQuantidades($item){
		$itq = $item->findDependentRowSet('ItemTamanhoQuantidade');
		$this->view->formValue['quantidade']=array();
		foreach($itq as $i){
			$this->view->formValue['quantidade'][$i->tamanho_id]=$i->quantidade;
		}
		return $this;
	}

	/**
	 * ADICIONA item no pedido
	 */

	public function addAction()
	{
		$this->view->action = 'add';
		$this->view->formValue = array('pedido' => $this->_request->getParam('pedido'));
		$_pedidos = new Pedidos();
		$pedido = $_pedidos->find( $this->_request->getParam('pedido') )->current();
		$this->_getTamanhos()->_loadScripts()->_makeSelects()->_populateProdutos($pedido);

		if ($this->_request->isPost()){
			$db = $this->item->getAdapter();
			$db->beginTransaction();
			$data = $this->_request->getPost();
			try{
				$item = $this->item->createRow();
				$this->_saveItem($item, $data);
				$this->_validaQuantidades($data['quantidade']);
				//just created item id
				$itemPedidoID = $db->lastInsertId();
	
				foreach($data['quantidade'] as $k => $v){
					if($v > 0){
						$row = $this->itemTamanhoQuantidade->createRow();
						$row->item_pedido_id = $itemPedidoID;
						$row->tamanho_id = $k;
						$row->quantidade = $v;
						$row->save();
					}
				}
				
				$db->commit();
				new QueryLogger();
				$this->_response->success = true;
			}catch(Exception $e){
				$db->rollBack();
				if (($e->getMessage()))
					$this->_addErrors($e->getMessage());
				$this->_response->error->message = $this->_getErrors();
			}
			$this->_helper->json($this->_response);
		}
	}

	/**
	 * Checa se a o pedido já tem a combinacao/item em questao
	 *
	 * @param int $pedido
	 * @param int $item
	 * @param obj $db
	 */
	private function _pedidoHasCombinacao($pedidoID, $itemID, $notID = 0){
		$db = $this->item->getAdapter();
		$where =	$db->quoteInto('pedidoID = ?',$pedidoID)
				  .	$db->quoteInto('AND itemID = ?',$itemID)
				  . $db->quoteInto('AND id != ?',$notID);

		$itens = $this->item->fetchAll($where);

		return count($itens) > 0 ? true : false;
	}


	function delAction(){
		$response = new stdClass();
		try{
			$id = $this->getRequest()->getPost('id');
			$del = $this->getRequest()->getPost('del');
			if ($id > 1 && $del == 'sim'){
				$where = $this->item->getAdapter()->quoteInto('id = ?', $id);
				$this->item->delete($where);
			}else{
				throw new Exception('O Item não pode ser excluido.');
			}

			$response->success = true;
		}catch(Exception $e){
			$response->error->messages = array($e->getMessage());
		}
		$this->_helper->json($response);
	}





	private function _getGrupoId()
	{
		$pedidoID = $this->getRequest()->getParam('pedido');
		$_pedidos = new Pedidos();
		$pedido =  $_pedidos->find($pedidoID)->current();
		return $pedido->findParentRow('Clientes')->divisaoID;
	}



	/**
	 *
	 * @param Zend_Db_Table_Row $pedido
	 * @return Object
	 */
	private function _populateProdutos($pedido = null)
	{
		try{
			$_produtos = new Produtos();
			$grupo = $pedido->findParentRow('Clientes')->divisaoID;
			$where = $_produtos->getAdapter()->quoteInto('divisaoID = ?', $grupo ? $grupo : $this->_getGrupoId() );

			$options = array(''=>'');
			$order = 'nome';
			foreach($_produtos->fetchAll($where,$order) as $row){
				$options[$row->id] =  $row->referencia .' | '. $row->nome;
			}
			$this->view->select->produtos = $options;
		}
		catch(Exception $e){}
		return $this;
	}


	/*
	 * Loads Scripts
	 */
    private function _loadScripts()
	{
		$this->view->script=array();
		$this->view->script[]="jquery.js";
		$this->view->script[]="ItemPedido.js";
		$this->view->script[]="wait.js";
		//$this->view->script[]="date.format.js";
		$this->view->script[]="jquery.autocomplete.js";
		$this->view->script[]="jquery.validate.pt_BR.js";
		//$this->view->script[]="jquery.validate.data.js";
        $this->view->script[]="jquery.json-1.3.min.js";
        $this->view->script[]="LoTaFunctions.js";
		$this->view->script[]="format.dinheiro.js";
		$this->view->script[]="jquery.validate.dinheiro.js";
        //$this->view->script[]="jquery.popupWindow.js";
        //$this->view->script[]="jquery.selectboxes.min.js";
		return $this;
	}


	/**
	 * Busca os itens de uma dada combinacao
	 * @return json
	 */
	public function getItensAction()
	{
		if ($this->getRequest()->isPost()){
			$id = $this->getRequest()->getPost('id');
			if ($_item = $this->item->find($id)->current()) {
				$this->_helper->json($this->_getItensCombinacao($_item->itemID));
			}
		}
	}

	private function _getItensCombinacao($id = null)
	{
		$_combinacao_item = new CombinacaoItem();
		$where = $_combinacao_item->getAdapter()->quoteInto('combinacao_id = ?' , $id);
		$order = 'importancia';
		$itens = $_combinacao_item->fetchAll($where,$order);

		$combinacoes = array();
		$i=0;
		foreach($itens as $item){
			$x = new stdClass();
			$gmc = $item->findParentRow('GrupoMaterialCor');
			$x->material->nome = $gmc->findParentRow('Materiais')->material;
			$x->estacao->nome = $gmc->findParentRow('Estacoes')->estacao;
			$x->cor->nome = $gmc->findParentRow('Cores')->cor;
			$x->grupo->nome = $gmc->findParentRow('Divisoes')->divisao;
			$x->codigo = $gmc->codigo;
			$x->id = $item->id;
			$x->importancia = $item->importancia;

			$combinacoes[] = $x;
		}
		return $combinacoes;
	}
	
}