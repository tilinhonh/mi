<?php
class EstadosController extends Zend_Controller_Action
{
	function indexAction()
	{
		$this->view->title = "Estados";
		$estados= new Estados();
		$this->view->estados = $estados->fetchAll();
	}
	
	function editAction()
	{
		$this->view->title="Editar Estado";
		$form=$this->getForm();
		$this->view->form=$form;
		$form->submit->setLabel('Atualizar');
		if($this->_request->isPost()){
			$formData=$this->_request->getPost();
			if($form->isValid($formData)){
				$estados=new Estados();
				$id=$this->_request->getPost('id');
				$estado=$estados->fetchRow('id='.$id);
				$estado->estado=$form->getValue('estado');
				$estado->sigla=$form->getValue('sigla');
				$estado->save();
				$this->_redirect('/estados');
			}else{//not valid
				$form->populate($formData);
			}
		}else{
			$id=(int)$this->_request->getParam('id',0);
			$estados=new Estados();
			$estado=$estados->fetchRow('id='.$id);
			$form->populate($estado->toArray());
		}
	}
	
	function addAction()
	{
		$this->view->title="++Estado";
		$form=$this->getForm();
		$form->submit->setLabel('Adicionar');
		$this->view->form=$form;
		if($this->_request->isPost()){
			$formData=$this->_request->getPost();
			if($form->isValid($formData)){
				$estados=new Estados();
				$row=$estados->createRow();
				$row->estado=$form->getValue('estado');
				$row->sigla=$form->getValue('sigla');
				$row->save();
				$this->_redirect('estados/edit/id/'.$row->id);
			}else{
				$form->populate($formData);
			}
		}
	}
	
	function delAction()
	{
		$this->view->title='(-)Estado';
		$estados=new Estados();
		if($this->_request->isPost()){
			$id=$this->_request->getPost('id');
			$del=$this->_request->getPost('del');
			if($del=='Sim' && $id>0){
				$where='id='.$id;
				$estados->delete($where);
			}
			$this->_redirect('/estados');
		}else{
			$id=$this->_request->getParam('id');
			$estado=$estados->fetchRow('id='.$id);
			$this->view->estado=$estado;			
		}
	}
	
	function getForm()
	{
		$form=new Zend_Form();		

		$id = new Zend_Form_Element_Hidden('id'); 
 
        $estado = new Zend_Form_Element_Text('estado'); 
        $estado->setLabel('Estado:') 
	        ->setRequired(true) 
	        ->addFilter('StripTags') 
	        ->addFilter('StringTrim')
            ->addFilter('StringToUpper')
	        ->addValidator('NotEmpty');
         	
        $sigla = new Zend_Form_Element_Text('sigla'); 
        $sigla->setLabel('Sigla:') 
	        ->setRequired(true) 
	        ->addFilter('StripTags') 
	        ->addFilter('StringTrim') 
	        ->addValidator('NotEmpty')
         	->addFilter('StringToUpper');
 
        $submit = new Zend_Form_Element_Submit('submit'); 
        $submit->setAttrib('id', 'submitbutton')
        	->setAttrib('class', 'button')
        	->removeDecorator('DtDdWrapper'); 
 
        $form->addElements(array($id, $estado, $sigla, $submit)); 
        
        $form->addElement('button','cancelar');
        $form->cancelar
				->setLabel('Cancelar')
				->setAttrib('class','button2')
				->removeDecorator('DtDdWrapper')
				->setAttrib('onclick',"javascript:window.location.href='".$_SERVER['HTTP_REFERER']."'")
				;
        
		return $form;
	}
	
} 




?>