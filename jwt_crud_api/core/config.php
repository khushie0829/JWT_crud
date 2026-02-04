<?php
if (!defined("INIT")) {
    die("Unauthorized access!");
}

require_once(__DIR__ . '/../init.php');

$port       = (int) $_ENV['DB_PORT'];
$servername = $_ENV['DB_HOST'];
$username   = $_ENV['DB_USER'];
$password   = $_ENV['DB_PASS'];
$database   = $_ENV['DB_NAME'];

