<?php
error_reporting(0);

$conn = new mysqli("localhost", "root", "", "db_skripsi");

// Check connection
if ($conn -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
}
