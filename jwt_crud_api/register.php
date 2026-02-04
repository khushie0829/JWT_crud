<?php
define("INIT", true);
require_once __DIR__ . '/init.php';

$name = $data['name'] ?? '';
$passwordRaw = $data['password'] ?? '';

if (empty($name) || empty($passwordRaw)) {
    $res_message = "";
    $res_error = "Name and password are required"; 
    $res_data = [];
    $res_status = 400;
}

$password = password_hash($passwordRaw, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO users (name, password) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $password);

if ($stmt->execute()) {
    $res_message = "User registered successfully";
    $res_error = ""; 
    $res_data = [];
    $res_status = 200;  
} else {
    $res_message = "";
    $res_error = "Failed to register"; 
    $res_data = [];
    $res_status = 500;
}
return responseJSON($res_message ?? '', $res_error ?? '', [], $res_status);
