<?php

/**
 * required in all my models
 */
require_once APPLICATION_PATH . '/models/DbTable/Nerdeez_Db_Table.php';

/**
 * handles the forgot password table
 *
 * @author Yariv Katz
 * @copyright nerdeez.com
 * @version 1.1
 */
class Application_Model_DbTable_Forgotpassword extends Nerdeez_Db_Table{
    
    /**
     * all the models will put their table create if not exist table in here
     * @var String 
     */
    protected $_sqlCreateTable = "CREATE TABLE IF NOT EXISTS `forgotpassword` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `users_id` int(10) unsigned NOT NULL,
        `token` varchar(200) NOT NULL,
        `starttime` int(10) unsigned NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    
    /**
     * a list of sql statments to try and execute 
     * will work only for the first time
     * @var Array 
     */
    protected $_aAlterStatments = array();
    
    /**
     * 
     * name of users table
     * @var String
     */
    protected $_name = 'forgotpassword';
    
    /**
     * the tables that we refrence in this table
     * @var array
     */
    protected $_referenceMap    = array(
        'User' => array(
            'columns'           => array('users_id'),
            'refTableClass'     => 'Application_Model_DbTable_Users',
            'refColumns'        => array('id')
        )
    );
    
    /**
     * inserts a new row
     * @param type $iUser
     * @param type $sToken
     * @param type $iTime
     * @return type
     */
    public function insertWithoutArray($iUser, $sToken, $iTime){
        $aNewRow = array(
            'users_id'              => $iUser , 
            'token'                 => $sToken ,
            'starttime'             => $iTime , 
        );
        return parent::insert($aNewRow);
    }
    
    /**
     * 
     * Tables primary key
     * @var String
     */
    protected $_primary = 'id';
}

?>