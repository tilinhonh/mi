<?php 
//bug report configuration 
	#error_reporting(E_ALL|E_STRICT); 
	#ini_set('display_errors',0); 


//regional configuration
	setlocale (LC_ALL, 'pt_BR');
    date_default_timezone_set('America/Sao_Paulo');


// directory setup and class loading 
	set_include_path('.' . PATH_SEPARATOR . '../library/' 
	     . PATH_SEPARATOR . '../application/models' 
	     . PATH_SEPARATOR . '../application/classes'
	     . PATH_SEPARATOR . '../application/views/forms'
	     . PATH_SEPARATOR . get_include_path()); 
	     
	#include "Zend/Loader.php";
	require_once 'Zend/Controller/Front.php';
 
	Zend_Loader::registerAutoload(); 

//blah
    //LOCALE
    $locale = new Zend_Locale('pt_BR');
    Zend_Registry::set('Zend_Locale', $locale);

// initializes database 
	new DbInitialize();
	
	$view = new Zend_View();
	$view->addHelperPath('../application/views/helper','My_View_Helper');

//starts mvc
	#$options=array('layoutPath'=>'../application/views/layouts');
	$options = array(
		'layout' => 'layout',
		'layoutPath'=>'../application/views/layouts',
		'layoutContent' => '../application/views/layouts/',
		'contentKey' => 'content'	);
	Zend_Layout::startMvc($options);
	


	
// setup controller 
$frontController = Zend_Controller_Front::getInstance(); 
$frontController->setControllerDirectory('../application/controllers');
//Turn ON on developemnt enviroment 
#$frontController->throwExceptions(true);

	//Zend_Session::start();

// run! 
	$frontController->dispatch();
