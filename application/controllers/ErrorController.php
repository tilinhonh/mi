<?php

/**
 * ErrorController - The default error controller class
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php' ;

class ErrorController extends Zend_Controller_Action {

	/**
	 * This action handles  
	 *    - Application errors
	 *    - Errors in the controller chain arising from missing 
	 *      controller classes and/or action methods
	 */
	public function errorAction () 
	{
		$errors = $this->_getParam ('error_handler');
        switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER :
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION :
            //( 'HTTP/1.1 404 Not Found' ) ;
            $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
				
				$this->view->title = 'Página não encontrada.';
				break ;
			default :
				// application error; display error page, but don't change                
				// status code
               $e = $errors->exception;
               
               if('ExceptionRegisterNotFound' == get_class($e)){
                   $this->view->title = $e->getMessage();
               }else{
                       $this->view->title = 'Erro de aplicação.';
                       $this->_logError($e);
                       $this->view->message = $errors->exception;
               }
    		break ;
		}
	}
	
	private function _logError($exception)
	{
	    $log = new Zend_Log(
                    new Zend_Log_Writer_Stream(
                        
                        '/var/log/zend/'.$_SERVER['HTTP_HOST'] . '.log'			
                    )
                );
        $log->debug(
            "\n--------------------------------------------------\n" .
            "IP:\t$_SERVER[REMOTE_ADDR]\t\tURL:\t$_SERVER[REDIRECT_URL]".
            "\n--------------------------------------------------\n" .
            $exception->getMessage() . "\n" .
            $exception->getTraceAsString()
         );
	}
	
}
