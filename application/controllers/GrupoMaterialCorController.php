<?php
class GrupoMaterialCorController extends Zend_Controller_Action
{
    function init()
    {
        $this->view->title = "Combinações";
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->grupoMaterialCor = new GrupoMaterialCor();
    }

    function indexAction()
    {
        $select = $this->grupoMaterialCor->select()->order('codigo');
        $this->view->grupoMaterialCor = $this->grupoMaterialCor->fetchAll($select);
    }
    
    function addAction()
    {
		$this->_makeSelects();
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
			if($this->grupoMaterialCor->disableValidationRulesForField('id')->isValid($data)){
                $row = $this->grupoMaterialCor->createRow();
                $this->_saveRegister($row,$data);
            }
            else{
                $this->_populateForm($data);
				$this->view->errors($this->grupoMaterialCor->getValidationMessages());
            }
        }
    }


	protected function _makeSelects(){
		$selects = array('Estacoes', 'Divisoes', 'Materiais', 'Cores');
		$this->view->select = SelectBoxes::makeSelects($selects);
	}

	protected function _populateForm($data)
	{
		$this->view->form = $data;
	}

    function editAction()
    {
        $this->_makeSelects();
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
			if($this->grupoMaterialCor->isValid($data)){
                if($row = $this->grupoMaterialCor->find($data['id'])->current())
					$this->_saveRegister($row, $data);
				else
					$this->view->errors('Registro não encontrado.');
            }
            else{
				$this->_populateForm($data);
				$this->view->errors($this->grupoMaterialCor->getValidationMessages());
            }
        }else{
            try{
                $id = (int) $request->getParam('id');
                $register = $this->grupoMaterialCor->find($id)->current();
                $this->_populateForm($register->toArray());
            }catch(Exception $e){
                throw new ExceptionRegisterNotFound();
            }
        }
    }


    function delAction()
    {
        if($this->getRequest()->isPost()){
            $del = $this->getRequest()->getPost('del');
            $id = (int) $this->getRequest()->getPost('id');
            if($id > 0 && $del == 'sim'){
                $where = 'id = ' . $id;
                $this->grupoMaterialCor->delete($where);
                new QueryLogger();
            }
            $this->_redirect($this->getRequest()->getControllerName());
        }else{
            $id = (int) $this->getRequest()->getParam('id');
            $this->view->grupoMaterialCor = $this->grupoMaterialCor->find($id)->current();
        }
    }


    private function _saveRegister($row, $data)
    {
		try{
			$codigo = array($data['estacaoID'], $data['divisaoID'], $data['materialID'] ,$data['corID']);
			$codigo = implode( '.' , $codigo );
			$filter = new My_Model_Filter('GrupoMaterialCor', $data);
			$row->estacaoID		= $filter->filter('estacaoID');
			$row->divisaoID		= $filter->filter('divisaoID');
			$row->materialID	= $filter->filter('materialID');
			$row->corID			= $filter->filter('corID');
			$row->codigo		= $filter->filter('codigo',$codigo);
			$row->observacoes	= $filter->filter('observacoes');
			$row->save();
			new QueryLogger();
			$this->_redirect('/grupo-material-cor/index');
		}catch(Exception $e){
			$this->_populateForm($data);
			$this->view->errors('Não foi possivel salvar a combinação. Verifique se ela já não existe.')
					->addMessage($e->getMessage());
		}
    }


    /**
     * Retorna gurpo, material, cor disponíveis para suggest purpose.
     *
     * @return string
     */
    public function suggestAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();

        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
                    ->from(array('GMC'=>'grupo_material_cor'),array('codigo','id'))
                    ->join(
                        array('G'=>'divisoes'),'GMC.divisaoID=G.id',array('grupo'=>'divisao')
                    )
                     ->join(
                        array('M'=>'materiais'),'GMC.materialID=M.id',array('material')
                    )
                    ->join(
                        array('C'=>'cores'),'GMC.corID=C.id',array('cor')
                    )
                    ->join(
                        array('E'=>'estacoes'),'GMC.estacaoID=E.id',array('estacao')
                    );


        
        $q = $this->getRequest()->getParam('q');
        $sql = $sql->where('codigo like ?', '%'.$q.'%')
                ->orWhere('divisao like ?', '%'.$q.'%')
                ->orWhere('material like ?', '%'.$q.'%')
                ->orWhere('cor like ?', '%'.$q.'%')
                ->order('codigo');
        $stmt=$db->query($sql);

        $fieldSeparator = " - ";
        while($row = $stmt->fetch()){
            echo $row['estacao'];
            echo $fieldSeparator;
            echo $row['codigo'];
            echo $fieldSeparator;
            echo $row['grupo'];
            echo $fieldSeparator;
            echo $row['material'];
            echo $fieldSeparator;
            echo $row['cor'];
            echo "|";
            echo $row['id'];
            echo "\n";
        }
         exit(0);
    }

    /**
     * Returns specific Group Material Cor json
     * @return json
     */

    public function getJsonAction()
    {
        if($this->getRequest()->isPost()){
            try{
                $gmcID = $this->getRequest()->getPost('gmcID');
                $GMC = new GrupoMaterialCor();
                $gmc = $GMC->find($gmcID)->current();

                $result = new stdClass();
                $result->id = $gmc->id;
                $result->codigo = $gmc->codigo;
                $result->estacao->nome= $gmc->findParentRow('Estacoes')->estacao;
                $result->grupo->nome= $gmc->findParentRow('Divisoes')->divisao;
                $result->cor->nome = $gmc->findParentRow('Cores')->cor;
                $result->material->nome = $gmc->findParentRow('Materiais')->material;
            }catch(Exception $e){

            }

            $this->_helper->json(array($result));
        }
    }

}