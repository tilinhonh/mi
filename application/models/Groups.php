<?php
class Groups extends  AbstractModelValidator
{
	protected $_name = 'groups';
	protected $_id = 'id';
	
	protected $_dependentTables = array('UserGroup');

	protected $_hasMany = array('Users');

	protected $_rules = array(
		array('name'=>'name', 'class'=>'StringLength', 'options'=>array(0, 50),'message'=>'Name: 50 caracteres no máximo.'),
		array('name'=>'id', 'class'=>'Int', 'message' => "Try it better, LOSER."),
		array('name'=>'name', 'class'=>'NotEmpty', 'message' => "Campo 'Nome' é obrigatório."),
		array('name'=>'description', 'class'=>'NotEmpty', 'message' => "Campo 'Description' é obrigatório.")
	);
}
?>