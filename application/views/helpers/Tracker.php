<?php
/**
 * Eases a bit the use of FlashMessenger
 *
 * On the controller: $this->view->flash(array('My freaking message.','And one more.'))
 * On the view: <?= $this->flash()->render() ?>
 *
 *
 *
 */

class Zend_View_Helper_Tracker
{
	protected $_urls;

    function tracker($urls = array())
    {
		$this->setUrls($urls);
		return $this;
    }

	public function setUrls($urls = array())
	{
		$this->_urls = $urls;
		return $this;
	}

	function render()
	{
		if(count($this->_urls) < 1)
			return null;

	
		$tracker = '<ul>';
		foreach($this->_urls as $text => $location){
			$tracker .= '<li><a href="' . $location . '">'  . $text . '</a></ul>';
		}

		$tracker .= '</ul>';
		return $tracker;

	}

}