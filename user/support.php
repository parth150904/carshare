<?php
require '../config.php';
session_start();
if (!isset($_SESSION['id'])) { header("location:login.php"); exit; }

$user = mysqli_query($con,"SELECT * from users where id='$_SESSION[id]'");
$user_row = mysqli_fetch_assoc($user);

if(isset($_POST['submit_ticket'])) {
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $message = mysqli_real_escape_string($con, $_POST['message']);
    
    $query = "INSERT INTO support_tickets (user_id, subject, message) VALUES ('$_SESSION[id]', '$subject', '$message')";
    if(mysqli_query($con, $query)) {
        $success = "Your support ticket has been submitted successfully. Our team will review it shortly.";
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contact Support — CarShare</title>
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../assets/css/dashboard.css" rel="stylesheet">
  <style>
    .support-form { max-width: 600px; background: white; padding: 30px; border-radius: 8px; margin: 0 auto; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .support-form .form-group { margin-bottom: 20px; }
    .support-form label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--ink); }
    .support-form input, .support-form textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit; font-size: 15px; }
    .support-form input:focus, .support-form textarea:focus { outline: none; border-color: var(--green); }
  </style>
</head>
<body class="ds-body">
  <nav class="ds-topnav">
    <a class="ds-brand" href="../index.php"><span class="ds-brand-mark"><i></i><i></i><i></i></span><span>car<strong>share</strong></span></a>
    <button class="ds-menu-toggle" onclick="document.querySelector('.ds-nav-links').classList.toggle('is-open')" aria-label="Toggle menu"><span></span><span></span><span></span></button>
    <ul class="ds-nav-links">
      <li><a href="profile-page.php"><i class="material-icons">home</i> Home</a></li>
      <li><a href="edit.php"><i class="material-icons">edit</i> Edit</a></li>
      <li><a href="add_ride.php"><i class="material-icons">directions_car</i> Add Ride</a></li>
      <li><a href="confirmed.php"><i class="material-icons">check_circle</i> Confirmed</a></li>
      <li><a class="active" href="support.php"><i class="material-icons">support_agent</i> Support</a></li>
      <li><a href="logout.php"><i class="material-icons">logout</i> Log Out</a></li>
    </ul>
  </nav>

  <section class="ds-hero">
    <h2>Help & Support</h2>
    <p class="ds-hero-email">Have an issue with a ride or user? Let us know.</p>
  </section>

  <main class="ds-main">
    <div class="support-form">
      <?php if(isset($success)) { echo '<div style="background:#e8f5e9; color:#2e7d32; padding:15px; border-radius:6px; margin-bottom:20px; font-weight:600;"><i class="material-icons" style="vertical-align:middle; margin-right:5px;">check_circle</i>'.$success.'</div>'; } ?>
      <?php if(isset($error)) { echo '<div style="background:#ffebee; color:#c62828; padding:15px; border-radius:6px; margin-bottom:20px; font-weight:600;"><i class="material-icons" style="vertical-align:middle; margin-right:5px;">error</i>'.$error.'</div>'; } ?>
      
      <form action="" method="post">
        <div class="form-group">
          <label>Subject / Topic</label>
          <input type="text" name="subject" required placeholder="e.g. Driver didn't show up, Payment issue, etc.">
        </div>
        <div class="form-group">
          <label>Detailed Message</label>
          <textarea name="message" required rows="6" placeholder="Please describe the issue in detail. If it involves a specific ride or user, please include their name or the ride details."></textarea>
        </div>
        <button type="submit" name="submit_ticket" class="ds-submit"><i class="material-icons" style="font-size:18px;">send</i> Submit Ticket</button>
      </form>
    </div>

    <h3 style="margin-top: 40px; text-align: center; color: var(--ink);">Your Past Tickets</h3>
    <div class="ds-panel-wrap" style="border-radius:10px; margin-top: 20px;">
      <div class="ds-panel active">
        <?php 
        $ret = mysqli_query($con,"SELECT * FROM support_tickets WHERE user_id='$_SESSION[id]' ORDER BY created_at DESC");
        if(mysqli_num_rows($ret) > 0) {
            echo '<table class="table" style="background:white; border-radius:8px; overflow:hidden; margin:0;">
                    <thead style="background:#f9fafa;">
                      <tr>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Subject</th>
                        <th style="padding:12px; text-align:left; border-bottom:2px solid #e0e4e2;">Date Submitted</th>
                        <th style="padding:12px; text-align:right; border-bottom:2px solid #e0e4e2;">Status</th>
                      </tr>
                    </thead>
                    <tbody>';
            while($row = mysqli_fetch_array($ret)) {
                $status_color = $row['status'] == 'Open' ? '#F28D5B' : '#4A8A62';
                echo '<tr>
                        <td style="padding:12px; border-bottom:1px solid #eee; font-weight:600;">'.htmlspecialchars($row['subject']).'</td>
                        <td style="padding:12px; border-bottom:1px solid #eee; color:var(--muted);">'.date('M d, Y h:i A', strtotime($row['created_at'])).'</td>
                        <td style="padding:12px; border-bottom:1px solid #eee; text-align:right;">
                          <span style="background:'.$status_color.'; color:white; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:600;">'.$row['status'].'</span>
                        </td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="ds-empty" style="padding: 20px;"><i class="material-icons">history</i><p>You haven\'t submitted any support tickets yet.</p></div>';
        }
        ?>
      </div>
    </div>
  </main>
  
  <footer class="ds-footer"><p>&copy; <?php echo date('Y'); ?> <a href="../index.php">CarShare</a></p></footer>
</body>
</html>

