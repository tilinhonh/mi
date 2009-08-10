<?php
class FabricasController extends Zend_Controller_Action
{
    function init(){
        $this->view->title="Fábrica";
        $this->fabricas = new Fabricas();
        $this->_getScripts();
    }
    
	function indexAction()
	{
		$this->view->title = "Fabricas";
		$fabricas=new Fabricas();
		$select = $fabricas->select()->order('fantasia');
		$this->view->fabrica=$fabricas->fetchAll($select);
	}
	
	function delAction()
	{
		$fabricasTable=new Fabricas();
		
		if($this->_request->isPost()){
			$id=$this->_request->getParam('id');
			$del=$this->_request->getPost('del');
			if($del == 'Sim' && $id > 0 )
			{
				$where='id='.$id;
				try{
					$fabricasTable->delete($where);
					new QueryLogger();
				}catch(Exception $e){
					$this->view->flash('Não foi possível excluir esta fábrica.');
				}
			}
			$this->_redirect('/fabricas');
		}else{
			$id=(int) $this->_request->getParam('id');
			$fabricas = $fabricasTable->find($id)->current();
			$this->view->fabrica = $fabricas; 
		}
	}
	
	
	function editAction()
	{
		$this->view->form = new FabricasForm();
		$this->view->form->submit->setLabel('Alterar');
		
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			$id = (int)$this->_request->getPost('id');
			if($this->fabricas->skipDbUniqueValidation($id)->isValid($formData))
			{
				$fabricasTable = new Fabricas();
				$register=$fabricasTable->find($id)->current();
				$this->saveRegister($register, $formData);
			}
			else{
				$this->view->errors($this->fabricas->getValidationMessages());
				$this->view->form->populate($formData);
				$this->view->form->setCidade($formData['cidadeID']);
			}
		}//not post
		else{
			$id = (int) $this->_request->getParam('id',0);
			if($id > 0){
				$fabricasTable = new Fabricas();
				$fabricas = $fabricasTable->find($id)->current();
				$this->view->form->populate($fabricas->toArray());
				$this->view->form->setCidade($fabricas->cidadeID);
			}
		}
		
	}
	
	
	function newAction()
	{
		$this->view->form=new FabricasForm();
		$this->view->form->submit->setLabel('Adicionar');
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->fabricas->disableValidationRulesForField('id')->isValid($formData))
			{
				$fabricasTable = new Fabricas();
				$register = $fabricasTable->createRow();
				$this->saveRegister($register, $formData);
			}
			else{
				$this->view->errors($this->fabricas->getValidationMessages());
				$this->view->form->populate($formData);
			}
		}
	}
	
	
	private function saveRegister($row, $data)
	{
		try{
			$filter = new My_Model_Filter('Fabricas', $data);
			$row->fantasia 	= $filter->filter('fantasia');
			$row->nome 		= $filter->filter('nome');
			$row->endereco	= $filter->filter('endereco');
			$row->numero	= $filter->filter('numero');
			$row->complemento	= $filter->filter('complemento');
			$row->bairro	= $filter->filter('bairro');
			$row->cidadeID	= $filter->filter('cidadeID');
			$row->contato	= $filter->filter('contato');

			$row->cep	= $filter->filter('cep');
			$row->cnpj	= $filter->filter('cnpj');
			$row->inscricaoEstadual	= $filter->filter('inscricaoEstadual');
			$row->telefone			= $filter->filter('telefone');
			$row->cnpj	= $filter->filter('cnpj');
			$row->fax	= $filter->filter('fax');
			$row->email	= $filter->filter('email');
			$row->ativo	= $filter->filter('ativo');

			$row->save();
			new QueryLogger();
			$this->view->flash('Fabrica salva com successo');
			$this->_redirect('/fabricas');
		}
		catch(Exception $e){
			//$this->view->errors('Fábrica não pode ser salva.');
		}
	}
	
	private function _getScripts()
	{
		$this->view->script=array();
		$this->view->script[]="jquery.js";
		$this->view->script[]="wait.js";
		$this->view->script[]="FabricasTransportadoras.js";
		$this->view->script[]="jquery.selectboxes.min.js";
		$this->view->script[]="jquery.validate.pt_BR.js";
		$this->view->script[]="jquery.validate.cnpj.js";
		$this->view->script[]="CNPJ.class.js";
	}


    /**
     * for select boxes
     * @return json
     */
    public function getAction()
    {
        $fabricas = new Fabricas();
        $select = $fabricas->select()->where('ativo = 1')->order('fantasia');
        foreach($fabricas->fetchAll($select) as $f){
            $fabrica[$f->id] = $f->fantasia;
        }
        $this->_helper->json($fabrica);
    }
	
}
?>