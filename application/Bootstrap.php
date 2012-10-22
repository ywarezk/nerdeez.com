<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
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

