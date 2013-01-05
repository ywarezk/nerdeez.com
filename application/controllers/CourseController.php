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
        //get the pfarams
        $iId = $this -> _aData['id'];
        $iFolder = $this -> _aData['folder'];
        $sError = $this -> _aData['error'];
        $sStatus = $this -> _aData['status'];
        $this -> view -> sError = $sError;
        $this -> view -> sStatus = $sStatus;
        
        //if folder id is null set it to zero
        if ($iFolder == NULL){
            $iFolder = 0;
        }
        
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
                -> where ('folders_id = ?' , $iFolder)
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
        
        //get the papa folder 
        $rPapaFolder = NULL;
        if ($rFolder != NULL && $rFolder['papa'] != -1){
            $rPapaFolder = $mFolders -> fetchRow($mFolders -> select() -> where('id = ?', $rFolder['papa']));
        }
        $this -> view -> rFolderPapa = $rPapaFolder;
        
        //get all the folders
        $rsFolders = NULL;
        $rsFolders = $mFolders ->fetchAll($mFolders ->select() 
                -> where('courses_id = 0 OR courses_id = ?', $rCourse['id'])
                -> order('title ASC'));
        $this -> view -> rsFolders = $rsFolders;
        
        //find the folders
        $rsFoldersShown = NULL;
        $selFolders = $mFolders ->select();
        $selFolders = $selFolders -> where('courses_id = 0 OR courses_id = ?', $rCourse['id']);
        if ($rFolder != NULL){
            $selFolders = $selFolders 
                -> where('papa = ?' , $rFolder['id']);
        }
        else{
            $selFolders = $selFolders -> where('papa = -1');
        }
        $selFolders = $selFolders ->order('title ASC');
        $rsFoldersShown = $mFolders -> fetchAll($selFolders);
        $this ->view -> rsFoldersShown = $rsFoldersShown;
        
        //get the id of the parent h.w folder
        $iHwFolder = 0;
        $rParentHWFolder = $mFolders ->fetchRow($mFolders -> select() 
                -> where ('title = ?', 'H.W') 
                -> where('papa = ?', -1));
        $iHwFolder = $rParentHWFolder['id'];
        $this -> view -> iHwFolder = $iHwFolder;
    }
    
    /**
     * when the user submits the file submit dialog
     * we need to get all the files and put it in the files database
     */
    public function postfilesAction(){
        //disable view rendering
        $this ->disableView();
        
        //get all the params
        $iFolderPapa = $this -> _aData['folder_papa'];
        $iHwNumber = $this -> _aData['hw_number'];
        $iId = $this -> _aData['id'];
        $sNewFolder = $this -> _aData['coursefolder'];
        
        //get the folder papa row
        $rFolderPapa = NULL;
        $mFolders = new Application_Model_DbTable_Folders();
        $rFolderPapa = $mFolders ->getRowWithId($iFolderPapa);
        if ($rFolderPapa == NULL){
            $this->_redirector->gotoUrl('/error/error/message/' . urlencode('Bad Params'));
            return;
        }
        
        //deal with the hw number id
        $iFoldersId = $rFolderPapa['id'];
        if ($rFolderPapa['title'] === 'H.W'){
            $rFolder = $mFolders ->getRowWithId($iHwNumber);
            if ($rFolder !== NULL)
                $iFoldersId = $rFolder['id'];
        }
        
        //should i create a new folder in the others sections for this course
        if ($sNewFolder !== ''){
            $rFolderOther = $mFolders ->fetchRow($mFolders -> select() -> where('title = ?', 'Other'));
            $iOtherId = $rFolderOther['id'];
            $rFolderExist = $mFolders -> fetchRow ($mFolders -> select() 
                    -> where('title = ?', $sNewFolder)
                    -> where('papa = ?', $iOtherId));
            if ($rFolderExist == NULL){
                $iFoldersId = $mFolders ->insertWithoutArray($sNewFolder, $iOtherId, $iId);
            }
            else{
                $iFoldersId = $rFolderExist['id'];
            }
        }
        
        //create the s3 object
        $s3 = new Nerdeez_Service_Amazon_S3();
        $s3->createBucket("nerdeez");
        
        //iterate on all the files uploaded and create a row in the files table for all of them
        $mFiles = new Application_Model_DbTable_Files();
        foreach ($this -> _aFiles as $nfFile) {
            /* @var $nfFile Nerdeez_Files */
            $bIsExist = $this->isFileExist($nfFile -> sHash);
            if ($bIsExist === FALSE){
                $mFiles ->insertWithoutArray($nfFile -> sName, 
                    $nfFile -> sUrl, 
                    $iId, 
                    $iFoldersId , 
                    $nfFile -> iSize ,
                    $nfFile -> sHash); 
            }
            else{
                $mFiles ->insertWithoutArray($nfFile -> sName, 
                    $bIsExist['path'], 
                    $iId, 
                    $iFoldersId , 
                    $nfFile -> iSize ,
                    $nfFile -> sHash); 
                $s3 ->removeObject($nfFile -> sUrl);
            }
        }
        
        //clear the session
        Zend_Session::start(); 
        unset($_SESSION['kstempfiles']);
        
        //redirect the user to the place he uploaded the files
        $this->_redirector->gotoUrl('/course/course/id/' . $iId . '/folder/' . $iFoldersId);
        return;
    }
    
    /**
     * when the user downloads files
     */
    public function downloadfilesAction(){
        //disable view rendering
        $this->disableView();
        
        //if the user is not auth to download than exit
        if (!$this->isAuthDownload()) return;
        
        //grab the params ids , disposition , folders
        $aIds = $this -> _aData['ids'];
        $iCourse = $this -> _aData['id'];
        $aFolders = $this -> _aData['folders'];
        $sDisposition = $this -> _aData['disposition'];
        
        //set the icourse to be default od zero
        if ($iCourse == NULL)
            $iCourse = 0;
        
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
        if ($iCourse != 0){
            $selFilesSelect = $selFilesSelect -> where('courses_id = ?' , $iCourse);
        }
        $rsFiles = $mFiles->fetchAll($selFilesSelect);
        if($rsFiles->count() == 0){
            $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Invalid params'));
            return;
        }
        
        //if there is only one file
        if ($rsFiles -> count() == 1){
            $rFile = $rsFiles -> getRow(0);
            $this->download($rFile['path'] , $rFile['title'] , $sDisposition);
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
            $sPath = $sUploadDir . $rParent['title'] . '/' . $rFile['title'];
            file_put_contents($sPath, $s3->getObject($rFile['path']));
            $aFiles[]=$sPath;
        }
        
        //create the zip file 
        $zipfile = $this -> createZip($aFiles);
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
                        <h3> file id:' . $iId . '</h3>
                    </body>
                </html>
            ';
        
        //send the report via mail
        $this->reportByMail($sEmail, $sBody, 'Copy right violation report');
        
        //send success
        $this ->ajaxReturnSuccess();
    }
    
    /**
     * check if the user is authorized to download the files he wants
     */
    public function checkauthAction(){
        $this->disableView();
        if($this->isAuthDownload()){
            $this->ajaxReturnSuccess();
        }
        else{
            $this->ajaxReturnFailed();
        }
    }
    
    /**
     * check if the user is authorized for the download
     * @return Boolean True if authorized false otherwise
     */
    private function isAuthDownload(){
        //if the user is registered than return true
        if($this->isRegistered())return TRUE;
        
        //grab the params ids , disposition , folders
        $aIds = $this -> _aData['ids'];
        $iCourse = $this -> _aData['id'];
        $aFolders = $this -> _aData['folders'];
        
        //if there is a folder in the array return false
        if (count($aFolders) > 0) return FALSE;
        
        //if there is more than one file return false
        if (count($aIds) > 1)return FALSE;
        
        //only one file than return true
        return TRUE;
        
    }
}

?>
