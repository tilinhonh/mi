<?php
class GroupsController extends Zend_Controller_Action
{
	var $groups;

	public function init()
	{
		$this->groups = new Groups();
		$this->view->title = 'Groups';
		$this->view->controllerName = $this->getRequest()->getControllerName();
	}

	public function indexAction()
	{
		$this->view->groups = $this->groups->fetchAll();
	}


	public function editAction()
	{
		if($this->_request->isPost()){
			$data = $this->_request->getPost();
			if($this->groups->isValid($this->_getValidationArray($data))){
				if($row = $this->groups->find($data['id'])->current())
					$this->_saveRegister($row,$data);
			}else{
				$this->view->errors = $this->groups->getValidationMessages();
				$this->view->formValue = $data;
			}
		}else{
			if ($group = $this->groups->find($this->getRequest()->getParam('id'))->current())
				$this->view->formValue = $group->toArray();
		}
	}

	public function delAction()
	{
		if($this->_request->isPost()){
			$id = $this->getRequest()->getPost('id');
			$del = $this->getRequest()->getPost('del');
			
			if($del == 'Sim' && $id > 0){
			
				if($group = $this->groups->find($id)->current())
					$group->delete();
				$this->_redirect('/groups');
			}

		}else{
			if ($group = $this->groups->find($this->_request->getParam('id'))->current())
				$this->view->group = $group;
		}

	}


	public function addAction()
	{
		if($this->_request->isPost()){
			$data = $this->_request->getPost();
			if($this->groups->isValid($this->_getValidationArray($data))){
				$row = $this->groups->createRow();
				$this->_saveRegister($row,$data);
			}else{
				
				$this->view->errors = $this->groups->getValidationMessages();
				$this->view->formValue = $data;
			}
		}
	}

	private function _saveRegister($row,$data)
	{
		$row->name = $data['name'];
		$row->description = $data['description'];
		$row->save();
		new QueryLogger();

		$id = $data['id'] ? $data['id'] : $this->groups->getAdapter()->lastInsertId();
		$this->_redirect('/groups/edit/id/' . $id);
	}
	
	private function _getValidationArray($data = array()){
		$validation = array(
			'name'=>$data['name'],
			'description'=>$data['description']
		);

		return $validation;
	}


}
