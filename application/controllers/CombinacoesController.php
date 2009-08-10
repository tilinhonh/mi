<?php
class CombinacoesController extends Zend_Controller_Action
{
    function init()
    {
        $this->combinacoes = new Combinacoes();
        $this->db = $this->combinacoes->getAdapter();
    }


    private function _combinacaoExiste($produtoID, $testingGMC, $skipCombinacao)
    {
        asort($testingGMC);
        $arr1 = implode(',', $testingGMC);

        $Combinacoes = $this->combinacoes;
        $CombinacaoItens = new CombinacaoItem();

        $db = $this->db;

        $where = $db->quoteInto('produtoID = ?', $produtoID);
        
        $combinacoes = $Combinacoes->fetchAll($where);

        $i=0;
        foreach($combinacoes as $combinacao){
            $itens = $combinacao->findDependentRowSet('CombinacaoItem');
            $itensGMC = array();
            foreach($itens as $item){
                if($item->combinacao_id !== $skipCombinacao){
                    $itensGMC[] = $item->gmc_id;
                }
            }
            asort($itensGMC);
            $arr2 = implode(',', $itensGMC);

            if($arr1 == $arr2){
                return true;
            }
        }
        return false;
    }


    function deleteAction()
    {
        $response = new stdClass();
        try{
            if($this->getRequest()->isPost()){
                $where = 'id = ' . $this->getRequest()->getPost('combinacaoID');
                $this->combinacoes->delete($where);
                new QueryLogger();
                $response->success = true;
            }
        }catch(Exception $e){
            $response->error->message[] = $e->getMessage();
        }

        $this->_helper->json($response);
        
    }




    function addAction()
    {
        try{
            $response = new stdClass();
            $response->error->message = array();
            $posted  = new stdClass();
            $db = $this->db;
            $db->beginTransaction();
            if($this->getRequest()->isPost()){
                //get all variables
                $posted->gmc = $this->getRequest()->getPost('gmcID');
                $posted->produtoID = $this->getRequest()->getPost('produtoID');
                $posted->importancia = $this->getRequest()->getPost('importancia');
                $posted->ncm->id = $this->getRequest()->getPost('ncmID');

                $this->_checkImportancia($posted->importancia);

                if($this->_combinacaoExiste($posted->produtoID, $posted->gmc)){
                    throw new Exception('Combinação já existe.');
                }

                //cria combinacao
                $combinacao = $this->combinacoes->createRow();
                $combinacao->produtoID = $posted->produtoID;
                $combinacao->ncmID = $posted->ncm->id ? $posted->ncm->id : null;
                $combinacao->save();
                //gets just inserted id
                $combinacaoID = $db->lastInsertId();

                $this->combinacaoItem = new CombinacaoItem();
                foreach($posted->gmc as $k => $v){
                    $combinacaoItem = $this->combinacaoItem->createRow();
                    $combinacaoItem->combinacao_id = $combinacaoID;
                    $combinacaoItem->importancia = $posted->importancia[$k];
                    $combinacaoItem->gmc_id = $v;
                    $combinacaoItem->save();
                }
                
                $db->commit();
                new QueryLogger();
                $response->success = true;
            }else{
                 throw new Exception();
            }
        }catch(Exception $e){
            $db->rollBack();
            if(strlen($e->getMessage()) > 0){
                $response->error->message[] = $e->getMessage();
            }
            $response->success = false;
        }

        $this->_helper->json($response);
    }

