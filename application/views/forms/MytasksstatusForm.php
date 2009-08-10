<?php
class MytasksstatusForm extends Zend_Form
{
	function __construct($options = null)
	{
		parent::__construct($options);
		
		
		$this->addElement('hidden','id');
		$this->id->removeDecorator('DtDdWrapper');

		
		$this->addElement('text','name');
		$this->name
				->setLabel('Status')
				->setAttrib('maxlength','30')
				->setAttrib('size','30')
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,255))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
				;

		$this->addElement('textarea','description');
		$this->description
				->setLabel('Description:')
				->setAttrib('rows','5')
				->setAttrib('cols','35')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,300))
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Campo obrigatório.')))
				;
				
		
		$this->addElement('submit','submit')
		    ->submit
				->setValue('Insert')
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
	
}
?>