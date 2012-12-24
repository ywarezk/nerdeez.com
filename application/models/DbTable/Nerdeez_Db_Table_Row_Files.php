<?php

/**
 * required for the extend part of the row
 */
require_once 'Zend/Db/Table/Row/Abstract.php';

/**
 * All the rows that will be displayed in the filebrowser
 * should implement this abstract class
 *
 * @author Yariv Katz
 * @copyright Nerdeez.com Ltd.
 */
abstract class Nerdeez_Db_Table_Row_Files extends Zend_Db_Table_Row_Abstract{
    
    /**
     * return the size of the folder/file 
     * @param int $iCourseId the course this folder belongs to
     * @return int the size in bytes
     */
    abstract public function getSize($iCourseId = 0);
    
    /**
     * the string representing the event triggered when row is clicked
     * @param int $iCourseId the course id
     * @return String the string representing the js event
     */
    abstract public function getClickJsEvent($iCourseId = 0);
    
    /**
     * returns the checkbox class in the filebrowser
     * @return String class name
     */
    abstract public function getCheckboxClass();
    
    /**
     * returns the class to append to the image where the regilar and hover is defined
     * @return String string to be appended to style
     */
    abstract public function getImageClass();
    
    
    /**
    * gets the size in bytes and formats it
    * @param int $bytes the size in bytes
    * @param int $precision the accuracy
    * @return String the formated string
    */
   private function formatSize($bytes, $precision = 2){
       $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

       $bytes = max($bytes, 0); 
       $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
       $pow = min($pow, count($units) - 1); 

       // Uncomment one of the following alternatives
       $bytes /= pow(1024, $pow);
       // $bytes /= (1 << (10 * $pow)); 

       return round($bytes, $precision) . ' ' . $units[$pow]; 
   }
   
   /**
    * returns a string representing the size in a formated state
    * @param type $iCourseId
    */
   public function getFormatedSize($iCourseId = 0){
       return $this->formatSize($this->getSize($iCourseId));
   }
   
   /**
    * the function on the download click button
    * @param int $iCourseId the course for the folder
    * @return String the js event string
    */
   abstract public function getJsDownloadEvent($iCourseId = 0);
   
   /**
    * the flag to display disable or enable
    * @return String the flag icon class to display
    */
   abstract public function getFlagClass();
   
   /**
    * the js action to perform on flag click
    */
   abstract public function getFlagAction();
    
}

?>
