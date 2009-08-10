<?php
class CombinacoesDoProdutoController extends Zend_Controller_Action
{
    function init()
    {
        $this->view->title = "Combinações do Produto";
        $this->_helper->layout->disableLayout();
    }
    
    
    /**
     * Mostra lista combinações de um dado produto
     * exemplo:
     * /combinacoes-do-produto/index/produto/3 
     * @return json
     */
	function indexAction()
	{

	    if($this->_request->isPost()){
	        
	        $produtoID=(int) $this->_request->getPost('produto');
            $combinacoes=new Combinacoes();
            $select = $combinacoes->select()->where('produtoID = ?', $produtoID);
            $combCores = new CombCores();
            $combMateriais = new CombMateriais();
            $db = $combCores->getAdapter();

            $c = 0;
            foreach($combinacoes->fetchAll($select) as $combinacao){
                /* combinacao*/
                $comb[$c] = new stdClass();
                $comb[$c]->ncm->id = $combinacao->ncmID;
                $comb[$c]->id = $combinacao->id;

                //tenta pegar descricao da combinacao
                try{
                    $text = $combinacao->findParentRow('Ncms')->descricao;
                }catch(Exception $e){
                    //there was no child row
                    $text = false;
                }
                $comb[$c]->ncm->text = $text ? $text : '';

                /*  Cores */
                $selectCores = $db->select()
                                //->from(array('cb'=>'combinacoes'))
                                ->from(array('cc'=>'combCores'))
                               // ->join(array('cc'=>'combCores'),'cb.id = cc.combinacaoID',array('corID'=>'corID','importancia'))
                                ->join(array('c'=>'cores'),'cc.corID=c.id',array('nome'=>'cor'))
                                ->where('combinacaoID = ?', $combinacao->id)
                                ->order('importancia');

                $stmt = $db->query($selectCores);
                $i=0;
                while($cor = $stmt->fetch()){
                    $comb[$c]->cor[$i]->id = $cor['corID'];
                    $comb[$c]->cor[$i]->rate = $cor['importancia'];
                    $comb[$c]->cor[$i++]->nome = $cor['nome'];
                }


                 /*  Materiais */
                $selectMateriais = $db->select()
                                ->from(array('cm'=>'combMateriais'))
                                ->join(array('m'=>'materiais'),
                                        'cm.materialID=m.id',
                                        array('nome'=>'material'))
                                ->where('combinacaoID = ?', $combinacao->id)
                                ->order('importancia');

                $stmt = $db->query($selectMateriais);
                $i=0;
                while($material = $stmt->fetch()){
                    $comb[$c]->material[$i]->id = $material['materialID'];
                    $comb[$c]->material[$i]->rate = $material['importancia'];
                    $comb[$c]->material[$i++]->nome = $material['nome'];
                }

                $c++; //increments combinação
            }

            $this->_helper->json($comb);
	    }
	}


    public function delAction(){
        //ajax call, não tem view
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        if($this->_request->isPost()
            && $this->_request->getPost('del') == 'yes'){
            try{
                $id=(int) $this->_request->getPost('combinacaoID');
                $where = 'id='.$id;

                $combinacoes= new Combinacoes();
                $combinacoes->delete($where);
                $this->logQuery();
            }catch(Exception $err){
                $error['message'][]="Exclusão negada. Certifique-se que não exista nenhum pedido associado a esta combinação.";
                $this->_helper->json($error);
            }
        }
    }

    public function getCombinacaoAction()
    {
       if($this->getRequest()->isPost())
       {
           $combID = $this->_request->getPost('combinacaoID');

           $tCombinacoes = new Combinacoes();

           $combinacao=$tCombinacoes->find($combID)->current();

           $json['id'] = $combID;
           //there might not have any NCMS yet
           try{
            $json['ncm']['id'] = $combinacao->ncmID;
            $desc = $combinacao->findParentRow('Ncms')->descricao;
            $text = str_replace("\r\n", ' ', $desc);
            $json['ncm']['text'] = $text;
           }
           catch(Exception $e){
               $json['ncm']['id'] = '';
               $json['ncm']['text'] = '';
           }

           //cores
           $combCores = new CombCores();


           $selectCores = $combCores->select()
                                    ->where('combinacaoID = ?', $combID)
                                    ->order('importancia');

           $i=0;
           foreach($combCores->fetchAll($selectCores) as $c){
                $json['cor'][$i]['id'] = $c->corID;
                $json['cor'][$i]['rate'] = $c->importancia;
                $json['cor'][$i++]['nome'] = $c->findParentRow('Cores')->cor;
           }



           //materiais
           $combMateriais = new CombMateriais();
           $selectMateriais = $combMateriais->select()
                                    ->where('combinacaoID = ?', $combID)
                                    ->order('importancia');

           $i=0;
           foreach($combMateriais->fetchAll($selectMateriais) as $m){
                $json['material'][$i]['id'] = $m->materialID;
                $json['material'][$i]['nome'] = $m->findParentRow('Materiais')->material;
                $json['material'][$i++]['rate'] = $m->importancia;
           }


            $this->_helper->json($json);
       }
    }


