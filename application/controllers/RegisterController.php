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
        
        //check password match
        if($password != $repassword ){
            $this->ajaxReturnFailed(array('repassworderrors' => "Password and Retype password don't match"));
            return;
        }
        
        //check password length
        if (strlen($password)< 5){
            $this->ajaxReturnFailed(array('passworderrors' => "Password must be longer than 5 chars"));
            return;
        }
        
        //check email is valid
        if (!$this->isValidEmail($mail)){
            $this->ajaxReturnFailed(array('emailerrors' => "Invalid email address"));
            return;
        }
        
        //create random serial for the user :)
        $serial = NULL;
        $serial = $ksfunctions -> createSerial();
        
        //check mail exists
        $mUsers = new Application_Model_DbTable_Users();
        $rUser = $mUsers -> fetchRow($mUsers -> select() ->where ("email = ?" , $mail));
        if ($rUser != NULL){
            $this->ajaxReturnFailed(array('emailerrors' => "Email address already exists"));
            return;
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
        $this->ajaxReturnSuccess(array('email' => $mail));
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
        
        //find the row matching
        $row = NULL;
        $mUsers = new Application_Model_DbTable_Users();
        $row = $mUsers ->fetchRow ($mUsers -> select() 
                -> where ("id = ?" , $id)
                -> where ("token = ?" , $serial));
        if ($row == NULL){
            $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Bad id or serial!'));
            return;
        }
        
        //found the row now update the row and change the activation status
        $newrow = array(
            'isActive'  => 1,
            'role'      => 1
        );
        $mUsers -> update ($newrow , 'id = ' . $id);
        
        //redirect to user profile page
        $this->_redirector->gotoUrl('/?is_activated=true'); 
        return;
    }
    
}

?>
