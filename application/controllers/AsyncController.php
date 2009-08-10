<?php

class AsyncController extends Zend_Controller_Action
{
    /**
     * Disable layout
     */
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
    }
    
    /**
     * Suggest NCMs
     * @return mixed
     */
    
    public function suggestNcmAction()
    {
        $ncms=new Ncms();
	    $param=$this->_request->getParam('q');
	    $select=$ncms->select()->order('descricao')
            ->where('descricao like ?','%'.$param.'%')
            ->orWhere('codigo like ?','%'.$param.'%')
            ->limit(10);
	    foreach($ncms->fetchAll($select) as $field){
	      $id=$field->id;
	      $text=$field->descricao;

	      $replace=array("\n","\r\n","\r");
	      $text=str_replace($replace,'',$text);
	      
	      echo "(" .$field->codigo . ") - ". $text ."|" . $id . "\n";
	    }
    }

    /**
     * Suggest materiais
     * @return mixed
     */
    public function suggestMateriaisAction()
	{
	    $materiais=new Materiais();
	    $param=$this->_request->getParam('q');
	    $select=$materiais->select()->order('material')->where('material like ?','%'.$param.'%')->limit(10);
	    foreach($materiais->fetchAll($select) as $material){
	      echo $material->material ."|" . $material->id . "\n";
	    }
	}    
    
    
    public function suggestCoresAction()
	{
	    $cores=new Cores();
	    $param=$this->_request->getParam('q');
	    $select=$cores->select()->order('cor')->where('cor like ?','%'.$param.'%')->limit(10);
	    foreach($cores->fetchAll($select) as $cor){
	      echo $cor->cor ."|" . $cor->id . "\n";
	    }
        exit(0);
	}
}
?>
