<?php

/**
 * required in all my models
 */
require_once APPLICATION_PATH . '/models/DbTable/Nerdeez_Db_Table.php';

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
    
}

?>
