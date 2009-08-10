<?php
class StatusController extends Zend_Controller_Action
{
	function indexAction()
	{
		$this->view->title = "Status de Pedido";
		$statusTable=new Status();
		$select = $statusTable->select()->order('status');
		$this->view->status=$statusTable->fetchAll($select);
								
								
	}
	
	function delAction()
	{
		$this->view->title="(-)Status de Pedido";

		$statusTable=new Status();
		
		if($this->_request->isPost()){
			$id=$this->_request->getParam('id');
			$del=$this->_request->getPost('del');
			if($del == 'Sim' && $id > 0 )
			{
				$where='id='.$id;
				$statusTable->delete($where);
			}
			$this->_redirect('/status');
		}else{
			$id=(int) $this->_request->getParam('id');
			$status = $statusTable->find($id)->current();
			$this->view->status = $status; 
		}
	}
	
	
	function editAction()
	{
		$this->createForm();
		$this->view->form->submit->setLabel('Edit');
		$this->view->title = "Status de Pedido";
		
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->view->form->isValid($formData))
			{
				$id=(int)$this->_request->getPost('id');
				$statusTable = new Status();
				$register=$statusTable->find($id)->current();
				$this->saveRegister($register);
				
				$this->_redirect('/status');
			}
			else{
				$this->view->form->populate($formData);
			}
		}//not post
		else{
			$id = (int) $this->_request->getParam('id',0);
			if($id > 0){
				$statusTable = new Status();
				$status = $statusTable->find($id)->current();
				$this->view->form->populate($status->toArray());
			}
		}
		
	}
	
	
	function addAction()
	{
		$this->view->title = '++Status de Pedido';
		$this->createForm();
		$this->view->form->submit->setLabel('Adicionar');
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->view->form->isValid($formData))
			{
				$statusTable = new Status();
				$register = $statusTable->createRow();
				$this->saveRegister($register);
				$this->_redirect('/status');
			}
			else{
				$this->view->form->populate($formData);
			}
		}
	}
	
	function createForm(){
		$form=new Zend_Form();
		$this->view->form=$form;
		
		$form->addElement('hidden','id');

				
		
		$form->addElement('text','status');
		$form->status
				->setLabel('Status:')
				->setAttrib('maxlength','15')
				->setAttrib('size','15')
				->setRequired(true)
				->addFilter('StringToUpper')
                ->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('stringLength',false,array(1,15))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Preencha o campo Código.')))
				;
				
		
		$form->addElement('submit','submit');
		$form->submit
				->setAttrib('class','button')
				->removeDecorator('DtDdWrapper')
				;
				
		$form->addElement('button','cancelar');
		$form->cancelar
				->setAttrib('class','button2')
				->setLabel('Cancelar')
				->removeDecorator('DtDdWrapper')
				->setAttrib('onclick',"javascript:window.location.href='".$_SERVER['HTTP_REFERER']."'")
				;
	}
	
	
	private function saveRegister($register)
	{
		$row=$register;
		
		$row->status = $this->view->form->getValue('status');
		$row->save();
	}
	
}
?>