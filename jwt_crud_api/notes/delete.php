<?php
define("INIT", true);
$req_protected = true;
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../core/middleware.php';

$id = isset($data['id']) ? intval($data['id']) : 0;

if ($id <= 0) {
    $res_error = "Invalid note ID";
    $res_status = 422;
    responseJSON($res_message, $res_error, [], $res_status);
    exit;
}

$sql = "DELETE FROM notes WHERE id = $id";
if (mysqli_query($conn, $sql)) {
    if (mysqli_affected_rows($conn) > 0) {
        $res_message = "Note deleted successfully";
        $res_error = "";
        $res_status = 200;
    } else {
        $res_message = "";
        $res_error = "Note not found or already deleted";
        $res_status = 404;
    }
} else {
    $res_message = "";
    $res_error = "Failed to delete note";
    $res_status = 500;
}

responseJSON($res_message, $res_error, [], $res_status);
exit;
