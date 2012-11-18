<?php
class Application_Model_Zip{
	public function createZip($files = array()){
		$config = new Zend_Config_Ini('../application/configs/application.ini','production');
                $uploaddir=$config->uploaddir;
		$overwrite = false;		
		$thefilename= rand(0, 99999) . '_' . rand(0, 99999) . '_' . rand(0, 99999) . '.zip';
		$destination = $uploaddir . $thefilename;
		$valid_files = array();
                
                
                
		//if files were passed in...
		/*if(is_array($files)) {
		    //cycle through each file
		    foreach($files as $file) {
		      //make sure the file exists
		      if(file_exists($file)) {
                          if(is_dir($file)){
                                $valid_files = array_merge($valid_files , Application_Model_KSFunctions::getInstance()->getDirectoryList($file));
                          }
                          else{
                                $valid_files[] = $file;
                          }
		      }
		    }
		}*/
		
                $valid_files = $files;
                //if we have good files...
		if(count($valid_files)) {
		    //create the archive
		    $zip = new ZipArchive();
		    if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
		      return "";
		    }
                    //iconv("UTF-8", "CP1255" , utf8_encode($file))
		    //add the files
		    foreach($valid_files as $file) {  
                        $zip->addFile($file, $this->fromHebrewToEnglish($file)); 
		    }
		    //debug
		    //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		    
		    //close the zip -- done!
		    $zip->close();
		    
		    //check to make sure the file exists
		    //return $thefilename;
                    return $destination;
		}
		else{
		    return "";
		}
		//return $thefilename;
                return $destination;
	}
        
    /**
     * converts all the hebrew string in the file to english
     * @param String $file the string to convert 
     */
    private function fromHebrewToEnglish($file){
        $file = str_replace('א', 'a' , $file);
        $file = str_replace('ב', 'b' , $file);
        $file = str_replace('ג', 'g' , $file);
        $file = str_replace('ד', 'd' , $file);
        $file = str_replace('ה', 'e' , $file);
        $file = str_replace('ו', 'u' , $file);
        $file = str_replace('ז', 'z' , $file);
        $file = str_replace('ח', 'ch' , $file);
        $file = str_replace('ט', 't' , $file);
        $file = str_replace('י', 'i' , $file);
        $file = str_replace('כ', 'k' , $file);
        $file = str_replace('ל', 'l' , $file);
        $file = str_replace('מ', 'm' , $file);
        $file = str_replace('נ', 'n' , $file);
        $file = str_replace('ס', 's' , $file);
        $file = str_replace('ע', 'a' , $file);
        $file = str_replace('פ', 'p' , $file);
        $file = str_replace('צ', 'ch' , $file);
        $file = str_replace('ק', 'k' , $file);
        $file = str_replace('ר', 'r' , $file);
        $file = str_replace('ש', 'sh' , $file);
        $file = str_replace('ת', 't' , $file);
        $file = str_replace('ם', 'm' , $file);
        $file = str_replace('ן', 'n' , $file);
        $file = str_replace('ף', 'f' , $file);
        $file = str_replace('ך', 'ch' , $file);
        $file = str_replace('ך', 'ch' , $file);
        $file = str_replace('ץ', 'ch' , $file);
        return $file;
    }
    
}
