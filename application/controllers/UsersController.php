<?php
class UsersController extends Zend_Controller_Action
{
	protected $form;
	protected $row;
	
	function init() {
		$this->users = new Users();
		$this->view->title="Usuários";
	}
	function editAction()
	{
		$this->getHelper('ViewRenderer')->setScriptAction('form');
		$id = (int) $this->getRequest()->getParam('id');
		$this->_getGroups($id);
		
		if ($this->_request->isPost()) {
			$data = $this->getRequest()->getPost();
			if ($this->users->isValid($this->_getValidation($data))) {
				if ($row = $this->users->find($id)->current())
					$this->saveRegister($row, $data);
			}else{
				$this->view->errors = $this->users->getValidationMessages();
				$this->_setGroups($data['groups']);
				$this->_populateForm($data);
			}
		} else {
			$this->_populateForm($this->users->find($id)->current()->toArray());
		}
	}


	function addAction()
	{
		$this->getHelper('ViewRenderer')->setScriptAction('form');
		if ($this->_request->isPost()) {
			$data = $this->getRequest()->getPost();
			if ($this->users->isValid($this->_getValidation($data))) {
					$this->saveRegister($this->users->createRow(), $data);
			}else{
				$this->view->errors = $this->users->getValidationMessages();
				$this->_populateForm($data);
			}
		}
	}

	private function _populateForm($data = array())
	{
		$this->view->formData = $data;
	}

	private function _getValidation($data)
	{
		$validation = array(
			'nome' => $data['nome'],
			'email' => $data['email'],
		);

		return $validation;
	}
	
	
	private function _getGroups($id)
	{
		$groups = new Groups();
		$this->view->groups = $groups->fetchAll();
		$this->_getSelectedGroups($id);
	}
	
	private function _setGroups($groups = array())
	{
		$this->view->groupsOfMine = $groups;
	}

	private function _getSelectedGroups($id)
	{
		$ug = new UserGroup();
		$this->view->groupsOfMine = $ug->getGroups($id);
	}
	
	function delAction() {
		$this->view->title="(-)Usuário";
		if ($this->_request->isPost())
		{
			$id=(int)$this->_request->getPost('id');
			$del=$this->_request->getPost('del');
			if ($del == 'Sim' && $id>0) {
				$users= new Users();
				$where='id='.$id;
				$users->delete($where);
			}			
			$this->_redirect('/users');
		}
		else
		{
			$id=(int)$this->_request->getParam('id');
			$users=new Users();
			$this->view->user=$users->find($id)->current();
		}
	}	


	
	function indexAction()
	{
		$this->view->users=$this->users->fetchAll();
	}
	
	private function setForm()
	{

		//ID	
			$id=new Zend_Form_Element_Hidden('id');
		
		//NAME
			$name=new Zend_Form_Element_Text('name');
			$name->setLabel('Username:')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->setRequired(true)
				->addValidators(
						array(
							array(
								'NotEmpty',false,
								array('messages'=>
									array(
										'isEmpty'=>'Insira um nome de usuário.'
									)
								)
							)
						)
					);
		
		//ESTADO
			$email=new Zend_Form_Element_Text('email');
			$email->setLabel('Email:')
				->addFilter('StringTrim')
				->addFilter('StripTags')
				->setRequired(true)
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Adicione um email.')));
		
		//THEME
			$theme=new Zend_Form_Element_Text('theme');
			$theme->setLabel('Tema:')
					->addFilter('StringTrim')
					->addFilter('StripTags')
					->setRequired(true)
					->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Escolha um tema.')));
					
				
		//SUBMIT
			$submit=new Zend_Form_Element_Submit('submit');
			$submit->setAttrib('id','submitbutton')
				->setAttrib('class','button');
				
		//FORM
		$this->form=new Zend_Form();	
		$this->form->addElements(array($id, $name,$email,$theme,$submit));
	}
	

	private function saveRegister($row,$data)
	{
		$row->name = $data['name'];
		$row->email = $data['email'];
		$row->save();
		new QueryLogger();
		$id = $row->id ? $row->id : $row->getAdapter()->lastInsertId();
		$this->_saveGroups($data['groups'],$row->id);
		$this->_helper->redirector()->goToUrlAndExit('/users/edit/id/' . $id);
	}

	private function _saveGroups($groups = array(), $id =  null)
	{
		if($id){
			$ug = new UserGroup();
			$ug->saveGroups($groups, $id);
		}
	}
}
?>