<?php
class TransportadorasController extends Zend_Controller_Action
{
    function init(){
        $this->view->title="Transportadoras";
        $this->transportadoras = new Transportadoras();
        $this->_getScripts();
    }
    
	function indexAction()
	{
		$Transportadoras=new Transportadoras();
		$select = $Transportadoras->select()->order('fantasia');
		$this->view->transportadora=$Transportadoras->fetchAll($select);
	}
	
	function delAction()
	{
		$TransportadorasTable=new Transportadoras();
		
		if($this->_request->isPost()){
			$id=$this->_request->getParam('id');
			$del=$this->_request->getPost('del');
			if($del == 'Sim' && $id > 0 )
			{
				$where='id='.$id;
				try{
					$TransportadorasTable->delete($where);
					new QueryLogger();
				}catch(Exception $e){
					$this->view->flash('Não foi possível excluir esta Transportadora.');
				}
			}
			$this->_redirect('/Transportadoras');
		}else{
			$id=(int) $this->_request->getParam('id');
			$Transportadoras = $TransportadorasTable->find($id)->current();
			$this->view->transportadora = $Transportadoras;
		}
	}
	
	
	function editAction()
	{
		$this->view->form = new TransportadorasForm();
		$this->view->form->submit->setLabel('Alterar');
		
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			$id = (int)$this->_request->getPost('id');
			if($this->transportadoras->skipDbUniqueValidation($id)->isValid($formData))
			{
				$TransportadorasTable = new Transportadoras();
				$register=$TransportadorasTable->find($id)->current();
				$this->saveRegister($register, $formData);
			}
			else{
				$this->view->errors($this->transportadoras->getValidationMessages());
				$this->view->form->populate($formData);
				$this->view->form->setCidade($formData['cidadeID']);
			}
		}//not post
		else{
			$id = (int) $this->_request->getParam('id',0);
			if($id > 0){
				$TransportadorasTable = new Transportadoras();
				$Transportadoras = $TransportadorasTable->find($id)->current();
				$this->view->form->populate($Transportadoras->toArray());
				$this->view->form->setCidade($Transportadoras->cidadeID);
			}
		}
		
	}
	
	
	function newAction()
	{
		$this->view->form=new TransportadorasForm();
		$this->view->form->submit->setLabel('Adicionar');
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->transportadoras->disableValidationRulesForField('id')->isValid($formData))
			{
				$TransportadorasTable = new Transportadoras();
				$register = $TransportadorasTable->createRow();
				$this->saveRegister($register, $formData);
			}
			else{
				$this->view->errors($this->transportadoras->getValidationMessages());
				$this->view->form->populate($formData);
			}
		}
	}
	
	
	private function saveRegister($row, $data)
	{
		try{
			$filter = new My_Model_Filter('Transportadoras', $data);
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
			$this->view->flash('transportadora salva com successo');
			$this->_redirect('/Transportadoras');
		}
		catch(Exception $e){
			//$this->view->errors('Fransportador não pode ser salva.');
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
        $Transportadoras = new Transportadoras();
        $select = $Transportadoras->select()->where('ativo = 1')->order('fantasia');
        foreach($Transportadoras->fetchAll($select) as $f){
            $transportadora[$f->id] = $f->fantasia;
        }
        $this->_helper->json($transportadora);
    }
	
}
?>