<?php
/**
 * classe pra converter formato db 1234.56 pra 1.234,56 e vice-versa
 */
class Dinheiro /*extends stdClass */ {

    static function toDbFormat($value)
    {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        return $value;
    }

    static function toBrazilFormat($value){

        $currency = new Zend_Currency('pt_BR');
        
        $currency->setFormat(array(
                    'display'=>Zend_Currency::NO_SYMBOL
        ));

		$value = is_numeric($value) ? $value : 0 ;


		// strange behavior for negative numbers
		//ie -585.00 => (585.00))
		$exclude = array('(',')');
		
        return str_replace($exclude, '', $currency->toCurrency($value));
    }

}