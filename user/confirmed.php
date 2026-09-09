
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Confirmed Rides — CarShare</title>
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../assets/css/dashboard.css" rel="stylesheet">
  <?php
  require '../config.php';
  session_start();
  if (!isset($_SESSION['id'])) { header("location:login.php"); exit; }
  $user = mysqli_query($con,"SELECT * from users WHERE id='$_SESSION[id]'");
  $user_row = mysqli_fetch_assoc($user);
  ?>
</head>
<body class="ds-body">
  <nav class="ds-topnav">
    <a class="ds-brand" href="../index.php"><span class="ds-brand-mark"><i></i><i></i><i></i></span><span>car<strong>share</strong></span></a>
    <button class="ds-menu-toggle" onclick="document.querySelector('.ds-nav-links').classList.toggle('is-open')" aria-label="Toggle menu"><span></span><span></span><span></span></button>
    <ul class="ds-nav-links">
      <li><a href="profile-page.php"><i class="material-icons">home</i> Home</a></li>
      <li><a href="edit.php"><i class="material-icons">edit</i> Edit</a></li>
      <li><a href="add_ride.php"><i class="material-icons">directions_car</i> Add Ride</a></li>
      <li><a class="active" href="confirmed.php"><i class="material-icons">check_circle</i> Confirmed</a></li>
      <li><a href="support.php"><i class="material-icons">support_agent</i> Support</a></li>
      <li><a href="logout.php"><i class="material-icons">logout</i> Log Out</a></li>
    </ul>
  </nav>
  <section class="ds-hero">
    <h2>Confirmed Rides</h2>
    <p class="ds-hero-email">All bookings that have been confirmed by you</p>
  </section>
  <main class="ds-main">
    <div class="ds-panel-wrap" style="border-radius:10px;">
      <div class="ds-panel active">
        <?php 
        $has_confirmed = false;
        $car_ride = mysqli_query($con,"SELECT * FROM car_ride WHERE owner='$_SESSION[id]'");
        while ($car_Rdata = mysqli_fetch_assoc($car_ride)) {
          $r_book = mysqli_query($con,"SELECT * FROM ride_book WHERE ride_id='$car_Rdata[r_id]' AND conform='Yes' ORDER BY booking_time DESC");
          while ($booking = mysqli_fetch_assoc($r_book)) {
            $has_confirmed = true;
            $buser = mysqli_query($con,"SELECT * FROM users WHERE id='$booking[book_by]' ");
            $buser_row = mysqli_fetch_assoc($buser);
            echo '<div class="card" style="margin-bottom:16px;">
              <div class="card-body">
                <h6 style="color:#4A8A62!important; margin-bottom:12px;"><i class="material-icons" style="font-size:16px;vertical-align:middle;margin-right:4px;">check_circle</i> RIDE CONFIRMED</h6>
                <table class="table" style="margin-bottom: 0;">
                  <tr><td>Ride</td><td>'.$car_Rdata['r_from'].' &rarr; '.$car_Rdata['r_to'].'</td></tr>
                  <tr><td>Booked By</td><td>'.$buser_row['name'].'</td></tr>
                  <tr><td>Contact</td><td>'.$buser_row['mo_num'].'</td></tr>
                  <tr><td>Booking Time</td><td>'.$booking['booking_time'].'</td></tr>
                </table>
              </div>
              <div style="padding:0 20px 20px;">
                <a href="rate_user.php?ride_id='.$car_Rdata['r_id'].'&user_id='.$booking['book_by'].'" class="ds-submit" style="margin:0; text-align:center; text-decoration:none; display:flex; padding:12px; font-size:14px; width: 100%;">
                  <i class="material-icons" style="font-size:16px;">star_rate</i> Rate Passenger
                </a>
              </div>
            </div>';
          }
        }
        if (!$has_confirmed) {
          echo '<div class="ds-empty"><i class="material-icons">event_available</i><p>No confirmed rides yet.</p></div>';
        }
        ?>
      </div>
    </div>
  </main>
  <footer class="ds-footer"><p>&copy; <?php echo date('Y'); ?> <a href="../index.php">CarShare</a></p></footer>
</body>
</html>

