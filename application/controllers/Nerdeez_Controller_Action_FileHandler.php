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
 * abstract class adds file managment to a controller
 *
 * @author Yariv Katz
 * @copyright Nerdeez.com Ltd.
 * @version 1.0
 */
abstract class Nerdeez_Controller_Action_FileHandler extends Nerdeez_Controller_Action{
    
    
    
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
        //get the object info
        $s3 = new Nerdeez_Service_Amazon_S3();
        $aObjectInfo = $s3 -> getInfo($sPath);

        if (is_array($aObjectInfo)) {
            header('Content-type: ' . $aObjectInfo['type']);
            header('Content-length: ' . $aObjectInfo['size']);
            if ($sTitle !== '')
                header('Content-Disposition: attachment; filename="'.rawurldecode($sTitle).'"');
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
