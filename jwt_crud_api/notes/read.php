<?php
define("INIT", true);
$req_protected = true;
require_once(__DIR__ . '/../init.php');
require_once __DIR__ . '/../core/middleware.php';


$sql = "SELECT * FROM notes";
$result = mysqli_query($conn, $sql);


$notes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notes[] = $row;
}

echo json_encode($notes);
?>
