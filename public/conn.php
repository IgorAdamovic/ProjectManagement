<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

$conn = new mysqli("localhost", "root", "root", "rpo");
if ($conn->connect_error) {
    die("Connection Failed!" . $conn->connect_error);
}


