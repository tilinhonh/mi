<?php
class PedidosController extends Zend_Controller_Action
{

	function init()
    {
       $this->view->title = "Pedidos";
       $this->view->controllerName = $this->getRequest()->getControllerName();
       $this->pedidos = new Pedidos();
       $this->_loadScripts();
	   $this->view->tracker(array(1,2,3));
    }

    function indexAction()
    {
		 $this->view->pedidos = $this->pedidos->fetchAll();
    }

	function delAction()
    {
		if($this->getRequest()->isPost()){
			$del = $this->_request->getPost('del');
			$id = $this->_request->getPost('id');
			if($id > 0 && $del == 'sim'){
				$where = $this->pedidos->getAdapter()->quoteInto('id= ? ', $id);
				$this->pedidos->delete($where);
				new QueryLogger();
			}
			$this->_forward('index');
		}
		else{
			$id = (int) $this->getRequest()->getParam('id');
			$this->view->pedido = $this->pedidos->find($id)->current();
		}
    }


    function addAction()
    {
        $this->_makeSelects();
        $request = $this->getRequest();
        if($request->isPost()){
            $postedData = $request->getPost();
			if($this->pedidos->disableValidationRulesForField('id')->isValid($this->_getValidation($postedData))){
				$row = $this->pedidos->createRow();
				$this->_saveRegister($row, $postedData);
            }
            else{
                $this->view->formData = $postedData;
                $this->view->errors($this->pedidos->getValidationMessages());
                $this->view->invalidFields = $this->pedidos->getInvalidFields();
            }
        }
		else{
			$this->view->formData = array('corsoID' =>$this->getRequest()->getParam('corso'));
		}
    }

	function editAction()
	{
		$this->_makeSelects();
		$request = $this->getRequest();
		if($request->isPost()){
			$data = $request->getPost();
			if($this->pedidos->skipDbUniqueValidation($data['id'])->isValid($data)){
				if($row = $this->pedidos->find($data['id'])->current());
					$this->_saveRegister($row, $data);
            }
            else{
                $this->view->formData = $data;
                $this->view->errors($this->pedidos->getValidationMessages());
                $this->view->invalidFields = $this->pedidos->getInvalidFields();
            }
		}else{
			$id = $request->getParam('id');
			if($row = $this->pedidos->find($id)->current()){
				$this->view->itens  = $this->_getItems($id);
				$this->view->formData = $row->toArray();
			}
		}
	}

	private function _getItems($id =null)
	{
		$db = $this->pedidos->getAdapter();
		$sql = $db->select()
					->from(array('p'=>'produtos'),
						array(
							'nome_produto'=>'nome',
							'referencia',
							'referencia_cliente'=>'referenciaCliente',
						)
					)
					->join(array('c'=>'combinacoes'),
						'p.id = c.produtoID',
						array()
					)
					->join(array('i'=>'itemPedido'),
						'c.id = i.itemID',
						array(
							'preco_fabrica'=>'precoFabrica',
							'preco_cliente'=>'precoCliente',
							'item_id'=>'id'
						)
					)
					->join(array('itq'=>'item_tamanho_quantidade'),
						'i.id = itq.item_pedido_id',
						array('quantidade' => 'SUM(quantidade)')
					)
					/* tentativa de mostrar material/cor
					->join(array('ic'=>'combinacao_item'),
						'c.id = ic.combinacao_id',
						array()
					)
					->join(array('gmc'=>'grupo_material_cor'),
						'ic.gmc_id = gmc.id',
						array()
					)
					->join(array('cr'=>'cores'),
						'gmc.corID = cr.id',
						array('cor')
					)
					->join(array('mt'=>'materiais'),
						'gmc.materialID = mt.id',
						array('material')
					)
					 * */

					->where('pedidoID = ? ', $id)
					->group('i.id')
					->order('p.nome')
					;

					//die($sql);
					$stmt = $db->query($sql);

					return $stmt->fetchAll();

	}


	/**
	 * Populates selects
	 */
    private function _makeSelects(){
        $selects = array(
                'Corsos',
                'Clientes',
                'Fabricas',
                'Transportadoras',
                'Status',
                'TipoEmbarque',
                'Representantes'
        );

        $this->view->select = SelectBoxes::makeSelects($selects);
    }


	private function _saveRegister($row,$data){
		$filter = new ModelFilter('Pedidos',$data);
		$row->corsoID			= $filter->filter('corsoID');
		$row->clienteID			= $filter->filter('clienteID');
		$row->representanteID	= $filter->filter('representanteID');
		//passado para o item
		//$row->fabricaID			= $filter->filter('fabricaID');
		$row->transportadoraID	= $filter->filter('transportadoraID');
		$row->representanteID	= $filter->filter('representanteID');
		$row->tipoEmbarqueID	= $filter->filter('tipoEmbarqueID');
		$row->representanteID	= $filter->filter('representanteID');
		$row->statusID			= $filter->filter('statusID');
		$row->pedidoCliente		= $filter->filter('pedidoCliente');

		$row->dataCliente		= BrazilianDate::toDbFormat($data['dataCliente']);
		$row->dataFabrica		= BrazilianDate::toDbFormat($data['dataFabrica']);
		$row->dataFabricaReprogramada = BrazilianDate::toDbFormat($data['dataFabricaReprogramada']);
		$row->saidaFabrica		= BrazilianDate::toDbFormat($data['saidaFabrica']);

		$row->save();

		new QueryLogger();

		$this->view->flash('Pedido salvo.');
		$id = $data['id'] ? $data['id'] : $this->pedidos->getAdapter()->lastInsertId();
		$this->_redirect($this->_request->getControllerName() . '/edit/id/' . $id);
	}

	/*
	 * Loads Scripts
	 */
    private function _loadScripts()
	{
		$this->view->script=array();
		$this->view->script[]="jquery.js";
		$this->view->script[]="Pedidos.js";
		$this->view->script[]="wait.js";
		$this->view->script[]="date.format.js";
		
		//$this->view->script[]="jquery.autocomplete.js";
		$this->view->script[]="jquery.validate.pt_BR.js";
		$this->view->script[]="jquery.validate.data.js";
		$this->view->script[]="format.dinheiro.js";
		$this->view->script[]="jquery.validate.dinheiro.js";
        $this->view->script[]="jquery.json-1.3.min.js";
        $this->view->script[]="LoTaFunctions.js";
        //$this->view->script[]="jquery.popupWindow.js";
        //$this->view->script[]="jquery.selectboxes.min.js";
	}


	private function _getValidation($data = array()){
		$validation = array();
		$automate = array('corsoID','clienteID','fabricaID','transportadoraID','representanteID',
					'tipoEmbarqueID','statusID','dataCliente','dataFabrica','pedidoCliente',
					'saidaFabrica','saidaFabrica','dataFabricaReprogramada');
		if($this->getRequest()->getActionName() != 'add')
			$validation['id']=$data['id'];

		foreach($automate as $v){
			$validation[$v] = $data[$v];
		}
		return $validation;
	}
}
