<?php

/**
 * required in all my models
 */
require_once APPLICATION_PATH . '/models/DbTable/Nerdeez_Db_Table.php';

/**
 * model for universities table
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class Application_Model_DbTable_Universities extends Nerdeez_Db_Table{
    
    /**
     * name of universities table
     * @var String
     */
    protected $_name = 'universities';
    
    
    /**
     * the dependant tables
     * @var array 
     */
    protected $_dependentTables = array('Application_Model_DbTable_Courses'); 
    
    /**
     * contain the sql string to create the table
     * @var String
     */
    protected $_sqlCreateTable = 'CREATE TABLE IF NOT EXISTS `universities` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `title` varchar(1000) NOT NULL,
            `description` varchar(1000) DEFAULT NULL,
            `image` varchar(1000) DEFAULT NULL,
            `website` varchar(1000) DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
    

    /**
     * @see Application_Model_DbTable_Model::insert
     * @param String $sTitle the title of the university
     * @param String $sDescription a sentence describing the university
     * @param String $sImage the image logi of the university
     * @param String $sWebsite the university web page
     */
    public function insert($sTitle , $sDescription = NULL , $sImage = NULL , $sWebsite = NULL){
        $aNewRow = array(
            'title'         => $sTitle , 
            'description'   => $sDescription , 
            'image'         => $sImage , 
            'website'       => $sWebsite ,
        );
        return parent::insert($aNewRow);
    }
}

?>
