<?php
$servername = "localhost";
$username = "root";       
$password = "root";      
$dbname = "comp1044_database";

//reate connection
$conn = new mysqli($servername, $username, $password, $dbname);

//Check connection 
if ($conn->connect_error) {
    die("Invalid connection: " . $conn->connect_error);
} 

?>