<?php

/**
 * model for universities table
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class Application_Model_DbTable_Universities extends Zend_Db_Table_Abstract{
    
    /**
     * constructor , will create the table if doesnt exist
     */
    function __construct() {
       //call the parent constructor
       parent::__construct();
       
       //buld the table if it doesnt exist
       $db = $this -> getAdapter();
       $sUniversitiesSql = 'CREATE TABLE IF NOT EXISTS `universities` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `title` varchar(1000) DEFAULT NULL,
            `description` varchar(1000) DEFAULT NULL,
            `image` varchar(1000) DEFAULT NULL,
            `website` varchar(1000) DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
        $db->query($sUniversitiesSql);
    }
    
    /**
     * name of universities table
     * @var String
     */
    protected $_name = 'universities';
    
    /**
     * 
     * Tables primary key
     * @var String
     */
    protected $_primary = 'id';
    
    /**
     * the dependant tables
     * @var array 
     */
    protected $_dependentTables = array('Application_Model_DbTable_Courses'); 
    
    
}

?>
