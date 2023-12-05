<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");

$admin_username = $_POST['admin_username'];
$admin_password = $_POST['admin_password'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];

// Check if admin already exists
// Hash the admin password
$hashed_admin_password = password_hash($admin_password, PASSWORD_DEFAULT);

// Insert admin user into the database
$insert_query = $mysqli->prepare('INSERT INTO users(first_name, last_name, username, password, role, approved) VALUES (?, ?, ?, ?, "admin", 1)');
$insert_query->bind_param('ssss', $first_name, $last_name, $admin_username, $hashed_admin_password);
$insert_query->execute();

if ($insert_query->affected_rows > 0) {
    // Issue a JWT for the newly created admin
    $admin_user_id = $insert_query->insert_id;
    $token_payload = [
        "user_id" => $admin_user_id,
        "username" => $admin_username,
        "role" => "admin"
    ];

    $secret_key = "lazy_susan";
    $jwt_token = jwt_encode($token_payload, $secret_key);

    $response = ["status" => "true", "token" => $jwt_token];
} else {
    $response = ["status" => "false", "message" => "Failed to create admin"];
}


echo json_encode($response);

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


