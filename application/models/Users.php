<?php
class Users extends  AbstractModelValidator
{
	protected $_name = 'users';
	protected $_id = 'id';
	
	protected $_dependentTables = array('UserGroup');


	protected $_rules = array(
		array('name'=>'name', 'class'=>'NotEmpty', 'options'=>array(), 'message'=>"Escolha um nome."),
		array('name'=>'email', 'class'=>'NotEmpty', 'options'=>array(), 'message'=>"Escolha um Email."),
		array('name'=>'email', 'class'=>'EmailAddress', 'options'=>array(), 'message'=>"Escolha um Email."),
	);
	
	
	public function getGroups($x)
	{
		$user=$this->find($x)->current();
		
		$groups=$user->findManyToManyRowset(
				'Groups',
				'UserGroup'
			);
			
		return $groups;
		
	}
}
?>