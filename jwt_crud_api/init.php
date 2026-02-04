<?php
require_once __DIR__ . '/core/preload.php'; 
header('Content-Type: application/json; charset=utf-8');
$debug = true;
if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
}
require_once "core/config.php";
require_once "core/db.php";
require_once "core/functions.php";

if (!defined("INIT")) die("Unauthorized access....!");
$data = getJsonInput();