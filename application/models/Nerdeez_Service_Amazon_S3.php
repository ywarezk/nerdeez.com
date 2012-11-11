<?php

/**
 * the s3 zend wrapper
 */
require_once 'Zend/Service/Amazon/S3.php';

/**
 * nerdeez S3 interface
 *
 * @author Yariv
 * @copyright Nerdeez.com Ltd.
 */
class Nerdeez_Service_Amazon_S3 extends Zend_Service_Amazon_S3{
    
    /**
     * amazon s3 key
     * @var String 
     */
    private $_sAwsKey = 'AKIAIVIUYDC6HTRM5VHQ';
    
    /**
     * amazon s3 secret key
     * @var String 
     */
    private $_sAwsSecretKey = 'YIK/IsFkQ4EU/Yno/cRDcoKkBsRjBur2Hgl8P7kx';
    
    /**
     *override the parent construct with the correct cradentials 
     */
    public function __construct() {
        parent::__construct($this -> _sAwsKey, $this -> _sAwsSecretKey);
    }
    
}

?>
