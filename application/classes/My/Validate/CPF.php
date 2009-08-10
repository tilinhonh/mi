<?php
class My_Validate_CPF extends Zend_Validate_Abstract
{
	const NOT_CPF = 'notCpf';


	protected $_messageTemplates = array(
        self::NOT_CPF        => "'%value%' não é um CPF válido.",
    );


	protected $_numericOnly;
	/**
	 * Se o CPF for apenas numerico 12345678909 então $numbersOnly é true
	 * Se não, 123.456.789-09
	 * @param bool $numbersOnly
	 */
	public function __construct($numbersOnly = false)
	{
		$this->_setNumericOnly($numbersOnly);
	}

    /**
     * Sets $_numericOnly
     * @param bool $bool
     */
    private function _setNumericOnly($bool = false){
        $this->_numericOnly = $bool;
        if($this->_numericOnly === true){
            $this->_regexp = '/^(\d){11}$/';
        }
        else{
            $this->_regexp = '/^(\d){3}(\.\d{3}){2}-(\d){2}$/';
        }
        return $this;
    }


    /**
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
         // checks regexp, first and second validation Digit
         if ( preg_match($this->_regexp, $value)
             && $this->_checkDigitOne($this->_removeNonDigits($value))
             && $this->_checkDigitTwo($this->_removeNonDigits($value))
                                                                                ){
            return true;
         }
         $this->_setValue($value);
         $this->_error(self::NOT_CPF);
         return false;
    }


    /**
     *
     * @param string $value
     * @return bool
     */
    private function _checkDigitOne($value)
    {
        $multipliers = array(10,9,8,7,6,5,4,3,2);
        return $this->_getDigit($value, $multipliers) == $value{9};
    }


    /**
     *
     * @param string $value
     * @return bool
     */

    private function _checkDigitTwo($value)
    {
        $multipliers = array(11,10,9,8,7,6,5,4,3,2);
        return $this->_getDigit($value, $multipliers) == $value{10};
    }

    /**
     *
     * @param string $value
     * @param array(int) $multipliers
     * @return int
     */
    private function _getDigit($value, $multipliers)
    {
        foreach($multipliers as $key => $v){
            $sum += $value{$key} * $v;
        }
        $digit = $sum % 11;
        if ($digit < 2) {
            $digit = 0;
        }else{
            $digit = 11 - $digit;
        }
        return $digit;
    }



    /**
     *
     * @param string $value
     * @return string
     */
    private function _removeNonDigits($value)
    {
        return preg_replace('/\D/', '', $value);
    }
}