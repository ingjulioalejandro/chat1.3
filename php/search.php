<?php
    session_start();
    include_once "config.php";

    $outgoing_id = $_SESSION['unique_id'];
    $searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);

    $sql = "SELECT * FROM users WHERE NOT unique_id = {$outgoing_id} AND (fname LIKE '%{$searchTerm}%' OR lname LIKE '%{$searchTerm}%') ";
    $output = "";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0){
        include_once "data.php";
    }else{
        $output .= 'No user found related to your search term';
    }


    $sql_groups = "SELECT c.* FROM chatrooms c
                   INNER JOIN group_members gm ON c.room_id = gm.group_id
                   WHERE gm.user_id = (SELECT user_id FROM users WHERE unique_id = {$outgoing_id})
                   AND c.room_name LIKE '%{$searchTerm}%'
                   ORDER BY c.room_id DESC";
    $query_groups = mysqli_query($conn, $sql_groups);
    while($row = mysqli_fetch_assoc($query_groups)){
        $output .= '<a href="group_chat.php?room_id='.$row['room_id'].'">
                    <div class="content">
                    <img src="php/images/default.png" alt="">
                    <div class="details">
                        <span>'.$row['room_name'].'</span>
                        <p>Group Chat</p>
                    </div>
                    </div>
                    <div class="status-dot"><i class="fas fa-circle"></i></div>
                </a>';
    }

    echo $output;
?>
