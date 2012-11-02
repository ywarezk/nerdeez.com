<?php

/**
 * required in all my models
 */
require_once APPLICATION_PATH . '/models/DbTable/Nerdeez_Db_Table.php';

/**
 * model for banips table
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class Application_Model_DbTable_Banips extends Nerdeez_Db_Table{
    
    
    /**
     * 
     * name of the table
     * @var String
     */
    protected $_name = 'banips';
    
    /**
     * all the models will put their table create if not exist table in here
     * @var String 
     */
    protected $_sqlCreateTable = "CREATE TABLE IF NOT EXISTS `banips` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `starttime` int(10) unsigned NOT NULL,
        `ip` varchar(50) NOT NULL,
        `email` varchar(100) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    
    /**
     * @see Nerdeez_Db_Table::insertWithoutArray
     * @param int $iStarttime the timestamp of the start of the hacking
     * @param String $sIp the ip address of the hacker
     * @param String $sEmail the email of the user
     */
    public function insertWithoutArray($iStarttime , $sIp , $sEmail){
        $aNewRow = array(
            'starttime'         => $iStarttime , 
            'ip'                => $sIp ,
            'email'             => $sEmail ,
        );
        return parent::insert($aNewRow);
    }
    
}

?>
