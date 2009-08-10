<?php
class Tests extends AbstractModelValidator
{
    protected $_name = "test";
    
    protected $_rules = array(
		array('name'=>'nome', 'class'=>'StringLength', 'options'=>array(0, 20),'message'=>'Tamanho máximo é 20 caracteres.'),
		array('name'=>'sobrenome', 'class'=>'StringLength', 'options'=>array(0, 20),'message'=>'Tamanho máximo é 20 caracteres.'),
        array('name'=>'email', 'class'=>'EmailAddress', 'options'=>array(),'message'=>'Email inválido.'),
        array('name'=>'email', 'class'=>'StringLength', 'options'=>array(0, 20),'message'=>'Tamanho máximo é 80 caracteres.'),
	);

    public function  init()
    {
        $_mandatoryFields = array(
            'nome' =>'Nome',
           // 'email' =>'Email',
            'sobrenome' =>'Sobrenome'
        );

        foreach($_mandatoryFields as $name => $alias):
            $this->_rules[] = array('name'=>$name, 'class'=>'NotEmpty', 'message' => "$alias é obrigatório.");
        endforeach;
    }
}