<?php
$servername = "young90health.chxw3sngrvwf.us-east-1.rds.amazonaws.com";
$username = "young90health";
$password = "young0291";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";
?>
