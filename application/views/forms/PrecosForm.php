<?php
class PrecosForm extends Zend_Form
{
	function __construct($options = null)
	{
		parent::__construct($options);

        $this->setAttrib('id','formPrecos');
		
		$this->addElement('hidden','precoID');
		
		$this->addElement('text','fabricaID')
		    ->fabricaID
				->setRequired(true)
				->setAttrib('size','30')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('Digits');

        $this->addElement('text','combinacaoID')
		    ->combinacaoID
				->setRequired(true)
				->setAttrib('size','30')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('Digits');

				
		$this->addElement('text','pvl')
		    ->pvl
				->addFilter('StripTags')
				->addFilter('StringTrim');


        $this->addElement('text','pFabrica')
		    ->pFabrica
				->addFilter('StripTags')
				->addFilter('StringTrim');


         $this->addElement('text','pVenda')
		    ->pVenda
				->addFilter('StripTags')
				->addFilter('StringTrim');


         $this->addElement('text','dataQuotacao')
		    ->dataQuotacao
				->addFilter('StripTags')
				->addFilter('StringTrim');

	}
}
?>