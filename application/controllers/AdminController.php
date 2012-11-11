<?php

/**
 * required in all my controllers
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
        
        //set the column which is papa
        $this -> view -> sPapaCol = array();
        
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
        //start the session
        Zend_Session::start();
        
        //grab the params
        $aData = NULL;
        $aData=$this->getRequest()->getParams();
        
        //grab the model text
        $sModel = $aData['model'];
        
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
            if ($sCol === 'path'){ // there is a file uploaded grab the path
                $serial = $aData['serial'];
                $counter = 0;
                foreach ($_SESSION['kstempfiles'][$serial] as $oSingleFile){
                    $aParams[$sCol] = $oSingleFile -> name;
                    $aParams['size'] = $oSingleFile -> size;
                    $_SESSION['kstempfiles'][$counter] = NULL;
                    $counter ++;
                }
                continue;
            }
            if ($aData[$sCol] === ''){
                $aParams[$sCol] = NULL;
                continue;
            }
            $aParams[$sCol] = $aData[$sCol];
        }
        
        //insert the actual row
        $mModel -> insert($aParams);
        
        //redirect to the same url
        $this->_redirector->gotoUrl($this->getReferer() . 'status/success/');
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
        
        //delete all the rows
        foreach ($aIds as $sId) {
            $mModel -> deleteRowWithId($sId);
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
    
}

?>
