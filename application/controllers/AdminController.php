<?php

/**
 * required in filehandlers controllers
 */
require_once APPLICATION_PATH . '/controllers/Nerdeez_Controller_Action_FileHandler.php';

/**
 * controller for the admin section
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class AdminController extends Nerdeez_Controller_Action_FileHandler{

    /**
     * init function for this controller 
     * all actions will pass first through here
     */
    public function init(){ 
        //call the parent init
        parent::init();
        
        //set the layout to be the admin
        Zend_Layout::getMvcInstance()->assign('nestedLayout', 'admin');
    }
    
    /**
     * admin index page
     */
    public function indexAction(){
        //set the layout to be the admin
        Zend_Layout::getMvcInstance()->assign('nestedLayout', 'guest');
    }
    
    /**
     * a place for the admin to modify the universities table
     */
    public function universitiesAction(){
        //set the title of the page
        $this -> view -> sTitle = 'Universities';
        
        //get the columns of the table
        $aCols = NULL;
        $mUniversities = new Application_Model_DbTable_Universities();
        $aCols = $mUniversities->info(Zend_Db_Table_Abstract::COLS);
        $this -> view -> aCols = $aCols; 
        
        //get all the rows from the database
        $rsRows = NULL;
        $rsRows = $mUniversities -> fetchAll($mUniversities -> select() -> order('title ASC'));
        $this -> view -> rsRows = $rsRows;
        
        //set the model name 
        $this -> view -> sModelName = 'Application_Model_DbTable_Universities';
        
        //get the rowset of the papa
        $rsPapas = NULL;
        $this -> view -> rsPapas = $rsPapas;
        
        //set the column which is papa
        $this -> view -> sPapaCol = array();
    }
    
    /**
     * a place for the admin to modify the courses table
     * gonna add here an option for adding by csv  
     */
    public function coursesAction(){
        //set the title of the page
        $this -> view -> sTitle = 'Courses';
        
        //set the model name 
        $this -> view -> sModelName = 'Application_Model_DbTable_Courses';
        
        //set the column which is papa
        $this -> view -> sPapaCol = array(
            'universities_id'
         );
        
        //get the columns of the table
        $aCols = NULL;
        $mCourses = new Application_Model_DbTable_Courses();
        $aCols = $mCourses->info(Zend_Db_Table_Abstract::COLS);
        $this -> view -> aCols = $aCols;
        
        //get all the rows from the database
        $rsRows = NULL;
        $rsRows = $mCourses -> fetchAll($mCourses -> select() -> order('title ASC'));
        $this -> view -> rsRows = $rsRows;
        
        //get the rowset of the papa
        $rsPapas = array();
        $mUniversities = new Application_Model_DbTable_Universities();
        $rsPapas['universities_id'] = $mUniversities -> fetchAll($mUniversities 
                -> select() 
                -> order('id ASC'));
        $this -> view -> rsPapas = $rsPapas;
        
    }
    
    /**
     * if you want to edit the folders table go here
     */
    public function foldersAction(){
        //set the title of the page
        $this -> view -> sTitle = 'Folders';
        
        //set the model name 
        $this -> view -> sModelName = 'Application_Model_DbTable_Folders';
        
        //set the column which is papa
        $this -> view -> sPapaCol = array();
        
        //get the columns of the table
        $aCols = NULL;
        $mFolders = new Application_Model_DbTable_Folders();
        $aCols = $mFolders->info(Zend_Db_Table_Abstract::COLS);
        $this -> view -> aCols = $aCols;
        
        //get all the rows from the database
        $rsRows = NULL;
        $rsRows = $mFolders -> fetchAll($mFolders -> select() -> order('title ASC'));
        $this -> view -> rsRows = $rsRows;
        
        //get the rowset of the papa
        $rsPapas = NULL;
        $this -> view -> rsPapas = $rsPapas;
    }
    
    /**
     * manually edit the files table
     */
    public function filesAction(){
        //set the title of the page
        $this -> view -> sTitle = 'Files';
        
        //set the model name 
        $this -> view -> sModelName = 'Application_Model_DbTable_Files';
        
        //set the column which is papa
        $this -> view -> sPapaCol = array(
            'courses_id' ,
            'folders_id' ,
        );
        
        //get the columns of the table
        $aCols = NULL;
        $mFiles = new Application_Model_DbTable_Files();
        $aCols = $mFiles->info(Zend_Db_Table_Abstract::COLS);
        $this -> view -> aCols = $aCols;
        
        //get all the rows from the database
        $rsRows = NULL;
        $rsRows = $mFiles -> fetchAll($mFiles -> select() -> order('id ASC'));
        $this -> view -> rsRows = $rsRows;
        
        //get the rowset of the papa
        $rsPapas = array();
        $mCourses = new Application_Model_DbTable_Courses();
        $mFolders = new Application_Model_DbTable_Folders();
        $rsPapas['courses_id'] = $mCourses -> fetchAll($mCourses -> select() -> order('id ASC'));
        $rsPapas['folders_id'] = $mFolders -> fetchAll($mFolders -> select() -> order('id ASC'));
        $this -> view -> rsPapas = $rsPapas;
    }


    /**
     * when we are using the insert table in our admin to insert a new row
     */
    public function insertrowAction(){
        $this->disableView();
        
        //grab the model text
        $sModel = $this->_aData['model'];
        
        //create the actual model
        $mModel = new $sModel();
        
        //get the columns from the model
        $aCols = NULL;
        $aCols = $mModel->info(Zend_Db_Table_Abstract::COLS);
        
        //create the params array
        $aParams = array();
        foreach ($aCols as $sCol) {
            if ($sCol === 'id')continue;
            if ($sCol === 'size')continue;
            if ($sCol === 'md5_hash')continue;
            if ($sCol === 'path'){ // there is a file uploaded grab the path
                $oSingleFile = $this->_aFiles[0];
                /* @var $oSingleFile Nerdeez_Files  */
                $aParams[$sCol] = $oSingleFile -> sUrl;
                $aParams['size'] = $oSingleFile -> iSize;
                $aParams['md5_hash'] = $oSingleFile -> sHash;
                continue;
            }
            if ($aData[$sCol] === ''){
                $aParams[$sCol] = NULL;
                continue;
            }
            $aParams[$sCol] = $this -> _aData[$sCol];
        }
        
        //insert the actual row
        try{
            $mModel -> insert($aParams);
        }
        catch (Exception $e){
            echo $e ->getMessage();
        }
        
        //redirect to the same url
        $this->_redirector->gotoUrl($this->getReferer() . '/status/success/');
    }
    
    /**
     * delete number of rows from a certain database
     */
    public function deleterowsAction(){
        //disable view rendering
        $this->_helper->layout()->disableLayout(); 
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        
        //grab the params
        $sModel = $sIds = NULL;
        $aData=$this->getRequest()->getParams();
        $sModel = $aData['model'];
        $sIds = $aData['ids'];
        
        //convert the json to object
        $aIds = json_decode(str_replace('\\', '', $sIds));
        
        //create the model
        $mModel = new $sModel();
        /* @var $mModel Nerdeez_Db_Table */
        
        //get the columns from the model
        $aCols = NULL;
        $aCols = $mModel->info(Zend_Db_Table_Abstract::COLS);
        
        //grab all the rows
        $rsRows = $mModel ->fetchAll($mModel ->select() -> where('id IN (?)' , $aIds));
        
        //delete all the rows if need delete s3 files also
        $s3 = new Nerdeez_Service_Amazon_S3();
        foreach($rsRows as $rRow){
            /* @var $rRow Zend_Db_Table_Row */
            if (in_array('path', $aCols)){
                $s3 ->removeObject($rRow['path']);
            }
            $rRow ->delete();
        }
        
        //pass success
        $userData=array(array('status'=>'success'));
        $dojoData= new Zend_Dojo_Data('status',$userData);
        echo $dojoData->toJson();
        return;
    }
    
    /**
     * updates the row in the table
     */
    public function updaterowAction(){
        //disable view rendering
        $this->_helper->layout()->disableLayout(); 
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        
        //grab the model and id
        $sModel = $iId = NULL;
        $aData=$this->getRequest()->getParams();
        $sModel = $aData['model'];
        $iId = $aData['id'];
        
        //create the actual model
        $mModel = new $sModel();
        
        //get the columns from the model
        $aCols = NULL;
        $aCols = $mModel->info(Zend_Db_Table_Abstract::COLS);
        
        //create the update array
        $aUpdate = array();
        foreach ($aCols as $sCol) {
            if ($sCol === 'id')continue;
            if ($aData[$sCol] === ''){
                $aUpdate[$sCol] = NULL;
                continue;
            }
            $aUpdate[$sCol] = $aData[$sCol];
        }
        
        //update the table
        $mModel -> update($aUpdate , 'id = ' . $iId);
        
        //pass success
        $userData=array(array('status'=>'success'));
        $dojoData= new Zend_Dojo_Data('status',$userData);
        echo $dojoData->toJson();
        return;
    }
    
    /**
     * generic download function for all the db files 
     */
    public function downloadAction(){
        //disable view rendering
        $this->_helper->layout()->disableLayout(); 
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        
        //grab the params
        $sModel = $iId = NULL;
        $aData=$this->getRequest()->getParams();
        $sModel = $aData['model'];
        $iId = $aData['id'];
        
        //grab the row
        $rRow = NULL;
        $mModel = new $sModel();
        $rRow = $mModel -> fetchRow($mModel -> select() -> where('id = ?' , $iId));
        
        //download the file
        parent::download($rRow['path'], $rRow['title']);
    }

     /**
     * initialize here common view variables and also attach the admin js file
     */
    public function preDispatch(){
        //call the parent predispatch
        parent::preDispatch();
        
        //set to include the admin js script
        $layout = new Zend_Layout();
        $layout->getView()->headScript()->appendFile('/js/admin.js');
        
        //get status and error params
        $aData=$this->getRequest()->getParams();
        $this -> view -> sError = $aData['error'];
        $this -> view -> sStatus = $aData['status'];
    }
    
    /**
     * when admin uploads files to courses via a single zip file
     */
    public function addbyzipAction(){
        //disable the view 
        $this->disableView();
        
        //get the zip file as a nerdeez file object
        /* @var $nfFile Nerdeez_Files */
        $nfFile = $this->_aFiles[0];
        
        //get the path to extract
        $sUploadDir = $this ->getUploadDir();
        
        //get the zip file to local file system
        $s3 = new Nerdeez_Service_Amazon_S3();
        file_put_contents($sUploadDir . $nfFile -> sFullName , $s3->getObject($nfFile -> sUrl));
        
        //extract the zip file
        $this->extractZip($sUploadDir . $nfFile -> sFullName);
        
        //grab the list of all the files extracted
        $aUniFiles = glob($sUploadDir . 'zipcache/' . '*', GLOB_MARK);
        
        //iterate on outer folders each folder represents a university
        $ksfunctions = new Application_Model_KSFunctions();
        $mUniversities = new Application_Model_DbTable_Universities();
        $mCourses = new Application_Model_DbTable_Courses();
        $mFiles = new Application_Model_DbTable_Files();
        $mFolders = new Application_Model_DbTable_Folders();
        $s3 = new Nerdeez_Service_Amazon_S3();
        $s3->createBucket("nerdeez");
        foreach ($aUniFiles as $sUniFile) {
            
            //if the file is not a dir here you can continue
            if(!is_dir($sUniFile))continue;
            
            //if there is a university with this title than grab it else create it
            $iUniId = 0;
            $sUniTitle = $ksfunctions ->grabFileNameFromPath($sUniFile);
            $rUni = $mUniversities ->fetchRow($mUniversities ->select() ->where('title = ?' , $sUniTitle));
            if ($rUni == NULL){
                $iUniId = $mUniversities ->insertWithoutArray($sUniTitle);
            }
            else{
                $iUniId = $rUni['id'];
            }
            
            //iterate on all the courses inside a university
            $aCourseFiles = glob($sUniFile . '*', GLOB_MARK);
            foreach ($aCourseFiles as $sCourseFile) {
                
                //if the file is not a dir here you can continue
                if(!is_dir($sUniFile))continue;
                
                //if there is a course with this title than grab it else create it
                $iCourseId = 0;
                $sCourseTitle = $ksfunctions ->grabFileNameFromPath($sCourseFile);
                $rCourse = $mCourses ->fetchRow($mCourses ->select() ->where('title = ?' , $sCourseTitle));
                if ($rCourse == NULL){
                    $iCourseId = $mCourses ->insertWithoutArray($sCourseTitle, $iUniId);
                }
                else{
                    $iCourseId = $rCourse['id'];
                }
                
                //iterate on all the folders inside the courses
                $aFolderPapas = glob($sCourseFile . '*' , GLOB_MARK);
                foreach ($aFolderPapas as $sFolderPapa) {
                    //if the file is not a dir here you can continue
                    if(!is_dir($sUniFile))continue;
                    
                    //get the folder id if not existing than continue
                    $iFolderPapaId = 0;
                    $sFolderPapaTitle = $ksfunctions ->grabFileNameFromPath($sFolderPapa);
                    $rFolderPapa = $mFolders ->fetchRow($mFolders ->select() -> where ('title = ?' , $sFolderPapaTitle));
                    if ($rFolderPapa == NULL)continue;
                    $iFolderPapaId = $rFolderPapa['id'];
                    
                    //iterate on all the files inside this folder
                    $aFolderSons = glob($sFolderPapa . '*' , GLOB_MARK);
                    foreach ($aFolderSons as $sFolderSon) {
                        
                        //if this is file than insert it to the database and upload to s3
                        $sFileTitle = $ksfunctions ->grabFileNameFromPath($sFolderSon);
                        if (!is_dir($sFolderSon)){
                            $sPath = 'nerdeez/' . rand( 0 , 99999) . '_' . $sFileTitle;
                            $hash = md5_file($sFolderSon);
                            $isRowExist = $this ->isFileExist($hash);
                            if ($isRowExist === FALSE){
                                $mFiles ->insertWithoutArray($sFileTitle, $sPath, $iCourseId, $iFolderPapaId, filesize($sFolderSon) , $hash);
                                $s3->putObject( $sPath, 
                                    file_get_contents($sFolderSon),
                                    array(Nerdeez_Service_Amazon_S3::S3_ACL_HEADER =>
                                    Nerdeez_Service_Amazon_S3::S3_ACL_PUBLIC_READ));
                            }
                            else{
                                $mFiles ->insertWithoutArray($sFileTitle, $isRowExist['path'], $iCourseId, $iFolderPapaId, $isRowExist['size'] , $hash);
                            }
                        }
                        
                        //this is a folder than iterate on sons and insert all files inside it
                        else{
                            
                            //find the folder son
                            $iFolderSon = 0;
                            $rFolderSon = $mFolders ->fetchRow($mFolders -> select() -> where('title = ?' , $sFileTitle));
                            if ($rFolderSon == NULL) continue;
                            $iFolderSon = $rFolderSon['id'];
                            
                            //iterate on all the files and insert them
                            $aFiles = glob($sFolderPapa . '*' , GLOB_MARK);
                            foreach($aFiles as $sFile){
                                if(is_dir($sFile))continue;
                                $sFileTitleSon = $ksfunctions ->grabFileNameFromPath($sFile);
                                $sPathSon = 'nerdeez/' . rand( 0 , 99999) . '_' . $sFileTitleSon;
                                $hash = md5_file($sFile);
                                $isRowExist = $this ->isFileExist($hash);
                                if ($isRowExist === FALSE){
                                    $mFiles ->insertWithoutArray($sFileTitleSon, $sPathSon, $iCourseId, $iFolderSon, filesize($sFile) , $hash);
                                    $s3->putObject( $sPath, 
                                        file_get_contents($sFile),
                                        array(Nerdeez_Service_Amazon_S3::S3_ACL_HEADER =>
                                        Nerdeez_Service_Amazon_S3::S3_ACL_PUBLIC_READ));
                                }
                                else{
                                    $mFiles ->insertWithoutArray($sFileTitle, $isRowExist['path'], $iCourseId, $iFolderSon, $isRowExist['size'] , $hash);
                                    
                                }
                                
                            }
                        }
                    }
                }
            }
        }
        
        //clear whats left from the zip file and delete the file
        $this ->clearZipDir();
        unlink($sUploadDir . $nfFile -> sFullName);
        $s3 ->removeObject($nfFile -> sUrl);
        
        //redirect to the same url
        $this->_redirector->gotoUrl($this->getReferer() . '/status/success/');
    }
    
    /**
     * when we click in files to clean our s3
     */
    public function cleans3Action(){
        //disable the view
        $this->disableView();
        
        //iterate on all the  files in s3
        $mFiles = new Application_Model_DbTable_Files();
        $s3 = new Nerdeez_Service_Amazon_S3();
        $list = $s3->getObjectsByBucket("nerdeez");
        foreach($list as $name) {
            
            //search for the file in the files table if not there than delete the file
            $rFile = $mFiles ->fetchRow($mFiles -> select() -> where ('path = ?' , 'nerdeez/' . $name));
            if ($rFile == NULL){
                $s3 ->removeObject('nerdeez/' . $name);
            }
            
        }
        
        //redirect to the same url
        $this->_redirector->gotoUrl($this->getReferer() . '/status/success/');
    }
    
}

?>