    function updateAction(){
        try{
            $response = new stdClass();
            $response->error->message = array();
            $posted  = new stdClass();
            $db = $this->db;
            $db->beginTransaction();
            if($this->getRequest()->isPost()){
                //get all variables
                $posted->gmc = $this->getRequest()->getPost('gmcID');
                $posted->combinacaoID = $this->getRequest()->getPost('combinacaoID');
                $posted->importancia = $this->getRequest()->getPost('importancia');
                $posted->ncm->id = $this->getRequest()->getPost('ncmID');

                $this->_checkImportancia($posted->importancia);

                if(sizeof($posted->gmc) !== sizeof($posted->importancia)){
                    throw new Exception('Número de itens e importancias não conferem');
                }
                
                $combinacao = $this->combinacoes->find($posted->combinacaoID)->current();

                //verifica se existe a mesma combinacao
                // se sim, ainda pode ser a mesma combinacao para fazer update
                    if($this->_combinacaoExiste($combinacao->produtoID, $posted->gmc, $combinacao->id)){
                        $text = 'Outra combinacão já possue os mesmos artigos!';
                        throw new Exception($text);
                    }

                $itens = $combinacao->findDependentRowset('CombinacaoItem');
                $Itens = new CombinacaoItem();
                

                //deleta e coleta existentes
                foreach($itens as $item){
                    $itensDb[]=$item->gmc_id;
                    //deletar
                    if(!in_array($item->gmc_id, $posted->gmc)){
                         $item->delete();
                    }
                }

                foreach($posted->gmc as $k => $v){
                    //throw new Exception(print_r($itensDb,1));
                    //verificar se combinacao não existe
                    if(! in_array($v,$itensDb)){
                        $row = $Itens->createRow();
                        $row->combinacao_id = $combinacao->id;
                        $row->gmc_id = $v;
                        $row->importancia = $posted->importancia[$k];
                        $row->save();
                    }else{
                        $sql = $Itens->select()
                                        ->where('combinacao_id = ?', $combinacao->id)
                                        ->where('gmc_id = ?', $v)->limit(1);

                        $row = $Itens->fetchRow($sql);

                        $row->importancia = $posted->importancia[$k];
                        $row->save();
                    }
                }

                $combinacao->ncmID = $posted->ncm->id ? $posted->ncm->id : null;
                $combinacao->save();


               // throw new Exception('testing:' . $error);
                $db->commit();
                new QueryLogger();
                $response->success = true;
            }else{
                 throw new Exception();
            }
        }catch(Exception $e){
            $db->rollBack();
            if(strlen($e->getMessage()) > 0){
                $response->error->message[] = $e->getMessage();
            }
            $response->success = false;
        }

        $this->_helper->json($response);
    }

    /**
     *Checa importancia por valores validos ou nao
     * @param array $importancia
     */
    private function _checkImportancia($importancia)
    {
         //checka importâncias
        foreach($importancia as $k => $v){
            // testa se um tem importancia 0 pelo menos
            if($v == 0){
                $importaciaZeroExists = true;
            }
            //testa se todos tem valores numericos
            if(!is_numeric($v) && $v < 100){
                throw new Exception('Preêncha todas as importâncias.');
            }
        }
        //exige uma importancia Zero
        if($importaciaZeroExists == false){
            throw new Exception('Pelo menos uma importância deve ser zero.');
        }
    }

	/**
	 * usado pra enviar as combinacoes de produtos, que serao vinculadas
	 * no pedido
	 */
  
