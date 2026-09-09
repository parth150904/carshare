<?php
session_start();
include 'dbconnection.php';
if (strlen($_SESSION['id'])==0) {
  header('location:logout.php');
  exit;
}

// Fetch analytics data
$users_q = mysqli_query($con, "SELECT COUNT(*) as total FROM users WHERE type='user'");
$users_count = mysqli_fetch_assoc($users_q)['total'];

$rides_q = mysqli_query($con, "SELECT COUNT(*) as total FROM car_ride");
$rides_count = mysqli_fetch_assoc($rides_q)['total'];

$bookings_q = mysqli_query($con, "SELECT COUNT(*) as total FROM ride_book");
$bookings_count = mysqli_fetch_assoc($bookings_q)['total'];

$reviews_q = mysqli_query($con, "SELECT COUNT(*) as total FROM reviews");
$reviews_count = mysqli_fetch_assoc($reviews_q)['total'];

$tickets_q = mysqli_query($con, "SELECT COUNT(*) as total FROM support_tickets WHERE status='Open'");
$tickets_count = mysqli_fetch_assoc($tickets_q)['total'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard | CarShare</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../assets/css/dashboard.css" rel="stylesheet">
  <style>
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: white; border-radius: 8px; padding: 24px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border-bottom: 4px solid var(--lime); }
    .stat-card h3 { font-size: 36px; margin: 0 0 8px; color: var(--ink); }
    .stat-card p { margin: 0; color: var(--muted); font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
    .stat-card .icon { font-size: 32px; color: var(--green); margin-bottom: 12px; }
  </style>
</head>
<body class="ds-body">
  <nav class="ds-topnav">
    <a class="ds-brand" href="dashboard.php"><span class="ds-brand-mark"><i></i><i></i><i></i></span><span>car<strong>share</strong> <small style="color:#d9f36b;">ADMIN</small></span></a>
    <button class="ds-menu-toggle" onclick="document.querySelector('.ds-nav-links').classList.toggle('is-open')"><span></span><span></span><span></span></button>
    <ul class="ds-nav-links">
      <li><a class="active" href="dashboard.php"><i class="material-icons">dashboard</i> Dashboard</a></li>
      <li><a href="manage-users.php"><i class="material-icons">people</i> Users</a></li>
      <li><a href="car_ride.php"><i class="material-icons">directions_car</i> Rides</a></li>
      <li><a href="booked_ride.php"><i class="material-icons">book_online</i> Bookings</a></li>
      <li><a href="manage_reviews.php"><i class="material-icons">star_rate</i> Reviews</a></li>
      <li><a href="support.php"><i class="material-icons">support_agent</i> Support <?php if($tickets_count > 0) echo '<span style="background:#F28D5B; color:white; padding:2px 6px; border-radius:10px; font-size:11px; margin-left:4px;">'.$tickets_count.'</span>'; ?></a></li>
      <li><a href="logout.php"><i class="material-icons">logout</i> Log Out</a></li>
    </ul>
  </nav>

  <section class="ds-hero" style="background:#173f36; color:white;">
    <h2>Platform Analytics</h2>
    <p class="ds-hero-email" style="color:rgba(255,255,255,0.7);">A quick overview of your platform's performance</p>
  </section>

  <main class="ds-main">
    <div class="stat-grid">
      <div class="stat-card">
        <i class="material-icons icon">people</i>
        <h3><?php echo $users_count; ?></h3>
        <p>Registered Users</p>
      </div>
      <div class="stat-card">
        <i class="material-icons icon">directions_car</i>
        <h3><?php echo $rides_count; ?></h3>
        <p>Total Rides Published</p>
      </div>
      <div class="stat-card">
        <i class="material-icons icon">event_available</i>
        <h3><?php echo $bookings_count; ?></h3>
        <p>Total Bookings</p>
      </div>
      <div class="stat-card">
        <i class="material-icons icon">star_rate</i>
        <h3><?php echo $reviews_count; ?></h3>
        <p>User Reviews</p>
      </div>
      <div class="stat-card" style="border-bottom-color: #F28D5B;">
        <i class="material-icons icon" style="color:#F28D5B;">support_agent</i>
        <h3><?php echo $tickets_count; ?></h3>
        <p>Open Support Tickets</p>
      </div>
    </div>
    
    <div class="ds-panel-wrap" style="border-radius:10px;">
      <div class="ds-panel active" style="text-align:center; padding:40px;">
        <i class="material-icons" style="font-size:48px; color:var(--lime); margin-bottom:16px;">admin_panel_settings</i>
        <h3 style="font-family:'Space Grotesk', sans-serif;">Welcome to the Admin Dashboard</h3>
        <p style="color:var(--muted); max-width:500px; margin:0 auto;">Use the navigation menu above to manage users, monitor published rides, moderate user reviews, and resolve support tickets.</p>
      </div>
    </div>
  </main>
  <footer class="ds-footer"><p>&copy; <?php echo date('Y'); ?> CarShare Admin Panel</p></footer>
</body>
</html>

