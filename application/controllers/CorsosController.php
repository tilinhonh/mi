<?php
class CorsosController extends Zend_Controller_Action
{
    function init()
    {
       $this->view->title = "Corsos";
       $this->corsos = new Corsos();
       $this->view->date = new Zend_Date();
    }

    function indexAction()
    {
        $this->_getCorsos();
        $this->view->controllerName = $this->getRequest()->getControllerName();
    }

    function delAction()
    {
        $this->title = "Exclusão de Pedido";
        if($this->getRequest()->isPost()){
            $del = $this->getRequest()->getPost('del');
            $id = (int) $this->getRequest()->getPost('id');
            if($id > 0 && $del == 'sim'){
                $where = 'id = ' . $id;
                $this->corsos->delete($where);
                new QueryLogger();
            }
            $this->_redirect($this->getRequest()->getControllerName());
        }else{
            $id = (int) $this->getRequest()->getParam('id');
            $this->view->corso = $this->corsos->find($id)->current();
        }
    }

    function addAction()
    {
		$this->_makeSelects();
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
			if($this->corsos->disableValidationRulesForField('id')->isValid($this->_getValidation($data))){
                $row = $this->corsos->createRow();
                $this->_saveRegister($row,$data);
            }
            else{
				$this->view->errors($this->corsos->getValidationMessages());
                $this->view->formData = $data;
            }
        }
    }

    function editAction()
    {
		$this->_makeSelects();
		$this->view->id = $this->getRequest()->getParam('id');
		// busca pedidos do corso
		$this->_getPedidos($this->getRequest()->getParam('id'));
        $request = $this->getRequest();

        if ($request->isPost()){
            $data = $request->getPost();
			$id = (int) $data['id'];
			$this->corsos->skipDbUniqueValidation($id);
			if($this->corsos->isValid($this->_getValidation($data))){
                if($register = $this->corsos->find($id)->current())
					$this->_saveRegister($register, $data);
				else
					throw new ExceptionRegisterNotFound();
            }
            else{
				$this->view->errors($this->corsos->getValidationMessages());
				$this->view->formData = $data;
            }
        }else{//not Post
			$id = (int) $request->getParam('id');
			if ($register = $this->corsos->find($id)->current())
				$this->view->formData = $register->toArray();
			else
				throw new ExceptionRegisterNotFound();
        }
    }

    private function _saveRegister($row, $data)
    {
		$fields = array('corso','estacaoID','observacaoInterna','observacaoFabrica');
        foreach($fields as $name){
			$row->$name = is_string($data[$name]) ? strtoupper($data[$name]) : $data[$name];
		}

        if($this->getRequest()->getActionName() == "add"){
            $row->dataInclusao = Zend_Date::now()->toString('YYYY-MM-dd');
            $isAdd=true;
        }
        $row->save();
        new QueryLogger();
        $id = $isAdd ?  Zend_Db_Table::getDefaultAdapter()->lastInsertId() : $data['id'];

		$this->view->flash('Corso salvo!');

		$this->_redirect("/".$this->getRequest()->getControllerName()."/edit/id/".$id);
    }

	private function _getPedidos($id = 0)
	{
		$sql = $this->corsos->getAdapter()->select()
					->from(array('c'=>'corsos'),
						array()
					)
					->join(array('p'=>'pedidos'),
						'c.id = p.corsoID',
						array(
							'id',
							'pedido_cliente'=>'pedidoCliente',
							'data_cliente'=>'dataCliente',
							'data_fabrica'=>'dataFabrica',
							'data_fabrica_reprogramada'=>'dataFabricaReprogramada',
							'data_embarue'=>'dataEmbarque',
							)
					)
					->join(array('i'=>'itemPedido'),
						'p.id = i.pedidoID',
						array()
					)
					->join(array('itq'=>'item_tamanho_quantidade'),
						'i.id = itq.item_pedido_id',
						/* aqui vai a soma dos pares */
						array('quantidade'=>'SUM(quantidade)')
					)
					->join(array('cl'=>'clientes'),
						'p.clienteID = cl.id',
						array('nome_cliente'=>'nome')
					)
					->join(array('r'=>'representantes'),
						'p.representanteID = r.id',
						array('nome_representante'=>'nome')
					)
					->join(array('t'=>'transportadoras'),
						'p.transportadoraID = t.id',
						array('nome_transportadora'=>'fantasia')
					)
					->join(array('s'=>'statusPedido'),
						'p.statusID = s.id',
						array('status')
					)
					->where('c.id = ? ', $id)
					->group('p.id')
					;
					//die($sql);

		$stmt = $this->corsos->getAdapter()->query($sql);

		$this->view->pedidos = $stmt->fetchAll();

		//die(print_r($this->view->pedidos,1));
	}

	private function _getCorsos()
	{
		$sql = $this->corsos->getAdapter()->select()
				->from(array('c'=>'corsos'),
					array(
						'id',
						'nome'=>'corso',
						'inclusao'=>'dataInclusao'
						)
				)
				->joinLeft(array('p'=>'pedidos'),
					'c.id = p.corsoID',
					array()
				)
				->joinLeft(array('i'=>'itemPedido'),
					'p.id = i.pedidoID',
					array()
				)
				->joinLeft(array('e'=>'estacoes'),
					'c.estacaoID = e.id',
					array('estacao')
				)
				->joinLeft(array('itq'=>'item_tamanho_quantidade'),
					'i.id = itq.item_pedido_id',
					/* aqui vai a soma dos pares */
					array('quantidade'=>'SUM(quantidade)')
				)
				->group('c.id')
				;

		$stmt = $this->corsos->getAdapter()->query($sql);

		$this->view->corsos = $stmt->fetchAll();

	}

	/**
	 * Populates selects
	 */
    private function _makeSelects(){
        $selects = array('Estacoes');
        $this->view->select = SelectBoxes::makeSelects($selects);
		//die(print_r($this->view->select->estacoes));
    }

	private function _getValidation($data)
	{
		$this->corsos->addError('Buhao');
		$validation = array(
			'corso'=>$data['corso'],
			'estacaoID'=>$data['estacaoID'],
			'observacaoPedido'=>$data['observacaoPedido'],
			'observacaoFabrica'=>$data['observacaoFabrica'],
		);

		$validation['UniqueCorsox'] = array(
			//'fields' => array('corso'=>$data['corso']),
			//'skipId'=>$data['id']
		);

		if($this->getRequest()->getActionName() !== 'add')
			$validation['id'] = $data['id'];

		return $validation;
	}

}
