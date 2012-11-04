<?php

/**
 * required in all my controllers
 */
require_once APPLICATION_PATH . '/controllers/Nerdeez_Controller_Action.php';

/**
 * the s3 zend wrapper
 */
require_once 'Zend/Service/Amazon/S3.php';

/**
 * abstract class adds file managment to a controller
 *
 * @author Yariv Katz
 * @copyright Nerdeez.com Ltd.
 * @version 1.0
 */
abstract class Nerdeez_Controller_Action_FileHandler extends Nerdeez_Controller_Action{
    
    /**
     * amazon s3 key
     * @var String 
     */
    private $_sAwsKey = 'AKIAIVIUYDC6HTRM5VHQ';
    
    /**
     * amazon s3 secret key
     * @var String 
     */
    private $_sAwsSecretKey = 'YIK/IsFkQ4EU/Yno/cRDcoKkBsRjBur2Hgl8P7kx';
    
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
                //$upload_handler->get();
                break;
            case 'POST':
                //if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
                //    $upload_handler->delete();
                //} else {
                $upload_handler->post();
                //}
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
     * try and download the file with the path
     * @param String $sPath the path to the file to download
     */
    protected function download($sPath , $sTitle = ''){
        //check file requested is readable
        if(!is_readable($sPath)){
            echo 'Failed reading file';
            exit();
        }

        //get file requested size
        $size = filesize($sPath);

        //get the file name 
        $name = rawurldecode($sTitle);

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
        $file_extension = strtolower(substr(strrchr($sPath,"."),1));

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
         
         header('Content-Disposition: attachment; filename="'.$sTitle.'"');
         
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

         $file=$sPath;

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
     * initialize here common view variables and also attach the admin js file
     */
    public function preDispatch(){
        //call the parent predispatch
        parent::preDispatch();
        
        //set to include the fielupload js files
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
        
    }
    
}

?>
