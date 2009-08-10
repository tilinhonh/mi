<?
abstract class AbstractModelValidator extends Zend_Db_Table_Abstract {
    protected $_rules;
    protected $_validationMessages;
    protected $_invalidFields;

    public function isValid($params = array()) {
        $this->_validationMessages = array();
        $this->_invalidFields = array();
        foreach($this->_rules as $rule) {
            foreach($params as $key=>$value) {
                if($rule['name'] == $key && !in_array($rule['name'],$skipRule)) {
                    $result = true;
                    if(isset($rule['options'])) {
                        $result = Zend_Validate::is($value, $rule['class'], $rule['options']);
                    }
                    else {
                        $result = Zend_Validate::is($value, $rule['class']);
                    }
                    if(!$result) {
                        $skipRule[] = $rule['name'];
                        $this->_validationMessages[] = $args['message'];
                        $this->_invalidFields[]=$rule['name'];
                    }
                    break;
                }
            }
        }
        if(sizeof($this->_validationMessages) > 0) {
            return false;
        }
        return true;
    }

    public function getValidationMessages() {
        return $this->_validationMessages;
    }
    public function getInvalidFields() {
        return $this->_invalidFields;
    }

	/**
	 * Add a error message causing the validation to return false
	 * didnt work
	 */
	public function addError($message){
		$this->_validationMessages[] = $message;
	}
}