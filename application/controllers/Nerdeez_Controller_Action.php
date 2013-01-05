<?php

/**
 * required for the extend part
 */
require_once 'Zend/Controller/Action.php';


/**
 * enum for the data we get in post get params
 */
class Nerdeez_ParamTypes{
    const INTEGER = 0;
    const STRING = 1;
    const JSONARRAYNUMBERS = 2;
}

/**
 * all of nerdeez controllers will extend this class
 *
 * @author Yariv Katz
 * @copyright nerdeez.com Ltd.
 * @version 1.0
 */
abstract class Nerdeez_Controller_Action extends Zend_Controller_Action{
    
    /**
     * redirector helper for other pages
     * @var Zend_Controller_Action_Helper_Redirector 
     */
    protected $_redirector = null;
    
    /**
     * this var will hold all the params sanitized
     * @var Array
     */
    protected $_aData = array();
    
    /**
     * this array will hold all the possible params from get post
     * @var Array 
     */
    protected $_aParams = array(
        array('name' => 'model' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 50) ,
        array('name' => 'error' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 200) ,
        array('name' => 'status' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 200) ,
        array('name' => 'message' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 300) ,
        array('name' => 'description' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 150) ,
        array('name' => 'image' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 300) ,
        array('name' => 'website' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 300) ,
        array('name' => 'coursefolder' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 100) ,
        array('name' => 'serial' , 'type' => Nerdeez_ParamTypes::INTEGER , 'min' => 0 , 'max' => 99999) ,
        array('name' => 'page' , 'type' => Nerdeez_ParamTypes::INTEGER , 'min' => 0 , 'max' => 99999) ,
        array('name' => 'folder_papa' , 'type' => Nerdeez_ParamTypes::INTEGER , 'min' => -1 , 'max' => 99999) ,
        array('name' => 'hw_number' , 'type' => Nerdeez_ParamTypes::INTEGER , 'min' => -1 , 'max' => 99999) ,
        array('name' => 'papa' , 'type' => Nerdeez_ParamTypes::INTEGER , 'min' => -2 , 'max' => 100) ,
        array('name' => 'id' , 'type' => Nerdeez_ParamTypes::INTEGER , 'min' => -1 , 'max' => 0) ,
        array('name' => 'courses_id' , 'type' => Nerdeez_ParamTypes::INTEGER , 'min' => -1 , 'max' => 0) ,
        array('name' => 'universities_id' , 'type' => Nerdeez_ParamTypes::INTEGER , 'min' => -1 , 'max' => 0) ,
        array('name' => 'folders_id' , 'type' => Nerdeez_ParamTypes::INTEGER , 'min' => -1 , 'max' => 0) ,
        array('name' => 'folder' , 'type' => Nerdeez_ParamTypes::INTEGER , 'min' => 0 , 'max' => 0) ,
        array('name' => 'search' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 300) ,
        array('name' => 'password' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 20) ,
        array('name' => 'repassword' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 20) ,
        array('name' => 'disposition' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 20) ,
        array('name' => 'email' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 100) ,
        array('name' => 'title' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 100) ,
        array('name' => 'token' , 'type' => Nerdeez_ParamTypes::STRING , 'length' => 20) ,
        array('name' => 'ids' , 'type' => Nerdeez_ParamTypes::JSONARRAYNUMBERS , 'length' => 150 , 'min' => 0 , 'max' => 0) ,
        array('name' => 'folders' , 'type' => Nerdeez_ParamTypes::JSONARRAYNUMBERS , 'length' => 150 , 'min' => 0 , 'max' => 0) ,
        
    );
    
