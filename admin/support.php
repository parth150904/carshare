<?php
session_start();
include 'dbconnection.php';
if (strlen($_SESSION['id'])==0) {
  header('location:logout.php');
  exit;
}

if(isset($_GET['resolve_id'])) {
  $ticketid = intval($_GET['resolve_id']);
  $msg = mysqli_query($con,"UPDATE support_tickets SET status='Resolved' WHERE id='$ticketid'");
  if($msg) {
    echo "<script>alert('Ticket marked as resolved.'); window.location.href='support.php';</script>";
  }
}

if(isset($_GET['delete_id'])) {
  $ticketid = intval($_GET['delete_id']);
  $msg = mysqli_query($con,"DELETE FROM support_tickets WHERE id='$ticketid'");
  if($msg) {
    echo "<script>alert('Ticket deleted.'); window.location.href='support.php';</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Support Tickets | Admin</title>
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
      <li><a href="manage_reviews.php"><i class="material-icons">star_rate</i> Reviews</a></li>
      <li><a class="active" href="support.php"><i class="material-icons">support_agent</i> Support</a></li>
      <li><a href="logout.php"><i class="material-icons">logout</i> Log Out</a></li>
    </ul>
  </nav>

  <section class="ds-hero" style="background:#173f36; color:white;">
    <h2>Support Tickets</h2>
    <p class="ds-hero-email" style="color:rgba(255,255,255,0.7);">Manage user issues and disputes</p>
  </section>

  <main class="ds-main">
    <div class="ds-panel-wrap" style="border-radius:10px;">
      <div class="ds-panel active">
        <?php 
        $ret = mysqli_query($con,"SELECT t.*, u.name as user_name, u.email 
                                  FROM support_tickets t 
                                  LEFT JOIN users u ON t.user_id = u.id 
                                  ORDER BY CASE WHEN t.status='Open' THEN 1 ELSE 2 END, t.created_at DESC");
        if(mysqli_num_rows($ret) > 0) {
            echo '<table class="table" style="background:white; border-radius:8px; overflow:hidden;">
                    <thead style="background:#f9fafa;">
                      <tr>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Ticket</th>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">User</th>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Subject / Message</th>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Status</th>
                        <th style="padding:12px; text-align:right; border-bottom:2px solid #e0e4e2;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>';
            while($row = mysqli_fetch_array($ret)) {
                $status_color = $row['status'] == 'Open' ? '#F28D5B' : '#4A8A62';
                echo '<tr>
                        <td style="padding:12px; border-bottom:1px solid #eee; vertical-align:top;"><strong>#'.$row['id'].'</strong><br><small style="color:var(--muted)">'.date('M d', strtotime($row['created_at'])).'</small></td>
                        <td style="padding:12px; border-bottom:1px solid #eee; vertical-align:top; font-weight:600;">'.htmlspecialchars($row['user_name']).'<br><small style="color:var(--muted)">'.htmlspecialchars($row['email']).'</small></td>
                        <td style="padding:12px; border-bottom:1px solid #eee; vertical-align:top; max-width:300px;">
                          <strong style="display:block; margin-bottom:4px;">'.htmlspecialchars($row['subject']).'</strong>
                          <span style="font-size:13px; color:#555;">'.nl2br(htmlspecialchars($row['message'])).'</span>
                        </td>
                        <td style="padding:12px; border-bottom:1px solid #eee; vertical-align:top;">
                          <span style="background:'.$status_color.'; color:white; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:600;">'.$row['status'].'</span>
                        </td>
                        <td style="padding:12px; border-bottom:1px solid #eee; vertical-align:top; text-align:right;">';
                if($row['status'] == 'Open') {
                    echo '<a href="support.php?resolve_id='.$row['id'].'" class="ds-submit" style="display:inline-block; margin-bottom:8px; padding:6px 12px; font-size:12px; text-decoration:none;"><i class="material-icons" style="font-size:14px; vertical-align:middle;">check</i> Resolve</a><br>';
                }
                echo '<a href="support.php?delete_id='.$row['id'].'" onclick="return confirm(\'Delete this ticket entirely?\');" style="color:#d9534f; text-decoration:none; font-size:13px;" title="Delete">
                             <i class="material-icons" style="font-size:16px; vertical-align:middle;">delete</i> Delete
                           </a>
                        </td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="ds-empty"><i class="material-icons">done_all</i><p>No support tickets found. Everything is running smoothly!</p></div>';
        }
        ?>
      </div>
    </div>
  </main>
  <footer class="ds-footer"><p>&copy; <?php echo date('Y'); ?> CarShare Admin Panel</p></footer>
</body>
</html>

