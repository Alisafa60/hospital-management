<?php

header('Access-Controll-Allow-Origin:*');
include('db_connection.php');
$username = $_POST['username'];
$password = $_POST['password'];

$query = $mysqli->prepare('SELECT user_id, username, password, role FROM users WHERE username=?');
$query->bind_param('s', $username);
$query->execute();
$query->store_result();

if ($query->num_rows > 0) {
    
    $query->bind_result($user_id, $username, $hashed_password, $role);
    $query->fetch();

    
    if (password_verify($password, $hashed_password)) {
        
        $token_payload = [
            "user_id" => $user_id,
            "username" => $username,
            "role" => $role
           
        ];

        
        $secret_key = "your_secret_key";
        $jwt_token = jwt_encode($token_payload, $secret_key);
        $response = ["status" => "true", "token" => $jwt_token];
        
    } else {
        
        $response = ["status" => "false", "message" => "Incorrect credentials"];
    }
} else {
    
    $response = ["status" => "false", "message" => "Incorrect credentials"];
}


echo json_encode($response);

// Function to encode a JWT token
function jwt_encode($payload, $secret_key) {
    // Base64Url encode the JWT header and payload
    $base64UrlHeader = base64UrlEncode(json_encode(["alg" => "HS256", "typ" => "JWT"]));
    $base64UrlPayload = base64UrlEncode(json_encode($payload));

    // Creating the signature using HMAC-SHA256
    $signature = hash_hmac("sha256", "$base64UrlHeader.$base64UrlPayload", $secret_key, true);

    // Base64Url encode the signature
    $base64UrlSignature = base64UrlEncode($signature);

    // Concatenate the base64Url-encoded header, payload, and signature to form the JWT token
    return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
}

// Function to perform Base64Url encoding
function base64UrlEncode($data) {
    $base64 = base64_encode($data);
    $base64Url = strtr($base64, '+/', '-_');
    return rtrim($base64Url, '=');
}