<?php
class TipoDeEmbarqueController extends Zend_Controller_Action
{
	function indexAction()
	{
		$this->view->title = "Tipo de Embarque";
		$tipoEmbarqueTable=new TipoEmbarque();
		$select = $tipoEmbarqueTable->select()->order('tipo');
		$this->view->tipoEmbarque=$tipoEmbarqueTable->fetchAll($select);
	}
	
	function delAction()
	{
		$this->view->title="(-)Tipo de Embarque";

		$tipoEmbarqueTable=new TipoEmbarque();
		
		if($this->_request->isPost()){
			$id=$this->_request->getParam('id');
			$del=$this->_request->getPost('del');
			if($del == 'Sim' && $id > 0 )
			{
				$where='id='.$id;
				$tipoEmbarqueTable->delete($where);
			}
			$this->_redirect('tipo-de-embarque');
		}else{
			$id=(int) $this->_request->getParam('id');
			$tipoEmbarque = $tipoEmbarqueTable->find($id)->current();
			$this->view->tipoEmbarque = $tipoEmbarque; 
		}
	}
	
	
	function editAction()
	{
		$this->createForm();
		$this->view->form->submit->setLabel('Alterar');
		$this->view->title = "Tipo de embarque";
		
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->view->form->isValid($formData))
			{
				$id=(int)$this->_request->getPost('id');
				$tipoEmbarqueTable = new TipoEmbarque();
				$register=$tipoEmbarqueTable->find($id)->current();
				$this->saveRegister($register);
				
				$this->_redirect('tipo-de-embarque');
			}
			else{
				$this->view->form->populate($formData);
			}
		}//not post
		else{
			$id = (int) $this->_request->getParam('id',0);
			if($id > 0){
				$tipoEmbarqueTable = new TipoEmbarque();
				$tipoEmbarque = $tipoEmbarqueTable->find($id)->current();
				$this->view->form->populate($tipoEmbarque->toArray());
			}
		}
		
	}
	
	
	function addAction()
	{
		$this->view->title = '++Tipo de Embarque';
		$this->createForm();
		$this->view->form->submit->setLabel('Adicionar');
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->view->form->isValid($formData))
			{
				$tipoEmbarqueTable = new TipoEmbarque();
				$register = $tipoEmbarqueTable->createRow();
				$this->saveRegister($register);
				$this->_redirect('tipo-de-embarque');
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
		$form->id->removeDecorator('DtDdWrapper');

				
		
		$form->addElement('text','tipo');
		$form->tipo
				->setLabel('Tipo:')
				->setAttrib('maxlength','45')
				->setAttrib('size','30')
				->setRequired(true)
				->addFilter('StringToUpper')
                ->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('stringLength',false,array(1,45))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Preencha o campo Tipo de Embarque.')))
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
				->setAttrib('onclick',"javascript:window.location.href='".@$_SERVER['HTTP_REFERER']."'")
				;
	}
	
	
	private function saveRegister($register)
	{
		$row=$register;
		
		$row->tipo = $this->view->form->getValue('tipo');
		$row->save();
	}
	
}
?>