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

class Zend_View_Helper_Flash extends Zend_Controller_Action_Helper_FlashMessenger
{
    protected $_paragraphClass = 'success';
    protected $_divClass = 'topMessages';

    function flash($messages = null)
    {
        if (null != $messages) {
            if (is_array($messages)) {
                $this->addMessages($messages);
            }else{
                $this->addMessage($messages);
            }
        }
        return $this;
    }

    /**
     *
     * @param array $messages
     * @return this
     */
    function addMessages($messages = array())
    {
        foreach($messages as $message){
            $this->addMessage($message);
        }
        return $this;
    }


    public function render()
    {
        $htmlMessage = '<div class="' . $this->_divClass . '">';

        foreach($this->getMessages() as $message){
            $htmlMessage .= '<p class="'. $this->_paragraphClass .'">- ' . $message . '</p>';
        }

        $htmlMessage  .= '</div>';

        return count($this->getMessages()) ? $htmlMessage : null;
    }


    /**
     *    Sets the paragraph style class
     *
     * @param string $class
     * @return object
     */
    public function setParagraphClass($class = null)
    {
		if($class){
			$this->_paragraphClass = $class;
		}
        return $this;
    }

    /**
     *    Sets the div style class
     *
     * @param string $class
     * @return object
     */
    function setDivClass($class = null)
    {
        $this->_divClass = $class;
        return $this;
    }

    /**
     *    Sets both, div and paragraph style classes
     *
     * @param string $divClass
     * @param string $paragraphClass
     * @return object
     */

    function setClasses($divClass, $paragraphClass)
    {
        $this->setDivClass($divClass);
        $this->setParagraphClass($paragraphClass);
        return $this;
    }










}