    <?php
    if (!defined("INIT")) die("Unauthorized access....!");
    function sanitize_input($data)
    {
        global $conn;
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return mysqli_real_escape_string($conn, $data);
    }

    function getJsonInput()
    {
        return json_decode(file_get_contents("php://input"), true);
    }


    function generateJWT($payload, $secret_key)
    {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $payload = json_encode($payload);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    function verifyJWT($jwt, $secret_key)
    {
        $tokenParts = explode('.', $jwt);
        if (count($tokenParts) !== 3) return false;

        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $tokenParts;

        $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $signatureEncoded));
        $expectedSignature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, $secret_key, true);

        if (!hash_equals($expectedSignature, $signature)) return false;

        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payloadEncoded)), true);
        return $payload;
    }

    function responseJSON($message = "", $error = "", $data = [], $status = 200)
    {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => $status,
            "error" => $error,
            "message" => $message,
            "data" => $data
        ]);
        exit;
    }
    ?>
