<?php

/**
 * required in all my controllers
 */
require_once APPLICATION_PATH . '/controllers/Nerdeez_Controller_Action.php';

/**
 * controller for the site login mechanizem
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class LoginController extends Nerdeez_Controller_Action{
    
    /**
     * main login form
     */
    public function indexAction(){
        //grab the params only error is valid here
        $data=$this->getRequest()->getParams();
        
        //pass to view
        $this -> view -> error = $data['error'];
    }
    
    /**
     * when the user logs in
     */
    public function loginAction(){
         $this ->disableView();
        
        //get all the params sanitized username password remember me :)
        $email = $this->_aData['email']; 
        $password = $this -> _aData['password'];
        $rememberme = TRUE;
        $data=$this->getRequest()->getParams();
        $rememberme = $data['rememberme'] == '1';
        $ksfunctions = new Application_Model_KSFunctions();
        
        //get the ip of the user :)
        $sIP = NULL;
        $sIP = $ksfunctions -> getRealIpAddr();
        
        //find all the rows with this ip and email
        $rsIps = NULL;
        $mIps = new Application_Model_DbTable_Banips();
        $rsIps = $mIps -> fetchAll($mIps -> select() 
                -> where ('email = ?' , $email)
                -> where ('ip = ?' , $sIP)
                -> order ('starttime DESC'));
        
        //count all the attempts in the past 10 minutes
        $iIpCounter = 0;
        $iNow = time();
        foreach ($rsIps as $rIp){
            if ($iNow - $rIp['starttime'] < 60*10) $iIpCounter++;
        }
        
        //if three atemtps then brute force assumed 
        if ($iIpCounter > 3){
            //$this->_redirector->gotoUrl('/index/index/error/' . urlencode('This account is banned for 10 minutes'));
            $this->_redirector->gotoSimple('error', 'error', NULL, array('title'=>'IP Banned', 'message' => 'Your IP is banned for the next 10 minutes'));
            return;
        }
        
        //find the salt with the email and if user is active
        $salt = NULL;
        $isActive = FALSE;
        $mUsers = new Application_Model_DbTable_Users();
        $row = $mUsers -> fetchRow($mUsers -> select() -> where ("email = ?" , $email));
        if ($row == NULL){
            $this->_redirector->gotoUrl($this->getReferer() . '?' . http_build_query(array('login_status' => Nerdeez_Errors::LOGIN_FAILED)));
            return;
        }
        $salt = $row['salt'];
        $isActive = $row['isActive'] == 1;
        
        //if the user is not active than kick him out
        if (!$isActive){
            $this->_redirector->gotoUrl($this->getReferer() . '?' . http_build_query(array('login_status' => Nerdeez_Errors::LOGIN_FAILED_ACTIVATE)));
            return;
        }
        
        if ($this->_process($email, $password)){
            
            //check if the user has cookies set
            $logincookies = new Application_Model_DbTable_Logincookies();
            $bIsCookies = FALSE;
            $bIsCookies = isset ($_COOKIE['email']) && isset ($_COOKIE['identifier']);
            $sOldIdentifier = $this -> sanitize_Title($_COOKIE['identifier'] , 200);

            //find all the old rows that i need to delete
            $rsLogincookies = NULL;
            if ($sOldIdentifier !== NULL){
                $rsLogincookies = $logincookies -> fetchAll($logincookies -> select() 
                        -> where ('email = ?' , $email)
                        -> where ('identifier = ?' , $sOldIdentifier));
            }

            //delete all the old rows
            $logincookies ->deleteRowset($rsLogincookies);
            
            //create the cookies for remember me
            if ($rememberme) {
                $auth = Zend_Auth::getInstance();
                $auth->setStorage(new Zend_Auth_Storage_Session('Users'));
                $userid = $auth->getIdentity()->id;
                

                //get the email , identifier , token
                $sEmail = $sIdentifier = $sToken = NULL;
                $sEmail = $email;
                $sIdentifier = $ksfunctions -> createSaltString2(200);
                $sToken = $ksfunctions -> createSaltString2(200);
                
                //insert the new values to the database
                $logincookies ->insertWithoutArray($sEmail, $sIdentifier, $sToken);
                
                //set the cookies
                $sUrl = $this -> sGetUrl();
                $inTwoMonths = 60 * 60 * 24 * 60 + time();
                setcookie('email', $sEmail, $inTwoMonths,"/", "." . $sUrl);
                setcookie('identifier', $sIdentifier, $inTwoMonths,"/", "." . $sUrl);
                setcookie('token', $sToken, $inTwoMonths,"/", "." . $sUrl);
                
            }
            
            //redirect the user to same page
            $this->_redirector->gotoUrl($this->getReferer());
            return;
        }
        else{
            //login failed add row in the ips table
            $mIps ->insertWithoutArray(time(), $ksfunctions -> getRealIpAddr(), $email);
        }
        $this->_redirector->gotoUrl($this->getReferer() . '?' . http_build_query(array('login_status' => Nerdeez_Errors::LOGIN_FAILED)));
        return;
    }
    
     public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('Users'));
        if ($auth->hasIdentity()) {
            ## Delete Cookie Code
            $logincookies = new Application_Model_DbTable_Logincookies();
            //$logincookies->deleteDBCookie($userid);
            //$logincookies ->deleteRowWithId($auth->getIdentity()->id);
            $logincookies ->deleteRowset($logincookies ->fetchAll($logincookies -> select() 
                    -> where('email = ?', $auth->getIdentity() -> email)));

        }
        $auth->clearIdentity();

        ## Clear Cookie
        $killtime = time() - 3600;
        $sUrl = $this -> sGetUrl();
        setcookie('email', '', $killtime,"/", "." . $sUrl);
        setcookie('identifier', '', $killtime,"/", "." . $sUrl);
        setcookie('token', '', $killtime,"/", "." . $sUrl);

        $this->_redirector->gotoUrl($this->getReferer());
    }
    
    /**
     * when the user wants to submit the forget action
     */
    public function forgotAction(){
        $data=$this->getRequest()->getParams();
        $this->view->error = $data['error'];
    }
    
    /**
     * when the user submits the reset password form
     */
    public function resetpasswordAction(){
        //disabel layout and view
        $this->_helper->layout()->disableLayout();
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        
        //get params
        $email = $this -> _aData['email']; 
        
        //find user row with this email
        $row = NULL;
        $mUsers = new Application_Model_DbTable_Users();
        $select = $mUsers -> select() -> where ("email = ?" , $email);
        $rows = $mUsers -> fetchAll($select);
        if ($rows -> count() != 1){
            $userData=array(array('status'=>'failed','msg'=>'Invalid Email'));
            $dojoData= new Zend_Dojo_Data('status',$userData);
	    echo $dojoData->toJson();
	    return;
        }
        $row = $rows -> getRow(0);
        
        //insert the new row to the forgot database
        $ksfunctions = new Application_Model_KSFunctions();
        $sToken = $ksfunctions -> createSerial();
        $aNewForgotRow = array(
            'users_id'      => $row['id'] ,
            'token'         => $sToken ,
            'starttime'     => time()
        );
        $mForgot = new Application_Model_DbTable_Forgotpassword();
        $mForgot -> insert($aNewForgotRow);
        
        //send him mail to approve the reset
        $ksfunctions->sendResetPasswordMail($sToken , $row['id'] , $row['email']);
        
        //return  success
        
    }
    
    public function approveresetAction(){
        //get the params
        $id = $this ->_aData['id'];
        $serial = $this -> _aData['token'];
        
        //get the login row
        $rForgot = NULL;
        $mForgot = new Application_Model_DbTable_Forgotpassword();
        $rsForgot = $mForgot -> fetchAll ($mForgot -> select() 
                -> where ('users_id = ?' , $id)
                -> where ('token = ?' , $serial)
                -> order ('starttime DESC'));
        if ($rsForgot -> count() == 0){
            $this -> view -> sError = "Bad params";
            return;
        }
        $rForgot = $rsForgot -> getRow(0);
        
        //check the time 
        $iNow = time();
        $iThen = $rForgot['starttime'];
        $iDelta = $iNow - $iThen;
        if ($iDelta > 60*60*24){
            $this -> view -> sError = "Reset password request expired. Try to reset again";
            //delete all the old rows
            foreach ($rsForgot as $rForgot) {
                $where = $mForgot->getAdapter()->quoteInto('id = ?', $rForgot['id']);
                $mForgot->delete($where);
            }
            return;
        }
        
        //create the new password
        $sPassword = NULL;
        $ksfunctions = new Application_Model_KSFunctions();
        $sPassword = $ksfunctions -> createSerial();
        
        //delete all the rows from the table
        foreach ($rsForgot as $rForgot) {
            $where = $mForgot->getAdapter()->quoteInto('id = ?', $rForgot['id']);
            $mForgot->delete($where);
        }
        
        //find the row
        $row = NULL;
        $mUsers = new Application_Model_DbTable_Users();
        $select = $mUsers -> select() 
                -> where ("id = ?" , $id);
        $rows = $mUsers -> fetchAll($select);
        if ($rows -> count() != 1){
            $this -> view -> sError = "Bad params";
            return;
        }
        $row = $rows -> getRow(0);
        
        //update the row with the new password
        $mUsers = new Application_Model_DbTable_Users();
        $data = array(
            'pass' => sha1(constant("Application_Model_KSFunctions::cSTATICSALT") . $sPassword . $row['salt'])
        );
        $mUsers -> update($data , 'id = ' . $id);
        
        //send the new password by masil to user
        $ksfunctions ->sendNewPasswordMail($row['email'] , $sPassword);
        
        $this -> view -> sStatus = "SUCCESS!";
        $this->view->sPassword = $sPassword;
        return;
    }
    
    /**
     * process login request
     * @param String $email
     * @param String $password
     * @return Boolean 
     */
    protected function _process($email , $password) {
        //create the auth adapter
        $adapter = new Zend_Auth_Adapter_DbTable(
            Zend_Db_Table::getDefaultAdapter(),
            'users',
            'email',
            'pass',
            "SHA1(CONCAT('"
            . constant("Application_Model_KSFunctions::cSTATICSALT")
            . "', ?, salt))"
        );
        
        //pass values to the adapter
        $adapter->setIdentity($email); 
        $adapter->setCredential($password);
        
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('Users'));
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            $user = $adapter->getResultRowObject();
            ## Check if Email has been confirmed
            $auth->getStorage()->write($user);
            return true;
        }
        return false;
    }
    
}

?>
