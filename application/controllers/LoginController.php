<?php
class LoginController extends Zend_Controller_Action
{
    function indexAction()
    {
        $this->_helper->layout()->setLayout('login');
        
        $this->view->title="Login";        
        
         // If we're already logged in, just redirect  
        if(Zend_Auth::getInstance()->hasIdentity())  
        {  
             $this->_redirect('/index');  
        }  
        
        $form = new LoginForm();
        //try login in
        if($this->_request->isPost()){
            $postData=$this->_request->getPost();
            if($form->isValid($postData)){
                //name and password from the form
                $username = $form->getValue('name');
                $password = $form->getValue('password');
                
                $db = Zend_Db_Table::getDefaultAdapter();
                $authAdapter= new Zend_Auth_Adapter_DbTable($db);
                
                $authAdapter->setTableName('users')
                    ->setIdentityColumn('name')
                    ->setCredentialColumn('password')
                    ->setCredentialTreatment('MD5(?)')
                ;
                
                $authAdapter->setIdentity($username)
                            ->setCredential($password)
                ;
                
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                //is the user a valid one?
                if($result->isValid()){
                    //Get all info about this user from the login table
                    //ommits the password though
                    $userInfo = $authAdapter->getResultRowObject(null,'password');
                    
                    //the default sotrage is a session with namespace Zend_Auth
                    $authStorage=$auth->getStorage();
                    $authStorage->write($userInfo);
                    
                    $this->_redirect('/index');
                }else{
                    $errorMessage="Usuário ou senha inválida. Tente novamente.";
                }
                
            }
            
        }
        
        $this->view->message=$errorMessage;
        $this->view->form=$form;
    }
    
    

   public function logoutAction()  
   {  
        // clear everything - session is cleared also!  
        Zend_Auth::getInstance()->clearIdentity();  
        $this->_redirect('/login/index');  
   }  
}
?>