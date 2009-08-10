<?php
class My_Exception_RegisterNotFound extends Zend_Exception
{
    public function __construct($options=null)
    {
        if(!$options){
            $options="Registro não encontrado.";
        }
        parent::__construct($options);
    } 
}
?>