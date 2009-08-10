<?php

class ProdutoPicture 
{
	
	protected $_user;
	protected $_errorMessages = array();
	protected $_uploadPath = '/images/_produtos/';
	protected $_filename;
	protected $_fileInfo;
	protected $_fileExtention;
	protected $_uploader;
	protected $_produto;
	
	/**
	 *
	 * @param Zend_Db_Table_Row
	 */
	function  __construct($produto) {
		$this->_produto = $produto;
		$this->_uploadPath = $_SERVER['DOCUMENT_ROOT'] . $this->_uploadPath;
		$this	->_startUploader()
				->_setFilename()
				->_configUploader();
	}


	/**
	 * starts uploader
	 * @return object
	 */
	private function _startUploader()
	{
		$this->_uploader = new Zend_File_Transfer_Adapter_Http();
		return $this;
	}

	/**
	 * configs uploader
	 * @return object
	 */
	private function _configUploader()
	{

		$file =  $this->_uploader->getFileInfo();

		$this->_uploader->setDestination($this->_uploadPath)
				->addFilter('Rename', array(
								'source' => $file['image']['tmp_name'],
								'target' => $this->_uploadPath . $this->_filename,
								'overwrite' => true ,
							))
				->addValidator('IsImage', false)
				->addValidator('ImageSize', false,
                      array('minwidth' => 300,
                            'maxwidth' => 300,
                            'minheight' => 225,
                            'maxheight' => 225)
                      );


		return $this;
	}


	/**
	 * Uploads file
	 * @return bool
	 */
	public function upload()
	{
		if(!$this->_uploader->receive()){
			$this->_setErrors($this->_uploader->getMessages());
			return false;
		}
		return $this->_dbSaveName();
	}

	/**
	 * save produto picture name to the database
	 * @return bool
	 */
	protected function _dbSaveName()
	{
		try{
			$string = $this->_produto->pictures . ';' . $this->_filename;
			$this->_produto->pictures = trim($string, ';');
			$this->_produto->save();
			new QueryLogger();
		}catch(Exception $e){
			$this->_setErrors($e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * sets error messages
	 * @param array $errors
	 * @return this
	 */
	protected function _setErrors($errors = array())
	{
		$this->_errorMessages = $errors;
		return $this;
	}


	/**
	 * return error messages
	 * @return array string
	 */
	public function getMessages()
	{
		return $this->_errorMessages;
	}

	/**
	 * sets file name
	 * @return this
	 */
	protected function _setFilename()
	{
		$filenames = explode(';', $this->_produto->pictures);
		$number =  $filenames[0] ? count($filenames) : 0;
		$this->_filename = $this->_produto->id
								.'_'
								. $number
								.'.'
								. $this->_getFileExtention();
		return $this;
	}

	/**
	 * gets file extention
	 * @return string
	 */
	protected function _getFileExtention()
	{
		$file = $this->_uploader->getFileInfo();
		$fileParts = explode('.',$file['image']['name']);
		$extention = $fileParts[(count($fileParts) - 1)];
		return strtolower($extention);
	}
}