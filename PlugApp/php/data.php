<?php
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

    if($row['chat_type'] == 'user'){
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
    } else {
        $output .= '<a href="group_chat.php?room_id='. $row['unique_id'] .'">
                    <div class="content">
                    <img src="php/images/group_icon.png" alt="">
                    <div class="details">
                        <span>'. $row['fname'] .'</span>
                        <p>'. $msg .'</p>
                    </div>
                    </div>
                    <div class="status-dot"><i class="fas fa-users"></i></div>
                </a>';
    }
}


$sql3 = "SELECT cr.room_id, cr.room_name, cr.created_by, m.msg, m.sent_at
         FROM chatrooms cr
         LEFT JOIN (
             SELECT room_id, msg, sent_at,
                    ROW_NUMBER() OVER (PARTITION BY room_id ORDER BY sent_at DESC) as rn
             FROM messages
             WHERE room_id IS NOT NULL
         ) m ON cr.room_id = m.room_id AND m.rn = 1
         ORDER BY m.sent_at DESC NULLS LAST";
$query3 = mysqli_query($conn, $sql3);

while($row3 = mysqli_fetch_assoc($query3)){
    $lastMsg = $row3['msg'] ? $row3['msg'] : "No message available";
    (strlen($lastMsg) > 28) ? $msg = substr($lastMsg, 0, 28) . '...' : $msg = $lastMsg;

    $output .= '<a href="group_chat.php?room_id='. $row3['room_id'] .'">
                <div class="content">
                <img src="php/images/group_icon.png" alt="">
                <div class="details">
                    <span>'. $row3['room_name'] .'</span>
                    <p>'. $msg .'</p>
                </div>
                </div>
                <div class="status-dot"><i class="fas fa-users"></i></div>
            </a>';
}
?>
