<?php

/**
 * required in all my controllers
 */
require_once APPLICATION_PATH . '/controllers/Nerdeez_Controller_Action.php';

/**
 * controller for the admin section
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class AdminController extends Nerdeez_Controller_Action{

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
        $this -> view -> sPapaCol = NULL;
        
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
        $this -> view -> sPapaCol = 'universities_id';
        
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
        $rsPapas = NULL;
        $mUniversities = new Application_Model_DbTable_Universities();
        $rsPapas = $mUniversities -> fetchAll($mUniversities 
                -> select() 
                -> order('id ASC'));
        $this -> view -> rsPapas = $rsPapas;
        
    }
    
    /**
     * when we are using the insert table in our admin to insert a new row
     */
    public function insertrowAction(){
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
