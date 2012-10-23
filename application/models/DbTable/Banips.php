<?php

/**
 * model for banips table
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class Application_Model_DbTable_Banips extends Zend_Db_Table_Abstract{
    
    /**
     * constructor , will create the table if doesnt exist
     */
    function __construct() {
       //call the parent constructor
       parent::__construct();
       
       //buld the table if it doesnt exist
       $db = $this -> getAdapter();
       $sBanipsSql = "CREATE TABLE IF NOT EXISTS `banips` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `starttime` int(10) unsigned NOT NULL,
        `ip` varchar(50) NOT NULL,
        `email` varchar(100) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $db->query($sBanipsSql);
    }
    
    /**
     * 
     * name of the table
     * @var String
     */
    protected $_name = 'banips';
    
    /**
     * 
     * Tables primary key
     * @var String
     */
    protected $_primary = 'id';
    
}

?>
