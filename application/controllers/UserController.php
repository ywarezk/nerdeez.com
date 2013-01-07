<?php

/**
 * required in all my controllers
 */
require_once APPLICATION_PATH . '/controllers/Nerdeez_Controller_Action.php';

/**
 * controller for the user account
 * @author Yariv Katz
 * @copyright Nerdeez.com
 * @version 1.1
 */
class UserController extends Nerdeez_Controller_Action{
    
    /**
     * when the user wants to update his nickname
     */
    public function updatenicknameAction(){
        $this->update();
    }
    
    private function update(){
        //disable the view
        $this->disableView();
        
        //create the users model
        $mUsers = new Application_Model_DbTable_Users();
        
        //get the columns of the table
        $aCols = $mUsers ->getModelColumns();
        
        //create the array to update 
        $aData = array();
        foreach ($aCols as $sCol) {
            if(isset($this->_aData[$sCol])){
                $aData[$sCol] = $this->_aData[$sCol];
            }
        }
        
        //update the user with the right values
        $mUsers -> update($aData , 'id = ' . $this->getUserInfo() -> id);
        
        //update the storage
        $this->updateUserInfo();
        
        //return success
        $this->ajaxReturnSuccess();
    }
    
}

?>
