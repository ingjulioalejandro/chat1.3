<?php
session_start();
include_once "config.php";

if(isset($_FILES['file']['name'])){
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $allowed_ext = array("jpg", "jpeg", "png", "gif", "pdf", "docx");
    
    if(in_array($file_ext, $allowed_ext)){
        $new_file_name = time() . "_" . $file_name;
        $upload_dir = "uploads/" . $new_file_name;
        
        if(move_uploaded_file($file_tmp, $upload_dir)){

            $outgoing_id = $_SESSION['unique_id'];
            $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
            $sql = "INSERT INTO messages (outgoing_msg_id, room_id, msg, sent_at, file_name) 
                    VALUES ({$outgoing_id}, {$room_id}, '', NOW(), '{$new_file_name}')";
            mysqli_query($conn, $sql);
            echo "File uploaded successfully.";
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "File type not allowed.";
    }
}
?>