    /**
     * common init for all my controllers
     */
    public function init(){ 
        //set the redirector
        $this->_redirector = $this->_helper->getHelper('Redirector');
        
        //get the params
        $aData=$this->getRequest()->getParams();
        
        //sanitize all the vars and put them in a local array
        foreach ($this->_aParams as $aParam) {
            $sName = $aParam['name'];
            if (!isset ($aData[$sName])) continue;
            $iValue = $aData[$sName];
            $iType = $aParam['type'];
            $iLength = isset ($aParam['length'])? $aParam['length'] : 0;
            $iMin = isset ($aParam['min'])? $aParam['min'] : 0;
            $iMax = isset ($aParam['max'])? $aParam['max'] : 0;
            
            //sanitize integer 
            if ($iType === Nerdeez_ParamTypes::INTEGER){
                if (!is_numeric($iValue)){
                    $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Invalid params'));
                    return;
                }
                if ( $iValue <= $iMin){
                    $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Invalid params'));
                    return;
                }
                if ($iMax > 0 && $iValue > $iMax){
                    $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Invalid params'));
                    return;
                }
            }
            
            //sanitize string
            if ($iType === Nerdeez_ParamTypes::STRING || $iType === Nerdeez_ParamTypes::JSONARRAYNUMBERS){
                if ($this ->sanitize_Title($iValue, $iLength) === NULL){
                    $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Invalid params'));
                    return;
                }
                
                //sanitize json array
                if ($iType === Nerdeez_ParamTypes::JSONARRAYNUMBERS){
                    $aIds = json_decode(str_replace('\\', '', $iValue));
                    if (is_array($aIds)){
                        foreach($aIds as $iId){
                            if (!is_numeric($iId)){
                                $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Invalid params'));
                                return;
                            }
                            if ( $iId <= $iMin){
                                $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Invalid params'));
                                return;
                            }
                            if ($iMax > 0 && $iId > $iMax){
                                $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Invalid params'));
                                return;
                            }
                        }
                        $iValue = $aIds;
                    }
                    else{
                        $iValue = array($aIds);
                    }
                }
            }
            //value is sanitized now you can put it in our array and sleep in peace
            $this -> _aData[$sName] = $iValue;
        }
        
        //check the remember me cookies
        $this ->rememberMe();
        
        //set the layout
        $layout = new Zend_Layout();
        $layout->setLayoutPath(APPLICATION_PATH . '/layouts/scripts/guest.phtml');
        $layout -> isRegistered = $this->isRegistered();
        $layout -> user = $this -> getUserInfo();
        //$layout -> menu = $this -> view -> render ('partials/menus/guest_menu.phtml');
        
        //set the view error and status messages
        $layout -> sError = $this -> _aData['error'];
        $layout -> sStatus = $this -> _aData['status'];
        
        
    }
    
    /**
     * gets the referer for this page
     * @return String the referer
     */
    protected function getReferer(){
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $referer = $request->getHeader('referer'); 
        return $referer;
    }
    
    /**
     *Strips and trims the tag and makes sure the length is no longer than langth chars
     * 
     * @param String $title 
     * @param int $length check the string is no longer than this length
     * @return String null if title is invalid or sanitized title if valid
     */
    protected function sanitize_Title($title , $length){
        if($title == null) return "";
        $title = str_replace('\\', '', $title);
        $link = $this->getMysqlConnection();
        $data = array('title' => mysql_real_escape_string($title , $link));
        $filters=array('title' => array('StringTrim' , 'StripTags'));
        $validators=array('title' => array(array('StringLength', array(0, $length))));
        $input = new Zend_Filter_Input($filters, $validators, $data);
        if(!$input->isValid()){         
            return null;
        }
        $data = $input->getEscaped();
        return $data['title'];       
    }
    
    /**
     * gets the mysql connection data from the config and returns the mysql link resource
     * @return ResourceBundle MySQL link identifier on success or FALSE on failure. 
     */
    private function getMysqlConnection(){
        //connect to config file 
        $config = new Zend_Config_Ini('../application/configs/application.ini','production');
        
        //get host user password 
        $host = $config->resources->db->params->host;
        $user = $config->resources->db->params->username;
        $pass = $config->resources->db->params->password;
        
        $link = mysql_connect('localhost', $user, $pass)
        OR die(mysql_error());
        
        return $link;
    }
    
