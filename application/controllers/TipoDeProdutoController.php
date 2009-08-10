<?php
class TipoDeProdutoController extends Zend_Controller_Action
{
	function indexAction()
	{
		$this->view->title = "Tipo de Produto";
		$tipoProdutoTable=new TipoProduto();
		$select = $tipoProdutoTable->select()->order('tipo');
		$this->view->tipoProduto=$tipoProdutoTable->fetchAll($select);
	}
	
	function delAction()
	{
		$this->view->title="(-)Tipo de Produto";

		$tipoProdutoTable=new TipoProduto();
		
		if($this->_request->isPost()){
			$id=$this->_request->getParam('id');
			$del=$this->_request->getPost('del');
			if($del == 'Sim' && $id > 0 )
			{
				$where='id='.$id;
				$tipoProdutoTable->delete($where);
			}
			$this->_redirect('/tipo-de-produto');
		}else{
			$id=(int) $this->_request->getParam('id');
			$tipoProduto = $tipoProdutoTable->find($id)->current();
			$this->view->tipoProduto = $tipoProduto; 
		}
	}
	
	
	function editAction()
	{
		$this->createForm();
		$this->view->form->submit->setLabel('Alterar');
		$this->view->title = "Tipo de produto";
		
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->view->form->isValid($formData))
			{
				$id=(int)$this->_request->getPost('id');
				$tipoProdutoTable = new TipoProduto();
				$register=$tipoProdutoTable->find($id)->current();
				$this->saveRegister($register);
				
				$this->_redirect('/tipo-de-produto');
			}
			else{
				$this->view->form->populate($formData);
			}
		}//not post
		else{
			$id = (int) $this->_request->getParam('id',0);
			if($id > 0){
				$tipoProdutoTable = new TipoProduto();
				$tipoProduto = $tipoProdutoTable->find($id)->current();
				$this->view->form->populate($tipoProduto->toArray());
			}
		}
		
	}
	
	
	function addAction()
	{
		$this->view->title = '++Tipo de Produto';
		$this->createForm();
		$this->view->form->submit->setLabel('Adicionar');
		if($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if($this->view->form->isValid($formData))
			{
				$tipoProdutoTable = new TipoProduto();
				$register = $tipoProdutoTable->createRow();
				$this->saveRegister($register);
				$this->_redirect('/tipo-de-produto');
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
				->addValidator('stringLength',false,array(1,30))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Preencha o campo Tipo de Produto.')))
				;
				
		$form->addElement('textarea','descricao');
		$form->descricao
				->setLabel('Descrição:')
				->setAttrib('rows',4)
				->setAttrib('cols',30)
				->setRequired(true)
				->addFilter('StringToUpper')
                ->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('stringLength',false,array(10,255))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Preencha o campo Descrição.')))
		
			;

        $form->addElement('text','unidadeMedida');
        $form->unidadeMedida
				->setLabel('Unidade de medida:')
				->setRequired(true)
				->addFilter('StringToUpper')
                ->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('stringLength',false,array(0,20))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Preencha o campo Descrição.')))

			;

        $form->addElement('text','unidadeMedidaShort');
        $form->unidadeMedidaShort
				->setLabel('Un. Abrev.:')
				->setRequired(true)
				->addFilter('StringToUpper')
                ->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('stringLength',false,array(0,20))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Preencha o campo Descrição.')))

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
		$row->descricao = $this->view->form->getValue('descricao');
        $row->unidadeMedida = $this->view->form->getValue('unidadeMedida');
        $row->unidadeMedidaShort = $this->view->form->getValue('unidadeMedidaShort');


		$row->save();
	}
	
}
?>