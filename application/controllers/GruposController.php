<?php
class GruposController extends Zend_Controller_Action
{
	function indexAction()
	{
		$this->view->title = "Grupos";
		$divisaoTable=new Divisoes();
		$select = $divisaoTable->select()->order('divisao');
		$this->view->divisao=$divisaoTable->fetchAll($select);
	}
	
	function delAction()
	{
		$this->view->title="(-)Grupo";

		$divisaoTable=new Divisoes();
		
		if($this->_request->isPost()){
			$id=$this->_request->getParam('id');
			$del=$this->_request->getPost('del');
			if($del == 'Sim' && $id > 0 )
			{
				$where='id='.$id;
				$divisaoTable->delete($where);
			}
			$this->_redirect('/grupos');
		}else{
			$id=(int) $this->_request->getParam('id');
			$divisao = $divisaoTable->find($id)->current();
			$this->view->divisao = $divisao; 
		}
	}
	
	
	function editAction()
	{
		$this->createForm();
		$this->view->form->submit->setLabel('Alterar');
		$this->view->title = "Grupos";
		
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->view->form->isValid($formData))
			{
				$id=(int)$this->_request->getPost('id');
				$divisaoTable = new Divisoes();
				$register=$divisaoTable->find($id)->current();
				$this->saveRegister($register);
				
				$this->_redirect('/grupos');
			}
			else{
				$this->view->form->populate($formData);
			}
		}//not post
		else{
			$id = (int) $this->_request->getParam('id',0);
			if($id > 0){
				$divisaoTable = new Divisoes();
				$divisao = $divisaoTable->find($id)->current();
				$this->view->form->populate($divisao->toArray());
			}
		}
		
	}
	
	
	function addAction()
	{
		$this->view->title = '++Grupo';
		$this->createForm();
		$this->view->form->submit->setLabel('Adicionar');
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->view->form->isValid($formData))
			{
				$divisaoTable = new Divisoes();
				$register = $divisaoTable->createRow();
				$this->saveRegister($register);
				$this->_redirect('/grupos');
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

				
		
		$form->addElement('text','divisao');
		$form->divisao
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
                ->addFilter('StripTags')
				->setLabel('Tipo:')
				->setAttrib('maxlength','45')
				->setAttrib('size','30')
				->setRequired(true)
				->addValidator('stringLength',false,array(0,45))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
				;
				
		$form->addElement('textarea','descricao');
		$form->descricao
				->setLabel('Descrição:')
				->setAttrib('rows',4)
				->setAttrib('cols',30)
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')				
				->addValidator('stringLength',false,array(0,255))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
		
			;		
				
		
		$form->addElement('submit','submit');
		$form->submit
				->setAttrib('class','button')
				->removeDecorator('DtDdWrapper')
				;
				
		$form->addElement('button','cancelar');
		$form->cancelar
				->setLabel('Cancelar')
				->setAttrib('class','button2')
				->removeDecorator('DtDdWrapper')
				->setAttrib('onclick',"javascript:window.location.href='".@$_SERVER['HTTP_REFERER']."'")
				;
	}
	
	
	private function saveRegister($register)
	{
		$row=$register;
		
		$row->divisao = $this->view->form->getValue('divisao');
		$row->descricao = $this->view->form->getValue('descricao');
		$row->save();
	}
	
}
?>