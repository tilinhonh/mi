<?php
class MyTasksStatusController extends Zend_Controller_Action
{
	public function indexAction()
    {
        $status = new MyTasksStatus();
        $this->view->status = $status->fetchAll();
    }

    public function newAction()
    {
        $this->view->form = new MytasksstatusForm();

        if($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            if($this->view->form->isValid($formData)){
                $status =  new MyTasksStatus();
                $row = $status->createRow();
                $this->_saveRegister($row,$this->view->form);

                $this->_redirect("/my-tasks-status");
            }else{
                $this->view->form->populate($formData);
            }
        }
    }
    
    public function editAction()
    {
        $this->view->form = new MytasksstatusForm();
        $status =  new MyTasksStatus();

        if($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            if($this->view->form->isValid($formData)){

                $id = $this->view->form->getValue('id');
                
                $row = $status->find($id)->current();
                $this->_saveRegister($row,$this->view->form);

                $this->_redirect("/my-tasks-status");
            }else{
                $this->view->form->populate($formData);
            }
        }else{
            $id = (int) $this->getRequest()->getParam('id');
            $currentStatus = $status->find($id)->current();
            $data = $currentStatus->toArray();
            $this->view->form->populate($data);
        }
    }




    private function _saveRegister($row,$form)
    {
        $row->name = $form->getValue('name');
        $row->description = $form->getValue('description');
        $row->save();
        new QueryLogger();
    }


    function delAction(){
        $status = new MyTasksStatus();
        if($this->getRequest()->isPost()){
            $id = (int) $this->getRequest()->getPost('id');
            $del = $this->getRequest()->getPost('del');
            if($del == 'yes'){
                $where = 'id =' . $id;
                $status->delete($where);
                new QueryLogger();
            }
            $this->_redirect('/my-tasks-status');
        }else{
            $id = (int) $this->getRequest()->getParam('id');
            $this->view->status = $status->find($id)->current();
        }
    }

}

?>