<?php
session_start();
if(isset($_SESSION['unique_id'])){
    include_once "config.php";

    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $outgoing_id = $_SESSION['unique_id'];


    if(!empty($message) || isset($_FILES['attachment'])){
        

        if(isset($_FILES['attachment']) && $_FILES['attachment']['size'] > 0){
            $file_name = time() . "_" . $_FILES['attachment']['name'];
            $file_tmp = $_FILES['attachment']['tmp_name'];
            $file_destination = "uploads/" . $file_name; 
            

            if(move_uploaded_file($file_tmp, $file_destination)){

                $sql = "INSERT INTO messages (room_id, outgoing_msg_id, msg, file_path) 
                        VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', '{$file_name}')";
            }
        } else {

            $sql = "INSERT INTO messages (room_id, outgoing_msg_id, msg) 
                    VALUES ({$incoming_id}, {$outgoing_id}, '{$message}')";
        }

        mysqli_query($conn, $sql);
    }
} else {
    header("location: ../login.php");
}
