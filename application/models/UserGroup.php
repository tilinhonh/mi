<?php
class UserGroup extends Zend_Db_Table_Abstract
{
	protected $_name = 'userGroup';
	protected $_id = 'id';
	
	protected $_referenceMap=array(
		'User' => array(
			'columns'	=>	'uid',
			'refTableClass'	=> 'Users',
			'refColumns' =>'id',
			'onDelete' => self::RESTRICT
		),
		'Group' => array(
			'columns'	=>	'gid',
			'refTableClass'	=> 'Groups',
			'refColumns' =>'id',
			'onDelete' => self::RESTRICT
		)
	);

	/**
	 *Return groups of a given user
	 *
	 * @param int $userID
	 * @return array
	 */
	public function getGroups($userID)
	{
		$where = $this->getAdapter()->quoteInto('uid = ?',$userID);
		$groups = array();
		foreach($this->fetchAll($where) as $row){
			$groups[] = $row->gid;
		}
		return $groups;
	}

	/**
	 * Save groups
	 * @param array $groups
	 * @param int $userID
	 */
	public function saveGroups($groups = array(), $userID = null)
	{
		$where = $this->getAdapter()->quoteInto('uid = ?',$userID);
		foreach($groups as $group){
			try{
				$row = $this->createRow();
				$row->gid = $group;
				$row->uid = $userID;
				$row->save();
				new QueryLogger();
			}catch(Exception $e){
				//register exists
			}
		}

		$this->_deleteUserGroup($groups,$userID);
	}

	/**
	 * Delete a userGroup relashionship
	 *
	 * @param array $groups
	 * @param int $userID
	 */
	private function _deleteUserGroup($groups = array(),$userID = null)
	{
		$userID = $this->getAdapter()->quote($userID);
		$groups = $this->getAdapter()->quote($groups);
		$where = 'gid NOT IN ('. $groups .') AND uid =' . $userID ;
		$this->delete($where);
		new QueryLogger();
	}


	
}
?>