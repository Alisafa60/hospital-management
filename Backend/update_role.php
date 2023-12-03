<?php

include("meddleware.php");
include("db_connection.php");

authorize('admin');

$user_id = $_POST['user_id'];
$new_role = $_POST['new_role'];

// user role update in the database
$query = $mysqli->prepare('UPDATE users SET role = ? WHERE user_id = ?');
$query->bind_param('si', $new_role, $user_id);
$query->execute();

if ($query->affected_rows > 0) {
    echo json_encode(["status" => "true", "message" => "User role updated successfully"]);
} else {
    error_log('Failed to update user role: ' . $mysqli->error);
    echo json_encode(["status" => "false", "message" => "Failed to update user role"]);
}
?>
