<?php
    session_start();
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $sql = "SELECT u.*, 
                   COALESCE(
                       (SELECT MAX(msg_id) 
                        FROM messages 
                        WHERE (incoming_msg_id = u.unique_id AND outgoing_msg_id = {$outgoing_id})
                           OR (outgoing_msg_id = u.unique_id AND incoming_msg_id = {$outgoing_id})
                           OR (room_id = u.unique_id)
                       ), 
                       0
                   ) as last_msg_id
            FROM (
                SELECT unique_id, fname, lname, img, status, 'user' as type FROM users WHERE NOT unique_id = {$outgoing_id}
                UNION ALL
                SELECT room_id as unique_id, room_name as fname, '' as lname, 'default.png' as img, 'Group' as status, 'group' as type 
                FROM chatrooms
                WHERE created_by = {$outgoing_id}
            ) u
            ORDER BY last_msg_id DESC, u.unique_id DESC";
    $query = mysqli_query($conn, $sql);
    $output = "";
    if(mysqli_num_rows($query) == 0){
        $output .= "No users are available to chat";
    }elseif(mysqli_num_rows($query) > 0){
        while($row = mysqli_fetch_assoc($query)){
            $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$row['unique_id']}
                    OR outgoing_msg_id = {$row['unique_id']} OR room_id = {$row['unique_id']})
                    ORDER BY msg_id DESC LIMIT 1";
            $query2 = mysqli_query($conn, $sql2);
            $row2 = mysqli_fetch_assoc($query2);
            (mysqli_num_rows($query2) > 0) ? $result = $row2['msg'] : $result ="No message available";
            (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;
            if(isset($row2['outgoing_msg_id'])){
                ($outgoing_id == $row2['outgoing_msg_id']) ? $you = "You: " : $you = "";
            }else{
                $you = "";
            }
            ($row['status'] == "Offline now") ? $offline = "offline" : $offline = "";
            ($outgoing_id == $row['unique_id']) ? $hid_me = "hide" : $hid_me = "";

            if($row['type'] == 'group'){
                $output .= '<a href="group_chat.php?room_id='. $row['unique_id'] .'">
                            <div class="content">
                            <img src="php/images/'. $row['img'] .'" alt="">
                            <div class="details">
                                <span>'. $row['fname'] .'</span>
                                <p>'. $you . $msg .'</p>
                            </div>
                            </div>
                            <div class="status-dot"><i class="fas fa-users"></i></div>
                        </a>';
            } else {
                $output .= '<a href="chat.php?user_id='. $row['unique_id'] .'">
                            <div class="content">
                            <img src="php/images/'. $row['img'] .'" alt="">
                            <div class="details">
                                <span>'. $row['fname']. " " . $row['lname'] .'</span>
                                <p>'. $you . $msg .'</p>
                            </div>
                            </div>
                            <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                        </a>';
            }
        }
    }
    echo $output;
?>
