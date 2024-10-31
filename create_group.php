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
    <section class="form signup">
      <header style="text-align:center">Create a New Group</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input">
          <label>Group Name</label>
          <input type="text" name="group_name" placeholder="Enter group name" required>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Create Group">
        </div>
      </form>
      <div class="back-button" style="text-align: center; margin-top: 15px;">
        <a href="users.php" class="back-btn" style="background: #333; color: #fff; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
          <i class="fas fa-arrow-left"></i> Back to Users
        </a>
      </div>
    </section>
  </div>

  <script src="javascript/create_group.js"></script>
</body>
</html>
