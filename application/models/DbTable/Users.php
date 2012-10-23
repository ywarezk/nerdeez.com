<?php

/**
 * model for users table
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class Application_Model_DbTable_Users extends Zend_Db_Table_Abstract{
    
    /**
     * constructor , will create the table if doesnt exist
     */
    function __construct() {
       //call the parent constructor
       parent::__construct();
       
       //buld the table if it doesnt exist
       $db = $this -> getAdapter();
       $sUsersSql = "CREATE TABLE IF NOT EXISTS `users` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(100) NOT NULL,
          `pass` text,
          `role` int(10) unsigned NOT NULL DEFAULT '0',
          `serial` varchar(100) NOT NULL,
          `email` varchar(100) DEFAULT NULL,
          `isActive` int(10) unsigned NOT NULL DEFAULT '0',
          `salt` varchar(60) NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `email` (`email`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;";
        $db->query($sUsersSql);
    }
    
    /**
     * 
     * name of users table
     * @var String
     */
    protected $_name = 'users';
    
    /**
     * 
     * Tables primary key
     * @var String
     */
    protected $_primary = 'id';
}

?>