    /**
     * @url /combinacoes-do-produto/add
     */
    public function addAction()
    {

        if($this->_request->isPost()){

            $db=Zend_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
             try{
                /**
                 * Pega post, valida.
                 * Caso passe na validação, tenta criar combinacao.
                 * Caso algo de errado aconteça, RollsBack
                 */


                 $produtoID=$this->_request->getPost('produtoID');
                 $newMateriais=$this->_request->getPost('material');
                 $newCores=$this->_request->getPost('cor');
                 $corRate=$this->_request->getPost('corRate');
                 $materialRate=$this->_request->getPost('materialRate');
                 $ncmID=$this->_request->getPost('ncmID');

                if(!is_numeric($produtoID)){
                    $error['message'][]='Error: $produtoID not caught';
                 }

                 if(count($newCores) < 1){
                     $error['message'][]='Escolha pelo menos uma cor.';
                 }
                 elseif(!in_array(0, $corRate)){
                     $error['message'][]='Pelo menos uma cor deve ser principal(Importância=0)';
                 }

                 if(count($newMateriais) < 1){
                     $error['message'][]='Escolha pelo menos um material.';
                 }elseif(!in_array(0, $materialRate)){
                     $error['message'][]='Pelo menos um material deve ser principal(Importância=0)';
                 }

                 if(count($newMateriais) !== count($materialRate) || count($newCores) !== count($corRate)){
                    $error['message'][]='Preêncha todas as importancias';
                 }

                //conta erros e joga mensagens
                if(count($error)>0){
                    $errors=Zend_Json_Encoder::encode($error);
                    throw new Exception();
                }


                //verifica todas as combinações do produto
                //e garante que não vai haver outra combinação igual
                //comeca por cores

                $combinacoes=new Combinacoes();
                $select=$combinacoes->select()
                                    ->where('produtoID=?',$produtoID);


                /*
                 * navega por todas as combinacoes do produto
                 * e compara suas combinacoes de cores/materiais
                 * com a cor que deseja-se incluir $cores[] $materiais[]
                 * uma combinação identica exista, a nova combinacao
                 * não poderá ser inserida
                 */
                $tCombCores = new CombCores();
                $tCombMateriais = new CombMateriais();
                //navega pelas combinacoes

                foreach($combinacoes->fetchAll($select) as $cmb){
                    $cmbID = $cmb->id;
                    $existeCombinacaoDeCores = false;
                    $existeCombinacaoDeMateriais = false;
                    //VERIFICACAO DE CORES**************
                    //busca todas as cores da combinacao e coloca num array
                    $coresExistentes = array(); //reseta array
                    //se ainda nao foi encontrado combinacao igual,
                    //tenta uma outra combinacao
                    if(false == $existeCombinacaoDeCores){
                        $selectCores = $tCombCores->select()
                            ->where('combinacaoID = ?',$cmbID);
                        foreach($tCombCores->fetchAll($selectCores) as $c){
                            $coresExistentes[] = $c->corID;
                        }

                        //if size is the same, compares more deeply
                       if(count($newCores) == count($coresExistentes)){
                            asort($coresExistentes);
                            asort($newCores);
                            $stringCoresExistentes=implode('',$coresExistentes);
                            $stringNewCores=implode('',$newCores);
                            if($stringCoresExistentes == $stringNewCores){
                               $existeCombinacaoDeCores=true;
                            }
                       }
                    }

                    //VERIFICACAO DE MATERIAIS****
                    //busca todas os materiais da combinacao e coloca num array
                    $materiaisExistentes=array(); //reseta array
                    //se ainda nao foi encontrado combinacao igual,
                    //tenta uma outra combinacao
                    if(false == $existeCombinacaoDeMateriais){
                        $selectMateriais = $tCombMateriais->select()
                                             ->where('combinacaoID=?',$cmbID);

                        foreach($tCombMateriais->fetchAll($selectMateriais) as $m){
                            $materiaisExistentes[] = $m->materialID;
                        }
                        //if size is the same, compares more deeply
                       if(count($newMateriais) == count($materiaisExistentes)){
                            asort($materiaisExistentes);
                            asort($newMateriais);
                            $stringMateriaisExistentes = implode('',$materiaisExistentes);
                            $stringNewMateriais = implode('',$newMateriais);
                            if($stringMateriaisExistentes == $stringNewMateriais){
                               $existeCombinacaoDeMateriais=true;
                            }
                       }
                    }


                    if($existeCombinacaoDeCores && $existeCombinacaoDeMateriais){
                        throw new Exception('Combinação já existe.');
                    }
                }

                //tenta colocar combinacoes no banco de dados

                //combinacao é inserida
                $combinacao=new Combinacoes();
                $row=$combinacao->createRow();
                $row->produtoID=$produtoID;

                if($ncmID){
                    $row->ncmID=$ncmID;
                }

                $row->save();
                //id gerado
                $combinationInsertedID=$db->lastInsertId();


                //tenta inserir cores
                foreach($newCores as $k => $v){
                    $row=$tCombCores->createRow();
                    $row->combinacaoID=$combinationInsertedID;
                    $row->corID=$v;
                    $row->importancia=$corRate[$k];
                    $row->save();
                }

                //tenta inserir materiais
                foreach($newMateriais as $k => $v){
                    $row=$tCombMateriais->createRow();
                    $row->combinacaoID=$combinationInsertedID;
                    $row->materialID=$v;
                    $row->importancia=$materialRate[$k];
                    $row->save();
                }


                //throw new Exception('Passou tudo' . $combinationInsertedID);

                $db->commit();
                $this->logQuery();
             }catch (Exception $e){
                $db->rollBack();
                if($e->getMessage()){
                    $error['message'][]=$e->getMessage();
                }

                $this->_helper->json($error);
             }

        }//if
    }//adicionaCombinacaoDeProdutoAction()

