<?php
session_start();
include 'dbconnection.php';
if (strlen($_SESSION['id'])==0) {
  header('location:logout.php');
  exit;
}

if(isset($_GET['id'])) {
  $revid = intval($_GET['id']);
  $msg = mysqli_query($con,"DELETE FROM reviews WHERE review_id='$revid'");
  if($msg) {
    echo "<script>alert('Review deleted successfully.'); window.location.href='manage_reviews.php';</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Reviews | Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="ds-body">
  <nav class="ds-topnav">
    <a class="ds-brand" href="dashboard.php"><span class="ds-brand-mark"><i></i><i></i><i></i></span><span>car<strong>share</strong> <small style="color:#d9f36b;">ADMIN</small></span></a>
    <button class="ds-menu-toggle" onclick="document.querySelector('.ds-nav-links').classList.toggle('is-open')"><span></span><span></span><span></span></button>
    <ul class="ds-nav-links">
      <li><a href="dashboard.php"><i class="material-icons">dashboard</i> Dashboard</a></li>
      <li><a href="manage-users.php"><i class="material-icons">people</i> Users</a></li>
      <li><a href="car_ride.php"><i class="material-icons">directions_car</i> Rides</a></li>
      <li><a href="booked_ride.php"><i class="material-icons">book_online</i> Bookings</a></li>
      <li><a class="active" href="manage_reviews.php"><i class="material-icons">star_rate</i> Reviews</a></li>
      <li><a href="support.php"><i class="material-icons">support_agent</i> Support</a></li>
      <li><a href="logout.php"><i class="material-icons">logout</i> Log Out</a></li>
    </ul>
  </nav>

  <section class="ds-hero" style="background:#173f36; color:white;">
    <h2>Manage Reviews</h2>
    <p class="ds-hero-email" style="color:rgba(255,255,255,0.7);">Moderate user ratings and feedback</p>
  </section>

  <main class="ds-main">
    <div class="ds-panel-wrap" style="border-radius:10px;">
      <div class="ds-panel active">
        <?php 
        $ret = mysqli_query($con,"SELECT r.*, u1.name as reviewer_name, u2.name as reviewee_name 
                                  FROM reviews r 
                                  LEFT JOIN users u1 ON r.reviewer_id = u1.id 
                                  LEFT JOIN users u2 ON r.reviewee_id = u2.id 
                                  ORDER BY r.created_at DESC");
        if(mysqli_num_rows($ret) > 0) {
            echo '<table class="table" style="background:white; border-radius:8px; overflow:hidden;">
                    <thead style="background:#f9fafa;">
                      <tr>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">ID</th>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Reviewer</th>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Reviewee</th>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Rating</th>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Comment</th>
                        <th style="padding:12px; text-align:right; border-bottom:2px solid #e0e4e2;">Action</th>
                      </tr>
                    </thead>
                    <tbody>';
            while($row = mysqli_fetch_array($ret)) {
                $stars = '';
                for($i=1; $i<=5; $i++) {
                    $stars .= $i <= $row['rating'] ? '<i class="material-icons" style="color:#F28D5B; font-size:14px;">star</i>' : '<i class="material-icons" style="color:#ddd; font-size:14px;">star_border</i>';
                }
                echo '<tr>
                        <td style="padding:12px; border-bottom:1px solid #eee;">'.$row['review_id'].'</td>
                        <td style="padding:12px; border-bottom:1px solid #eee; font-weight:600;">'.htmlspecialchars($row['reviewer_name']).'<br><small style="color:var(--muted)">Ride #'.$row['ride_id'].'</small></td>
                        <td style="padding:12px; border-bottom:1px solid #eee; font-weight:600;">'.htmlspecialchars($row['reviewee_name']).'</td>
                        <td style="padding:12px; border-bottom:1px solid #eee;">'.$stars.'</td>
                        <td style="padding:12px; border-bottom:1px solid #eee; max-width:250px; font-style:italic;">"'.htmlspecialchars($row['comment']).'"</td>
                        <td style="padding:12px; border-bottom:1px solid #eee; text-align:right;">
                           <a href="manage_reviews.php?id='.$row['review_id'].'" onclick="return confirm(\'Delete this review entirely?\');" style="color:#d9534f; text-decoration:none;" title="Delete">
                             <i class="material-icons">delete</i>
                           </a>
                        </td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="ds-empty"><i class="material-icons">star_border</i><p>No reviews have been left yet.</p></div>';
        }
        ?>
      </div>
    </div>
  </main>
  <footer class="ds-footer"><p>&copy; <?php echo date('Y'); ?> CarShare Admin Panel</p></footer>
</body>
</html>

