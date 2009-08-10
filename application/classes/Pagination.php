<?php
class Pagination extends Zend_Controller_Action
{
	protected $page;
	protected $resultsPerPage;
	
	 function Pagination()
	{
		$this->page = (is_numeric(CidadesController::$_request->getParam('page'))) ? (int)CidadesController::$_request->getParam('page') : 0 ;
		$this->resultsPerPage = (is_numeric(CidadesController::$_request->getParam('resultsPerPage'))) ? (int)CidadesController::$_request->getParam('resultsPerPage') : 50 ;
		
		$this->view->nextPage=$this->page + 1;
		$this->view->backPage=($this->page>0) ? $this->page - 1: 0;
	}
}
?>