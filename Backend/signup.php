<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];
$approved = 0;

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$token_payload = [
    "user_id" => $user_id,
    "username" => $username,
    "role" => $role,
    "approved" => $approved
];
$secret_key = "lazy_susan";
$jwt_token = jwt_encode($token_payload, $secret_key);


$query = $mysqli->prepare('INSERT INTO users(first_name, last_name, email, username, password, role, approved) VALUES (?, ?, ?, ?, ?, ?)');
$query->bind_param('ssssssi', $first_name, $last_name, $email, $username, $hashed_password, $role, $approved);
$query->execute();

$response = [];
$response["token"] = $jwt_token;
$response["status"] = "true";

echo json_encode($response);
?>

