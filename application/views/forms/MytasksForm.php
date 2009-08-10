<?php
class   MytasksForm extends Zend_Form
{
	function __construct($options = null)
	{
		parent::__construct($options);
		
		
		$this->addElement('hidden','id');
		$this->id->removeDecorator('DtDdWrapper');

		
		$this->addElement('text','title');
		$this->title
				->setLabel('Title')
				->setAttrib('maxlength','255')
				->setAttrib('size','80')
                ->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('stringLength',false,array(0,255))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
				;


        $this->addElement('select','my_tasks_status_id')
		    ->my_tasks_status_id
                ->setRequired(true)
		       ->setLabel('Status:');

    	$this->_popularStatus();


        $this->addElement('select','my_tasks_priority_id')
		    ->my_tasks_priority_id
		       ->setLabel('Priority:')
               ->setRequired(true);

    	$this->_popularPriorities();


        $this->addElement('textarea','description');
		$this->description
				->setLabel('Description:')
                ->setAttrib('rows','15')
                ->setAttrib('cols','90')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
				;



		$this->addElement('submit','submit')
		    ->submit
				->setValue('Opa')
				->setAttrib('class','button')
				->removeDecorator('Label')
				->removeDecorator('DtDdWrapper');
				
		$location= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/transportadoras';
		$this->addElement('button','cancelar')
		    ->cancelar
				->setValue('Algo')
				->setAttrib('class','button2')
				->setAttrib('onclick',"javascript:window.location.href='".$location."'")
				->removeDecorator('DtDdWrapper')
				->removeDecorator('Label');

	}
	
    private function _popularStatus()
	{
		$status=new MyTasksStatus();
        $select = $status->select()->order('name');
		foreach($status->fetchAll($select) as $s){
			$this->my_tasks_status_id->addMultiOption($s->id,$s->name);
		}
        $this->my_tasks_status_id->setValue(2);
	}

    private function _popularPriorities()
	{
		$priorities=new MyTasksPriorities();
        $select = $priorities->select()->order('order');
		foreach($priorities->fetchAll($select) as $s){
			$this->my_tasks_priority_id->addMultiOption($s->id,$s->name);
		}
        $this->my_tasks_priority_id->setValue(1);
	}
}
?>