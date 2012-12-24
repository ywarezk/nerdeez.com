<?php
/**
 * this script when run will create a backup to the nerdeez database
 * on s3
 * 
 */

/**
 * nerdeez s3 wrapper
 */
require_once APPLICATION_PATH . '/models/Nerdeez_Service_Amazon_S3.php';

$result = 0;
$output = array();
$result1 = exec('mysqldump -u root -p7422S8h2A0b3A6082 nerdeez > ~/backup.sql', $output);
if (count($output) != 0 || $result1 != ''){
    $result = 1;
}
if (!file_exists('/upload/backup.sql')){
    $result = 2;
}


?>


