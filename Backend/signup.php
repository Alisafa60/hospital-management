<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];
$role = "patient";
$approved = 0;

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$token_payload = [
    "username" => $username,
    "role" => $role,
];
$secret_key = "lazy_susan";
$jwt_token = jwt_encode($token_payload, $secret_key);


$query = $mysqli->prepare('INSERT INTO users(first_name, last_name, email, username, password, role, approved) VALUES (?, ?, ?, ?, ?, ?,?)');
$query->bind_param('ssssssi', $first_name, $last_name, $email, $username, $hashed_password, $role, $approved);
$query->execute();

$response = [];
$response["status"] = "true";
$response["token"] = $jwt_token;

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
?>

