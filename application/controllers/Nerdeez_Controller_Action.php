<?php

/**
 * required for the extend part
 */
require_once 'Zend/Controller/Action.php';


/**
 * all of nerdeez controllers will extend this class
 *
 * @author Yariv Katz
 * @copyright nerdeez.com Ltd.
 * @version 1.0
 */
abstract class Nerdeez_Controller_Action extends Zend_Controller_Action{
    
    /**
     * redirector helper for other pages
     * @var Zend_Controller_Action_Helper_Redirector 
     */
    protected $_redirector = null;
    
    /**
     * common init for all my controllers
     */
    public function init(){ 
        //set the redirector
        $this->_redirector = $this->_helper->getHelper('Redirector');
    }
    
    /**
     * gets the referer for this page
     * @return String the referer
     */
    protected function getReferer(){
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $referer = $request->getHeader('referer'); 
        return $referer;
    }
    
    
    
    
}

?>
