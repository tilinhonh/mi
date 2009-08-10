<?php
class MateriaisController extends Zend_Controller_Action
{
    function init(){
		$this->materiais = new Materiais();
		$this->view->title = "Material";
    }

	function indexAction()
	{
		$this->view->material = $this->materiais->fetchAll(null, 'material');
	}

	function delAction()
	{
		if ($this->_request->isPost()){
			$id = (int) $this->_request->getPost('id');
			$del = $this->_request->getPost('del');
			if ($del == 'Sim' && $id > 0 )
			{
				$this->materiais->find($id)->current()->delete();
			}
			$this->_redirect('/materiais');
		}else{
			$id = (int) $this->_request->getParam('id');
			$this->view->material = $this->materiais->find($id)->current();
		}
	}


	function editAction()
	{
		$this->createForm('Alterar');
		$this->view->title = "Materiais";

		if ($this->_request->isPost()){
			$data = $this->_request->getPost();
			$id = (int) $this->_request->getPost('id');
			if ($this->materiais->skipDbUniqueValidation($id)->isValid($data)){
				if ($register = $this->materiais->find($id)->current()){
					$this->saveRegister($register,$data);
				}else{
					$this->view->error('Material não encontrado.');
				}
			}
			else{
				$this->view->errors( $this->materiais->getValidationMessages());
				$this->view->form->populate($data);
			}
		}//not post
		else{
			$id = (int) $this->_request->getParam('id',0);
			if ($id > 0){
				if ($material = $this->materiais->find($id)->current())
					$this->view->form->populate($material->toArray());
				else{
					$this->view->error('Material não encontrado.');
				}
			}
		}
	}


	function addAction()
	{
		$this->createForm();
		if ($this->_request->isPost()){
			$data = $this->_request->getPost();
			if ($this->materiais->isValid($data))
			{
				$register = $this->materiais->createRow();
				$this->saveRegister($register,$data);
			}
			else{
				$this->view->form->populate($data);
				$this->view->errors( $this->materiais->getValidationMessages() );
			}
		}
	}

	function createForm($action = 'Adicionar'){
		$form=new Zend_Form();
		$this->view->form=$form;

		$form->addElement('hidden','id');
		$form->id->removeDecorator('DtDdWrapper');



		$form->addElement('text','material');
		$form->material
				->setLabel('Material:')
				->setAttrib('maxlength','45')
				->setAttrib('size','45');


		$form->addElement('submit','submit');
		$form->submit
				->setAttrib('class','button')
				->setLabel($action)
				->removeDecorator('DtDdWrapper')
				;

        //no need to cancel if it is a popup window
		if (!$this->getRequest()->getParam('clean')){
            $form->addElement('button','cancelar');
            $form->cancelar
                    ->setLabel('Cancelar')
                    ->setAttrib('class','button2')
                    ->removeDecorator('DtDdWrapper')
                    ->setAttrib('onclick',"javascript:window.location.href='/materiais'")
                    ;
        }
	}


	private function saveRegister($register,$data)
	{
		$filter = new ModelFilter('Materiais', $data);
		$row = $register;

		$row->material = $filter->filter('material');
		$row->save();

		new QueryLogger();
		$this->view->flash('Material salvo');
		$this->_redirect('/materiais');
	}

}
?>