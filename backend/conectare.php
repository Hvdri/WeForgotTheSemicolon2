<?php
$db_name = "giveahand";
$mysql_username = "test";
$mysql_password = "test";
$server_name = "localhost";
// Create connection
$conn = new mysqli($server_name, $mysql_username, $mysql_password, $db_name);
mysqli_set_charset($conn, 'utf8');
// Check connection
if (mysqli_connect_error()) {
    die("Database connection failed: " . mysqli_connect_error());
}
