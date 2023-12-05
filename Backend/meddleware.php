<?php

function decode_jwt($jwt_token, $secret_key) {
    $token_parts = explode(".", $jwt_token);
    $token_payload = json_decode(base64UrlDecode($token_parts[1]), true);

    // verify the signature
    $signature = base64UrlDecode($token_parts[2]);
    $expected_signature = hash_hmac("sha256", "$token_parts[0].$token_parts[1]", $secret_key, true);

    if (hash_equals($expected_signature, $signature)) {
        return $token_payload;
    } else {
        return false;
    }
}
function base64UrlDecode($data) {
    $base64 = strtr($data, '-_', '+/');
    $base64_padded = str_pad($base64, strlen($data) % 4, '=', STR_PAD_RIGHT);
    return base64_decode($base64_padded);
}
//authorization middleware
function authorize($required_role) {

    $secret_key = "lazy_susan";

    // JWT token from the authorization header
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
    list($jwt_token) = sscanf($auth_header, 'Bearer %s');

    if ($jwt_token) {
        $token_payload = decode_jwt($jwt_token, $secret_key);
        if ($token_payload && isset($token_payload['role']) && $token_payload['role'] === $required_role) {
            return true;
        }
    }

    // unauthorized access
    http_response_code(401);
    echo json_encode(["status" => "false", "message" => "Unauthorized access"]);
    exit();
}