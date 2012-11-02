<?php
/**
 * required in all my models
 */
require_once APPLICATION_PATH . '/models/DbTable/Nerdeez_Db_Table.php';

/**
 * model for login cookies table
 *
 * @author Yariv Katz
 * @copyright Knowledge-Share.com Ltd.
 */
class Application_Model_DbTable_Logincookies extends Nerdeez_Db_Table{
    
    
    
    /**
     * contain the sql string to create the table
     * @var String
     */
    protected $_sqlCreateTable = "CREATE TABLE IF NOT EXISTS `logincookies` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `email` varchar(100) CHARACTER SET utf8 NOT NULL,
          `identifier` varchar(200) CHARACTER SET utf8 NOT NULL,
          `token` varchar(200) CHARACTER SET utf8 NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    
    
    /**
     * 
     * name of  table
     * @var String
     */
    protected $_name = 'logincookies'; 
    
    
    /**
     * delte login cookies with email 
     * @param String $sEmail 
     */
    public function deleteRowWithEmail($sEmail) {
        $this->delete("email ='" . $sEmail . "'");
    }
    
    /**
     * @see Nerdeez_Db_Table::insertWithoutArray
     * @param String $sEmail the email of the user
     * @param String $sIdentifier the identifier string
     * @param String $sToken the token string
     */
    public function insertWithoutArray($sEmail , $sIdentifier , $sToken){
        $aNewRow = array(
            'email'         => $sEmail , 
            'identifier'    => $sIdentifier , 
            'token'         => $sToken ,
        );
        return parent::insert($aNewRow);
    }
    
}

?>
