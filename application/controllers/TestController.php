<?php
class TestController extends  Zend_Controller_Action
{
    public function indexAction()
    {
        $estados = new Estados();
        $this->view->estados=array();
        foreach($estados->fetchAll() as $estado){
            $this->view->estados[$estado->id] = $estado->sigla;
        }
        if($this->getRequest()->isPost()){
            $test = new Tests();
            if($test->isValid($this->getRequest()->getPost())){
                $this->view->message = "Post OK.";
                $row = $test->createRow();
                $row->nome = strtoupper($this->getRequest()->getPost('nome'));
                $row->sobrenome = strtoupper($this->getRequest()->getPost('sobrenome'));
                $row->email = strtoupper($this->getRequest()->getPost('email'));
                $row->estado = strtoupper($this->getRequest()->getPost('estado'));
                $row->save();
            }
            else{
                $this->view->message = "######## ERROR #######.";
                $this->view->errors = $test->getValidationMessages();
                $this->view->formValue = $this->getRequest()->getPost();
            }
        }

    }


    public function addAction() {
		$this->view->req = $this->_request;
		if($this->_request->isPost()) {
			$u = new User();
			if($u->isValid($this->_request->getParams())) {

			}
			else {
				$this->view->errors = $u->getValidationMessages();
			}
		}
	}

	public function test1Action()
	{
		$options = array('field'=>'id','value'=>'1');
		$unique = new Zend_Validate_Db_NoRecordExists('users','name',$options);
		if($unique->isValid('marcelo')){
			$msg = "There's no such user. Yet.";
		}else{
			$msg = "User exists.";
		}

		die('<h3>'. $msg . '</h3>');
	}

	public function test2Action()
	{
		$value = 'marcelo';
		$args = array('users','name',array('field'=>'id','value'=>'1'));


		$doNotExist = Zend_Validate::is($value, 'Db_NoRecordExists', $args);
		
		
		$msg = $doNotExist ? "There's no such user. Yet." : "User exists." ;

		die('<h3>'. $msg . '</h3>');
	}

	public function test3Action()
	{
		$this->view->flash(array('Looks like flash is working', 'Or is it?'));
		$this->_redirect('/test/test4');
	}
	
	public function test4Action()
	{
		$this->view->errors('Errors');
	}

	public function test5Action()
	{
		$value = '1.235,12345';
		$prefix = 'Zend_Filter_';
		$postfix = 'LocalizedToNormalized';
		$class = $prefix.$postfix;
		$filter = new $class(array('precision'=>2));
		$filtered = $filter->filter($value);

		die('filtered: ' . $filtered);
	}
	
	public function test6Action()
	{
	
		$value = '           Meu Nome é Marcelo';
		
		$filter = new ModelFilter('Cores');
		$data = array(
			'cor'=>$value,
			'preco'=>'158.584,12358'
		);
		$filter->setData($data);
	
		$filtered = $filter->filter('preco','-4.585,98756 ');

		die('filtered: ' . $filtered);
	}


	public function test7Action()
	{

		$user = new Users();
		$marcelo = $user->find(1)->current();
		$groups = new Groups();
		$userGroups = new UserGroup();


		$marceloGroups = $marcelo->findGroupsViaUserGroup();

		die('<pre>'. print_r($marceloGroups,1) . '</pre>');


	}

	public function test8Action()
	{
		$acl = new Zend_Acl();

		$acl->addRole(new Zend_Acl_Role('guest'))
			->addRole(new Zend_Acl_Role('member'))
			->addRole(new Zend_Acl_Role('admin'));

		$parents = array('guest', 'member', 'admin');
		$acl->addRole(new Zend_Acl_Role('someUser'), $parents);

		$acl->add(new Zend_Acl_Resource('someResource'));

		$acl->deny('guest', 'someResource');
		$acl->allow('member', 'someResource');

		echo $acl->isAllowed('someUser', 'someResource') ? 'allowed' : 'denied';

		die();

	}

	
	public function test9Action()
	{


		$users = new Users();
		$user = $users->find(1)->current();
		
		
		$_groups = new Groups();

		$acl = new Zend_Acl();

		//cria resources
		foreach($_groups->fetchAll() as $group){
			$acl->add(new Zend_Acl_Resource($group->name));
		}
		//cria role pra usuario
		$acl->addRole(new Zend_Acl_Role('marcelo'));

		//adiciona permite usuario aos devidos resources
		foreach($user->findGroupsViaUserGroup() as $group){
			$acl->allow('marcelo',$group->name);
		}

		echo $acl->isAllowed('marcelo', 'preco fabrica escrita') ? 'allowed' : 'denied';
			
		die();

	}

	public function test10Action()
	{
		$this->session->user = $this->_getParam('user');
	}

