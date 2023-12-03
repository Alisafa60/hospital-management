<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = 'patient';

$query = $mysqli->prepare('INSERT INTO users(first_name, last_name, email, username, password, role) VALUES (?, ?, ?, ?, ?, ?)');
$query->bind_param('ssssss', $first_name, $last_name, $email, $username, $hashed_password, $role);
$query->execute();

$response = [];
$response["status"] = "true";

echo json_encode($response);
?>

