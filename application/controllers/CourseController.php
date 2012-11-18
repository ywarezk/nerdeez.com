<?php

/**
 * required in filehandlers controllers
 */
require_once APPLICATION_PATH . '/controllers/Nerdeez_Controller_Action_FileHandler.php';

/**
 * Display the course page
 *
 * @author Yariv
 * @copyright Nerdeez.com Ltd.
 */
class CourseController extends Nerdeez_Controller_Action_FileHandler{
    
    /**
     * the main course page 
     */
    public function courseAction(){
        //get the params
        $iId = 0;
        $aData=$this->getRequest()->getParams();
        $iId = $this -> _aData['id'];
        $iFolder = $this -> _aData['folder'];
        $sError = $this -> _aData['error'];
        $sStatus = $this -> _aData['status'];
        $this -> view -> sError = $sError;
        $this -> view -> sStatus = $sStatus;
        
        //find the course row in the database if didnt find redirect to bad url
        $rCourse = NULL;
        $mCourses = new Application_Model_DbTable_Courses();
        $rCourse = $mCourses ->getRowWithId($iId);
        if ($rCourse == NULL){
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
        $this -> view -> rsFiles = $rsFiles;
        
        //find the folders
        $rsFolders = NULL;
        $mFolders = new Application_Model_DbTable_Folders();
        $rsFolders = $mFolders -> fetchAll($mFolders -> select() -> order('title ASC'));
        $this ->view -> rsFolders = $rsFolders;
        
        //find the folder row
        $rFolder = NULL;
        if ($iFolder != NULL){
            $rsFolders = $mFolders -> fetchAll($mFolders -> select() -> where ('id = ?' , $iFolder));
            if ($rsFolders -> count() > 0)
                $rFolder = $rsFolders -> getRow(0);
        }
        $this -> view -> rFolder = $rFolder;
        
        
    }
    
    /**
     * when the user submits the file submit dialog
     * we need to get all the files and put it in the files database
     */
    public function postfilesAction(){
        //disable view rendering
        $this->_helper->layout()->disableLayout(); 
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        
        //get all the params
        $iSerial = $this -> _aData['serial'];
        $iFolderPapa = $this -> _aData['folder_papa'];
        $iHwNumber = $this -> _aData['hw_number'];
        $iId = $this -> _aData['id'];
        
        //get the folder papa row
        $rFolderPapa = NULL;
        $mFolders = new Application_Model_DbTable_Folders();
        $rFolderPapa = $mFolders ->getRowWithId($iFolderPapa);
        if ($rFolderPapa == NULL){
            $this->_redirector->gotoUrl('/error/error/message/' . urlencode('Bad Params'));
            return;
        }
        
        //get the folder id of the new files row
        $iFoldersId = $rFolderPapa['id'];
        if ($rFolderPapa['title'] === 'H.W'){
            $rFolder = $mFolders ->getRowWithId($iHwNumber);
            if ($rFolder !== NULL)
                $iFoldersId = $rFolder['id'];
        }
        
        //iterate on all the files uploaded and create a row in the files table for all of them
        $mFiles = new Application_Model_DbTable_Files();
        foreach ($this -> _aFiles[$iSerial] as $nfFile) {
            /* @var $nfFile Nerdeez_Files */
            $nfFile = unserialize (serialize ($nfFile));
            $mFiles ->insertWithoutArray($nfFile -> sName, 
                    $nfFile -> sUrl, 
                    $iId, 
                    $iFoldersId , 
                    $nfFile -> iSize); 
        }
        
        //redirect the user to the place he uploaded the files
        $this->_redirector->gotoUrl('/course/course/id/' . $iId . '/folder/' . $iFoldersId);
        return;
    }
    
    /**
     * when the user downloads files
     */
    public function downloadfilesAction(){
        //disable view rendering
        $this->_helper->layout()->disableLayout(); 
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        
        //grab the ids
        $aIds = $this -> _aData['ids'];
        
        //create the model for the posts and files
        $mFiles = new Application_Model_DbTable_Files();
        
        //grab the rows
        $rsFiles = $mFiles->find($aIds);
        if($rsFiles->count() == 0){
            $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Invalid params'));
            return;
        }
        
        if ($rsFiles -> count() == 1){
            $rFile = $rsFiles -> getRow(0);
            $this->download($rFile['path'] , $rFile['title']);
            return;
        }
        
        
        $s3 = new Nerdeez_Service_Amazon_S3();
        $aFiles = array();
        foreach ($rsFiles as $rFile){
            //$aFiles[] = $rFile['path'];
            $aFiles[] = $s3->getObject($rFile['path']);
        }
        
        //create the zip file 
        $zZip = new Application_Model_Zip();
        $zipfile = $zZip -> createZip($aFiles);
        //send the zip file to download
        $this->downloadFile($zipfile,$zipfile);
        
    }
}

?>
