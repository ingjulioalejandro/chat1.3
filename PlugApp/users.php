<?php
    session_start();
    include_once "php/config.php";
    if(!isset($_SESSION['unique_id'])){
        header("location: login.php");
    }
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="users">
      <header>
        <div class="content">
          <?php 
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
            if(mysqli_num_rows($sql) > 0){
              $row = mysqli_fetch_assoc($sql);
            }
          ?>
          <img src="php/images/<?php echo $row['img']; ?>" alt="">
          <div class="details">
            <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
            <p><?php echo $row['status']; ?></p>
          </div>
        </div>
        <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout">Logout</a>
      </header>
      <div class="search">
        <span class="text">Select a user to start chat</span>
        <input type="text" placeholder="Enter name to search...">
        <button><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">
        <?php
        $outgoing_id = $_SESSION['unique_id'];
        echo "<p>Outgoing ID: $outgoing_id</p>";

        $sql = "SELECT u.unique_id, u.fname, u.lname, u.img, u.status, 
                       m.msg, m.sent_at
                FROM users u
                LEFT JOIN (
                    SELECT incoming_msg_id, outgoing_msg_id, msg, sent_at,
                           ROW_NUMBER() OVER (PARTITION BY 
                               CASE 
                                   WHEN incoming_msg_id = {$outgoing_id} THEN outgoing_msg_id
                                   ELSE incoming_msg_id
                               END 
                           ORDER BY sent_at DESC) as rn
                    FROM messages
                    WHERE incoming_msg_id = {$outgoing_id} OR outgoing_msg_id = {$outgoing_id}
                ) m ON (u.unique_id = m.incoming_msg_id AND m.outgoing_msg_id = {$outgoing_id})
                      OR (u.unique_id = m.outgoing_msg_id AND m.incoming_msg_id = {$outgoing_id})
                WHERE u.unique_id != {$outgoing_id} AND m.rn = 1
                ORDER BY m.sent_at DESC";

        echo "<p>SQL Query: " . htmlspecialchars($sql) . "</p>";

        $query = mysqli_query($conn, $sql);
        
        if (!$query) {
            echo "<p>Error en la consulta: " . mysqli_error($conn) . "</p>";
        } else {
            echo "<p>Número de filas: " . mysqli_num_rows($query) . "</p>";
            
            if(mysqli_num_rows($query) == 0){
                echo "No users are available to chat";
            } elseif(mysqli_num_rows($query) > 0) {
                while($row = mysqli_fetch_assoc($query)){
                    echo "<pre>" . print_r($row, true) . "</pre>";
                    
                    $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$row['unique_id']}
                        OR outgoing_msg_id = {$row['unique_id']}) AND (outgoing_msg_id = {$outgoing_id} 
                        OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";
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

                    echo '<a href="chat.php?user_id='. $row['unique_id'] .'">
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
        ?>
      </div>
    </section>
  </div>

  <script src="javascript/users.js"></script>

</body>
</html>
