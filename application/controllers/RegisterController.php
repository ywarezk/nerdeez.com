<?php

/**
 * controller for the site registration
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class RegisterController extends Zend_Controller_Action{
    
    
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
        $mail = NULL;
        $password = NULL;
        $repassword = NULL;
        $data=$this->getRequest()->getParams();
        $validator = new Zend_Validate_EmailAddress();
        $ksfunctions = new Application_Model_KSFunctions();
        if ($validator->isValid($data['email'])) {
            $mail = $data['email'];
        } else {
            // email is invalid; print the reasons
            $reasons = '';
            foreach ($validator->getMessages() as $messageId => $message) {
                $reasons .= "ERROR: Validation failure '$messageId': $message<br/>";
            }
            $reasons = urlencode($reasons);
            //redirect to the same page just with error
            $this->_redirector->gotoUrl('/index/index/error/' . $reasons);
            return;
        } 
        $password = $ksfunctions -> sanitize_Title($data['password'] , 20);
        if($password == null || strlen($password) < 5){
            //redirect to the same page just with error
            $this->_redirector->gotoUrl('/index/index/error/' . urlencode('Invalid password - password have to be more than 5 characters and less than 20!'));
            return;
        }
        $repassword = $ksfunctions -> sanitize_Title($data['repassword'] , 20);
        if($repassword == null){
            //redirect to the same page just with error
            $this->_redirector->gotoUrl('/index/index/error/' . urlencode('Invalid repassword!'));
            return;
        }
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
        
        //find the unviersity thats simply the first uni
        /**$uni_id = 0;
        $mKSPost = new Application_Model_DbTable_KSPost();
        $select = $mKSPost -> select() -> where("type = 'university'");
        $university = $mKSPost -> fetchRow($select);
        $uni_id = $university['id'];*/
        
        //create the row to pass to database
        $salt = $ksfunctions -> createSaltString();
        $mUsers ->insert(
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
        $serial = NULL;
        $id = NULL;
        $data=$this->getRequest()->getParams();
        $ksfunctions = new Application_Model_KSFunctions();
        if($ksfunctions -> is_IdValid($data['id'])){
            $id = $data['id'];                
        }
        else{
            //redirect to registration error
            $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Invalid activation id!'));
            return;
        } 
        $serial = $ksfunctions -> sanitize_Title($data['serial'] , 20);
        if($serial == null){
            $this->_redirector->gotoUrl('/index/index/error/' . urlencode('ERROR: Invalid serial!'));
            return;
        }
        
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
