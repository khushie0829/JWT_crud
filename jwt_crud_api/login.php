<?php
define("INIT", true);
require_once __DIR__ . '/init.php';

// Initialize response variables
$res_message = "";
$res_error = "";
$res_data = [];
$res_status = 200;

// Get input data
$name = $data['name'] ?? '';
$password = $data['password'] ?? '';

if (empty($name) || empty($password)) {
    $res_error = "Name and Password are required";
    $res_status = 400;
} else {
    // Fetch user from DB
    $sql = "SELECT * FROM users WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $res_error = "User not found";
        $res_status = 404;
    } else {
        $user = $result->fetch_assoc();

        // Verify password
        if (!password_verify($password, $user['password'])) {
            $res_error = "Invalid password";
            $res_status = 400;
        } else {
            // Check for existing valid token
            $sqlToken = "SELECT * FROM user_tokens 
                         WHERE user_id = ? AND revoked = 0 AND expires_at > NOW() 
                         ORDER BY id DESC LIMIT 1";
            $stmtToken = $conn->prepare($sqlToken);
            $stmtToken->bind_param("i", $user['id']);
            $stmtToken->execute();
            $tokenResult = $stmtToken->get_result();

            if ($tokenResult->num_rows > 0) {
                $tokenRow = $tokenResult->fetch_assoc();
                $jwt = $tokenRow['token'];
            } else {
                // Generate new JWT
                $payload = [
                    "name" => $name,
                    "iat" => time(),
                    "exp" => time() + 3600 // 1 hour 
                ];
                $jwt = generateJWT($payload, $_ENV['JWT_KEY']);


                $issued_at = date("Y-m-d H:i:s", $payload['iat']);
                $expires_at = date("Y-m-d H:i:s", $payload['exp']);

                // Check if token exists in table
                $sqlCheck = "SELECT id FROM user_tokens WHERE user_id = ?";
                $stmtCheck = $conn->prepare($sqlCheck);
                $stmtCheck->bind_param("i", $user['id']);
                $stmtCheck->execute();
                $checkResult = $stmtCheck->get_result();

                if ($checkResult->num_rows > 0) {
                    $sqlUpdate = "UPDATE user_tokens 
                                  SET token = ?, issued_at = ?, expires_at = ?, revoked = 0 
                                  WHERE user_id = ?";
                    $stmtUpdate = $conn->prepare($sqlUpdate);
                    $stmtUpdate->bind_param("sssi", $jwt, $issued_at, $expires_at, $user['id']);
                    $stmtUpdate->execute();
                } else {
                    $sqlInsert = "INSERT INTO user_tokens (user_id, token, issued_at, expires_at, revoked) 
                                  VALUES (?, ?, ?, ?, 0)";
                    $stmtInsert = $conn->prepare($sqlInsert);
                    $stmtInsert->bind_param("isss", $user['id'], $jwt, $issued_at, $expires_at);
                    $stmtInsert->execute();
                }
            }
            $res_message = "Token is generated successfully";
            $res_data = ["token" => $jwt];
            $res_status = 200;
        }
    }
}

return responseJSON($res_message, $res_error, $res_data, $res_status);
