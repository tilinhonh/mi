<?php
class TamanhosController extends Zend_Controller_Action
{
	function indexAction()
	{
		$this->view->title = "Tamanhos";
		$tamanhoTable=new Tamanhos();
		$select = $tamanhoTable->select()->order('tamanho');
		$this->view->tamanho=$tamanhoTable->fetchAll($select);
	}
	
	function delAction()
	{
		$this->view->title="(-)Tamanhos";

		$tamanhoTable=new Tamanhos();
		
		if($this->_request->isPost()){
			$id=$this->_request->getParam('id');
			$del=$this->_request->getPost('del');
			if($del == 'Sim' && $id > 0 )
			{
				$where='id='.$id;
				$tamanhoTable->delete($where);
			}
			$this->_redirect('/tamanhos');
		}else{
			$id=(int) $this->_request->getParam('id');
			$tamanho = $tamanhoTable->find($id)->current();
			$this->view->tamanho = $tamanho; 
		}
	}
	
	
	function editAction()
	{
		$this->createForm();
		$this->view->form->submit->setLabel('Alterar');
		$this->view->title = "Tamanhos";
		
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->view->form->isValid($formData))
			{
				$id=(int)$this->_request->getPost('id');
				$tamanhoTable = new Tamanhos();
				$register=$tamanhoTable->find($id)->current();
				$this->saveRegister($register);
				
				$this->_redirect('/tamanhos');
			}
			else{
				$this->view->form->populate($formData);
			}
		}//not post
		else{
			$id = (int) $this->_request->getParam('id',0);
			if($id > 0){
				$tamanhoTable = new Tamanhos();
				$tamanho = $tamanhoTable->find($id)->current();
				$this->view->form->populate($tamanho->toArray());
			}
		}
		
	}
	
	
	function addAction()
	{
		$this->view->title = '++Tamanhos';
		$this->createForm();
		$this->view->form->submit->setLabel('Adicionar');
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->view->form->isValid($formData))
			{
				$tamanhoTable = new Tamanhos();
				$register = $tamanhoTable->createRow();
				$this->saveRegister($register);
				$this->_redirect('/tamanhos');
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

				
		
		$form->addElement('text','tamanho');
		$form->tamanho
				->setLabel('Tamanho:')
				->setAttrib('maxlength','10')
				->setAttrib('size','10')
				->setRequired(true)
				->addFilter('StringToUpper')
                ->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('stringLength',false,array(1,10))
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
		
		$row->tamanho = $this->view->form->getValue('tamanho');
		$row->save();
	}
	
}
?>