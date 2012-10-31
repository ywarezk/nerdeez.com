<?php

/**
 * controller for the admin section
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class AdminController extends Zend_Controller_Action{
    
    protected $_redirector = null;
    
    /**
     *get rredirector 
     */
    public function init(){ 
        //set the redirector
        $this->_redirector = $this->_helper->getHelper('Redirector');
    }
    
    /**
     * admin index page
     */
    public function indexAction(){}
    
    /**
     * a place for the admin to modify the universities table
     */
    public function universitiesAction(){
        //get all the universities
        $rsUniversities = NULL;
        $mUniversities = new Application_Model_DbTable_Universities();
        $rsUniversities = $mUniversities -> fetchAll($mUniversities -> select() -> order('title ASC'));
        $this -> view -> rsUniversities = $rsUniversities;
        $this -> view -> mUniversities = $mUniversities;
        
        //get all the countries
        /*$rsCountries = NULL;
        $mCountries = new Application_Model_DbTable_Countries();
        $rsCountries = $mCountries -> fetchAll($mCountries -> select() -> order('title ASC'));
        $this -> view -> rsCountries = $rsCountries;*/
        
        //get status and error params
        $aData=$this->getRequest()->getParams();
        $this -> view -> sError = $aData['error'];
        $this -> view -> sStatus = $aData['status'];
    }
    
    /**
     * a place for the admin to modify the courses table
     * gonna add here an option for adding by csv
     */
    public function coursesAction(){
        //get all the courses
        $rsCourses = NULL;
        $mCourses = new Application_Model_DbTable_Courses();
        $rsCourses = $mCourses -> fetchAll($mCourses -> select() -> order('title ASC'));
        $this -> view -> rsCourses = $rsCourses;
        $this -> view -> mCourses = $mCourses;
        
        //get all the universities
        $rsUniversities = NULL;
        $mUniversities = new Application_Model_DbTable_Universities();
        $rsUniversities = $mUniversities -> fetchAll($mUniversities -> select() -> order('title ASC'));
        $this -> view -> rsUniversities = $rsUniversities; 
        
        //get status and error params
        $aData=$this->getRequest()->getParams();
        $this -> view -> sError = $aData['error'];
        $this -> view -> sStatus = $aData['status'];
    }
    
    
    
    
    
}

?>
