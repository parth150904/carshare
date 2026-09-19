<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Profile — CarShare</title>
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../assets/css/dashboard.css" rel="stylesheet">
  <?php
  require '../config.php';
  session_start();

  if (!isset($_SESSION['id'])) {
     header("location:login.php");
   } 

   $user = mysqli_query($con,"SELECT * from users WHERE id='$_SESSION[id]'");
   $user_row = mysqli_fetch_assoc($user);

   if (isset($_POST['save'])) {
     $name= $_POST['name'];
     $email =  $_POST['email'];
     $num = $_POST['num'];
     $sex = $_POST['sex'];
     $update = mysqli_query($con,"UPDATE users SET name='$name',email='$email',mo_num='$num',gender='$sex' WHERE id='$_SESSION[id]'");
     echo "<script>window.location.href='edit.php';</script>";
   }
  ?>
</head>
<body class="ds-body">

  <nav class="ds-topnav">
    <a class="ds-brand" href="../index.php">
      <span class="ds-brand-mark"><i></i><i></i><i></i></span>
      <span>car<strong>share</strong></span>
    </a>
    <button class="ds-menu-toggle" onclick="document.querySelector('.ds-nav-links').classList.toggle('is-open')" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
    <ul class="ds-nav-links">
      <li><a href="profile-page.php"><i class="material-icons">home</i> Home</a></li>
      <li><a class="active" href="edit.php"><i class="material-icons">edit</i> Edit</a></li>
      <li><a href="add_ride.php"><i class="material-icons">directions_car</i> Add Ride</a></li>
      <li><a href="confirmed.php"><i class="material-icons">check_circle</i> Confirmed</a></li>
      <li><a href="support.php"><i class="material-icons">support_agent</i> Support</a></li>
      <li><a href="logout.php"><i class="material-icons">logout</i> Log Out</a></li>
    </ul>
  </nav>

  <section class="ds-hero">
    <img class="ds-avatar" src="../assets/img/faces/christian.jpg" alt="Profile picture">
    <h2><?php echo htmlspecialchars($user_row['name']); ?></h2>
    <p class="ds-hero-email"><?php echo htmlspecialchars($user_row['email']); ?></p>
  </section>

  <main class="ds-main">
    <div class="ds-form-card">
      <div class="ds-form-header">
        <i class="material-icons" style="vertical-align:middle; margin-right:8px;">edit</i>
        Edit Profile
      </div>
      <div class="ds-form-body">
        <form method="post" action="">

          <div class="ds-field">
            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user_row['name']); ?>" required placeholder="First-Name Last-Name">
          </div>

          <div class="ds-field">
            <label>Phone Number</label>
            <input type="text" name="num" value="<?php echo htmlspecialchars($user_row['mo_num']); ?>" required placeholder="Contact number">
          </div>

          <div class="ds-field">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user_row['email']); ?>" required placeholder="you@example.com">
          </div>

          <div class="ds-field">
            <label>Gender</label>
            <div style="display:flex; gap:24px; margin-top:6px;">
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:500; color:var(--ink);">
                <input type="radio" name="sex" value="Male" <?php if($user_row['gender']!='Female') echo 'checked'; ?> style="accent-color:var(--green); width:18px; height:18px;">
                Male
              </label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:500; color:var(--ink);">
                <input type="radio" name="sex" value="Female" <?php if($user_row['gender']=='Female') echo 'checked'; ?> style="accent-color:var(--green); width:18px; height:18px;">
                Female
              </label>
            </div>
          </div>

          <button type="submit" name="save" class="ds-submit">
            <i class="material-icons" style="font-size:18px;">save</i> Save Changes
          </button>

        </form>
      </div>
    </div>
  </main>

  <footer class="ds-footer">
    <p>&copy; <?php echo date('Y'); ?> <a href="../index.php">CarShare</a>. Better rides, shared.</p>
  </footer>

<?php include 'chatbot.php'; ?>
</body>
</html>


