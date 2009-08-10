<?php
class NcmsController extends Zend_Controller_Action
{
     function init(){
        if($this->getRequest()->getParam('popup')==true)
            $this->_helper->layout()->setLayout('popup');

        if($this->getRequest()->getParam('close')==true)
            $this->view->close=true;
    }

	function indexAction()
	{
		$this->view->title = "NCMS - Nomenclatura Comum no Mercosul";
		$ncmsTable=new Ncms();
		$select = $ncmsTable->select()->order('codigo');
		$this->view->ncms=$ncmsTable->fetchAll($select);
								
								
	}
	
	function delAction()
	{
		$this->view->title="(-)NCMS";

		$ncmsTable=new Ncms();
		
		if($this->_request->isPost()){
			$id=$this->_request->getParam('id');
			$del=$this->_request->getPost('del');
			if($del == 'Sim' && $id > 0 )
			{
				$where='id='.$id;
				$ncmsTable->delete($where);
			}
			$this->_redirect('/ncms');
		}else{
			$id=(int) $this->_request->getParam('id');
			$ncms = $ncmsTable->find($id)->current();
			$this->view->ncms = $ncms; 
		}
	}
	
	
	function editAction()
	{
		$this->createForm();
		$this->view->form->submit->setLabel('Edit');
		$this->view->title = "NCMS - Nomenclatura Comum no Mercosul";
		
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->view->form->isValid($formData))
			{
				$id=(int)$this->_request->getPost('id');
				$ncmsTable = new Ncms();
				$register=$ncmsTable->find($id)->current();
				$this->saveRegister($register);
				
				$this->_redirect('/ncms');
			}
			else{
				$this->view->form->populate($formData);
			}
		}//not post
		else{
			$id = (int) $this->_request->getParam('id',0);
			if($id > 0){
				$ncmsTable = new Ncms();
				#$ncms = $ncmsTable->fetchRow('id='.$id);
				$ncms = $ncmsTable->find($id)->current();
				$this->view->form->populate($ncms->toArray());
			}
		}
		
	}
	
	
	function addAction()
	{
		$this->view->title = '++NCMS';
		$this->createForm();
		$this->view->form->submit->setLabel('Adicionar');
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->view->form->isValid($formData))
			{
				$ncmsTable = new Ncms();
				$register = $ncmsTable->createRow();
				$this->saveRegister($register);
                $location=$this->view->close=true ? '/mensagem/registro-adicionado' : '/ncms';
				$this->_redirect($location);
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

				
		
		$form->addElement('text','codigo');
		$form->codigo
				->setLabel('Codigo:')
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->setAttrib('maxlength','10')
				->setAttrib('size','10')
				->setRequired(true)
				->addValidator('stringLength',false,array(1,10))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Preencha o campo Código.')))
				
				;
				
		$form->addElement('textarea','descricao');
		$form->descricao
				->setLabel('Descrição:')
				->setAttrib('rows',4)
				->setAttrib('cols',30)
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,255))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Preencha o campo Descrição.')))
		
			;
		
		$form->addElement('submit','submit');
		$form->submit
				->setAttrib('class','button')
				->removeDecorator('DtDdWrapper')
				;
        if( ! $this->getRequest()->getParam('popup')){
            $form->addElement('button','cancelar');
            $form->cancelar
                    ->setLabel('Cancelar')
                    ->setAttrib('class','button2')
                    ->removeDecorator('DtDdWrapper')
                    ->setAttrib('onclick',"javascript:window.location.href='".$_SERVER['HTTP_REFERER']."'")
                    ;
        }
	}
	
	
	private function saveRegister($register)
	{
		$row=$register;
		$row->codigo = $this->view->form->getValue('codigo');
		$row->descricao = $this->view->form->getValue('descricao');
		$row->save();
	}
	
}
?>