<?php
session_start();
include_once "config.php";

$outgoing_id = $_SESSION['unique_id'];


$sql = "SELECT users.unique_id AS id, users.fname, users.lname, users.img, users.status 
        FROM users 
        WHERE NOT unique_id = {$outgoing_id}";


$sql_groups = "SELECT chatrooms.room_id AS id, chatrooms.room_name AS fname, '' AS lname, '' AS img, 'group' AS status 
               FROM chatrooms";


$sql_final = "({$sql}) UNION ({$sql_groups})";
$query = mysqli_query($conn, $sql_final);

$output = "";

if(mysqli_num_rows($query) == 0){
    $output .= "No users or groups are available";
} elseif(mysqli_num_rows($query) > 0) {
    while($row = mysqli_fetch_assoc($query)) {

        if ($row['status'] == 'group') {
            $output .= '<a href="group_chat.php?room_id='. $row['id'] .'">
                            <div class="content">
                                <img src="php/images/group-icon.png" alt="Group Icon">
                                <div class="details">
                                    <span>'. htmlspecialchars($row['fname']) .'</span>
                                    <p>Group chat</p>
                                </div>
                            </div>
                            <div class="status-dot"><i class="fas fa-circle"></i></div>
                        </a>';
        } else {

            $output .= '<a href="chat.php?user_id='. $row['id'] .'"> 
                            <div class="content">
                                <img src="php/images/'. htmlspecialchars($row['img']) .'" alt="User Image">
                                <div class="details">
                                    <span>'. htmlspecialchars($row['fname']). " " . htmlspecialchars($row['lname']) .'</span>
                                    <p>'. htmlspecialchars($row['status']) .'</p>
                                </div>
                            </div>
                            <div class="status-dot '. ($row['status'] == "Active now" ? "online" : "offline") .'"><i class="fas fa-circle"></i></div>
                        </a>';
        }
    }
}

echo $output;
?>
