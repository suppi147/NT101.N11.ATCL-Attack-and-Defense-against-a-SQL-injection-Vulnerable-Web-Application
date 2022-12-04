<?php
$server="localhost";
$username="root";
$password="uitcisco";
$database="Catinger";

$connect=new mysqli($server,$username,$password,$database);

if($connect->connect_error)
   echo "Connection falied: ".$connect->connect_error;
else
   echo "Connection is created successfully";
?>
