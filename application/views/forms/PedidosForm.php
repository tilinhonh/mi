<?php
class PedidosForm extends Zend_Form
{
	function __construct($options = null)
	{
		parent::__construct($options);

        $this->setAttrib('id','formPedidos');

		$this->addElement('hidden','id');
		$this->id->removeDecorator('DtDdWrapper');


		$this->addElement('select','corsoID')
		    ->corsoID
				->setLabel('Corso:')
				->setRequired(true)
                ->addValidator('Digits')
				->addValidator('stringLength',false,array(0,10))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

         $this->_populate('corsoID',
                            array(
                                'className'=>'Corsos',
                                 'order'=>'dataInclusao desc',
                                 'id'=>'id',
                                 'showField'=>'corso',
                                 'firstField'=>array("","")
                                )
                            );
       

        $this->addElement('select','clienteID')
		    ->clienteID
				->setLabel('Cliente:')
				->setRequired(true)
                ->addValidator('Digits')
				->addValidator('stringLength',false,array(0,10))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

        $this->_populate('clienteID',
                                    array(
                                        'className'=>'Clientes',
                                        'order'=>'nome',
                                        'showField'=>'nome',
                                        'firstField'=>array("","")
                                        )
                                    );


        $this->addElement('select','fabricaID')
		    ->fabricaID
				->setLabel('Fábrica:')
				->setRequired(true)
                ->addValidator('Digits')
				->addValidator('stringLength',false,array(0,10))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

        $this->_populate('fabricaID',
                                    array(
                                        'className'=>'Fabricas',
                                         'order'=>'nome',
                                         'showField'=>'nome',
                                         'firstField'=>array("","")
                                        )
                                    );


          $this->addElement('select','transportadoraID')
		    ->transportadoraID
				->setLabel('Transportadora:')
				->setRequired(true)
                ->addValidator('Digits')
				->addValidator('stringLength',false,array(0,10))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

        $this->_populate('transportadoraID',
                                    array(
                                        'className'=>'Transportadoras',
                                         'order'=>'fantasia',
                                         'showField'=>'fantasia',
                                         'firstField'=>array("","")
                                        )
                                    );

         $this->addElement('select','representanteID')
		    ->representanteID
				->setLabel('Representante:')
			//	->setRequired(true)
                ->addValidator('Digits')
				->addValidator('stringLength',false,array(0,10))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );


        $this->_populate('representanteID',
                                    array(
                                        'className'=>'Representantes',
                                         'order'=>'representante',
                                         'showField'=>'representante',
                                         'firstField'=>array("","")
                                        )
                                    );

          $this->addElement('select','tipoEmbarqueID')
		    ->tipoEmbarqueID
				->setLabel('Embarque:')
				->setRequired(true)
                ->addValidator('Digits')
				->addValidator('stringLength',false,array(0,10))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

        $this->_populate('tipoEmbarqueID',
                                    array(
                                        'className'=>'TipoEmbarque',
                                         'order'=>'tipo',
                                         'showField'=>'tipo',
                                         'firstField'=>array("","")
                                        )
                                    );


         $this->addElement('select','statusID')
		    ->statusID
				->setLabel('Status:')
				->setRequired(true)
                ->addValidator('Digits')
				->addValidator('stringLength',false,array(0,10))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

        $this->_populate('statusID',
                                    array(
                                        'className'=>'Status',
                                         'order'=>'status',
                                         'showField'=>'status',
                                         'firstField'=>array("","")
                                        )
                                    );




       $this->addElement('text','pedidoCliente')
		    ->pedidoCliente
				->setLabel('Pedido Cliente:')
				//->setRequired(true)
				->setAttrib('maxlength','45')
				->setAttrib('size','15')
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,45))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

          $this->addElement('text','dataCliente')
		    ->dataCliente
				->setLabel('Data Cliente:')
				->setRequired(true)
				->setAttrib('maxlength','8')
				->setAttrib('size','8')
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,8))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );


            $this->addElement('text','dataCliente')
		    ->dataCliente
				->setLabel('Cliente:')
				->setRequired(true)
				->setAttrib('maxlength','8')
				->setAttrib('size','8')
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,8))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

           $this->addElement('text','dataFabrica')
		    ->dataFabrica
				->setLabel('Fabrica:')
				//->setRequired(true)
				->setAttrib('maxlength','8')
				->setAttrib('size','8')
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,8))
				->addValidator('NotEmpty',false,array(
						'messages'=>array('isEmpty'=>'Campo obrigatório.'))
				    );

            $this->addElement('text','dataFabricaReprogramada')
		    ->dataFabricaReprogramada
				->setLabel('Fábrica Reprogramada:')
				//->setRequired(true)
				->setAttrib('maxlength','8')
				->setAttrib('size','8')
				->addFilter('StripTags')
				->addFilter('StringTrim')
                ->addFilter('StringToUpper')
				->addValidator('stringLength',false,array(0,8))
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

		$location= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/pedidos';
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
            $id = $options['id'] ? $options['id'] : 'id';
            foreach($table->fetchAll($select) as $row){
                $this->$element->addMultiOption($row->$id,$row->$options['showField']);
            }
        }
	}
}
?>