    /**
     * set the user as logged in if there is good remember me cookies
     * @return type 
     */
    private function rememberMe(){
        //check if user has identity saved in the sessions
        $bIsIdentity = FALSE;
        $bIsIdentity = $this -> isRegistered();
        
        //if the user has identity than return
        if ($bIsIdentity)return;
        
        //check if the user has cookies set
        $bIsCookies = FALSE;
        $bIsCookies = isset ($_COOKIE['email']) && isset ($_COOKIE['identifier']);
        
        //if the user dont have a remember me cookie than return
        if (!$bIsCookies)return;
        
        //grab the email identifier and token
        $sEmail = $sIdentifier = $sToken =  NULL;
        $sEmail = $this -> sanitize_Title($_COOKIE['email'] , 100);
        $sIdentifier = $this -> sanitize_Title($_COOKIE['identifier'] , 200);
        $sToken = $this -> sanitize_Title($_COOKIE['token'] , 200);
        
        //check that the row matches the logincoockies row
        $bIsRowExist = FALSE;
        $mLogincookies = new Application_Model_DbTable_Logincookies();
        try{
            $rLogin = $mLogincookies -> fetchRow($mLogincookies -> select()
                -> where ('email = ?' , $sEmail)
                -> where ('identifier = ?' , $sIdentifier)
                -> where ('token = ?' , $sToken));
        }
        catch(Exception $e){
            return;
        }
        $bIsRowExist = $rLogin != NULL;
        
        //if the row doesnt exist check if the email and identifier exists if so security breach
        if (!$bIsRowExist){
            try{
             $rsLogincookies = $mLogincookies -> fetchAll($mLogincookies -> select()
                -> where ('email = ?' , $sEmail)
                -> where ('identifier = ?' , $sIdentifier));
            }
            catch(Exception $e){
                return;
            }
            if ($rsLogincookies -> count() > 0){
                //delete all the rows you found
                $mLogincookies -> deleteRowset($rsLogiמncookies);
                
                //redirect to main page with cookie theft suspicion
                $this -> _redirector ->gotoUrl('/error/' . urlencode('suspected cookie theft please change your password!'));
                return;
            }
            return;
        }
        
        //got a triplet match then need to change token and update db
        $sNewtoken = $this ->createSaltStringWithLength(200);
        $aLoginUpdate = array(
            'token'     => $sNewtoken
        );
        $where = $mLogincookies->getAdapter()->quoteInto('id = ?', $rLogin['id']);
        $mLogincookies->update($aLoginUpdate, $where);
        
        //update the cookie with the new token
        $sUrl = $this ->sGetUrl();
        $inTwoMonths = 60 * 60 * 24 * 60 + time();
        setcookie('token', $sToken, $inTwoMonths,"/", "." . $sUrl);
        
        //grab the user row 
        $rUser = NULL;
        $mUsers = new Application_Model_DbTable_Users();
        $rUsers = $mUsers -> fetchRow($mUsers -> select() -> where ('email = ?' , $sEmail));
        if ($rUser == NULL) return;
        
        //get the columns from the model
        $aCols = NULL;
        $aCols = $mModel->info(Zend_Db_Table_Abstract::COLS);
        
        //from the user row create the users object
        $oUser = NULL;
        $oUser = new stdClass();
        foreach ($aCols as $sCol) {
            $oUser -> $sCol = $rUser[$sCol];
        }
        
        //write the object to auth
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('Users'));
        $auth->getStorage()->write($oUser);
        