    public function getCombinacoesAction()
    {
        $response = new stdClass();
        $response->error->message = array();
        $tItem = new CombinacaoItem();
        $gmc = new GrupoMaterialCor();
        if($this->getRequest()->isPost()){
            try{
				/**
				 * Quando vou atualizar um item do pedido,
				 * preciso informar se a combinacao é a escolhida:
				 */

				$itemPedido = $this->getRequest()->getPost('item-do-pedido');
				if($itemPedido){
					$_item = new ItemPedido();
					$_itemPedidoSelecionado = $_item->find($itemPedido)->current()->itemID;
				}

                $produto = new stdClass();
                $produto->id = $this->getRequest()->getPost('produtoID');
                $produto->combinacao = array();
                $combinacaoID = $this->getRequest()->getPost('combinacaoID');
				
				//way 2
				if($combinacaoID){
					//$sql = $this->combinacoes->select()->where('id = ?', $combinacaoID); //way 1
					$where = $this->combinacoes->getAdapter()->quoteInto('id = ?', $combinacaoID);
					$order = null;
                }else{
					//$sql = $this->combinacoes->select()->where('produtoID = ?', $produto->id)->order('id'); //way 2
                    $where = $this->combinacoes->getAdapter()->quoteInto('produtoID = ?', $produto->id);
					$order = 'id';
                }


                $newCombinacao = new stdClass();

                $i=0;//incrementa combinacao
                //foreach($this->combinacoes->fetchAll($sql) as $c){ //way 1
                foreach($this->combinacoes->fetchAll($where, $order) as $c){ //way 2

                    //COMBINACOES
                    $newCombinacao = null;
                    $newCombinacao->id = $c->id;
                    $newCombinacao->selected = $c->id == $_itemPedidoSelecionado ? true : false;

                    try{// there may not be ncmID
                        $newCombinacao->ncm->id = $c->ncmID;
                        $newCombinacao->ncm->descricao = $c->findParentRow('Ncms')->descricao;
                        $newCombinacao->ncm->codigo = $c->findParentRow('Ncms')->codigo;
                    }catch(Exception $e){
                        $newCombinacao->ncm->id = '';
                        $newCombinacao->ncm->codigo = '';
                        $newCombinacao->ncm->descricao = '';
                    }

                    $produto->combinacao[$i] = $newCombinacao;
                    $produto->combinacao[$i]->item = array();

                    $selectItems = null;
                    $selectItems = $tItem->select()
                            ->where('combinacao_id = ?', $c->id)
                            ->order('importancia');

                    $b = 0;//incrementa item
                    //ITEM DA COMBINAÇÃO
                    foreach($tItem->fetchAll($selectItems) as $item){
                        //$newItem = null;
                        $newItem = new stdClass();
                        $newItem->id = $item->id;
                        $newItem->importancia = $item->importancia;

                        $newItem->gmc->codigo = $item->findParentRow('GrupoMaterialCor')
                                                    ->codigo;
                        $newItem->gmc->id = $item->findParentRow('GrupoMaterialCor')
                                                    ->id;

                        $newItem->grupo->id = $item->findParentRow('GrupoMaterialCor')
                                                    ->divisaoID;
                        $newItem->grupo->nome = $item->findParentRow('GrupoMaterialCor')
                                                    ->findParentRow('Divisoes')
                                                    ->divisao;

						//* ESTACAO, ULTIMA ALTERAÇAO AUG 05 2009
                        $newItem->estacao->id = $item->findParentRow('GrupoMaterialCor')
                                                    ->estacaoID;
                        $newItem->estacao->nome = $item->findParentRow('GrupoMaterialCor')
                                                    ->findParentRow('Estacoes')
                                                    ->estacao;
						 //*/


                        $newItem->material->id = $item->findParentRow('GrupoMaterialCor')
                                                    ->materialID;
                        $newItem->material->nome = $item->findParentRow('GrupoMaterialCor')
                                                    ->findParentRow('Materiais')
                                                    ->material;

                        $newItem->cor->id = $item->findParentRow('GrupoMaterialCor')
                                                    ->corID;
                        $newItem->cor->nome = $item->findParentRow('GrupoMaterialCor')
                                                    ->findParentRow('Cores')
                                                    ->cor;

                        $produto->combinacao[$i]->item[$b] = $newItem;
                        $b++;
                    }
                     
                    
                    //incrementa combinacao
                    $i++;
                }
                // se foi pedido combinacao, devolve combinacao
                //se nao, devolve produto com todas as combinacoes
                $this->_helper->json($combinacaoID ? $produto->combinacao[0] : $produto);
            }catch(Exception $e){
                $response->success = false;
                $msg = $e->getMessage();
                if(strlen($msg) > 0){
                    $response->error->message[] = $msg;
                }
            }

        }else{
            $response->success = false;
        }
        $this->_helper->json($response);
    }
}