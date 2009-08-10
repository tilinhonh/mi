<?php
class GrupomaterialcorForm extends Zend_Form
{
	function __construct($options = null)
	{
		parent::__construct($options);

        $this->setAttrib('id','formCombinacoes');

		$this->addElement('hidden','id');
		$this->id->removeDecorator('DtDdWrapper');

        $this->addElement('select','divisaoID')
		    ->divisaoID
				->setLabel('Grupo:')
				->setRequired(true)
                ->addValidator('Digits')
				->addValidator('stringLength',false,array(0,10))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

         $this->_populate('divisaoID',
                            array(
                                'className'=>'Divisoes',
                                 'order'=>'divisao',
                                 'showField'=>'divisao',
                                 'where'=>'id !=3',
                                 'firstField'=>array("","")
                                )
                            );



        $this->addElement('select','materialID')
		    ->materialID
				->setLabel('Material:')
				->setRequired(true)
                ->addValidator('Digits')
				->addValidator('stringLength',false,array(0,10))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

         $this->_populate('materialID',
                            array(
                                'className'=>'Materiais',
                                 'order'=>'material',
                                 'showField'=>'material',
                                 'firstField'=>array("","")
                                )
                            );


		$this->addElement('select','corID')
		    ->corID
				->setLabel('Cor:')
				->setRequired(true)
                ->addValidator('Digits')
				->addValidator('stringLength',false,array(0,10))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

         $this->_populate('corID',
                            array(
                                'className'=>'Cores',
                                 'order'=>'cor',
                                 'showField'=>'cor',
                                 'firstField'=>array("","")
                                )
                            );


         $this->addElement('textarea','observacoes')
		    ->observacoes
				->setLabel('Observações:')
                ->setAttrib('rows',5)
                ->setAttrib('cols',40)
                ->addFilter('StripTags')
				->addFilter('StringTrim')
                //->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,255))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

          /**
		 * submit buttons
		 */
		$this->addElement('submit','submit')
		    ->submit
				->setValue('Salvar')
				->setAttrib('class','button')
				->removeDecorator('Label')
				->removeDecorator('DtDdWrapper');

		$location= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/combinacoes';
		$this->addElement('button','cancelar')
		    ->cancelar
				->setAttrib('class','button2')
				->setAttrib('onclick',"javascript:window.location.href='".$location."'")
				->removeDecorator('DtDdWrapper')
				->setLabel('Cancelar');

	}

	/**
	 * Popula qq select id
	 * @return array(string)
	 */
    private function _populate($element,$options)
    {
        if(is_array($options)){
            $table = new $options['className']();
            if(is_array($options['firstField'])){
                $this->$element->addMultiOption($options['firstField'][0],$options['firstField'][1]);
            }
            $select=$table->select()->order($options['order']);
            if($options['where']){
                $select = $select->where($options['where']);
            }
            $id = $options['id'] ? $options['id'] : 'id';
            foreach($table->fetchAll($select) as $row){
                $this->$element->addMultiOption($row->$id,$row->$options['showField']);
            }
        }
	}
}
?>