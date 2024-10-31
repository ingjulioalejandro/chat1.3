<?php
    session_start();
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];

    // Modificar la consulta para mostrar solo los últimos mensajes relevantes
    $sql = "SELECT 
                u.unique_id, 
                u.fname, 
                u.lname, 
                u.img, 
                u.status,
                (
                    SELECT msg 
                    FROM messages 
                    WHERE (incoming_msg_id = u.unique_id AND outgoing_msg_id = {$outgoing_id})
                       OR (outgoing_msg_id = u.unique_id AND incoming_msg_id = {$outgoing_id})
                    ORDER BY msg_id DESC 
                    LIMIT 1
                ) as last_msg,
                'user' as chat_type
            FROM users u 
            WHERE u.unique_id != {$outgoing_id}

            UNION ALL

            SELECT 
                cr.room_id as unique_id,
                cr.room_name as fname,
                '' as lname,
                'group_icon.png' as img,
                'Group' as status,
                (
                    SELECT msg 
                    FROM messages 
                    WHERE room_id = cr.room_id 
                    ORDER BY msg_id DESC 
                    LIMIT 1
                ) as last_msg,
                'group' as chat_type
            FROM chatrooms cr
            INNER JOIN group_members gm ON cr.room_id = gm.group_id
            WHERE gm.user_id = {$outgoing_id}

            ORDER BY last_msg DESC NULLS LAST";

    $query = mysqli_query($conn, $sql);
    $output = "";
    
    if(mysqli_num_rows($query) == 0){
        $output .= "No users are available to chat";
    }elseif(mysqli_num_rows($query) > 0){
        while($row = mysqli_fetch_assoc($query)){
            // Procesar el mensaje para la vista previa
            $msg = $row['last_msg'] ?? "No message available";
            $msg = strlen($msg) > 28 ? substr($msg, 0, 28) . '...' : $msg;
            
            // Determinar si el usuario está offline
            $offline = $row['status'] == "Offline now" ? "offline" : "";
            
            // Construir la salida HTML
            if($row['chat_type'] == 'group'){
                $output .= '<a href="group_chat.php?room_id='. $row['unique_id'] .'">
                            <div class="content">
                                <img src="php/images/group_icon.png" alt="">
                                <div class="details">
                                    <span>'. htmlspecialchars($row['fname']) .'</span>
                                    <p>'. htmlspecialchars($msg) .'</p>
                                </div>
                            </div>
                            <div class="status-dot"><i class="fas fa-circle"></i></div>
                        </a>';
            } else {
                $output .= '<a href="chat.php?user_id='. $row['unique_id'] .'">
                            <div class="content">
                                <img src="php/images/'. htmlspecialchars($row['img']) .'" alt="">
                                <div class="details">
                                    <span>'. htmlspecialchars($row['fname'] . " " . $row['lname']) .'</span>
                                    <p>'. htmlspecialchars($msg) .'</p>
                                </div>
                            </div>
                            <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                        </a>';
            }
        }
    }
    echo $output;
?>
