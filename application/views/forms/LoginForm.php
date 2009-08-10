<?php
class LoginForm extends Zend_Form
{
    public function __construct($options = null)
	{
		parent::__construct($options);
    
        
        $this->addElement('text','name')
            ->name
                ->setLabel('Username:')  
                ->setRequired(true);  
                
        $this->addElement('password','password')
            ->password
                ->setLabel('Senha:')  
                ->setRequired(true);  
            
            
        $this->addElement('submit','submit')
            ->submit
                ->setLabel('Entrar')
                ->setAttrib('class','button');        
    }
}
?>