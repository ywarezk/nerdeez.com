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
        
        //find the folder row
        $rFolder = NULL;
        $mFolders = new Application_Model_DbTable_Folders();
        if ($iFolder != NULL){
            $rsFolders = $mFolders -> fetchAll($mFolders -> select() -> where ('id = ?' , $iFolder));
            if ($rsFolders -> count() > 0)
                $rFolder = $rsFolders -> getRow(0);
        }
        $this -> view -> rFolder = $rFolder;
        
        //get all the folders
        $rsFolders = NULL;
        $rsFolders = $mFolders ->fetchAll($mFolders ->select() -> order('title ASC'));
        $this -> view -> rsFolders = $rsFolders;
        
        //find the folders
        $rsFoldersShown = NULL;
        $selFolders = $mFolders ->select();
        if ($rFolder != NULL){
            $selFolders = $selFolders ->where('papa = ?' , $rFolder['id']);
        }
        else{
            $selFolders = $selFolders ->where('papa = -1');
        }
        $selFolders = $selFolders ->order('title ASC');
        $rsFoldersShown = $mFolders -> fetchAll($selFolders);
        $this ->view -> rsFoldersShown = $rsFoldersShown;
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
        
        //grab the params ids , disposition , folders
        $aIds = $this -> _aData['ids'];
        $sDisposition = $this -> _aData['disposition'];
        $aFolders = $this -> _aData['folders'];
        
        //if the user entered folders than deal with them
        $aFoldersIds = array();
        if (count($aFolders) > 0){
        
            //grab all the sons folders
            $rsFolderSons = NULL;
            $mFolders = new Application_Model_DbTable_Folders();
            $rsFolderSons = $mFolders ->fetchAll($mFolders -> select() -> where('papa IN (?)' , $aFolders));

            //grab all the father folders
            $rsFolderesPapa = NULL;
            $rsFolderesPapa = $mFolders ->fetchAll($mFolders ->select() ->where('id IN (?)' , $aFolders));

            //create array of ids from all the folders you found
            foreach ($rsFolderSons as $rFolder) {
                $aFoldersIds[]=$rFolder['id'];
            }
            foreach ($rsFolderesPapa as $rFolder) {
                $aFoldersIds[]=$rFolder['id'];
            }
            
        }
        
        //create the model for the posts and files
        $mFiles = new Application_Model_DbTable_Files();
        
        //grab the rows
        $selFilesSelect = $mFiles ->select();
        if (count($aIds) > 0){
            $selFilesSelect = $selFilesSelect -> where('id IN (?)' , $aIds);
        }
        if(count($aFoldersIds) > 0){
            $selFilesSelect = $selFilesSelect -> orwhere('folders_id IN (?)' , $aFoldersIds);
        }
        $rsFiles = $mFiles->fetchAll($selFilesSelect);
        if($rsFiles->count() == 0){
            $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Invalid params'));
            return;
        }
        
        //if there is only one file
        if ($rsFiles -> count() == 1){
            $rFile = $rsFiles -> getRow(0);
            ($sDisposition != NULL) ? $this->download($rFile['path'] , $rFile['title'] , $sDisposition) : $this->download($rFile['path'] , $rFile['title']);
            return;
        }
        
        //grab the upload dir
        $sUploadDir = NULL;
        $config = new Zend_Config_Ini('../application/configs/application.ini','production');
        $sUploadDir = $config->uploaddir;
        
        //if there is many files
        $s3 = new Nerdeez_Service_Amazon_S3();
        $aFiles = array();
        $aParents = array();
        foreach ($rsFiles as $rFile){
            /* @var $rFile Zend_Db_Table_Row */
            //create the folder dir
            $rParent = $rFile ->findParentRow('Application_Model_DbTable_Folders');
            mkdir($sUploadDir . $rParent['title']);
            $aParents[]=$rParent;
            
            //save all the files in the hd
            $sPath = NULL;
            $iRandPrefix = rand(0, 99999);
            $aName = explode('nerdeez/', $rFile['path']);
            $sPath = $sUploadDir . $rParent['title'] . '/' . $iRandPrefix . '_' . $aName[1];
            file_put_contents($sPath, $s3->getObject($rFile['path']));
            $aFiles[]=$sPath;
        }
        
        //create the zip file 
        $zZip = new Application_Model_Zip();
        $zipfile = $zZip -> createZip($aFiles);
        //send the zip file to download
        $this->downloadFile($zipfile,$zipfile);
        
        //delete all the files and folders created
        unlink($zipfile);
        foreach($aFiles as $sFile){
            unlink($sFile);
        }
        foreach ($aParents as $rParent) {
            rmdir($sUploadDir . $rParent['title']);
        }
        
    }
    
    /**
     * when the user reports a copyright violation
     */
    public function flagAction(){
        //disable view 
        $this->disableView();
        
        //get the params sent
        $iId = $this -> _aData['id'];
        $sMessage = $this -> _aData['message'];
        $sTitle = $this -> _aData['title'];
        
        //get the admin mail
        $sEmail = $this -> getFromConfig('BugReportMail');
        
        //create the body
        $sBody = NULL;
        $sBody = 
            '
                <html>
                    <body>
                        <h1>Copy right violation report</h1>
                        <h2>Reason: ' . $sTitle . '</h2>
                        <h3>Freetext: ' . $sMessage . '</h3>
                    </body>
                </html>
            ';
        
        //send the report via mail
        $this->reportByMail($sEmail, $sBody, 'Copy right violation report');
        
        //send success
        $this ->ajaxReturnSuccess();
    }
}

?>
