<?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        $is_group = isset($_POST['is_group']) ? $_POST['is_group'] : 'false';

        $file_path = '';
        $file_name = '';
        if(isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0){
            $allowed = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv', 'zip', 'rar', 'mp3', 'mp4', 'avi', 'mov');
            $filename = $_FILES['attachment']['name'];
            $file_name = $filename;
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(in_array(strtolower($ext), $allowed)){
                $new_name = time() . '_' . $filename;
                $path = 'uploads/' . $new_name;
                if(move_uploaded_file($_FILES['attachment']['tmp_name'], '../' . $path)){
                    $file_path = $path;
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to move uploaded file"]);
                    exit;
                }
            } else {
                echo json_encode(["status" => "error", "message" => "File type not allowed"]);
                exit;
            }
        }

        if(!empty($message) || !empty($file_path)){
            $current_time = date("Y-m-d H:i:s");
            if($is_group == 'true'){
                $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, file_path, file_name, dateTimeMsg, room_id) 
                                            VALUES (0, {$outgoing_id}, '{$message}', '{$file_path}', '{$file_name}', '{$current_time}', {$incoming_id})");
            } else {
                $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, file_path, file_name, dateTimeMsg) 
                                            VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', '{$file_path}', '{$file_name}', '{$current_time}')");
            }
            if($sql){
                echo json_encode(["status" => "success", "message" => "Message sent successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to send message: " . mysqli_error($conn)]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Empty message and no file attached"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not logged in"]);
    }
?>
