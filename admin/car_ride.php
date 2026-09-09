<?php
session_start();
include 'dbconnection.php';
if (strlen($_SESSION['id'])==0) {
  header('location:logout.php');
  exit;
}

if(isset($_GET['id'])) {
  $rideid = intval($_GET['id']);
  $msg = mysqli_query($con,"DELETE FROM car_ride WHERE r_id='$rideid'");
  if($msg) {
    echo "<script>alert('Ride deleted successfully.'); window.location.href='car_ride.php';</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>All Rides | Admin</title>
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="ds-body">
  <nav class="ds-topnav">
    <a class="ds-brand" href="manage-users.php"><span class="ds-brand-mark"><i></i><i></i><i></i></span><span>car<strong>share</strong> <small style="color:#d9f36b;">ADMIN</small></span></a>
    <button class="ds-menu-toggle" onclick="document.querySelector('.ds-nav-links').classList.toggle('is-open')" aria-label="Toggle menu"><span></span><span></span><span></span></button>
    <ul class="ds-nav-links">
      <li><a href="dashboard.php"><i class="material-icons">dashboard</i> Dashboard</a></li>
      <li><a href="manage-users.php"><i class="material-icons">people</i> Users</a></li>
      <li><a class="active" href="car_ride.php"><i class="material-icons">directions_car</i> Rides</a></li>
      <li><a href="booked_ride.php"><i class="material-icons">book_online</i> Bookings</a></li>
      <li><a href="manage_reviews.php"><i class="material-icons">star_rate</i> Reviews</a></li>
      <li><a href="support.php"><i class="material-icons">support_agent</i> Support</a></li>
      <li><a href="logout.php"><i class="material-icons">logout</i> Log Out</a></li>
    </ul>
  </nav>

  <section class="ds-hero" style="background:#173f36; color:white;">
    <h2>Published Rides</h2>
    <p class="ds-hero-email" style="color:rgba(255,255,255,0.7);">Monitor all rides published by drivers</p>
  </section>

  <main class="ds-main">
    <div class="ds-panel-wrap" style="border-radius:10px;">
      <div class="ds-panel active">
        <?php 
        $ret = mysqli_query($con,"SELECT * FROM `car_ride` ORDER BY r_id DESC");
        if(mysqli_num_rows($ret) > 0) {
            echo '<table class="table" style="background:white; border-radius:8px; overflow:hidden;">
                    <thead style="background:#f9fafa;">
                      <tr>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">ID</th>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Owner (ID)</th>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Route</th>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Date & Time</th>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Fare</th>
                        <th style="padding:12px; text-align:right; border-bottom:2px solid #e0e4e2;">Action</th>
                      </tr>
                    </thead>
                    <tbody>';
            while($row = mysqli_fetch_array($ret)) {
                echo '<tr>
                        <td style="padding:12px; border-bottom:1px solid #eee;">'.$row['r_id'].'</td>
                        <td style="padding:12px; border-bottom:1px solid #eee; font-weight:600;">'.$row['owner'].'</td>
                        <td style="padding:12px; border-bottom:1px solid #eee;">'.$row['r_from'].' &rarr; '.$row['r_to'].' <br><small style="color:var(--muted)">via '.$row['r_via'].'</small></td>
                        <td style="padding:12px; border-bottom:1px solid #eee;">'.$row['date'].' <br><small style="color:var(--muted)">'.$row['start_time'].' - '.$row['end_time'].'</small></td>
                        <td style="padding:12px; border-bottom:1px solid #eee; color:#4A8A62;">&#8377;'.$row['ppc'].'</td>
                        <td style="padding:12px; border-bottom:1px solid #eee; text-align:right;">
                           <a href="car_ride.php?id='.$row['r_id'].'" onclick="return confirm(\'Delete this ride entirely?\');" style="color:#d9534f; text-decoration:none;" title="Delete">
                             <i class="material-icons">delete</i>
                           </a>
                        </td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="ds-empty"><i class="material-icons">directions_car</i><p>No rides published yet.</p></div>';
        }
        ?>
      </div>
    </div>
  </main>
  <footer class="ds-footer"><p>&copy; <?php echo date('Y'); ?> CarShare Admin Panel</p></footer>
</body>
</html>