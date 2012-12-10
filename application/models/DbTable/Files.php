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
class Nerdeez_Files_Row extends Nerdeez_Db_Table_Row_Files
{
    /**
     * @see parent::getSize($iCourseId = 0)
     */
    public function getSize($iCourseId = 0){
        return $this['size'];
    }
    
    /**
     * @see Nerdeez_Db_Table_Row_Files::getClickJsEvent()
     * @param type $iCourseId
     * @return type
     */
    public function getClickJsEvent($iCourseId = 0){
        return "ksPeek(" . $this['id']  . ", '" . $this['title'] . "');";
    }
    
    /**
     * @see parent::getCheckboxClass()
     * @return string
     */
    public function getCheckboxClass(){
        return 'ksFileBrowserCheckbox';
    }
    
    /**
     * @see Nerdeez_Db_Table_Row_Files
     * @return string
     */
    public function getImageClass(){
        //determine the background position from the extension
        $ext = strtolower(pathinfo($this['title'], PATHINFO_EXTENSION));
        return $ext;
    }
    
    /**
     * @see Nerdeez_Db_Table_Row_Files
     * @param type $iCourseId
     */
    public function getJsDownloadEvent($iCourseId = 0){
        return 'ksDownloadFile(' . $this['id'] . ');';
    }
}

/**
 * The files table holds all the site knowledge files
 *
 * @author Yariv Katz
 * @copyright Nerdeez.com Ltd.
 * @version 1.0
 */
class Application_Model_DbTable_Files extends Nerdeez_Db_Table{
    
    /**
     * all the models will put their table create if not exist table in here
     * @var String 
     */
    protected $_sqlCreateTable = "CREATE TABLE IF NOT EXISTS `files` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(100) NOT NULL,
      `path` text NOT NULL,
      `courses_id` int(10) unsigned NOT NULL,
      `folders_id` int(10) unsigned NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    
    /**
     * a list of sql statments to try and execute 
     * will work only for the first time
     * @var Array 
     */
    protected $_aAlterStatments = array(
        "ALTER TABLE `files` ADD `size` INT UNSIGNED NOT NULL DEFAULT '0'" ,
        "ALTER TABLE `files` ADD `md5_hash` VARCHAR( 100 ) NULL DEFAULT NULL" ,
    );
    
    /**
     * 
     * name of files table
     * @var String
     */
    protected $_name = 'files';
    
    /**
     * the tables that we refrence in this table
     * @var array
     */
    protected $_referenceMap    = array(
        'Folder' => array(
            'columns'           => array('folders_id'),
            'refTableClass'     => 'Application_Model_DbTable_Folders',
            'refColumns'        => array('id')
        ),
    );
    
    /**
     * inserts a new row to the files
     * @param String $sTitle
     * @param String $sPath
     * @param int $iCoursesId
     * @param int $iFoldersId
     * @param int $iSize
     * @return int the pk 
     */
    public function insertWithoutArray($sTitle , $sPath , $iCoursesId , $iFoldersId , $iSize , $sHash = NULL){
        $aNewRow = array(
            'title'                 => $sTitle , 
            'path'                  => $sPath ,
            'courses_id'            => $iCoursesId , 
            'folders_id'            => $iFoldersId ,
            'size'                  => $iSize ,
            'md5_hash'              => $sHash ,
        );
        return parent::insert($aNewRow);
    }
    
    /**
     * our custom files row
     * @var String 
     */
    protected $_rowClass = 'Nerdeez_Files_Row';
    
}

?>
