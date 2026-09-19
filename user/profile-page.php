<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard — CarShare</title>
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../assets/css/dashboard.css" rel="stylesheet">
  <?php
  require '../config.php';
  session_start();

  if (!isset($_SESSION['user'])) {
     if (!isset($_SESSION['id'])) {
      header("location:login.php"); 
     }
   } 
   else
   {
    $idq = mysqli_query($con,"SELECT id from users WHERE email='$_SESSION[user]'");
    $id_row = mysqli_fetch_assoc($idq);
    $_SESSION['id'] = $id_row['id'];
    unset($_SESSION['user']); 
   }

   $user = mysqli_query($con,"SELECT * from users WHERE id='$_SESSION[id]'");
   $user_row = mysqli_fetch_assoc($user);
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
      <li><a class="active" href="profile-page.php"><i class="material-icons">home</i> Home</a></li>
      <li><a href="add_ride.php"><i class="material-icons">directions_car</i> Add Ride</a></li>
      <li><a href="confirmed.php"><i class="material-icons">check_circle</i> Confirmed</a></li>
      <li><a href="support.php"><i class="material-icons">support_agent</i> Support</a></li>
      <li><a href="logout.php"><i class="material-icons">logout</i> Log Out</a></li>
    </ul>
  </nav>

  <section class="ds-hero">
    <img class="ds-avatar" src="../assets/img/profile.png" alt="Profile picture">
    <h2><?php echo htmlspecialchars($user_row['name']); ?></h2>
    <p class="ds-hero-email"><?php echo htmlspecialchars($user_row['email']); ?></p>
    
    <?php
    $rev_q = mysqli_query($con, "SELECT AVG(rating) as avg_rating, COUNT(review_id) as total_rev FROM reviews WHERE reviewee_id='$_SESSION[id]'");
    $rev_data = mysqli_fetch_assoc($rev_q);
    if($rev_data['total_rev'] > 0) {
        $avg = round($rev_data['avg_rating'], 1);
        echo '<div style="margin-top:8px; display:flex; align-items:center; gap:4px; color:#F28D5B; font-weight:600;">
                <i class="material-icons" style="font-size:20px;">star</i>
                <span>'.$avg.' ('.$rev_data['total_rev'].' reviews)</span>
              </div>';
    } else {
        echo '<div style="margin-top:8px; color:var(--muted); font-size:14px;">No reviews yet</div>';
    }
    ?>
    
    <div class="ds-hero-actions">
      <a href="edit.php" class="ds-btn-lime"><i class="material-icons" style="font-size:16px">edit</i> Edit Profile</a>
      <a href="add_ride.php" class="ds-btn-ghost"><i class="material-icons" style="font-size:16px">add</i> Add Ride</a>
    </div>
  </section>

  <main class="ds-main">
    <div class="ds-tabs">
      <button class="ds-tab active" data-tab="bookings" onclick="switchTab(this)">
        <i class="material-icons">person_pin_circle</i> Your Booking
      </button>
      <button class="ds-tab" data-tab="cars" onclick="switchTab(this)">
        <i class="material-icons">directions_car</i> Your Car
      </button>
      <button class="ds-tab" data-tab="requests" onclick="switchTab(this)">
        <i class="material-icons">notifications</i> Requests
      </button>
    </div>

    <div class="ds-panel-wrap">
      <div class="ds-panel active" id="panel-bookings">
        <a href="ride.php" class="ds-cta">
          <i class="material-icons">search</i> Search for Rides
        </a>
        <div id="your_booking"></div>
      </div>

      <div class="ds-panel" id="panel-cars">
        <a href="confirmed.php" class="ds-cta">
          <i class="material-icons">check_circle</i> View confirmed bookings
        </a>
        <div id="your_car"></div>
      </div>

      <div class="ds-panel" id="panel-requests">
        <div id="req_content"></div>
      </div>
    </div>
  </main>

  <footer class="ds-footer">
    <p>&copy; <?php echo date('Y'); ?> <a href="../index.php">CarShare</a>. Better rides, shared.</p>
  </footer>

  <script src="../assets/js/core/jquery.min.js"></script>
  <script>
    function switchTab(btn) {
      document.querySelectorAll('.ds-tab').forEach(function(t) { t.classList.remove('active'); });
      document.querySelectorAll('.ds-panel').forEach(function(p) { p.classList.remove('active'); });
      btn.classList.add('active');
      document.getElementById('panel-' + btn.dataset.tab).classList.add('active');
    }

    $("#req_content").load('req.php');
    $("#your_booking").load('your_booking.php');
    $("#your_car").load('your_car.php');

    function confirm(mss) {
       $.post('req.php',{'c_id':mss},function(response){
        $("#req_content").load('req.php');});
    }
    function cancel(mss) {
       $.post('req.php',{'cn_id':mss},function(response){
        $("#req_content").load('req.php');});
    }
    function your_booking_cancel(mss) {
       $.post('your_booking.php',{'c_id':mss},function(response){
        $("#your_booking").load('your_booking.php');});
    }
    function cancel_ride(mss) {
       $.post('your_car.php',{'c_id':mss},function(response){
        $("#your_car").load('your_car.php');});
    }
  </script>
<?php include 'chatbot.php'; ?>
</body>
</html>


