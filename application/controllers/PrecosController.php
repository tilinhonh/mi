<?php

class PrecosController extends Zend_Controller_Action
{
    /**
     * disable layout
     */
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
    }

    public function getAction(){

        if($this->getRequest()->isPost()){
            $date = new Zend_Date();
            $combinacaoID = $this->getRequest()->getPost('combinacaoID');
            $precoID = $this->getRequest()->getPost('precoID');
            $precos = new Precos();
            if($combinacaoID){

                $select = $precos->select()
                            ->where('combinacaoID = ?', $combinacaoID)
                            ->order('dataQuotacao')
                                ;
                $i=0;
                $currency = new Zend_Currency();
                $currency->setFormat(array('display'=>Zend_Currency::NO_SYMBOL));
                foreach($precos->fetchAll($select) as $p){
                    $preco = new stdClass();
                    $preco->id = $p->id;
                    $preco->fabrica->id = $p->fabricaID;
                    $preco->fabrica->nome = $p->findParentRow('Fabricas')->fantasia;
                    $preco->pFabrica = Dinheiro::toBrazilFormat($p->pFabrica);
                    $preco->pVenda = Dinheiro::toBrazilFormat($p->pVenda);
                    $preco->pvl = Dinheiro::toBrazilFormat($p->pvl);

                    $dbDate = $p->dataQuotacao;
                    if(Zend_Date::isDate($dbDate,'YYYY-MM-dd')){
                       $date->set($dbDate,'YYYY-MM-dd');
                       $preco->data = $date->toString('dd/MM/YYYY');
                    }else{
                        $preco->data = '';
                    }
                    

                    $obj[] = $preco;
                    $preco = null;
                }
            }//COMBINACAO ID
            elseif($precoID){
                $preco = $precos->find($precoID)->current();
                $obj = new stdClass();
                $obj->id = $preco->id;
                $obj->fabrica->id = $preco->fabricaID;
                $obj->pFabrica = Dinheiro::toBrazilFormat($preco->pFabrica);
                $obj->pVenda = Dinheiro::toBrazilFormat($preco->pVenda);
                $obj->pvl = Dinheiro::toBrazilFormat($preco->pvl);
                
                $dbDate = $preco->dataQuotacao;
                if(Zend_Date::isDate($dbDate,'YYYY-MM-dd')){
                   $date->set($dbDate,'YYYY-MM-dd');
                   $obj->data = $date->toString('dd/MM/YYYY');
                }else{
                    $obj->data = '';
                }
            }
            $this->_helper->json($obj);
        }//is Post
    }//get action

    public function saveRegisterAction()
    {

        $obj = new stdClass(); //contem mensagens de falha, sucesso, etc
        if($this->getRequest()->isPost()){

            $postedData = $this->getRequest()->getPost();
            $form = new PrecosForm();

            if($form->isValid($postedData)){

                try{

                    $precoID = $form->getValue('precoID');
                    $precos = new Precos();

                    if($this->getRequest()->getPost('precoID')){
                        $preco = $precos->find($precoID)->current();
                    }else{
                        $preco = $precos->createRow();
                        $preco->combinacaoID =     $form->getValue('combinacaoID');
                    }
                    $preco->fabricaID = $form->getValue('fabricaID');
                    $preco->pvl =       Dinheiro::toDbFormat($form->getValue('pvl'));
                    $preco->pVenda =    Dinheiro::toDbFormat($form->getValue('pVenda'));
                    $preco->pFabrica =  Dinheiro::toDbFormat($form->getValue('pFabrica'));

                    //checks date
                    $dataQuotacao = $form->getValue('dataQuotacao');
                    
                    if($dataQuotacao){
                        $newDate = new Zend_Date($dataQuotacao,'dd/MM/YYYY');
                    }

                    if(Zend_Date::isDate($newDate,'dd/MM/YY')){
                        $preco->dataQuotacao = $newDate->toString('YYYY-MM-dd');
                    }
                    else{
                        throw new Exception('Data de Quotação inválida');
                    }

                    $preco->save();
                    $obj->success = true;

                    new QueryLogger();

                }catch(Exception $e){
                    $error = $e->getMessage();
                    $obj->error->message[]=$error;
                }
                
            }//is valid
            else{
                $obj->error->message[] = "Valores inválidos";
            }
        }//is post
        else{
            $obj->error->message[] = "Nothing was posted";
        }//post

        $this->_helper->json($obj);
    }


    public function delAction(){
        $response = new stdClass();
        if($this->getRequest()->isPost()){
            try{

                $form = new PrecosForm();

                $id = $this->getRequest()->getPost('precoID');
                if($form->precoID->isValid($id)){
                    $where = 'id = ' . $id;
                    $precos = new Precos();
                    $precos->delete($where);
                    
                    new QueryLogger();

                    $response->success = true;

                }

            }catch(Exception $e){
                $response->success = false;
                $response->error->message[] = $e->getMessage();
            }
            
        }else{
            $response->success=false;
            $resonse->error->message[] = "Não foi possível deletar preço.";
        }

        $this->_helper->json($response);

    }

	function getLastPriceAction()
	{
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$combinacao = (int) $request->getPost('combinacao');
				$fabrica = (int) $request->getPost('fabrica');
				if($combinacao > 0 && $fabrica > 0){
					$_precos = new Precos();
					$where = $_precos->getAdapter()->quoteInto('combinacaoID = ? ', $combinacao)
							 .$_precos->getAdapter()->quoteInto('AND fabricaID = ? ', $fabrica);

					$order = 'dataQuotacao DESC';
					$precos = $_precos->fetchAll($where, $order, 1);

					$json = new stdClass();
					$json->fabrica=Dinheiro::toBrazilFormat($precos[0]->pFabrica);
					$json->venda=Dinheiro::toBrazilFormat($precos[0]->pVenda);
					$json->pvl=Dinheiro::toBrazilFormat($precos[0]->pvl);
					$json->data=BrazilianDate::toBrazilFormat($precos[0]->dataQuotacao);

				}
			}
		}catch(Exception $e){
			$json = $e->getMessage();
		}
		$this->_helper->json($json);
	}


	public function getAllPricesAction()
	{
		if ($this->getRequest()->isPost()){
			/*
			 SELECT fabricas.fantasia , pFabrica, pVenda, pvl, dataQuotacao
			 FROM precos INNER JOIN fabricas ON precos.fabricaID = fabricas.id
			 	INNER JOIN combinacoes ON precos.combinacaoID = combinacoes.id
				INNER JOIN produtos ON combinacoes.produtoID = produtos.id
			WHERE produtos.id=11
			ORDER BY combinacoes.id, dataquotacao
			 */
			$db = Zend_Db_Table::getDefaultAdapter();
			$sql = $db->select()
					->from(array('pr'=>'precos'),
						array(	'preco_fabrica'	=>	'pFabrica',
								'preco_venda'	=>	'pVenda',
								'preco_pvl'		=>	'pvl',
								'data'			=>	'dataQuotacao',
								'id',
							))
					->join(array('f'=>'fabricas'), 'pr.fabricaID = f.id',
							array('fabrica'=>'fantasia'))
					->join(array('c'=>'combinacoes'), 'pr.combinacaoID = c.id',
							array('combinacao_id'=>'id'))
					->join(array('p'=>'produtos'), 'c.produtoID = p.id',
							array())
					->where('p.id = ? ', $this->getRequest()->getPost('produtoID'))
					->order('c.id')
					->order('dataQuotacao');

					
			foreach($db->fetchAll($sql) as $row){
				$r = new stdClass();
				$r->preco->fabrica	= $row['preco_fabrica'];
				$r->preco->venda	= $row['preco_venda'];
				$r->preco->pvl		= $row['preco_pvl'];
				$r->preco->data		= BrazilianDate::toBrazilFormat($row['data']);
				$r->fabrica->nome	= $row['fabrica'];
				$r->id				= $row['id'];
				$r->combinacao->id	= $row['combinacao_id'];
				$result[] = $r;
			}
			$this->_helper->json($result);
		}
	}

}
?>
