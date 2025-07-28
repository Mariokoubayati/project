<?php

//global variables

$_GLOBALS["id"]=0;

$servername = "localhost";
$username = "root";
$password= "root";
$dbname = "management";

try{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
}

// Function to add, update and delete a department