        //the end
    }
    
    /**
     * for the ajax functions call this to disable the view loading
     */
    protected function disableView(){
        $this->_helper->layout()->disableLayout(); 
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
    }
    
    /**
     * gets a value from the config file
     * @param String $sKey the key to retrieve
     * @return String the value 
     */
    protected function getFromConfig($sKey){
        $config = new Zend_Config_Ini('../application/configs/application.ini','production');
        return $config->{$sKey};
    }
    
    /**
     *send mail to {$mail} with  content {$body}
     * 
     * @param String $mail - the mail address
     * @param String $body  - the text content of the mail 
     */
    public function reportByMail($email , $body , $title){
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: admin@nerdeez.com' . "\r\n";
        @mail($email,$title,$body,$headers);
    }
    
    /**
     * when ajax was completed successfully pass it to the user
     */
    public function ajaxReturnSuccess(){
        $userData=array(array('status'=>'success','data'=>''));
        $dojoData= new Zend_Dojo_Data('status',$userData);
	echo $dojoData->toJson();
    }
    
    /**
     * when ajax was completed successfully pass it to the user
     * @param String $sMsg the failed message to send
     */
    public function ajaxReturnFailed($sMsg = '', $sType= ''){
        $userData=array(array('status'=>'failed','msg'=>$sMsg, 'type'=>$sType));
        $dojoData= new Zend_Dojo_Data('status',$userData);
	echo $dojoData->toJson();
    }
    
    /**
     * gets the role of the user
     * @return String user , guest
     */
    public function getRole(){
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('Users'));
        return $auth -> getIdentity() -> role;
    }
    
    /**
     * gets the upload dir from the config
     * @return String the path
     */
    public function getUploadDir(){
        return $this->getFromConfig('uploaddir');
    }
    
    /**
     * is this development or production server
     * @return Bool TRUE if this is production server
     */
    protected function isProduction(){
        //server is development
        if ($_SERVER['SERVER_ADDR'] === $this->getFromConfig('developmentip')){
            return FALSE;
        }
        else{
            return TRUE;
            //return FALSE;
        }
    }
    
    /**
     * 
     */
    public function preDispatch() {
        parent::preDispatch();
        
        //set all the js files and css files
        $layout = new Zend_Layout();
        if ($this -> isProduction()){
            $layout -> getView() -> headScript() -> appendFile($this->view->baseUrl('js/static.min.js'));
            $layout -> getView() -> headLink()->prependStylesheet($this->view->baseUrl('styles/static.min.css'));
        }
        else{
            $layout -> getView() -> headScript() -> prependFile($this->view->baseUrl('js/jquery.ksfunctions.js'));
            $layout -> getView() -> headScript() -> prependFile($this->view->baseUrl('js/jquery.ez-pinned-footer.js'));
            $layout -> getView() -> headScript() -> prependFile($this->view->baseUrl('js/superfish.js'));
            $layout -> getView() -> headScript() -> prependFile($this->view->baseUrl('js/jquery.validate.min.js'));
            $layout -> getView() -> headScript() -> prependFile($this->view->baseUrl('js/jquery-1.7.1.min.js'));
            $layout -> getView() -> headLink()->prependStylesheet($this->view->baseUrl('styles/styles.css'));
            $layout -> getView() -> headLink()->prependStylesheet($this->view->baseUrl('styles/superfish-navbar.css'));
            $layout -> getView() -> headLink()->prependStylesheet($this->view->baseUrl('styles/superfish-vertical.css'));
            $layout -> getView() -> headLink()->prependStylesheet($this->view->baseUrl('styles/superfish.css'));
        }
    }
    
    /**
     * init the paginator
     * @param Zend_Db_Table_Select $select the selection from the database
     * @param int $page the page of the paginator
     */
    protected function setPagination($select, $page = 1){
        $adapter = new Zend_Paginator_Adapter_DbSelect($select);
        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $this -> view -> paginator = $paginator;
    }
    
    /**
     * sends registration activation mail
     * @param String $serial the serial number for the activation 
     * @param int the row of the user
     * @param String $email the email address to send to
     */
    public function sendActivationMail($serial , $users_id , $email){
        //create the mail body
        $body = '<HTML><BODY><CENTER>
        <h1>Nerdeez Account activation</h1>
        <p>
        Please confirm your Nerdeez account by clicking this link:
        </p>
        <p>
            <a href="http://'. $this ->sGetUrl() .'/register/activateaccount/id/'. $users_id . '/token/'. $serial .'">
                activate account
            </a>
        </p>
        <p>
        Regards, Nerdeez Team
        </p>
        </CENTER></BODY>
        </HTML>';		
        
        //mail title
        $title = "Nerdeez account activation";
        
        //send the mail 
        $this->reportByMail($email, $body, $title);
    }
    
    /**
     * returns the url of the site
     * @return String the url of the site without http://www. 
     */
    public function sGetUrl(){
        //$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
      	return
    		/*($https ? 'https://' : 'http://').*/
    		(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
    		(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
    		($https && $_SERVER['SERVER_PORT'] === 443 ||
    		$_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
    		substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }
    
    /**
     * determine if the user is registered
     * @return Boolean true if registered
     */
    public function isRegistered(){
        $isIdentity = FALSE;
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('Users'));
        $isIdentity = $auth->hasIdentity();
        return $isIdentity;
    }
    
    /**
     * creates a random salt string
     * @return String
     */
    public function createSaltStringWithLength($length){
        $dynamicSalt = '';
        for ($i = 0; $i < $length; $i++) {
            $dynamicSalt .= chr(rand(33, 126));
        }
        return $dynamicSalt;
    }
    
    /**
     * gets the user info from the auth
     */
    protected function getUserInfo(){
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('Users'));
        return $auth ->getIdentity();
    }
    
}



?>
