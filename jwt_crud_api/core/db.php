<?php

if (!defined("INIT")) {
    die("Unauthorized access!");
}

try {
    $conn = new mysqli($servername, $username, $password, $database, $port);
    if ($conn->connect_error) {
        writeLog("Database connection failed");
        die(" Server problem, please try again later " . $conn->connect_error);
    }
} catch (\Throwable $th) {
    //throw $th;
    writeLog("Database connection failed", "CRITICAL");
}

if (!$conn) {
    die(" Server Error");
}
