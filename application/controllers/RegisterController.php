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
        $this->_helper->layout()->disableLayout();
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        
        //get the params
        $mail = $this -> _aData['email'];
        $password = $this -> _aData['password'];
        $repassword = $this -> _aData['repassword'];
        $ksfunctions = new Application_Model_KSFunctions();
        
        //check password match
        if($password != $repassword ){
            $this->_redirector->gotoUrl('/index/index/error/' . urlencode('repassword must match the password!'));
            return;
        }
        
        //create random serial for the user
        $serial = NULL;
        $serial = $ksfunctions -> createSerial();
        
        //create the model
        $mUsers = new Application_Model_DbTable_Users();
        
        //check mail exists
        $select = $mUsers -> select() ->where ("email = ?" , $mail);
        $rows = $mUsers -> fetchAll($select);
        if ($rows -> count() != 0){
            $this->_redirector->gotoUrl('/index/index/error/' . urlencode('User with this mail already exists!'));
            return;
        }
        
        //check if user has a row in users
        $users_id = NULL;
        $users_id = $ksfunctions -> getUserId();
        
        //grab/create the title
        $title = $ksfunctions -> createUserName();
        if ($users_id != NULL){
            $select = $mUsers -> select() -> where ('id = ?' , $users_id);
            $rows = $mUsers -> fetchAll ($select);
            if ($rows -> count() == 0){
                $users_id = NULL;
            }
            else{
                $row = $rows -> getRow(0);
                $title = $row['title'];
            }
        }
        
        
        //create the row to pass to database
        $salt = $ksfunctions -> createSaltString();
        $mUsers ->insertWithoutArray(
                $title , 
                sha1(constant("Application_Model_KSFunctions::cSTATICSALT") . $password . $salt) , 
                $serial , 
                $mail , 
                $salt);
        
        //send activation mail
        $ksfunctions -> sendActivationMail ($serial , $users_id , $mail);
        $this->_redirector->gotoUrl('/index/index/message/' . urlencode('An account activation mail was sent. To complete the activation process you have to follow the link in the mail.'));
    }
    
    /**
     * when the user clicks the activation link
     */
    public function activateaccountAction(){
        //disable layout and view
        $this->_helper->layout()->disableLayout();
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        
        //grab the params
        $serial = $this -> _aData['token'];
        $id = $this -> _aData['id'];
        $ksfunctions = new Application_Model_KSFunctions();
        
        //find the row matching
        $row = NULL;
        $mUsers = new Application_Model_DbTable_Users();
        $select = $mUsers -> select() 
                -> where ("id = ?" , $id)
                -> where ("serial = ?" , $serial);
        $rows = $mUsers -> fetchAll ($select);
        if ($rows -> count () != 1){
            $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Bad id or serial!'));
            return;
        }
        $row = $rows -> getRow(0);
        
        //found the row now update the row and change the activation status
        $newrow = array(
            'isActive' => 1
        );
        $mUsers -> update ($newrow , 'id = ' . $id);
        
        //redirect to user profile page
        $this->_redirector->gotoUrl('/index/index/message/'. urlencode('MESSAGE: Account activated please login to continue') ); 
        return;
    }
    
}

?>
