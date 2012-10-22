<?php
/**
 * controller for the main screen
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class IndexController extends Zend_Controller_Action
{

    /**
     * init stuff here
     */
    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * main index page
     */
    public function indexAction()
    {
        // action body
    }
    
    /**
     * when the user clicks the about page
     */
    public function aboutAction(){}

    /**
     * when the user reports an issue
     */
    public function sendreportAction(){
        //disable layout and view
        $this->_helper->layout()->disableLayout();
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);   
        
        //grab the params
        $data=$this->getRequest()->getParams();
        
        //check message
        $ksfunctions = new Application_Model_KSFunctions();
        $message = $ksfunctions -> sanitize_Title($data['message'] , 300);
        $mail = $ksfunctions -> sanitize_Title($data['mail'] , 100);
        if($message == null ){
            $userData=array(array('status'=>'failed','data' => 'report must be less than 300 chars and not emapty'));
            $dojoData= new Zend_Dojo_Data('status',$userData);
	    echo $dojoData->toJson();
	    return;
        }
        
        //mail myself the report and send success status
        $ksfunctions -> bugReport($message , $mail);
        
        $userData=array(array('status'=>'success'));
        $dojoData= new Zend_Dojo_Data('status',$userData);
        echo $dojoData->toJson();
        return;
    }
    
    /**
     * contains the terms of service disclaimer 
     */
    public function termsAction(){}

}

