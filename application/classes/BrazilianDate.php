<?php
/**
 * classe pra converter formato db 2009-07-10 pra 10/07/2009 e vice-versa
 */
class BrazilianDate /*extends stdClass */ {

    static function toDbFormat($value = null)
    {
		if(!$value)
			return null;

		$input_format = 'dd/MM/YYYY';
		$output_format = 'YYYY-MM-dd';

        $date = new Zend_Date($value, $input_format);

		$string = Zend_Date::isDate($value, $input_format) ? $date->toString($output_format) : null;
		return $string;
    }

    static function toBrazilFormat($value){
		if(!$value)
			return null;
		$input_format = 'YYYY-MM-dd';
		$output_format = 'dd/MM/YYYY';

		// value may already be in brazilian format
		if(Zend_Date::isDate($value, $output_format))
			return $value;

        $date = new Zend_Date($value, $input_format);

		$string = Zend_Date::isDate($value, $input_format) ? $date->toString($output_format) : null;
		return $string;
    }

}