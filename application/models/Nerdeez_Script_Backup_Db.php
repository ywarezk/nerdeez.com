<?php
/**
 * this script when run will create a backup to the nerdeez database
 * on s3
 * 
 */

//define APPLICATION_PATH if necesary
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

/**
 * nerdeez s3 wrapper
 */
require_once APPLICATION_PATH . '/models/Nerdeez_Service_Amazon_S3.php';

//dump database to hd
$result = 0;
$output = array();
$result1 = exec('mysqldump -u root -p7422S8h2A0b3A6082 nerdeez > ~/backup.sql', $output);
if (count($output) != 0 || $result1 != ''){
    $result = 1;
}
if (!file_exists('~/backup.sql')){
    $result = 2;
}

//get the file and put it in s3
$s3 = new Nerdeez_Service_Amazon_S3();
$s3->createBucket("nerdeez");
$s3->putObject( 'nerdeez/backup.sql', 
        file_get_contents('~/backup.sql'),
        array(Nerdeez_Service_Amazon_S3::S3_ACL_HEADER =>
        Nerdeez_Service_Amazon_S3::S3_ACL_PRIVATE));




?>