    /**
     * edita combinacoes
     */
    public function editAction()
    {
        if($this->getRequest()->isPost()){
            $db=Zend_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
             try{

                $combinacaoID = $this->getRequest()->getPost('combinacaoID');
                $postedMateriais = $this->getRequest()->getPost('material');
                $postedMateriaisRate = $this->getRequest()->getPost('materialRate');
                $postedCores = $this->getRequest()->getPost('cor');
                $postedCoresRate = $this->getRequest()->getPost('corRate');
                $pvl=$this->getRequest()->getPost('pvl');
                $pVenda=$this->getRequest()->getPost('pVenda');
                $pFabrica=$this->getRequest()->getPost('pFabrica');
                $ncmID=$this->getRequest()->getPost('ncmID');

                $tCombinacoes = new Combinacoes();
                $combinacao = $tCombinacoes->find($combinacaoID)->current();
                if($pvl){
                    $combinacao->pvl=$pvl;
                }
                if($pVenda){
                    $combinacao->pVenda=$pVenda;
                }
                if($pFabrica){
                    $combinacao->pFabrica=$pFabrica;
                }
                if($ncmID){
                    $combinacao->ncmID=$ncmID;
                }

               // throw new Exception($this->getRequest()->getPost('ncmID'));

                $combinacao->save();


                //put posted data into an object
                $posted = new stdClass();
                $i=0;
                foreach($postedCores as $k => $v){
                    $posted->cores[$v]->id = $v;
                    $posted->cores[$v]->importancia = $postedCoresRate[$k];
                }


                $i=0;
                foreach($postedMateriais as $k => $v){
                    $posted->materiais[$v]->id = $v;
                    $posted->materiais[$v]->importancia = $postedMateriaisRate[$k];
                }

                //put existing data into $inDb obj
                $CombCores = new CombCores();
                $cores = $CombCores->select()->where('combinacaoID = ?', $combinacaoID);

                $CombMateriais = new CombMateriais();
                $materiais = $CombMateriais->select()->where('combinacaoID = ?', $combinacaoID);

                $inDb = new stdClass();
                $i++;
                foreach($CombCores->fetchAll($cores) as $k){
                    $id = $k->corID;
                    $inDb->cores[$id]->id = $id;
                    $inDb->cores[$id]->importancia = $k->importancia;
                }

                $i++;
                foreach($CombMateriais->fetchAll($materiais) as $k){
                    $id = $k->materialID;
                    $inDb->materiais[$id]->id = $id;
                    $inDb->materiais[$id]->importancia = $k->importancia;
                }

                $addCor = array_diff_key((array) $posted->cores, (array) $inDb->cores);
                $delCor = array_diff_key((array) $inDb->cores,(array) $posted->cores);
                $editCor= array_intersect_key((array) $posted->cores, (array) $inDb->cores);

                $addMat = array_diff_key((array) $posted->materiais, (array) $inDb->materiais);
                $delMat = array_diff_key((array) $inDb->materiais, (array) $posted->materiais);
                $editMat = array_intersect_key((array) $posted->materiais, (array) $inDb->materiais);


                /* delete combinacoes */
                foreach($delCor as $k => $v){
                    $where[0] = $db->quoteInto('combinacaoID = ?',  $combinacaoID);
                    $where[1] = $db->quoteInto('corID = ?', $k);
                    $CombCores->delete($where);
                    $message .= $k . "\t";
                }

                 foreach($addCor as $k => $v){
                    $row=$CombCores->createRow();
                    $row->combinacaoID=$combinacaoID;
                    $row->corID=$k;
                    $row->importancia=$posted->cores[$k]->importancia;
                    $row->save();
                }
                
                 foreach($editCor as $k => $v){
                    $corToEdit=$CombCores->select()
                               ->where('combinacaoID = ?',$combinacaoID)
                               ->where('corID = ?',$k)
                               ->limit(1);
                    $row=$CombCores->fetchRow($corToEdit);
                    if($row->importancia !== $posted->cores[$k]->importancia){
                        $row->importancia=$posted->cores[$k]->importancia;
                        $row->save();
                    }
                }

                //materiais
                foreach($delMat as $k => $v){
                    $where[0] = $db->quoteInto('combinacaoID = ?',  $combinacaoID);
                    $where[1] = $db->quoteInto('materialID = ?', $k);
                    $CombMateriais->delete($where);
                    $message .= $k . "\t";
                }

                 foreach($addMat as $k => $v){
                    $row=$CombMateriais->createRow();
                    $row->combinacaoID=$combinacaoID;
                    $row->materialID=$k;
                    $row->importancia=$posted->materiais[$k]->importancia;
                    $row->save();
                }

                 foreach($editMat as $k => $v){
                    $materialToEdit=$CombMateriais->select()
                               ->where('combinacaoID = ?',$combinacaoID)
                               ->where('materialID = ?',$k)
                               ->limit(1);
                    $row=$CombMateriais->fetchRow($materialToEdit);
                    if($row->importancia !== $posted->materiais[$k]->importancia){
                        $row->importancia=$posted->materiais[$k]->importancia;
                        $row->save();
                    }
                }


                 $selectNumMateriais = $CombMateriais->select()
                                            ->where('combinacaoID=?',$combinacaoID);
                 $numMateriais = $CombMateriais->fetchAll($selectNumMateriais)
                                               ->count();


                 $selectNumCores = $CombCores->select()
                                            ->where('combinacaoID=?',$combinacaoID);
                 $numCores = $CombCores->fetchAll($selectNumCores)
                                       ->count();

                 if($numCores < 1 || $numMateriais <1){
                     throw new Exception("Escolha pelo menos uma cor e um material.");
                 }

                //testa importâncias de cores

                $selectZeroCores = $CombCores->select()
                                    ->where('combinacaoID=?',$combinacaoID)
                                    ->where('importancia=0');

                $zeroCores = $CombCores->fetchAll($selectZeroCores)->count();

                if($zeroCores <1 ){
                    throw new Exception("Pelo menos uma cor deve ter importancia 0");
                }

                //testa importancias de materiais
                $selectZeroMateriais = $CombMateriais->select()
                                    ->where('combinacaoID=?',$combinacaoID)
                                    ->where('importancia=0');

                $zeroMateriais = $CombMateriais->fetchAll($selectZeroMateriais)->count();

                if($zeroMateriais <1 ){
                    throw new Exception("Pelo menos um material deve ter importancia 0");
                }

                

                
               // throw new Exception($message);
                $db->commit();
                $this->logQuery();
             }catch (Exception $e){
                $db->rollBack();
                if($e->getMessage()){
                    $json['message'][]=$e->getMessage();
                }

                $this->_helper->json($json);
             }
        }//isPost()
        $this->_helper->json($json);
    }


    function logQuery(){
        new QueryLogger();
    }
}
?>