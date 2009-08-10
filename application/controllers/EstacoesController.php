<?php
class EstacoesController extends Zend_Controller_Action
{
	function indexAction()
	{
		$this->view->title = "Estações";
	        $estacoes = new Estacoes(); 
	        $this->view->estacoes = $estacoes->fetchAll();
	}
	
	
	function addAction()
	{
		$this->view->title = "++Estação";
        #$form = new FormEstacao();
        $form=$this->getForm();
		$form->submit->setLabel('Adicionar');
		$this->view->form = $form;
		
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				$estacoes = new Estacoes();
				$row = $estacoes->createRow();
				$row->estacao = $form->getValue('estacao');
				$row->save();
				$this->_redirect('/estacao');
			} else {
				$form->populate($formData); 
			}
		}
	}
	
	
	function editAction()
	{
		$this->view->title= "Estação";
		
		$form=$this->getForm();
		$form->submit->setLabel('Salvar');
		$this->view->form=$form;
		
		if($this->_request->isPost()){
			$formData = $this->_request->getPost();
			if($form->isValid($formData)){
				$estacoes=new Estacoes();
				$id=(int)$form->getValue('id');
				$row=$estacoes->fetchRow('id='.$id);
				$row->estacao=$form->getValue('estacao');
				$row->save();
				$this->_redirect('/estacao');				
			}else{
				$form->populate($formData);				
			}
		}else{
			$id=(int)$this->_request->getParam('id',0);
			if($id>0){
				$estacoes=new Estacoes();
				$estacao=$estacoes->fetchRow('id='.$id);
				$form->populate($estacao->toArray());
			}
		}
	}//edit
	
	function delAction()
	{
		$this->view->title="(-)Estação";
		if($this->_request->isPost()){
			$id=(int)$this->_request->getPost('id');
			$del=$this->_request->getPost('del');
			if($del=='Sim' && $id>0){
				$estacoes=new Estacoes();
				$where='id=' . $id;
				$estacoes->delete($where);
			}
			$this->_redirect('/estacao');
		}else{
			$id=(int)$this->_request->getParam('id');
			if($id > 0){
				$estacoes= new Estacoes();
				$this->view->estacao=$estacoes->fetchRow('id='.$id);
				
			}
		}
	}
	
	function getForm()
	{
		$form=new Zend_Form();
        $id = new Zend_Form_Element_Hidden('id'); 
 
        $estacao = new Zend_Form_Element_Text('estacao'); 
        $estacao->setLabel('Estação') 
	        ->setRequired(true) 
	        ->addFilter('StripTags') 
	        ->addFilter('StringTrim')->addFilter('StringToUpper')
	        ->addValidator('NotEmpty')
	        ->addValidators(array(
					        	array('NotEmpty', true, 
					        		array('messages' =>
					        			array('isEmpty' => 'Insira uma Estacão')
					        			)
									)
								))
         ->addFilter('StringToUpper');
 
        $submit = new Zend_Form_Element_Submit('submit'); 
        $submit->setAttrib('id', 'submitbutton')
        	->setAttrib('class', 'button')->removeDecorator('DtDdWrapper');
 
        $form->addElements(array($id, $estacao, $submit));
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