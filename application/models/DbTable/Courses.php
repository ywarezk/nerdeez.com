<?php

/**
 * model for courses table
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class Application_Model_DbTable_Courses extends Zend_Db_Table_Abstract{
    
    /**
     * constructor , will create the table if doesnt exist
     */
    function __construct() {
       //call the parent constructor
       parent::__construct();
       
       //buld the table if it doesnt exist
       $db = $this -> getAdapter();
       $sCoursesSql = 'CREATE TABLE IF NOT EXISTS `courses` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(100) NOT NULL,
          `description` varchar(1000) DEFAULT NULL,
          `universities_id` int(10) unsigned NOT NULL,
          `website` varchar(1000) DEFAULT NULL,
          `connections` varchar(1000) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
        $db->query($sCoursesSql);
    }
    
    /**
     * 
     * name of courses table
     * @var String
     */
    protected $_name = 'courses';
    
    /**
     * 
     * Tables primary key
     * @var String
     */
    protected $_primary = 'id';
    
    /**
     * the dependant tables
     * @var type 
     */
    /*protected $_dependentTables = array(
        'Application_Model_DbTable_Files' , 
        'Application_Model_DbTable_Scheduale' , 
        'Application_Model_DbTable_Calendar' , 
        'Application_Model_DbTable_Messages' , 
        'Application_Model_DbTable_Movies' ,
        );*/
    
    
    /**
     * the tables that we refrence in this table
     * @var array
     */
    protected $_referenceMap    = array(
        'University' => array(
            'columns'           => array('universities_id'),
            'refTableClass'     => 'Application_Model_DbTable_Universities',
            'refColumns'        => array('id')
        ),
    );
}

?>
