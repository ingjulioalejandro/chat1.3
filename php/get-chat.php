<?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $output = "";
        $sql = "SELECT m.*, u.img, u.fname, u.lname 
                FROM messages m
                LEFT JOIN users u ON u.unique_id = m.outgoing_msg_id
                WHERE (m.outgoing_msg_id = {$outgoing_id} AND m.incoming_msg_id = {$incoming_id})
                OR (m.outgoing_msg_id = {$incoming_id} AND m.incoming_msg_id = {$outgoing_id})
                OR (m.room_id = {$incoming_id})
                ORDER BY m.msg_id";
        $query = mysqli_query($conn, $sql);
        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){
                $time = date('h:i A', strtotime($row['sent_at']));
                if($row['outgoing_msg_id'] == 0 && $row['incoming_msg_id'] == 0){

                    $output .= '<div class="chat system-message">
                                <div class="details">
                                    <p>'. $row['msg'] .'</p>
                                    <div class="time">'. $time .'</div>
                                </div>
                                </div>';
                } else if($row['outgoing_msg_id'] === $outgoing_id){
                    $output .= '<div class="chat outgoing">
                                <div class="details">';
                    if($row['file_path']){
                        $file_name = basename($row['file_path']);
                        $output .= '<p><a href="'. $row['file_path'] .'" target="_blank">'. $file_name .'</a></p>';
                    }
                    if(!empty($row['msg'])){
                        $output .= '<p>'. $row['msg'] .'</p>';
                    }
                    $output .= '<div class="time">
                                    <span class="sender-name">You</span> '. $time .'
                                </div>
                                </div>
                                </div>';
                }else{
                    $sender_name = $row['fname'] . ' ' . $row['lname'];
                    $output .= '<div class="chat incoming">
                                <img src="php/images/'.$row['img'].'" alt="">
                                <div class="details">';
                    if($row['file_path']){
                        $file_name = basename($row['file_path']);
                        $output .= '<p><a href="'. $row['file_path'] .'" target="_blank">'. $file_name .'</a></p>';
                    }
                    if(!empty($row['msg'])){
                        $output .= '<p>'. $row['msg'] .'</p>';
                    }
                    $output .= '<div class="time">
                                    <span class="sender-name">'. $sender_name .'</span> '. $time .'
                                </div>
                                </div>
                                </div>';
                }
            }
        }else{
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        }
        echo $output;
    }else{
        header("location: ../login.php");
    }
?>
