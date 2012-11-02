<?php

/**
 * required in all my models
 */
require_once APPLICATION_PATH . '/models/DbTable/Nerdeez_Db_Table.php';

/**
 * model for courses table
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class Application_Model_DbTable_Courses extends Nerdeez_Db_Table{
    
    /**
     * all the models will put their table create if not exist table in here
     * @var String 
     */
    protected $_sqlCreateTable = 'CREATE TABLE IF NOT EXISTS `courses` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(100) NOT NULL,
          `description` varchar(1000) DEFAULT NULL,
          `universities_id` int(10) unsigned NOT NULL,
          `website` varchar(1000) DEFAULT NULL,
          `connections` varchar(1000) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
    
    /**
     * a list of sql statments to try and execute 
     * will work only for the first time
     * @var Array 
     */
    protected $_aAlterStatments = array(
        'ALTER TABLE courses ADD FULLTEXT(title, description);' ,
    );
    
    /**
     * 
     * name of courses table
     * @var String
     */
    protected $_name = 'courses';
    
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
    
    /**
     * @see Nerdeez_Db_Table::insertWithoutArray
     * @param String $sTitle course title
     * @param int $iUniversities_id many to one connection pk
     * @param String $sDescription description of the course
     * @param String $sWebsite the course web site
     * @param String $sConnections JSON string representing a
     */
    public function insertWithoutArray($sTitle , $iUniversities_id , $sDescription = NULL , $sWebsite = NULL , $sConnections = NULL){
        $aNewRow = array(
            'title'                 => $sTitle , 
            'universities_id'       => $iUniversities_id , 
            'description'           => $sDescription ,
            'website'               => $sWebsite , 
            'connections'           => $sConnections ,
        );
        return parent::insert($aNewRow);
    }
    
    /**
     * when the user searches for courses
     * @param String $sSearch unsanitized search string
     * @return Zend_Db_Table_Rowset the set we found 
     */
    public function search($sSearch){ 
        //sanitize the param
        $sSanSearch = NULL;
        $ksfunctions = new Application_Model_KSFunctions();
        $bIsValid = TRUE;
        $sSanSearch = $ksfunctions -> sanitize_Title($sSearch , 100);
        if($sSanSearch == null ){
            $bIsValid = FALSE;
        }
        
        //grab the course rows that match the search description
        $rsCourses = NULL;
        if ($bIsValid){
            $selCourseSelect = $this -> select()
                        -> where('MATCH (title , description) AGAINST (?)' , $sSanSearch)
                        -> orwhere("title LIKE '%" . $sSanSearch . "%'")
                        -> orwhere("description LIKE '%" . $sSanSearch . "%'")
                        -> order ('title ASC');
        }
        else{
            $selCourseSelect = $this -> select() -> order('title ASC');
        }
        $rsCourses = $this -> fetchAll($selCourseSelect);
        return $rsCourses;
    }
}

?>
