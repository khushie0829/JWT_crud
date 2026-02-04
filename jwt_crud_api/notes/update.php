<?php
define("INIT", true);
$req_protected = true;
require_once(__DIR__ . '/../init.php');
require_once __DIR__ . '/../core/middleware.php';

$id = $data['id'] ?? null;

$title = $data['title'] ?? '';
$content = $data['content'] ?? '';


if ($id && $title && $content) {
    $sql = "UPDATE notes SET title='$title', content='$content' WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        $res_message = "Note updated successfully";
        $res_error = "";
        $res_status = 200;
    } else {
        $res_message = "";
        $res_error = "Failed to update note";
        $res_status = 400;
    }
} else {
    $res_message = "";
    $res_error = "Invalid or missing data";
    $res_status = 422;
}

responseJSON($res_message, $res_error, [], $res_status);
