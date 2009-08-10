<?php
class My_Validate_CPFOrCNPJ extends Zend_Validate_Abstract
{
    const NOT_VALID = 'notValid';

    protected $_messageTemplates = array(
        self::NOT_VALID        => "'%value%' não é nem CNPJ nem CPF.",
    );

    protected $_numericOnly;


    /**
     * Se o CPF/CNPJ for apenas numerico 12345678911234 12345678909 então $numbersOnly é true
     * Se não, 12.345.678/9112-34 123.456.789-09
	 *
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
        return $this;
    }


    /**
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
		$cpf = new My_Validate_CPF($this->_numericOnly);
		$cnpj = new My_Validate_CNPJ($this->_numericOnly);

		if ($cpf->isValid($value) || $cnpj->isValid($value))
			return true;
        
        $this->_setValue($value);
        $this->_error(self::NOT_VALID);
        return false;
    }
}