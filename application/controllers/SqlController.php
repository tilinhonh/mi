<?php

/**
 * Description of SqlController
 *
 * @author marcelo
 */
class SqlController extends Zend_Controller_Action
{
	public function init()
	{
		$this->view->title = "SQL";
	}

	public function indexAction(){
		$this->_helper->layout()->setLayout('sql');
		if($this->getRequest()->isPost()){
			$sql = $this->getRequest()->getPost('sql');
			
			$db = Zend_Db_Table::getDefaultAdapter();

			try{
				$this->view->resultset = $db->fetchAll($sql, null, Zend_Db::FETCH_ASSOC);

				


			}catch (Exception $e){
				$this->view->errors($e->getMessage());
							//->addMessages(explode('#',$e->getTraceAsString()));
			}
			$this->view->sql = $sql;
		}
	}
}
?>
