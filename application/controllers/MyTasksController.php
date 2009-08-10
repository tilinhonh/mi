<?php
class MyTasksController extends Zend_Controller_Action
{
	public function indexAction()
    {
        $tasks = new MyTasks();
        $form = new MytasksForm();
        $form->clearDecorators();
        $form->my_tasks_status_id->addMultiOption("","ALL");
        $form->my_tasks_status_id->setValue(3);
        $this->view->my_tasks_status_id = $form->my_tasks_status_id;
        $form->my_tasks_priority_id->addMultiOption("","ALL");
        $form->my_tasks_priority_id->setValue("");
        $this->view->my_tasks_priority_id = $form->my_tasks_priority_id;
        $this->view->status_not = "CHECKED";


        $db = $tasks->getAdapter();

        $select = $tasks->select();

        $select = $db->select()
                        ->from(array('tasks'=>'my_tasks'),
                            array('id',
                                    'task_title' =>'title',
                                    'task_date',
                                    'task_finish_date' =>'finish_date',
                                    'task_description' =>'description'
                            )
                        )
                        ->join(array('priority'=>'my_tasks_priorities'),
                               'tasks.my_tasks_priority_id = priority.id',
                                array('priority_name'=>'name',
                                    'priority'=>'order'
                                )
                          )
                       ->join(array('status'=>'my_tasks_status'),
                               'tasks.my_tasks_status_id = status.id',
                                array('status_name'=>'name')
                         )
                       ->order('priority');

        


        if($this->getRequest()->isPost()){
            $status = $this->getRequest()->getPost('my_tasks_status_id');
            $priority = $this->getRequest()->getPost('my_tasks_priority_id');
            $text = $this->getRequest()->getPost('text');
            $priority_not = $this->getRequest()->getPost('priority_not');
            $status_not = $this->getRequest()->getPost('status_not');

            if($status){
                $operator = $status_not ? '!=' : '=';
                if(!$status_not){
                    $this->view->status_not = "";
                }
                $select = $select->where('my_tasks_status_id '.$operator.' ?', $status);
                $this->view->my_tasks_status_id->setValue($status);
            }
            if($priority){
                $operator = $priority_not ? '!=' : '=';
                if($priority_not){
                    $this->view->priority_not = "checked";
                }
                $select = $select->where('my_tasks_priority_id '.$operator.' ?', $priority);
                $this->view->my_tasks_priority_id->setValue($priority);
            }
            if($text){
                $select = $select->where('description like ?', "%".$text."%");
                $this->view->text = $text;
            }

        }

        $stmt = $select->query();
        $this->view->tasks = $stmt->fetchAll();

        //die(print_r($this->view->tasks));
    }


    public function newAction()
    {
        $this->view->form = new MytasksForm();

        if($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            if($this->view->form->isValid($formData)){
                $tasks =  new MyTasks();
                $row = $tasks->createRow();
                $this->_saveRegister($row,$this->view->form);
            }else{
                $this->view->form->populate($formData);
            }
        }
    }

    public function editAction()
    {
        $this->view->form = new MytasksForm();
        $tasks =  new MyTasks();

        if($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            if($this->view->form->isValid($formData)){

                $id = $this->view->form->getValue('id');

                $row = $tasks->find($id)->current();
                $this->_saveRegister($row,$this->view->form);
            }else{
                $this->view->form->populate($formData);
            }
        }else{
            $id = (int) $this->getRequest()->getParam('id');
            $task = $tasks->find($id)->current();
            $data = $task->toArray();
            $this->view->form->populate($data);
        }
    }




    private function _saveRegister($row,$form)
    {
        $row->title = $form->getValue('title');
        $row->description = $form->getValue('description');
        $row->my_tasks_status_id = $form->getValue('my_tasks_status_id');
        $row->my_tasks_priority_id = $form->getValue('my_tasks_priority_id');

        if($this->getRequest()->getActionName() == 'new'){
            $row->task_date	= Zend_Date::now()->toString('YYYY-MM-dd');
        }else{
            $row->finish_date = $form->getValue('status_id') == 3 ?
                        Zend_Date::now()->toString('YYYY-MM-dd'): 0;
        }
        

        $row->save();
        new QueryLogger();
        $this->_redirect("/my-tasks");
    }


    function delAction(){
        $tasks = new MyTasks();
        if($this->getRequest()->isPost()){
            $id = (int) $this->getRequest()->getPost('id');
            $del = $this->getRequest()->getPost('del');
            if($del == 'yes'){
                $where = 'id =' . $id;
                $tasks->delete($where);
                new QueryLogger();
            }
            $this->_redirect('/my-tasks');
        }else{
            $id = (int) $this->getRequest()->getParam('id');
            $this->view->task = $tasks->find($id)->current();
        }
    }

}
