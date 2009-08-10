<?php
class CidadesController extends Zend_Controller_Action
{

	function indexAction()
	{
		$this->viewAction();
		//$this->render('view');
	}

    function viewAction(){
        $this->_loadScripts();
    }
	
	function newAction()
	{
		$this->view->title="++Cidade";
		$this->setForm();
		$this->view->form=$this->form;
		$this->form->submit->setLabel('Adicionar');
		if($this->_request->isPost())
		{
			$formData=$this->_request->getPost();
			if($this->form->isValid($formData)){
				$cidades=new Cidades();
				
				$register=$cidades->createRow();
				//saves only what the user is allowed to
				$this->saveRegister($register);
				
			}else{
				$this->form->populate($formData);
			}
		}
	}
	
	
	function editAction()
	{
		$this->view->title="++Cidade";
		$this->setForm();
		$this->view->form=$this->form;
		$this->form->submit->setLabel('Salvar');
		
		if($this->_request->isPost())
		{
			$formData=$this->_request->getPost();
			if($this->form->isValid($formData)){
				$cidades=new Cidades();
				$id=(int)$this->form->getValue('id');
				$row=$cidades->find($id)->current();
				//saves register
				$this->saveRegister($row);
			}
			else{
				$this->form->populate($formData);
			}
		}else{
			$id=(int)$this->_request->getParam('id');
			if($id > 0){
				$cidades=new Cidades();
				$cidade = $cidades->find($id)->current();
				if($cidade){
    				//populates form with database values
    				$data=$cidade->toArray();
    				$this->form->populate($data);
				}else{
				    throw new ExceptionRegisterNotFound('Registro não encontrado.');
				}
			}
		}
	}
	

	function delAction()
	{
		$this->view->title='(-)Cidade';
		
		
		if($this->_request->isPost())
		{
			$id=(int)$this->_request->getPost('id');
			$del=$this->_request->getPost('del');
			
			if($del == 'Sim' && $id>0)
			{
				$cidades=new Cidades();
				$where='id='.$id;
				$cidades->delete($where);
				new QueryLogger();
			}
			$this->_redirect('/cidades');
		}else{
			$id=$this->_request->getParam('id');
			if( $id > 0 ){
				$cidades=new Cidades();
				$this->view->cidade=$cidades->fetchRow('id='.$id);
			}
		}
	}
	
	
	private function setForm()
	{

		//ID	
			$id=new Zend_Form_Element_Hidden('id');
			$id->removeDecorator('DtDdWrapper');
		
		//CIDADE
			$cidade=new Zend_Form_Element_Text('cidade');
			$cidade->setLabel('Cidade:')
				->addFilter('StripTags')
                ->addFilter('StringToUpper')
				->addFilter('StringTrim')
				->setRequired(true)
				->addValidators(
						array(
							array(
								'NotEmpty',false,
								array('messages'=>
									array(
										'isEmpty'=>'Insira um nome para sua cidade.'
									)
								)
							)
						)
					);
		
		//ESTADO
			$estado=new Zend_Form_Element_Select('estadoID');
			$estado->setLabel('Estado:')
				->addFilter('StringTrim')
				->addFilter('StripTags')
				->setRequired(true)
				->addValidator('NotEmpty',false,array('messages'=>array('isEmpty'=>'Selecione um estado.')));
		
			$estadoTable=new Estados();
			$estado->addMultiOption('','');
			foreach($estadoTable->fetchAll() as $option)
			{
				$estado->addMultiOption($option->id,$option->estado);
			}

		
		//SUBMIT
			$submit=new Zend_Form_Element_Submit('submit');
			$submit->setAttrib('id','submitbutton')
				->removeDecorator('DtDdWrapper')
				->setAttrib('class','button');
				
		//FORM
		$this->form=new Zend_Form();	
		$this->form->addElements(array($id, $cidade,$estado,$submit));
		
		$this->form->addElement('button','cancelar');
		$this->form->cancelar
				->setLabel('Cancelar')
				->setAttrib('class','button2')
				->removeDecorator('DtDdWrapper')
				->setAttrib('onclick',"javascript:window.location.href='".$_SERVER['HTTP_REFERER']."'")
				;
		
		
	}
	

	private function saveRegister($register)
	{
		$this->row=$register;
		$this->row->cidade = $this->form->getValue('cidade');
		$this->row->estadoID = $this->form->getValue('estadoID');
		$this->row->save();
		
		
		new QueryLogger();
		$this->_redirect('/cidades');
		
	}
	
	private function paginate()
	{
		$this->page = (is_numeric($this->_request->getParam('page'))) ? (int)$this->_request->getParam('page') : 1 ;
		$this->resultsPerPage = (is_numeric($this->_request->getParam('resultsPerPage'))) ? (int)$this->_request->getParam('resultsPerPage') : 50 ;
		
		$this->view->nextPage=$this->page + 1;
		$this->view->backPage=($this->page>1) ? $this->page - 1: 1;
	}
	
	
	/**
	 * 
	 * @return json
	 * 
	 * @param estadoID
	 * 
	 * Gets a parameter uf and returns a json object
	 * conteining all the cities in 
	 */
	function mostrarCidadesAction()
	{
		$cidades=new Cidades();
		#$estadoID=(int) $this->_request->getPost('uf');
		$estadoID=(int) $this->_request->getParam('uf');
		$select=$cidades->select()->where('estadoID='.$estadoID)->order('cidade');
		
		$json=array();
		foreach($cidades->fetchAll($select) as $cidade)
		{
			$json[$cidade->id]=$cidade->cidade;
		}
		die(json_encode($json));
	}

    private function _loadScripts()
	{
		$this->view->script=array();
		$this->view->script[]="jquery.js";
    	$this->view->script[]="wait.js";
        $this->view->script[]="jquery.json-1.3.min.js";
        $this->view->script[]="Cidades.js";
	}

    function suggestAction(){
        if($this->getRequest()->isPost()){
            $q = $this->getRequest()->getPost('q');
            $cidades = new Cidades();
            $db = $cidades->getAdapter();
            $select = $db->select()
                ->from(array('C'=>'cidades'),
                    array('id',
                            'nome' => 'cidade'
                    )
                )
                ->join(array('E'=>'estados'),
                    'C.estadoID=E.id',array('uf'=>'sigla','estado')
                )
                ->where('cidade like ?','%'.$q.'%')
             //   ->orWhere('estado like ?','%'.$q.'%')
                ->order('cidade')
                ->limit(50);

            $stmt = $db->query($select);
            $result = $stmt->fetchAll();

            $this->_helper->json($result);
            
        }
    }

}

?>