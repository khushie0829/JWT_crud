<?php
// Path to .env file
define('BASE_PATH', __DIR__ . '/../../');
$envFile = __DIR__ . '/../../.env'; 
$logsPath = BASE_PATH . '/logs';

// Check if .env exists
if (!file_exists($envFile)) {
    die(" .env file not found at: $envFile");
}

// Parse the .env file
$env = parse_ini_file($envFile, false, INI_SCANNER_RAW);

// Check parsing result
if ($env === false) {
    die(" Failed to parse .env file");
}

// Save into $_ENV for global usage
foreach ($env as $key => $value) {
    $_ENV[$key] = $value;
}

// for log file... 
function writeLog($message, $type = "INFO", $logDir = null) {
    $logDir = $logDir ?? BASE_PATH . '/logs';

    // Ensure logs directory exists
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }

    // Log file path
    $logFile = $logDir . "/log_" . date("Y-m-d") . ".log";

    // Timestamp
    $timestamp = date("Y-m-d H:i:s");

    // Format message
    $logMessage = "$type [$timestamp] $message" . PHP_EOL;

    // Save to daily log file
    if (!@file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX)) {
        // Fallback if cannot write log
        error_log("Unable to write log: $logFile");
    }
}