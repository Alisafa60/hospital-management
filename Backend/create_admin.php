<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");

$admin_username = $_POST['admin_username'];
$admin_password = $_POST['admin_password'];

// check if admin already exists
$check_query = $mysqli->prepare('SELECT user_id FROM users WHERE username=? AND role="admin"');
$check_query->bind_param('s', $admin_username);
$check_query->execute();
$check_query->store_result();

if ($check_query->num_rows > 0) {
    $response = ["status" => "false", "message" => "Admin already exists"];
} else {
    $hashed_admin_password = password_hash($admin_password, PASSWORD_DEFAULT);

    $insert_user_query = $mysqli->prepare('INSERT INTO users(username, password, role) VALUES (?, ?, "admin")');
    $insert_user_query->bind_param('ss', $admin_username, $hashed_admin_password);
    $insert_user_query->execute();

    if ($insert_user_query->affected_rows > 0) {
        $admin_user_id = $insert_user_query->insert_id;

        $insert_admin_query = $mysqli->prepare('INSERT INTO admins(user_id, first_name, last_name) VALUES (?, ?, ?)');
        $insert_admin_query->bind_param('iss', $admin_user_id, $first_name, $last_name);
        
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];

        $insert_admin_query->execute();

        if ($insert_admin_query->affected_rows > 0) {
            $token_payload = [
                "user_id" => $admin_user_id,
                "username" => $admin_username,
                "role" => "admin"
            ];

            $secret_key = "lazy_susan";
            $jwt_token = jwt_encode($token_payload, $secret_key);

            $response = ["status" => "true", "token" => $jwt_token];
        } else {
            $response = ["status" => "false", "message" => "Failed to create admin details"];
        }

        $insert_admin_query->close();
    } else {
        $response = ["status" => "false", "message" => "Failed to create admin"];
    }

    $insert_user_query->close();
}

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

echo json_encode($response);


