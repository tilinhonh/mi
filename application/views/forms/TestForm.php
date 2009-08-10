<?php
class TestForm extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);


		
		$this->setName('test');

		
		$this->addElement('text','name');
		$this->name
				->clearDecorators()
				->addDecorator('Label')
				->addDecorator('ViewHelper')
				->setLabel('Nome:');
		
		$this->addElement('text','address');
		$this->address
				->setLabel('Address:');
				
		$this->addElement('text','outro');
		$this->outro->setLabel('Outro:');
		
		$this->addElement('text','maisum');
		$this->maisum->setLabel('Mais Um:');

		

		$this->addElement('submit','sim');
		$this->sim
			->setValue('No way')
			->setAttrib('class','button2');
				

		$this->addElement('submit','nao');
		$this->nao
				->setValue('Não')
				->setAttrib('class','button2');
				
				
		$this->clearDecorators();
		$this->setDecorators(array('FormElements', 'Form'));
		
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'table')), 
			'Form'	
			));
			
			
	// Fields that have already been added to the form
	$elements = array('name', 'address');

	// Create a display group within a form
	$this->addDisplayGroup($elements, 'group');



	// Retrive the display group to apply decorators
	$this->getDisplayGroup('group')
	     ->setLegend('Grouped')
	     ->clearDecorators()
	     ->addDecorator('FormElements')
	     ->addDecorator('Fieldset');
	     
	     $this->removeElementDecorators();
			
				
	}
}
?>