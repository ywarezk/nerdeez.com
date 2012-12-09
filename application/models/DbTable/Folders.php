<?php

/**
 * required in all my models
 */
require_once APPLICATION_PATH . '/models/DbTable/Nerdeez_Db_Table.php';

/**
 * the row will be displyed in the filebrowser
 * thus must extend this class
 */
require_once APPLICATION_PATH . '/models/DbTable/Nerdeez_Db_Table_Row_Files.php';

/**
 * custom folder row class
 */
class Nerdeez_Folder_Row extends Nerdeez_Db_Table_Row_Files
{
    /**
     * @see parent::getSize($iCourseId = 0)
     */
    public function getSize($iCourseId = 0){
        //get all the sons of this row
        $rsFolders = NULL;
        $mFolders = $this ->getTable();
        $rsFolders = $mFolders ->fetchAll($mFolders ->select() ->where('papa = ?' , $this['id']));
        
        //create array of all the ids
        $aIds = array();
        $aIds[]=$this['id'];
        foreach ($rsFolders as $rFolder) {
            $aIds[]=$rFolder['id'];
        }
        
        //fetch all the files belong to the folders
        $rsFiles = NULL;
        $mFiles = new Application_Model_DbTable_Files();
        $rsFiles = $mFiles ->fetchAll($mFiles ->select() 
                -> where('folders_id IN (?)' , $aIds)
                ->where('courses_id = ?' , $iCourseId));
        
        //from all the files calculate and return the size
        $iSize = 0;
        foreach ($rsFiles as $rFile) {
            $iSize+=$rFile['size'];
        }
        return $iSize;
    }
    
    /**
     * @see Nerdeez_Db_Table_Row_Files::getClickJsEvent()
     * @param type $iCourseId
     * @return type
     */
    public function getClickJsEvent($iCourseId = 0){
        return 'gotoFolder(' . $this['id'] . ' , ' . $iCourseId . ');';
    }
    
    /**
     * @see parent::getCheckboxClass()
     * @return string
     */
    public function getCheckboxClass(){
        return 'ksFolderBrowserCheckbox';
    }
    
    /**
     * @see Nerdeez_Db_Table_Row_Files
     * @return string
     */
    public function getImageClass(){
        return 'folder';
    }
    
    
    /**
     * @see Nerdeez_Db_Table_Row_Files
     * @param type $iCourseId
     */
    public function getJsDownloadEvent($iCourseId = 0){
        return 'downloadFolder(' . $this['id'] . ' , ' . $iCourseId . ');';
    }
}

/**
 * file browser will have identical folders in all the courses
 * this will controll these folders
 *
 * @author Yariv Katz
 * @copyright Nerdeez.com Ltd.
 * @version 1.0
 */
class Application_Model_DbTable_Folders extends Nerdeez_Db_Table{
    
    /**
     * all the models will put their table create if not exist table in here
     * @var String 
     */
    protected $_sqlCreateTable = "CREATE TABLE IF NOT EXISTS `folders` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(100) NOT NULL,
      `papa` int(11) NOT NULL DEFAULT '-1',
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    
    /**
     * a list of sql alter statments to try and execute 
     * will work only for the first time
     * @var Array 
     */
    protected $_aAlterStatments = NULL;
    
    /**
     * 
     * name of folders table
     * @var String
     */
    protected $_name = 'folders';
    
    /**
     * the dependant tables
     * @var array 
     */
    protected $_dependentTables = array('Application_Model_DbTable_Files'); 
    
    /**
     * just another insert method
     * @param String $sTitle course title
     * @param int $iPapa is this type inside another type
     * @return int the primary key
     * 
     */
    public function insertWithoutArray($sTitle , $iPapa = -1){
        $aNewRow = array(
            'title'                 => $sTitle , 
            'papa'                  => $iPapa,
        );
        return parent::insert($aNewRow);
    }
    
    /**
     * our custom folder row
     * @var String 
     */
    protected $_rowClass = 'Nerdeez_Folder_Row';
    
}

?>
