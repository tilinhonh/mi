<?php

/**
 * Description of ModelFilterer
 *
 * @author marcelo
 */
class My_Model_Filter {

	private $_filterClassPrefix = 'Zend_Filter_';

	/**
	 *
	 * @param string $model
	 * @param array $data
	 */
	function  __construct($model,$data = array())
	{
		$this->setData($data);
		$this->_setFilters($model);
	}
	

	/**
	 * Sets an array of data
	 * @param array $data
	 * @return obj
	 */
	public function setData($data = array())
	{
		$this->_data = $data;
		return $this;
	}


	private function _setFilters($model)
	{
		$this->_model = new $model();
		$this->_filters = $this->_model->applyFilters;
	}


	public function filter($field, $value = null)
	{
		//if no value were provided, it means the value was already set
		if (!$value){
			$value = $this->_getData($field);
		}

		$value = $this->_generalFilter($value);

		$value = $this->_specificFilter($field, $value);


		return strlen($value) > 0 ? $value : null;
	}


	/**
	 *
	 * @param string $field
	 * @return void
	 */
	private function _getData($field)
	{
		return $this->_data[$field];
	}

	/**
	 *
	 * @param string $value
	 * @return string
	 */
	private function _generalFilter($value)
	{
		return $this->_applyFilterFor('all',$value);
	}

	private function _specificFilter($field,$value)
	{
		return $this->_applyFilterFor($field, $value);
	}

	/**
	 *
	 * @param string $filter
	 * @param string $value
	 * @return string
	 */
	private function _applyFilterFor($filter, $value)
	{
		foreach($this->_filters[$filter] as $name => $options){
			//options were provided
			if (is_array($options)){
				$filterClass = $this->_filterClassPrefix . $name;
				$_filter = new $filterClass($options);
			}else{
				$filterClass = $this->_filterClassPrefix . $options;
				$_filter = new $filterClass();
			}
			$value = $_filter->filter($value);
		}
		return $value;
	}

	public function filterAll($data = null)
	{
		if($data == null)
			$data = $this->_data;
		$filtered = array();
		foreach($data as $name => $value){
			$filtered[$name] = $this->filter($name, $value);
		}
		return $filtered;
	}
}
?>
