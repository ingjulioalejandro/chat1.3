<?php
session_start();
if(isset($_SESSION['unique_id'])){
    include_once "config.php";
    $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
    
    $output = "";

    $sql = "SELECT * FROM messages 
            LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id 
            WHERE room_id = {$room_id} ORDER BY msg_id";
    $query = mysqli_query($conn, $sql);

    if(mysqli_num_rows($query) > 0){
        while($row = mysqli_fetch_assoc($query)){
            if($row['outgoing_msg_id'] === $_SESSION['unique_id']){
                $output .= '<div class="chat outgoing">
                            <div class="details">
                                <p>'. $row['msg'] .'</p>';
                if (!empty($row['file_path'])) {
                    $output .= '<a href="php/uploads/'. $row['file_path'] .'" target="_blank">Ver archivo</a>';
                }
                $output .= '</div></div>';
            } else {
                $output .= '<div class="chat incoming">
                            <img src="php/images/'. $row['img'] .'" alt="">
                            <div class="details">
                                <p>'. $row['msg'] .'</p>';
                if (!empty($row['file_path'])) {
                    $output .= '<a href="php/uploads/'. $row['file_path'] .'" target="_blank">Ver archivo</a>';
                }
                $output .= '</div></div>';
            }
        }
    } else {
        $output .= '<div class="text">No hay mensajes disponibles.</div>';
    }

    echo $output;
} else {
    header("location: ../login.php");
}
