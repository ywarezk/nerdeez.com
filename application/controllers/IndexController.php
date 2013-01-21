<?php

/**
 * required in all my controllers
 */
require_once APPLICATION_PATH . '/controllers/Nerdeez_Controller_Action.php';

/**
 * controller for the main screen
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class IndexController extends Nerdeez_Controller_Action
{

    /**
     * main index page
     */
    public function indexAction()
    {
        //get all the courses and pass them to the view
        $this -> view -> rsCourses = NULL;
        $mCourses = new Application_Model_DbTable_Courses();
        $this -> view -> rsCourses = $mCourses -> fetchAll ($mCourses -> select() -> order('title ASC'));
        
        //get all the universities
        $rsUniversities = NULL;
        $mUniversities = new Application_Model_DbTable_Universities();
        $rsUniversities = $mUniversities -> fetchAll($mUniversities -> select() 
                -> order('title ASC'));
        $this -> view -> rsUniversities = $rsUniversities;
        
        //send the is activated if set
        $bIsActivated = FALSE;
        if(isset($this->_aData['is_activated']) && strtolower($this->_aData['is_activated']) === 'true'){
            $bIsActivated = TRUE;
        }
        $this->view->bIsActivated = $bIsActivated;
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
        $message = $this -> _aData['message'];
        $mail = $this -> _aData['email'];
        
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
    
    /**
     * contains the privacy statment
     */
    public function privacyAction(){}
    
    /**
     * when the user want s to view the community guidelines
     */
    public function communityguidelinesAction(){}
    
    /**
     * in the guidelines there is a safety section
     */
    public function safetyAction(){}
    
    /**
     * when the user input text in the search course text box
     * @deprecated
     *  
     */
    public function searchcourseAction(){ 
        //disable view and layout
        $this->_helper->layout()->disableLayout();
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        
        //get the course rows that match the search
        $mCourses = new Application_Model_DbTable_Courses();
        $rsCourses = $mCourses -> search($this -> _aData['search']);
        
        //get all the universities
        $rsUniversities = NULL;
        $mUniversities = new Application_Model_DbTable_Universities();
        $rsUniversities = $mUniversities -> fetchAll($mUniversities -> select());
        
        //get the html that i need to pass
        $html = NULL;
        ob_start();
        echo $this->view->partial('partials/courselist.phtml', array('rsCourses'  =>  $rsCourses , 'rsUniversities' => $rsUniversities));
        $html = ob_get_contents();
        ob_end_clean();
        
        //pass everything back
        $userData=array(array('status'=>'success' , 'data' => $html));
        $dojoData= new Zend_Dojo_Data('status',$userData);
        echo $dojoData->toJson();
        return;
    }
    
    /**
     * explaining about the open source project
     */
    public function opensourceAction(){}

}

