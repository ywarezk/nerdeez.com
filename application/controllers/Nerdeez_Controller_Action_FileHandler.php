<?php

/**
 * required in all my controllers
 */
require_once APPLICATION_PATH . '/controllers/Nerdeez_Controller_Action.php';

/**
 * nerdeez s3 wrapper
 */
require_once APPLICATION_PATH . '/models/Nerdeez_Service_Amazon_S3.php';



/**
 * nerdeez file object class
 */
class Nerdeez_Files{
    /**
     * the name of the file without the number prefix
     * @var String 
     */
    public $sName;
    
    /**
     * the size of the file
     * @var int 
     */
    public $iSize;
    
    /**
     * the type of the file
     * @var String 
     */
    public $sType;
    
    /**
     * the url of the file in s3
     * @var String
     */
    public $sUrl;
    
    /**
     * the file name with the number prefix
     * @var String 
     */
    public $sFullName;
    
    /**
     * the file after going through md5_file
     * @var String 
     */
    public $sHash;
    
    /**
     * build a file object
     * @param String $name the name of the file with rand prefix
     * @param int $size the size of the file in bytes
     * @param String $type the mime type of the file
     * @param String $url the url of the file in s3
     * @param String $hash the hash of the file uploaded
     */
    public function __construct($name , $size , $type , $url , $hash){
        //init the vars
        $this -> sName = $name;
        $this -> iSize = $size;
        $this -> sType = $type;
        $this -> sUrl = $url;
        $this -> sHash = $hash;
        
        //init the full name var
        $this -> sFullName = NULL;
        $ksfunctions = new Application_Model_KSFunctions();
        $this->sFullName = $ksfunctions -> grabFileNameFromPath($url);
    }
    
    
}

/**
 * abstract class adds file managment to a controller
 *
 * @author Yariv Katz
 * @copyright Nerdeez.com Ltd.
 * @version 1.0
 */
abstract class Nerdeez_Controller_Action_FileHandler extends Nerdeez_Controller_Action{
    
    /**
     * will hold all the files uploaded
     * @var Array 
     */
    protected $_aFiles = NULL;

    /**
     * init common vars for the file uploaders
     */
    public function init(){
        //call the parent init
        parent::init();
        
        //init the files var
        Zend_Session::start();
        if (isset ($this->_aData['serial'])){
            $this->_aFiles = array();
            $counter = 0;
            if (isset($_SESSION['kstempfiles'][$this->_aData['serial']]) && $_SESSION['kstempfiles'][$this->_aData['serial']] != NULL){
                foreach ($_SESSION['kstempfiles'][$this->_aData['serial']] as $nfFile) {
                    $this->_aFiles[]=unserialize (serialize ($nfFile));
                    //$_SESSION['kstempfiles'][$this->_aData['serial']][$counter] = NULL;
                    $counter++;
                }
            }
        }
    }

    /**
     * each controller extending this class will have an upload action for file upload
     */
    public function uploadAction(){
        //disable the layout
        $this->_helper->layout()->disableLayout();
	Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);		                        
        
        error_reporting(E_ALL | E_STRICT);


        $upload_handler = new Application_Model_UploadHandler();

        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
                break;
            case 'HEAD':
            case 'GET':
                break;
            case 'POST':
                $info = $upload_handler->post();
                //save in the session 

