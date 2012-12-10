<?php
/**
 * this script will trnsfer db from production to development
 */



$result = 0;
$output = array();
$result1 = passthru("sshpass -p 'KhruiNhe$%Vhpv' scp ywarezk2824@198.61.208.58:/upload/backup.sql /upload/backup.sql");

if (!file_exists('/upload/backup.sql')){
    $result = 2;
}

?>
