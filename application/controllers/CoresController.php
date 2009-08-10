<?php
class CoresController extends Zend_Controller_Action
{
    function init(){
		$this->cores = new Cores();
		$this->view->title = "Cor";
    }

	function indexAction()
	{
		$this->view->cor = $this->cores->fetchAll(null, 'cor');
	}
	
	function delAction()
	{
		if ($this->_request->isPost()){
			$id = (int) $this->_request->getPost('id');
			$del = $this->_request->getPost('del');
			if($del == 'Sim' && $id > 0 )
			{
				$this->cores->find($id)->current()->delete();
			}
			$this->_redirect('/cores');
		}else{
			$id = (int) $this->_request->getParam('id');
			$this->view->cor = $this->cores->find($id)->current();
		}
	}
	
	
	function editAction()
	{
		$this->createForm();
		$this->view->form->submit->setLabel('Alterar');
		$this->view->title = "Cores";
		
		if($this->_request->isPost())
		{
			$data = $this->_request->getPost();
			$id = (int) $this->_request->getPost('id');
			if($this->cores->skipDbUniqueValidation($id)->isValid($data))
			{
				$register = $this->cores->find($id)->current();
				$this->saveRegister($register,$data);
			}
			else{
				$this->view->errors( $this->cores->getValidationMessages());
				$this->view->form->populate($data);
			}
		}//not post
		else{
			$id = (int) $this->_request->getParam('id',0);
			if($id > 0){
				$cor = $this->cores->find($id)->current();
				$this->view->form->populate($cor->toArray());
			}
		}
		
	}
	
	
	function addAction()
	{
		$this->createForm();
		$this->view->form->submit->setLabel('Adicionar');
		if ($this->_request->isPost()){
			$data = $this->_request->getPost();
			if($this->cores->isValid($data))
			{
				$register = $this->cores->createRow();
				$this->saveRegister($register,$data);
			}
			else{
				$this->view->form->populate($data);
				$this->view->errors( $this->cores->getValidationMessages() );
			}
		}
	}
	
	function createForm(){
		$form=new Zend_Form();
		$this->view->form=$form;
		
		$form->addElement('hidden','id');
		$form->id->removeDecorator('DtDdWrapper');

				
		
		$form->addElement('text','cor');
		$form->cor
				->setLabel('Cor:')
				->setAttrib('maxlength','45')
				->setAttrib('size','45')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(1,45))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Preencha o campo Tamanho.')))
				;
				
		
		$form->addElement('submit','submit');
		$form->submit
				->setAttrib('class','button')
				->removeDecorator('DtDdWrapper')
				;

        //no need to cancel if it is a popup window
		if(!$this->getRequest()->getParam('clean')){
            $form->addElement('button','cancelar');
            $form->cancelar
                    ->setLabel('Cancelar')
                    ->setAttrib('class','button2')
                    ->removeDecorator('DtDdWrapper')
                    ->setAttrib('onclick',"javascript:window.location.href='/cores'")
                    ;
        }
	}
	
	
	private function saveRegister($register,$data)
	{
		$filter = new ModelFilter('Cores', $data);
		$row = $register;
		
		$row->cor = $filter->filter('cor');
		$row->save();

		new QueryLogger();
		$this->view->flash('Cor salva');
		$this->_redirect('/cores');
	}
	
}
?>