<?php

/**
 * required in all my models
 */
require_once APPLICATION_PATH . '/models/DbTable/Nerdeez_Db_Table.php';

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
     * a list of sql alter statments to try and execute 
     * will work only for the first time
     * @var Array 
     */
    protected $_aAlterStatments = NULL;
    
    /**
     * 
     * name of files table
     * @var String
     */
    protected $_name = 'files';
    
    /**
     * just another insert method
     * @param String $sTitle course title
     * @param int $iPapa is this type inside another type
     * @return int the primary key
     * 
     */
    public function insertWithoutArray($sTitle , $sPath , $iCoursesId , $iFoldersId){
        $aNewRow = array(
            'title'                 => $sTitle , 
            'path'                  => $sPath ,
            'courses_id'            => $iCoursesId , 
            'folders_id'            => $iFoldersId ,
        );
        return parent::insert($aNewRow);
    }
    
}

?>