	public function test11Action()
	{
		die('user: '.$this->session->user);
	}
	public function test12Action()
	{
		die('user: '.$this->marcelo);
	}

	public function cpfAction()
	{
		$this->view->script=array();
		$this->view->script[]="jquery.js";
		$this->view->script[]='Test.js';
		$this->view->script[]='Cpf.class.js';
		$this->view->script[]='Cnpj.class.js';
	}

	public function preDispatch()
	{
		$this->session = new Zend_Session_Namespace('My_User_Perms');
	}

	public function cpfValidAction(){

		$_cpf = '';
		$cpf = new My_Validate_Cpf();

		$str = $cpf->isValid('00980855063') ? 'Valido' : 'INVALIDO';

		$str = $cpf->getMessages();

		die(print_r($str,1));

		die('Cpf é ' . $str);

	}


	public function cpfValid2Action(){

		$value = '00980855063';

		$str = Zend_Validate::is($value, 'CPF', array(), array('My_Validate') ) ? 'Valido' : 'INVALIDO';



		die('Cpf é ' . $str);

	}

	public function regexpAction()
	{
		$cnpj = '08.052.387/0001-31';
		$_cnpj = '/^(\d){2}(\.\d{3}){2}\/(\d){4}-(\d){2}$/';
		
		$other = "123456";
		$_other = "/\d/";

		$pattern = $_cnpj;
		$subject = $cnpj;
		$result = preg_match($pattern, $subject);

		die((string) $result);


	}


	public function cnpjAction()
	{
		$bool = false;

		$cpf = new My_Validate_CPFOrCNPJ($bool);

		$value = $bool ? '97161129000112' :'97.161.129/0001-12';
		
		echo $cpf->isValid($value) ? 'Valido' : 'Invalido';

		die();
	}


	public function testCnpjCpfAction()
	{
		$text = new Zend_Form_Element_Text('text');
		$text->addValidator(new My_Validate_CPFOrCNPJ(true));

		$value = '123456789-09';
		$message = $text->isValid($value) ? array('notValid'=>'Valido') : $text->getMessages();

		echo $message['notValid'];
		exit();
	}


	public function validatorsForStringLengthAction()
	{
		$array = array(
			'fantasia'=> 45, 'nome'=>45, 'endereco'=>80, 'numero'=>10,
			'complemento'=> 10, 'bairro'=> 45,  'cep'=>10, 'cnpj'=>18,
			'email' =>80, 'contato' => 45,
		);

		/*
			'StringLength' => array(
					'options'=>array(0, 10),
					'message'=>'Id deve ter no máximo 10 caracteres.'
				)
		 //*/

		foreach($array as $k => $v){
			echo "'StringLength' => array(<br />";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;'options'=>array(0, " . $v . "),<br />";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;'message'=>\"'". ucfirst($k) ."' at||| ". $v." caracteres.\"<br />";
			echo "),<br />";
		}
		exit();
	}



	function currencyAction(){
		$value = $this->_request->getParam('value');

		$currency = new Zend_Currency('pt_BR');

        $currency->setFormat(array(
                    'display'=>Zend_Currency::NO_SYMBOL
        ));


		Zend_Debug::dump($toCurrency);

		exit();
	
	}


	function clientAction()
	{
		$clientes = new Clientes();
		$cliente = $clientes->find(6)->current();
		$total  = $clientes->getSaldoDisponivel($cliente);
		Zend_Debug::dump($total);
		exit();
	}


	function paginacaoAction()
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql = 'SELECT * FROM cidades';
		$result = $db->fetchAll($sql);
		$page = $this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);
		$paginator->setItemCountPerPage(50);
		$paginator->setCurrentPageNumber($page);

		$this->view->paginator=$paginator;
		
	}

	function fileTransferAction()
	{
		$this->view->title = "Upload An image";
		if($this->getRequest()->isPost()){
			$filename = 'pic123.jpg';
			$path = $_SERVER['DOCUMENT_ROOT'] . '/images/_produtos/';
			$upload = new Zend_File_Transfer_Adapter_Http();
			$file =  $upload->getFileInfo();
			
			$upload->setDestination($path)
					->addFilter('Rename', array(
									'source' =>$file['image']['tmp_name'],
									'target' => $path . $filename,
									'overwrite' => false ,
								));

			if (!$upload->receive()) {
				$this->view->errors($upload->getMessages());
			}

			Zend_Debug::dump($file);
		}
	}
	function fileTransfer2Action()
	{

		$this->view->title = "ProdutoPicture";

		if($this->getRequest()->isPost()){

			$produto = new Produtos();
			$produto = $produto->find(11)->current();

			$picture = new ProdutoPicture($produto);
			
			if (!$picture->upload()) {
				$this->view->errors($picture->getMessages());
			}
		}
	}









}
