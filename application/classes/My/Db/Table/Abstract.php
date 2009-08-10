<?

/**
 * Based on http://jmgtan.com/2008/10/24/zend-framework-model-based-validation-part-1/
 * developed by Jan Michael Tan http://jmgtan.com/about/
 *
 * @author Marcelo
 *
 */
abstract class My_Db_Table_Abstract extends Zend_Db_Table_Abstract {
    protected $_rules;
    protected $_validationMessages;
    protected $_invalidFields;
    protected $_defaultInvalidMessage = "Alguns campos estão inválidos.";
    protected $_isValid;

    public function isValid($data) {
        $this->_validationMessages = array();
        $this->_invalidFields = array();
        $this->_isValid = true;

		if(is_array($this->applyFilters)){
			$filter = new My_Model_Filter(get_class($this), $data);
			$data = $filter->filterAll($data);
		}

		foreach($this->_validate as $field => $rules){
			//skips verification of non mandatory fields
			if (false == array_key_exists('NotEmpty',$rules) && false == Zend_Validate::is($data[$field], 'NotEmpty') ){
				$skip[] = $field;
			}
			
			foreach($rules as $rule => $args){
				if (!in_array($field , $skip)){
					if (count($args['namespace']) > 0 ) {
						$result = Zend_Validate::is($data[$field], $rule,$args['options'], $args['namespace']);
					}
					elseif (count($args['options']) > 0 ) {
						$result = Zend_Validate::is($data[$field], $rule,$args['options']);
					}
					else {
						$result = Zend_Validate::is($data[$field], $rule);
					}
					if(false === $result){
						$this->_isValid = false;
						if($args['message'])
							$this->_validationMessages[] = $args['message'];
						$this->_invalidFields[] = $field;
						$skip[] = $field;
					}
				}
			}
		}

		return $this->_isValid ;
    }

    public function getValidationMessages() {
		if (!count($this->_validationMessages))
			$this->_validationMessages[] = "Um ou mais campos inválidos.";
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

	/**
	 *
	 * @param string/int $id
	 * @param string $fieldName
	 */
	public function skipDbUniqueValidation($id, $fieldName = null)
	{
		if (null == $fieldName)
			$fieldName = 'id';
		foreach($this->_validate as $field => $rules){
			foreach($rules as $rule => $args){
				if ($rule === 'Db_NoRecordExists') {
					$this->_validate[$field][$rule]['options'][2] = array('field'=>$fieldName ,'value'=> $id);
					break;
				}
			}
		}
		return $this;
	}

	/**
	 * Disable validation when it is neded. I.E. When adding a record to a database
	 *
	 * $this->disableValidationRulesForField('id')
	 *
	 *
	 * @param string $field
	 * @return Obj
	 */
	public function disableValidationRulesForField($field = null)
	{
		unset($this->_validate[$field]);
		return $this;
	}

	/**
	 * Disable validation when it is neded.
	 * I.E. When user has no permition to change the record,
	 * it has to skip NotEmpty validation
	 *
	 * $this->disableValidationRulesForField(array('salary','price'))
	 *
	 *
	 * @param string array $field
	 * @return Obj
	 */

	public function disableValidationRulesForFields($fields = array())
	{
		foreach($fields as $field)
		{
			$this->disableValidationRulesForField($field);
		}
		return $this;
	}
}