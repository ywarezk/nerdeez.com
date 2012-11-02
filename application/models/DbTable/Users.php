<?php

/**
 * required in all my models
 */
require_once APPLICATION_PATH . '/models/DbTable/Nerdeez_Db_Table.php';

/**
 * model for users table
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class Application_Model_DbTable_Users extends Nerdeez_Db_Table{
    
    /**
     * all the models will put their table create if not exist table in here
     * @var String 
     */
    protected $_sqlCreateTable = "CREATE TABLE IF NOT EXISTS `users` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(100) NOT NULL,
          `pass` text,
          `role` int(10) unsigned NOT NULL DEFAULT '0',
          `serial` varchar(100) NOT NULL,
          `email` varchar(100) NOT NULL,
          `isActive` int(10) unsigned NOT NULL DEFAULT '0',
          `salt` varchar(60) NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `email` (`email`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;";
    
    /**
     * 
     * name of users table
     * @var String
     */
    protected $_name = 'users';
    
    /**
     * @see Nerdeez_Db_Table::insertWithoutArray
     * @param String $sTitle the title of the user default is usually random student number
     * @param String $sPass the password of the user
     * @param String $sSerial serial number for the activation
     * @param String $sEmail the email address of the user
     * @param String $sSalt salf string for pass encryption
     * @param int $iRole the role of the user 0 - guest , 1 - user , 2- admin
     * @param int $iIsActive did the user activate his account?
     */
    public function insertWithoutArray($sTitle , $sPass , $sSerial , $sEmail , $sSalt , $iRole = 0 , $iIsActive = 0){
        $aNewRow = array(
            'title'         => $sTitle , 
            'pass'          => $sPass , 
            'role'          => $iRole ,
            'serial'        => $sSerial ,
            'email'         => $sEmail , 
            'isActive'      => $iIsActive , 
            'salt'          => $sSalt ,
        );
        return parent::insert($aNewRow); 
    } 
    
}

?>
