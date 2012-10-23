<?php
/**
 * used for the cms
 */
require_once 'Plugins/AccessCheck.php';

/**
 * put site initialization here
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.0
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    /**
     * init the meta information of the web site
     */
    protected function _initMeta(){
        //get the view
        $this->bootstrap('view');
        $view = $this->getResource('view');
        
        //set the encoding
        $view->setEncoding('UTF-8');
        
        //setting the headmeta
        $view->headMeta()->appendHttpEquiv('Content-type' , 'text/html; charset=UTF-8')
                         ->appendName('description' , constant('Application_Model_KSFunctions::cMETADESCRIPTIONMAIN'));
    }
    
    /**
     * control managment system
     */
    protected function _initCMS(){
        $acl=new Application_Model_Acl();
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('Users'));
        $fc=Zend_Controller_Front::getInstance();
        $fc->registerPlugin(new Application_Plugin_AccessCheck($acl,$auth));
    }
    
    /**
     * check if the user has remember me coockies set and if so 
     * update the session if necessary
     */
    protected function _initRememberMe(){
        //get to the functions class
        $ksfunctions = new Application_Model_KSFunctions();
        
        //check if user has identity saved in the sessions
        $bIsIdentity = FALSE;
        $bIsIdentity = $ksfunctions -> isRegistered();
        
        //if the user has identity than return
        if ($bIsIdentity)return;
        
        //check if the user has cookies set
        $bIsCookies = FALSE;
        $bIsCookies = isset ($_COOKIE['email']) && isset ($_COOKIE['identifier']);
        
        //if the user dont have a remember me cookie than return
        if (!$bIsCookies)return;
        
        //grab the email identifier and token
        $sEmail = $sIdentifier = $sToken =  NULL;
        $sEmail = $ksfunctions -> sanitize_Title($_COOKIE['email'] , 100);
        $sIdentifier = $ksfunctions -> sanitize_Title($_COOKIE['identifier'] , 200);
        $sToken = $ksfunctions -> sanitize_Title($_COOKIE['token'] , 200);
        
        //check that the row matches the logincoockies row
        $bIsRowExist = FALSE;
        $mLogincookies = new Application_Model_DbTable_Logincookies();
        $rsLogincookies = $mLogincookies -> fetchAll($mLogincookies -> select()
                -> where ('email = ?' , $sEmail)
                -> where ('identifier = ?' , $sIdentifier)
                -> where ('token = ?' , $sToken));
        $bIsRowExist = $rsLogincookies -> count() > 0;
        $rLogin = $rsLogincookies -> getRow(0);
        
        //if the row doesnt exist check if the email and identifier exists if so security breach
        if (!$bIsCookies){
             $rsLogincookies = $mLogincookies -> fetchAll($mLogincookies -> select()
                -> where ('email = ?' , $sEmail)
                -> where ('identifier = ?' , $sIdentifier));
            if ($rsLogincookies -> count() > 0){
                //delete all the rows you found
                foreach ($rsLogincookies as $rLogincookie) {
                    $where = $mLogincookies->getAdapter()->quoteInto('id = ?', $rLogincookie['id']);
                    $mLogincookies->delete($where);
                }
                
                //redirect to main page with cookie theft suspicion
                $redirector = $this->_helper->getHelper('Redirector');
                $redirector->gotoUrl('/error/' . urlencode('suspected cookie theft please change your password!'));
                return;
            }
        }
        
        //got a triplet match then need to change token and update db
        $sNewtoken = $ksfunctions -> createSaltString2(200);
        $aLoginUpdate = array(
            'token'     => $sNewtoken
        );
        $where = $mLogincookies->getAdapter()->quoteInto('id = ?', $rLogin['id']);
        $mLogincookies->update($aLoginUpdate, $where);
        
        //update the cookie with the new token
        $sUrl = $ksfunctions ->sGetUrl();
        $inTwoMonths = 60 * 60 * 24 * 60 + time();
        setcookie('token', $sToken, $inTwoMonths,"/", "." . $sUrl);
        
        //grab the user row 
        $rUser = NULL;
        $mUsers = new Application_Model_DbTable_Users();
        $selSelectUsers = $mUsers -> select() -> where ('email = ?' , $sEmail);
        $rsUsers = $mUsers -> fetchAll($selSelectUsers);
        if ($rsUsers -> count() != 1) return;
        $rUser = $rsUsers -> getRow(0);
        
        //from the user row create the users object
        $oUser = NULL;
        $oUser = new stdClass();
        $oUser -> id = $rUser['id'];
        $oUser -> title = $rUser['title'];
        $oUser -> pass = $rUser['pass'];
        $oUser -> role = $rUser['role'];
        $oUser -> serial = $rUser['serial'];
        $oUser -> email = $rUser['email'];
        $oUser -> isActive = $rUser['isActive'];
        $oUser -> salt = $rUser['salt'];
        
        //write the object to auth
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('Users'));
        $auth->getStorage()->write($oUser);
        
        //the end
    }
    
    

}

