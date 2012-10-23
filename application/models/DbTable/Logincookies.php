<?php

/**
 * model for login cookies table
 *
 * @author Yariv Katz
 * @copyright Knowledge-Share.com Ltd.
 */
class Application_Model_DbTable_Logincookies extends Zend_Db_Table_Abstract{
    
    /**
     * constructor , will create the table if doesnt exist
     */
    function __construct() {
       //call the parent constructor
       parent::__construct();
       
       //buld the table if it doesnt exist
       $db = $this -> getAdapter();
       $sLogincookiesSql = "CREATE TABLE IF NOT EXISTS `logincookies` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `email` varchar(100) CHARACTER SET utf8 NOT NULL,
          `identifier` varchar(200) CHARACTER SET utf8 NOT NULL,
          `token` varchar(200) CHARACTER SET utf8 NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        $db->query($sLogincookiesSql);
    }
    
    /**
     * 
     * name of  table
     * @var String
     */
    protected $_name = 'logincookies';
    
    /**
     * 
     * Tables primary key
     * @var String
     */
    protected $_primary = 'id';
    
    public function deleteDBCookie($id) {
        $this->delete('id =' . (int) $id);
    }
    
    public function addDBCookie($id, $ucode) {
        $data = array(
            'id' => $id,
            'ucode' => $ucode,
            'date' => new Zend_Db_Expr('NOW()'),
        );
        $this->insert($data);
    }
}

?>
