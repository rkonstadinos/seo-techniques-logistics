<?php
    $servername = 'localhost';
    $username = 'root';
    $password = 'root';
    $dbname = 'logistics';
IF( isset($servername,$username,$password,$dbname) ) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    mysqli_set_charset($conn,"utf8");
} else {echo "error db"; die;}
?>