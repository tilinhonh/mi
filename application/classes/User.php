<?php
class User
{
	protected $uid;
	protected $groups;
	protected $user;
	
	public function __construct()
	{
		
		$this->uid = 1;
		
		$userTable = new Users();
		
		$this->user = $userTable
						->find( $this->uid )
						->current();
						
		$this->groups = $userTable->getGroups( $this->uid );
		
	}
	
	
	public function dumpGroups()
	{
		
		foreach($this->groups as $group)
		{
			echo $group->name . '<br/>';
		}
		
	}
	
	
	public function belongsToGroup($groupname)
	{
		//tests whether user fits a given groupname
		foreach($this->groups as $group)
		{
			if($group->name == $groupname)
			{
				return true;
			}
		}
		return false;
	}
	
	

}
?>