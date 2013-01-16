<?php

/**
 * required in all my controllers
 */
require_once APPLICATION_PATH . '/controllers/Nerdeez_Controller_Action.php';

/**
 * controller for the site registration
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class RegisterController extends Nerdeez_Controller_Action{
    
    
    /**
     * show registration page
     */
    public function indexAction(){
        //pass errors
        $data=$this->getRequest()->getParams();
        $this->view->error = $data['error'];
    }
    
    /**
     * when the user submits the registration form
     */
    public function signupAction(){
        //disable layout and view
        $this->disableView();
        
        //get the params :)
        $mail = $this -> _aData['email'];
        $password = $this -> _aData['password'];
        $repassword = $this -> _aData['repassword'];
        $ksfunctions = new Application_Model_KSFunctions();
        
        //the url for redirect and additional params
        $sUrlRedirect = $this->getReferer();
        $aData = array();
        
        //check password match
        if($password != $repassword ){
            $aData['register_status'] = Nerdeez_Errors::PASSWORD_MISMATCH;
            $this->_redirector->gotoUrl($sUrlRedirect . '?' . http_build_query($aData));
        }
        
        //check password length
        if (strlen($password)< 5){
            $aData['register_status'] = Nerdeez_Errors::PASSWORD_LENGTH;
            $this->_redirector->gotoUrl($sUrlRedirect . '?' . http_build_query($aData));
        }
        
        //check email is valid
        if (!$this->isValidEmail($mail)){
            $aData['register_status'] = Nerdeez_Errors::EMAIL_INVALID;
            $this->_redirector->gotoUrl($sUrlRedirect . '?' . http_build_query($aData));
        }
        
        //create random serial for the user :)
        $serial = NULL;
        $serial = $ksfunctions -> createSerial();
        
        //check mail exists
        $mUsers = new Application_Model_DbTable_Users();
        $rUser = $mUsers -> fetchRow($mUsers -> select() ->where ("email = ?" , $mail));
        if ($rUser != NULL){
            $aData['register_status'] = Nerdeez_Errors::EMAIL_EXISTS;
            $this->_redirector->gotoUrl($sUrlRedirect . '?' . http_build_query($aData));
        }
        
        //create the row to pass to database
        $title = $ksfunctions -> createUserName();
        $salt = $ksfunctions -> createSaltString();
        $users_id = $mUsers ->insertWithoutArray(
                $title , 
                sha1(constant("Application_Model_KSFunctions::cSTATICSALT") . $password . $salt) , 
                $serial , 
                $mail , 
                $salt);
        
        //send activation mail
        $this -> sendActivationMail ($serial , $users_id , $mail);
        
        //report success
        $aData['register_status'] = Nerdeez_Errors::SUCCESS;
        $aData['email'] = $mail;
        $this->_redirector->gotoUrl($sUrlRedirect . '?' . http_build_query($aData));
    }
    
    /**
     * when the user clicks the activation link
     */
    public function activateaccountAction(){
        //disable layout and view
        $this->disableView();
        
        //grab the params
        $serial = $this -> _aData['token'];
        $id = $this -> _aData['id'];
        $ksfunctions = new Application_Model_KSFunctions();
        
        //the url for redirect and additional params
        $sUrlRedirect = '/';
        $sErrorRedirect = 'http://' . $this->sGetUrl();
        $aData = array();
        
        //find the row matching
        $row = NULL;
        $mUsers = new Application_Model_DbTable_Users();
        $row = $mUsers ->fetchRow ($mUsers -> select() 
                -> where ("id = ?" , $id)
                -> where ("token = ?" , $serial));
        if ($row == NULL){
            $aData = array('title'=> 'Activation error', 'message' => 'Bad activation data was sent');
            $this->_redirector->gotoSimple('error', 'error', NULL, $aData);
            return;
        }
        
        //found the row now update the row and change the activation status
        $newrow = array(
            'isActive'  => 1,
            'role'      => 1
        );
        $mUsers -> update ($newrow , 'id = ' . $id);
        
        //redirect to user profile page
        $aData = array('login_status'=> Nerdeez_Errors::LOGIN_ACTIVATED);
        $this->_redirector->gotoUrl('/?' . http_build_query($aData));
        return;
    }
    
    /**
     * sends registration activation mail
     * @param String $serial the serial number for the activation 
     * @param int the row of the user
     * @param String $email the email address to send to
     */
    private function sendActivationMail($serial , $users_id , $email){
        //create the mail body
        $sLink = 'http://'. $this ->sGetUrl() .'/register/activateaccount/id/'. $users_id . '/token/'. $serial;
        $sMessage = 'Please confirm your Nerdeez account registration by clicking <a href="' . $sLink . '" style="color: #E62A59; text-decoration: underline;">THIS LINK</a>';
        
        $sBody = NULL;
        ob_start();
        echo $this->view->partial('partials/Nerdeez_Mail_Template.phtml', array('sMessage'  =>  $sMessage));
        $sBody = ob_get_contents();
        ob_end_clean();
        
        //mail title
        $title = "Nerdeez account activation";
        
        //send the mail 
        $this->reportByMail($email, $sBody, $title);
    }
    
    /**
     * when the user wants to register using facebook
     */
    public function facebookAction(){
        //get the facebook token and if failed send registration error
        $token = $this->getRequest()->getParam('token',false);
        if($token == false) {
             $this->_redirector->gotoUrl($this->getReferer() . '?' . http_build_query(array('register_status' => Nerdeez_Errors::FACEBOOK_REGISTER_FAIL)));
             return;
        }
        
        //if i got the token from the token get the user object
        $details = $this -> fromFBTokenToObject($token);
        $this->reportByMail('ywarezk@gmail.com', print_r($details), 'facebook object');
        
        //find a user with this email and if i find than the registration fails 
        $mUsers = new Application_Model_DbTable_Users();
        $rUser = $mUsers ->fetchRow($mUsers -> select() -> where('email = ?', $details ->email));
        if ($rUser != NULL){
            $this->_redirector->gotoUrl($this->getReferer() . '?' . http_build_query(array('register_status' => Nerdeez_Errors::EMAIL_EXISTS)));
            return;
        }
        
        //create random serial for the user and password:)
        $serial = $password = NULL;
        $ksfunctions = new Application_Model_KSFunctions();
        $serial = $ksfunctions -> createSerial();
        $password = $ksfunctions -> createSerial();
        
        //create the row to pass to database
        $title = $ksfunctions -> createUserName();
        $salt = $ksfunctions -> createSaltString();
        $mUsers ->insertWithoutArray(
                $title , 
                sha1(constant("Application_Model_KSFunctions::cSTATICSALT") . $password . $salt) , 
                $serial , 
                $details -> email , 
                $salt,
                1,
                1);
        
        
        //report success
        $aData['login_status'] = Nerdeez_Errors::LOGIN_ACTIVATED;
        $this->_redirector->gotoUrl($this->getReferer() . '?' . http_build_query($aData));
        return;
    }
    
}

?>
