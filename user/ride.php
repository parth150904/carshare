<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Find a Ride — CarShare</title>
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../assets/css/dashboard.css" rel="stylesheet">
  <?php
  require '../config.php';
  session_start();
  if (!isset($_SESSION['id'])) { header("location:login.php"); }
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
      <li><a href="confirmed.php"><i class="material-icons">check_circle</i> Confirmed</a></li>
      <li><a href="logout.php"><i class="material-icons">logout</i> Log Out</a></li>
    </ul>
  </nav>
  <section class="ds-hero">
    <h2>Find a Ride</h2>
    <p class="ds-hero-email">Select your city to browse available rides</p>
  </section>
  <main class="ds-main">
    <div class="ds-form-card">
      <div class="ds-form-header"><i class="material-icons" style="vertical-align:middle;margin-right:8px;">search</i> Search Rides</div>
      <div class="ds-form-body">
        <div class="ds-field">
          <label>Select Your City</label>
          <select id="city">
            <option>Select City</option>
            <?php $city_q = mysqli_query($con,"SELECT * FROM city"); while ($city = mysqli_fetch_assoc($city_q)) { echo '<option value="'.$city['city'].'">'.$city['city'].'</option>'; } ?>
          </select>
        </div>
      </div>
    </div>
    <div id="data" style="margin-top:24px;"></div>
  </main>
  <footer class="ds-footer"><p>&copy; <?php echo date('Y'); ?> <a href="../index.php">CarShare</a></p></footer>
  <script src="../assets/js/core/jquery.min.js"></script>
  <script>
    $("#city").change(function(){
      var city=$(this).val();
      $.post("ride_serch.php",{'city':city},function(response){ $("#data").show().html(response); });
    });
  </script>
</body>
</html>
