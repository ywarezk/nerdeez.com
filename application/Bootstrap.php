<?php
/**
 * used for the cms
 */
require_once 'Plugins/AccessCheck.php';


/**
 * put site initialization here
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    /**
     * control managment system
     */
    protected function _initCMS(){
        $acl=new Application_Model_Acl();
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('Users'));
        $fc=Zend_Controller_Front::getInstance();
        $fc->registerPlugin(new Application_Plugin_AccessCheck($acl,$auth));
    }
    
    /**
     * init the meta information of the web site
     */
    protected function _initMeta(){
        //get the view
        $this->bootstrap('view');
        $view = $this->getResource('view');
        
        //set the encoding
        $view->setEncoding('UTF-8');
        
        //setting the headmeta
        $view->headMeta()->appendHttpEquiv('Content-type' , 'text/html; charset=UTF-8')
                         ->appendName('description' , constant('Application_Model_KSFunctions::cMETADESCRIPTIONMAIN'));
    }
    
    
    
    
    
    

}

