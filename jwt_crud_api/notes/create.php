<?php
define("INIT", true);
$req_protected = true;
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../core/middleware.php';


$title = $data['title'] ?? null;
$content = $data['content'] ?? null;

if (!$title || !$content) {
    $res_message = "";
    $res_error = "Title and content are required"; 
    $res_data = [];
    $res_status = 400;  
}

$stmt = $conn->prepare("INSERT INTO notes (title, content) VALUES (?, ?)");
$stmt->bind_param("ss", $title, $content);
if($stmt->execute()){
    $res_message = "Note created successfully"; 
    $res_error = ""; 
    $res_data = []; 
    $res_status = 200;
} else {
    $res_error = "Failed to create note";
}
return responseJSON($res_message ?? '', $res_error ?? '', [], $res_status ?? '');
?>
