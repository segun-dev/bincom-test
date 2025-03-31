<?php

$db_server = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "bincom_test";
$conn ="";

$conn = mysqli_connect($db_server, $db_user, $db_password, $db_name);
if(!$conn){
    echo " Not Connected". mysqli_connect_error($conn);
}

?>