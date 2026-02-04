<?php
require_once __DIR__ . '/../init.php';

$res_message = "";
$res_error = "";
$res_status = 200;
$res_data = [];

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';


if (!$authHeader) {
    $res_error = "Authorization header missing";
    $res_status = 401;
} elseif (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $res_error = "Invalid Authorization header";
    $res_status = 401;
} else {
    $jwt = $matches[1];
    $payload = verifyJWT($jwt, $_ENV['JWT_KEY']); 

    if (!$payload) {
        $res_error = "Invalid token signature";
        $res_status = 401;
    } elseif (!isset($payload['exp']) || time() > $payload['exp']) {
        $res_error = "Token has expired";
        $res_status = 401;
    } else {
        $stmt = $conn->prepare("SELECT * FROM user_tokens WHERE token = ? AND revoked = 0");
        $stmt->bind_param("s", $jwt);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $res_error = "Token revoked or invalid";
            $res_status = 401;
        } else {
            $res_message = "Token is valid";
        }
    }
}
return responseJSON($res_message, $res_error, $res_data, $res_status);