                //get the serial and validate it
                Zend_Session::start(); 
                if (isset($this -> _aData['serial'])){
                    $serial = $this -> _aData['serial'];
                    if(!isset ($_SESSION['kstempfiles'])){
                        $_SESSION['kstempfiles'] = array();
                    }
                    if(!isset ($_SESSION['kstempfiles'][$serial])){
                        $_SESSION['kstempfiles'][$serial] = array();
                    }

                    //for all the files build a file object and put it in the session
                    foreach ($info as $oFile) {
                        if (isset($oFile -> url)){
                            $nfFile = new Nerdeez_Files($oFile ->name, $oFile -> size, $oFile -> type, $oFile -> url , $oFile -> hashing);
                            $_SESSION['kstempfiles'][$serial][] = $nfFile;
                        }
                    }
                }
                break;
            case 'DELETE':
                //$upload_handler->delete();
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }
        
        
        
        
        //$s3 = new Zend_Service_Amazon_S3($this -> _sAwsKey, $this -> _sAwsSecretKey);
        //$s3->createBucket("Nerdeez");
        //$s3->putObject("Nerdeez/myobject", "somedata");
        
    }
    
    /**
     * try and download the file with the path from s3
     * @param String $sPath the path to the file to download
     */
    protected function download($sPath , $sTitle = '' , $sDisposition = 'attachment'){
        //get the object info
        $s3 = new Nerdeez_Service_Amazon_S3();
        $aObjectInfo = $s3 -> getInfo($sPath);

        if (is_array($aObjectInfo)) {
            header('Content-type: ' . $aObjectInfo['type']);
            header('Content-length: ' . $aObjectInfo['size']);
            if ($sTitle !== '')
                header('Content-Disposition: '. $sDisposition .'; filename="'.rawurldecode($sTitle).'"');
            echo $s3->getObject($sPath);
        }
        else {
            header('HTTP/1.0 404 Not Found');
        }
    }
    
    
    
    /**
     * initialize here common view variables and also attach the admin js file
     */
    public function preDispatch(){
        //call the parent predispatch
        parent::preDispatch();
        
        //set to include the fielupload js files
//        if (!$this->isProduction()){
            $layout = new Zend_Layout();
            $layout->getView()->headScript()->appendFile('/js/jquery-ui.min.js');
            $layout->getView()->headScript()->appendFile('/js/jquery.ui.widget.js');
            $layout->getView()->headScript()->appendFile('/js/tmpl.min.js');
            $layout->getView()->headScript()->appendFile('/js/load-image.min.js');
            $layout->getView()->headScript()->appendFile('/js/canvas-to-blob.min.js');
            $layout->getView()->headScript()->appendFile('/js/bootstrap.min.js');
            $layout->getView()->headScript()->appendFile('/js/bootstrap-image-gallery.min.js');
            $layout->getView()->headScript()->appendFile('/js/jquery.iframe-transport.js');
            $layout->getView()->headScript()->appendFile('/js/jquery.fileupload.js');
            $layout->getView()->headScript()->appendFile('/js/jquery.fileupload-fp.js');
            $layout->getView()->headScript()->appendFile('/js/jquery.fileupload-ui.js');
            $layout->getView()->headScript()->appendFile('/js/locale.js');

            //set to include the file upload css files
            $layout->getView()->headLink()->prependStylesheet('/styles/bootstrap.min.css');
//        }
    }
    
    protected function downloadFile($path , $title = '' , $sContentDispositon = 'attachment'){

        //get file requested size
        $size = filesize($path);

        //get the file name 
        $name = rawurldecode($title);

        //table of possible mime types
        $known_mime_types=array(
                "pdf" => "application/pdf", 	
                "zip" => "application/zip",
                "doc" => "application/msword",
                "xls" => "application/vnd.ms-excel",
                "ppt" => "application/vnd.ms-powerpoint",
                "gif" => "image/gif",
                "png" => "image/png",
                "jpeg"=> "image/jpg",
                "docx"=>"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                "xlsx"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                "pptx"=>"application/vnd.openxmlformats-officedocument.presentationml.presentation",
                "bmp"=>"image/bmp",
                "jpg" =>  "image/jpg",
                "c" => "text/plain",
                "cpp" => "text/x-c",
                "rar" => "application/x-rar-compressed",
                "h" => "text/plain"
         );

         //get file extension
        $file_extension = strtolower(substr(strrchr($path,"."),1));

        //grab the right mime type from table and extension
         if(array_key_exists($file_extension, $known_mime_types)){
                $mime_type=$known_mime_types[$file_extension];
         }
         else{
            echo 'Bad mime type';
            exit();
         }

        @ob_end_clean(); //turn off output buffering to decrease cpu usage
        // required for IE, otherwise Content-Disposition may be ignored
        if(ini_get('zlib.output_compression'))
                ini_set('zlib.output_compression', 'Off');

        //set headers for download
         header('Content-Type: ' . $mime_type);
         
         header('Content-Disposition: $sContentDispositon; filename="'.$title.'"');
         
         header("Content-Transfer-Encoding: binary");
         header('Accept-Ranges: bytes');
         /* The three lines below basically make the 
            download non-cacheable */
         header("Cache-control: private");
         header('Pragma: private');
         header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");


          // multipart-download and download resuming support
         if(isset($_SERVER['HTTP_RANGE']))
         {
                list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
                list($range) = explode(",",$range,2);
                list($range, $range_end) = explode("-", $range);
                $range=intval($range);
                if(!$range_end) {
                        $range_end=$size-1;
                } else {
                        $range_end=intval($range_end);
                }

                $new_length = $range_end-$range+1;
                header("HTTP/1.1 206 Partial Content");
                header("Content-Length: $new_length");
                header("Content-Range: bytes $range-$range_end/$size");
         } else {
                $new_length=$size;
                header("Content-Length: ".$size);
         }

         $file=$path;

         /* output the file itself */
         $chunksize = 1*(1024*1024); //you may want to change this
         $bytes_send = 0;
         if ($file = fopen($file, 'r'))
         {
            if(isset($_SERVER['HTTP_RANGE']))
            fseek($file, $range);

            while(!feof($file) && 
                    (!connection_aborted()) && 
                    ($bytes_send<$new_length)
                  )
            {
                    $buffer = fread($file, $chunksize);
                    print($buffer); //echo($buffer); // is also possible
                    flush();
                    $bytes_send += strlen($buffer);
            }
            fclose($file);
         } 
         else {
            return;
         }

        return;
    }
    
    /**
     * from the list of files we create a zip file in the upload path and return the zip name
     * @param array $files the path to the files that we need to save
     * @return string the path to the zip file
     */
    protected function createZip($files = array()){
        $uploaddir=$this ->getUploadDir();
        $overwrite = false;		
        $thefilename= rand(0, 99999) . '_' . rand(0, 99999) . '_' . rand(0, 99999) . '.zip';
        $destination = $uploaddir . 'Nerdeez.zip';

        //if we have good files...
        if(count($files)) {
            //create the archive
            $zip = new ZipArchive();
            if($zip->open($destination,ZIPARCHIVE::CREATE) !== true) {
              return NULL;
            }

            //add the files
            foreach($files as $file) {  
                $new_filename = substr($file,  8);
                $zip->addFile($file, $new_filename); 
            }

            //close the zip -- done!
            $zip->close();

            return $destination;
        }
        else{
            return NULL;
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
    
    /**
     * will get the zip file path and extract it to the zip file folder
     * @param type $sPath 
     * @return bool true if success
     */
    protected function extractZip($sPath){
        //get the path to extract the zip
        $sPathToExtract = $this ->getUploadDir() . 'zipcache';
        
        //if the dir doesnt exist i will create it
        if (!is_dir($sPathToExtract)){
            mkdir($sPathToExtract);
        }
        
        //extract the zip to the destination
        $zip = new ZipArchive();
        $zip->open($sPath);
        $zip ->extractTo($sPathToExtract);
        $zip ->close();
    }
    
    /**
     * will delete all the files in the zip cache dir
     */
    protected function clearZipDir(){
        //get the path to extract the zip
        $sPath = $this ->getUploadDir() . 'zipcache';
        
        //delete the folder
        $this->recursiveDelete($sPath);
    }
    
    /**
     * Delete a file or recursively delete a directory
     *
     * @param string $str Path to file or directory
     */
    public function recursiveDelete($str){
        if(is_file($str)){
            return @unlink($str);
        }
        elseif(is_dir($str)){
            //$scan = glob(rtrim($str,'/').'/*');
            foreach (scandir($str) as $sLeftover) {
                if ($sLeftover === '.' || $sLeftover === '..')continue;
                $this -> recursiveDelete($this->add_ending_slash($str) . $sLeftover);
            }
            return @rmdir($str);
        }
    }
    
    /**
     * add ending slash to path if necessary
     * @param String $path
     * @return String
     */
    private function add_ending_slash($path){

        $slash_type = (strpos($path, '\\')===0) ? 'win' : 'unix'; 

        $last_char = substr($path, strlen($path)-1, 1);

        if ($last_char != '/' and $last_char != '\\') {
            // no slash:
            $path .= ($slash_type == 'win') ? '\\' : '/';
        }

        return $path;
    }


    
    /**
     * gets md5 of file and checks if the file exist if exist return Zend_Db_Table_Row of the file table
     * if not return false
     * @param String $md5file 
     * @return False if the file doesnt exist Zend_Db_Table_Row if the file exist
     */
    protected function isFileExist($md5file){
        $mFiles = new Application_Model_DbTable_Files();
        $rFile = $mFiles ->fetchRow($mFiles -> select() -> where ('md5_hash = ?' , $md5file));
        if ($rFile == NULL)return FALSE;
        return $rFile;
    }
    
    
    
}

?>
