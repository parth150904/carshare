<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rate User — CarShare</title>
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../assets/css/dashboard.css" rel="stylesheet">
  <style>
    .star-rating {
      display: flex;
      flex-direction: row-reverse;
      justify-content: flex-end;
      gap: 8px;
    }
    .star-rating input {
      display: none;
    }
    .star-rating label {
      cursor: pointer;
      color: #ddd;
      font-size: 32px;
      transition: color 0.2s;
    }
    .star-rating label .material-icons {
      font-size: 40px;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
      color: #F28D5B; /* Orange from design system */
    }
  </style>
  <?php
  require '../config.php';
  session_start();
  if (!isset($_SESSION['id'])) { header("location:login.php"); exit; }

  $reviewer_id = $_SESSION['id'];
  $ride_id = isset($_GET['ride_id']) ? intval($_GET['ride_id']) : 0;
  $reviewee_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

  // Fetch reviewee details
  $q = mysqli_query($con, "SELECT name FROM users WHERE id='$reviewee_id'");
  $reviewee = mysqli_fetch_assoc($q);

  if (!$reviewee) {
      die("User not found.");
  }

  // Check if already reviewed
  $check = mysqli_query($con, "SELECT * FROM reviews WHERE ride_id='$ride_id' AND reviewer_id='$reviewer_id' AND reviewee_id='$reviewee_id'");
  if(mysqli_num_rows($check) > 0) {
      $already_reviewed = true;
      $existing = mysqli_fetch_assoc($check);
  } else {
      $already_reviewed = false;
  }

  if(isset($_POST['submit_review'])) {
      $rating = intval($_POST['rating']);
      $comment = mysqli_real_escape_string($con, $_POST['comment']);
      
      if(!$already_reviewed) {
          mysqli_query($con, "INSERT INTO reviews (ride_id, reviewer_id, reviewee_id, rating, comment) VALUES ('$ride_id', '$reviewer_id', '$reviewee_id', '$rating', '$comment')");
          echo "<script>window.location.href='profile-page.php';</script>";
          exit;
      }
  }
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
      <li><a href="confirmed.php"><i class="material-icons">check_circle</i> Confirmed</a></li>
      <li><a href="support.php"><i class="material-icons">support_agent</i> Support</a></li>
      <li><a href="logout.php"><i class="material-icons">logout</i> Log Out</a></li>
    </ul>
  </nav>

  <section class="ds-hero">
    <h2>Rate & Review</h2>
    <p class="ds-hero-email">Leave feedback for <?php echo htmlspecialchars($reviewee['name']); ?></p>
  </section>

  <main class="ds-main">
    <div class="ds-form-card">
      <div class="ds-form-header">
        <i class="material-icons" style="vertical-align:middle;margin-right:8px;">star_rate</i>
        Feedback Form
      </div>
      <div class="ds-form-body">
        
        <?php if($already_reviewed): ?>
          <div class="ds-empty">
            <i class="material-icons" style="color:#F28D5B;">star</i>
            <p>You have already rated <?php echo htmlspecialchars($reviewee['name']); ?> for this ride.</p>
            <p style="font-size:24px; margin: 10px 0;">
              <?php 
                for($i=1; $i<=5; $i++) {
                  echo $i <= $existing['rating'] ? '<i class="material-icons" style="color:#F28D5B;">star</i>' : '<i class="material-icons" style="color:#ddd;">star_border</i>';
                }
              ?>
            </p>
            <p style="font-style:italic; color:var(--muted);">"<?php echo htmlspecialchars($existing['comment']); ?>"</p>
            <a href="profile-page.php" class="ds-submit" style="display:inline-block; text-align:center; text-decoration:none; margin-top:20px;">Go Back</a>
          </div>
        <?php else: ?>
          <form method="post">
            <div class="ds-field">
              <label>How was your experience with <?php echo htmlspecialchars($reviewee['name']); ?>?</label>
              <div class="star-rating">
                <input type="radio" id="star5" name="rating" value="5" required /><label for="star5" title="5 stars"><i class="material-icons">star</i></label>
                <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars"><i class="material-icons">star</i></label>
                <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars"><i class="material-icons">star</i></label>
                <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars"><i class="material-icons">star</i></label>
                <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star"><i class="material-icons">star</i></label>
              </div>
            </div>

            <div class="ds-field">
              <label>Leave a Comment (Optional)</label>
              <textarea name="comment" rows="4" placeholder="Write your feedback here..." style="width:100%; border:1px solid #e0e4e2; border-radius:6px; padding:12px; font-family:inherit; font-size:15px; background:var(--cream);"></textarea>
            </div>

            <div style="display:flex;gap:12px; margin-top: 20px;">
              <button type="submit" name="submit_review" class="ds-submit" style="flex:2;">
                <i class="material-icons" style="font-size:18px;">send</i> Submit Review
              </button>
              <a href="profile-page.php" class="ds-submit" style="flex:1;background:rgba(242,141,91,.12);color:var(--orange);text-decoration:none;text-align:center;">Cancel</a>
            </div>
          </form>
        <?php endif; ?>

      </div>
    </div>
  </main>
  <footer class="ds-footer"><p>&copy; <?php echo date('Y'); ?> <a href="../index.php">CarShare</a></p></footer>
<?php include 'chatbot.php'; ?>
</body>
</html>



