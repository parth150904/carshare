<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Ride — CarShare</title>
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
  if (isset($_POST['save'])) {
    $city = $_POST['city']; $r_start = $_POST['r_start']; $r_via = $_POST['r_via'];
    $r_end = $_POST['r_end']; $start = $_POST['start_time']; $end = $_POST['end_time'];
    $date = $_POST['date']; $cost = $_POST['cost']; $seat = $_POST['seat'];
    $ride_type = $_POST['ride_type'];
    mysqli_query($con,"INSERT INTO `car_ride` (`owner`, `city`, `r_from`, `r_via`, `r_to`,`ride_type`, `ppc`,`seat`,`start_time`, `end_time`, `date`) VALUES ($_SESSION[id],'$city','$r_start','$r_via','$r_end','$ride_type','$cost','$seat','$start','$end','$date')");
    echo "<script>window.location.href='profile-page.php';</script>";
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
      <li><a class="active" href="add_ride.php"><i class="material-icons">directions_car</i> Add Ride</a></li>
      <li><a href="confirmed.php"><i class="material-icons">check_circle</i> Confirmed</a></li>
      <li><a href="logout.php"><i class="material-icons">logout</i> Log Out</a></li>
    </ul>
  </nav>
  <section class="ds-hero">
    <h2>Add Your Ride</h2>
    <p class="ds-hero-email">Share your journey and help others travel comfortably</p>
  </section>
  <main class="ds-main">
    <div class="ds-form-card">
      <div class="ds-form-header"><i class="material-icons" style="vertical-align:middle;margin-right:8px;">directions_car</i> Ride Details</div>
      <div class="ds-form-body">
        <form method="post">
          <div class="ds-field">
            <label>Select Your City</label>
            <select name="city" id="citySelect">
              <?php $city_q = mysqli_query($con,"SELECT * FROM city"); while ($city = mysqli_fetch_assoc($city_q)) { echo '<option value="'.$city['city'].'">'.$city['city'].'</option>'; } ?>
            </select>
          </div>
          <div id="map" style="width:100%;border-radius:8px;overflow:hidden;margin-bottom:22px;display:none;"></div>
          <div class="ds-field"><label>Ride Start From</label><input type="text" name="r_start" required placeholder="Starting location"></div>
          <div class="ds-field"><label>Via</label><input type="text" name="r_via" required placeholder="Route waypoint"></div>
          <div class="ds-field"><label>Ride End To</label><input type="text" name="r_end" required placeholder="Destination"></div>
          <div class="ds-field">
            <label>Ride Type</label>
            <select name="ride_type" required>
              <option>Auto</option><option>Micro</option><option>Mini</option>
              <option>Prime Sedan</option><option>Prime Play</option><option>Prime SUV</option>
            </select>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="ds-field"><label>Start Time</label><input type="time" name="start_time" required></div>
            <div class="ds-field"><label>End Time</label><input type="time" name="end_time" required></div>
          </div>
          <div class="ds-field"><label>Ride Date</label><input type="date" name="date" required></div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="ds-field"><label>Available Seats</label><input type="number" name="seat" value="3" min="1" required></div>
            <div class="ds-field"><label>Charge Per Person</label><input type="number" name="cost" min="0" max="10000" step="any" placeholder="0.00" required></div>
          </div>
          <button type="submit" name="save" class="ds-submit"><i class="material-icons" style="font-size:18px;">add_circle</i> Save Ride</button>
        </form>
      </div>
    </div>
  </main>
  <footer class="ds-footer"><p>&copy; <?php echo date('Y'); ?> <a href="../index.php">CarShare</a></p></footer>
  <script src="../assets/js/core/jquery.min.js"></script>
  <script>
    $("#citySelect").change(function(){
      var city=$(this).val();
      $("#map").show().html("<iframe style='width:100%;height:300px;border:0;' src='https://maps.google.com/maps?width=700&height=300&hl=en&q="+city+"+(City)&ie=UTF8&t=&z=11&iwloc=B&output=embed'></iframe>");
    });
  </script>
</body>
</html>
