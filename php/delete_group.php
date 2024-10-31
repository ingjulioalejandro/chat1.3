<?php
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id']) && isset($_POST['group_id'])){
    $user_id = $_SESSION['unique_id'];
    $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);


    $check_creator = mysqli_query($conn, "SELECT * FROM chatrooms WHERE room_id = {$group_id} AND created_by = {$user_id}");
    if(mysqli_num_rows($check_creator) > 0){

        mysqli_query($conn, "DELETE FROM messages WHERE room_id = {$group_id}");
        

        mysqli_query($conn, "DELETE FROM group_members WHERE group_id = {$group_id}");
        

        $delete_group = mysqli_query($conn, "DELETE FROM chatrooms WHERE room_id = {$group_id}");
        
        if($delete_group){
            echo json_encode(["status" => "success", "message" => "Group deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete group: " . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "You are not authorized to delete this group"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
