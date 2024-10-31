<?php
    session_start();
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $group_name = $_POST['group_name'];
    $members = isset($_POST['members']) ? $_POST['members'] : '';

    if(!empty($group_name)){

        $stmt = $conn->prepare("INSERT INTO chatrooms (room_name, created_by) VALUES (?, ?)");
        $stmt->bind_param("si", $group_name, $outgoing_id);
        $stmt->execute();
        
        if($stmt->affected_rows > 0){
            $room_id = $stmt->insert_id;
            

            $current_time = date('Y-m-d H:i:s');
            $formatted_time = date('h:i A | M d', strtotime($current_time));
            $system_message = "Group '{$group_name}' has been created on {$formatted_time}.";
            $stmt = $conn->prepare("INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, room_id, sent_at) VALUES (?, ?, ?, ?, ?)");
            $incoming_id = 0;
            $outgoing_id = 0;
            $stmt->bind_param("iisis", $incoming_id, $outgoing_id, $system_message, $room_id, $current_time);
            $stmt->execute();
            

            if(!empty($members)){
                $member_ids = explode(",", $members);
                foreach($member_ids as $member_id){
                    if($member_id != $outgoing_id){

                        add_member_to_group($conn, $room_id, $member_id);
                        
                        $user_query = $conn->prepare("SELECT fname, lname FROM users WHERE unique_id = ?");
                        $user_query->bind_param("i", $member_id);
                        $user_query->execute();
                        $user_result = $user_query->get_result();
                        $user = $user_result->fetch_assoc();
                        $user_name = $user['fname'] . ' ' . $user['lname'];
                        
                        $add_message = "{$user_name} has been added to the group.";
                        $stmt = $conn->prepare("INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, room_id, sent_at) VALUES (?, ?, ?, ?, ?)");
                        $current_time = date('Y-m-d H:i:s'); 
                        $stmt->bind_param("iisis", $incoming_id, $outgoing_id, $add_message, $room_id, $current_time);
                        $stmt->execute();
                    }
                }
            }
            
            echo "success";
        }else{
            echo "Something went wrong. Please try again!";
        }
    }else{
        echo "Group name is required!";
    }


    function add_member_to_group($conn, $room_id, $member_id) {

        $stmt = $conn->prepare("INSERT INTO group_members (room_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $room_id, $member_id);
        return $stmt->execute();
    }
?>
