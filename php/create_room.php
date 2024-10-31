<?php
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id'])){
    $room_name = mysqli_real_escape_string($conn, $_POST['room_name']);
    
    if(!empty($room_name)){

        $sql = "INSERT INTO chatrooms (room_name, created_at) VALUES ('{$room_name}', NOW())";
        if(mysqli_query($conn, $sql)){
            echo "Group created successfully";
        } else {
            echo "Error creating group. Please try again.";
        }
    } else {
        echo "Please enter a group name.";
    }
}
?>
