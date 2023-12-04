<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('admin'); 

$user_id = $_POST['user_id'];
$new_role = $_POST['new_role'];

// Get user details
$query = $mysqli->prepare('SELECT * FROM users WHERE user_id = ? AND approved = 0');
$query->bind_param('i', $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // update user role (either change or keep) and approval status in the users table
    $update_query = $mysqli->prepare('UPDATE users SET role = ?, approved = 1 WHERE user_id = ?');
    $update_query->bind_param('si', $new_role, $user_id);
    $update_query->execute();

    if ($update_query->affected_rows > 0) {
        // insert user details into its specific table
        if ($new_role === 'doctor') {
            $insert_query = $mysqli->prepare('INSERT INTO doctors(user_id, email, first_name, last_name) VALUES (?, ?, ?, ?)');
            $insert_query->bind_param('isss', $user_id, $user['email'], $user['first_name'], $user['last_name']);
            $insert_query->execute();
        } elseif ($new_role === 'patient') {
            $insert_query = $mysqli->prepare('INSERT INTO patients(user_id, email, first_name, last_name) VALUES (?, ?, ?, ?)');
            $insert_query->bind_param('isss', $user_id, $user['email'], $user['first_name'], $user['last_name']);
            $insert_query->execute();
        }

        if ($insert_query->affected_rows > 0) {
            echo json_encode(["status" => "true", "message" => "User approved and details inserted into the appropriate table"]);
        } else {
            echo json_encode(["status" => "false", "message" => "Failed to insert user details into the appropriate table"]);
        }
    } else {
        echo json_encode(["status" => "false", "message" => "Failed to update user role"]);
    }
} else {
    echo json_encode(["status" => "false", "message" => "User not found or already approved"]);
}
