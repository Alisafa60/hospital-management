<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('patient');

$user_id = $_POST['user_id'];
//fetch from user user_id of role patient, and request emergency room
$user_query = $mysqli->prepare('SELECT first_name, last_name FROM users WHERE user_id = ? AND role = "patient"');
$user_query->bind_param('i', $user_id);
$user_query->execute();
$user_query->bind_result($first_name, $last_name);

if ($user_query->fetch()) {
    $user_query->close();  
    $insert_request_query = $mysqli->prepare('INSERT INTO emergencyroomrequest(user_id, first_name, last_name, status) VALUES (?, ?, ?, "pending")');
    $insert_request_query->bind_param('iss', $user_id, $first_name, $last_name);

    if ($insert_request_query->execute()) {
        $request_id = $mysqli->insert_id;

        echo json_encode(["status" => "true", "message" => "Emergency request generated successfully", "request_id" => $request_id]);
    } else {
        echo json_encode(["status" => "false", "message" => "Failed to generate emergency request: " . $mysqli->error]);
    }

    $insert_request_query->close();
} else {
    echo json_encode(["status" => "false", "message" => "User not found"]);
}

$mysqli->close();
?>
