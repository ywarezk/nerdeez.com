<?php

/**
 * required in all my controllers
 */
require_once APPLICATION_PATH . '/controllers/Nerdeez_Controller_Action.php';

/**
 * Display the course page
 *
 * @author Yariv
 * @copyright knowledge-share Ltd.
 */
class CourseController extends Nerdeez_Controller_Action{
    
    /**
     * the main course page 
     */
    public function courseAction(){
        //get the params
        $iId = 0;
        $aData=$this->getRequest()->getParams();
        $iId = $aData['id'];
        $sError = $aData['error'];
        $sStatus = $aData['status'];
        $this -> view -> sError = $sError;
        $this -> view -> sStatus = $sStatus;
        
        //if the id is not positive numeric number redirect to bad url
        if (!is_numeric($iId) || $iId <= 0){
            $this->_redirector->gotoUrl('/error/error/message/' . urlencode(constant("Application_Model_KSFunctions::cERROR_404")));
            return;
        }
        
        //find the course row in the database if didnt find redirect to bad url
        $rCourse = NULL;
        $mCourses = new Application_Model_DbTable_Courses();
        $selSelectCourses = $mCourses -> select() -> where ('id = ?' , $iId);
        $rsCourses = $mCourses -> fetchAll ($selSelectCourses);
        if ($rsCourses !== NULL || $rsCourses -> count() > 0){
            $rCourse = $rsCourses -> getRow(0);
        }
        else{
            $this->_redirector->gotoUrl('/error/error/message/' . urlencode(constant("Application_Model_KSFunctions::cERROR_404")));
            return;
        }
        $this -> view -> rCourse = $rCourse; 
        
        //find the file rows
        $rsFiles = NULL;
        $mFiles = new Application_Model_DbTable_Files();
        $rsFiles = $mFiles -> fetchAll($mFiles -> select() 
                -> where ('courses_id = ?' , $iId)
                -> order ('title ASC'));
    }
    
}

?>
