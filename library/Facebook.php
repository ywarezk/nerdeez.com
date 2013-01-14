<?php

/**
 * Facebook auth
 *
 * @author Yariv Katz
 * @copyright (c) 2013, Nerdeez
 * @version V1.2
 */
class Zend_Auth_Adapter_Facebook implements Zend_Auth_Adapter_Interface{
    private $token = null;
    private $user = null;
 
    public function __construct($token) {
        $this->token = $token;
    }
 
    public function getUser() {
        return $this->user;
    }
    
    public function authenticate() {
        if($this->token == null) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                            false, array('Token was not set'));
        }
 
        $graph_url = "https://graph.facebook.com/me?access_token=" . $this->token;
        $details = json_decode(file_get_contents($graph_url));
        $mUsers = new Application_Model_DbTable_Users();
        $rUser = $mUsers ->fetchRow($mUsers -> select() -> where('email = ?', $details ->email));
        if ($rUser == NULL){
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                            false, array('You are not registered'));
        }
        $this->user = $rUser;
        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,$user);
    }
}

?>
