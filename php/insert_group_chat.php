<?php
session_start();
if(isset($_SESSION['unique_id'])){
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    

    if(isset($_FILES['attachment']) && $_FILES['attachment']['name'] != ""){
        $file_name = time() . "_" . $_FILES['attachment']['name'];
        $file_tmp_name = $_FILES['attachment']['tmp_name'];
        $file_path = "uploads/" . $file_name;
        move_uploaded_file($file_tmp_name, $file_path);


        $sql = mysqli_query($conn, "INSERT INTO messages (room_id, outgoing_msg_id, msg, file_path) 
                                    VALUES ({$room_id}, {$outgoing_id}, '', '{$file_path}')");
    } elseif(!empty($message)) {

        $sql = mysqli_query($conn, "INSERT INTO messages (room_id, outgoing_msg_id, msg) 
                                    VALUES ({$room_id}, {$outgoing_id}, '{$message}')");
    }
} else {
    header("location: ../login.php");
}
?>
