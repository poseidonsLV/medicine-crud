<?php
$servername = "localhost:3307";
$username = "root";
$password = "poseidons";
$database = "medicine";

// Create connection
$dsn = "mysql:host=". $servername .";dbname=". $database;
$conn = new PDO($dsn, $username,$password);
$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
if ($conn) {
    return $conn;
} else {
    return 'Error!';
}
?>