<?php
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id']) && isset($_POST['user_id']) && isset($_POST['group_id'])){
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
    

    $user_check = mysqli_query($conn, "SELECT * FROM users WHERE user_id = {$user_id}");
    if(mysqli_num_rows($user_check) == 0){
        echo "User not found";
        exit();
    }
    

    $member_check = mysqli_query($conn, "SELECT * FROM group_members WHERE group_id = {$group_id} AND user_id = {$user_id}");
    if(mysqli_num_rows($member_check) > 0){
        echo "User is already a member of this group";
        exit();
    }
    

    $group_check = mysqli_query($conn, "SELECT * FROM chatrooms WHERE room_id = {$group_id}");
    $group = mysqli_fetch_assoc($group_check);
    if($group['created_by'] != $_SESSION['unique_id'] && $group['anyone_can_add'] == 0){
        echo "You don't have permission to add members to this group";
        exit();
    }
    

    $sql = mysqli_query($conn, "INSERT INTO group_members (group_id, user_id) VALUES ({$group_id}, {$user_id})");
    if($sql){
        echo "success";
    } else {
        echo "Error adding member to group: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request";
}
?>
