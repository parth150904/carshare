<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Book Ride — CarShare</title>
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../assets/css/dashboard.css" rel="stylesheet">
  
  <!-- Leaflet Map CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
  
  <?php
  require '../config.php';
  session_start();
  if (!isset($_SESSION['id'])) { header("location:login.php"); }
  $user = mysqli_query($con,"SELECT * from users WHERE id='$_SESSION[id]'");
  $user_row = mysqli_fetch_assoc($user);
  $id = $_GET['ride'];
  $ride = mysqli_query($con,"SELECT * FROM car_ride WHERE r_id='$id' ORDER BY add_time DESC");
  $r_data = mysqli_fetch_assoc($ride);
  if (isset($_POST['save'])) {
    $pers = $_POST['pers'];
    mysqli_query($con,"INSERT INTO ride_book (book_by,ride_id,person) VALUES ($_SESSION[id],$id,$pers)");
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
      <li><a href="add_ride.php"><i class="material-icons">directions_car</i> Add Ride</a></li>
      <li><a href="confirmed.php"><i class="material-icons">check_circle</i> Confirmed</a></li>
      <li><a href="support.php"><i class="material-icons">support_agent</i> Support</a></li>
      <li><a href="logout.php"><i class="material-icons">logout</i> Log Out</a></li>
    </ul>
  </nav>
  <section class="ds-hero">
    <h2>Book This Ride</h2>
    <p class="ds-hero-email"><?php echo htmlspecialchars($r_data['r_from']); ?> &rarr; <?php echo htmlspecialchars($r_data['r_to']); ?></p>
  </section>
  <main class="ds-main">
    <div class="ds-form-card">
      <div class="ds-form-header"><i class="material-icons" style="vertical-align:middle;margin-right:8px;">map</i> Route Map & Details</div>
      <div class="ds-form-body">
        
        <!-- Interactive Map Container -->
        <div id="route-map" style="height: 280px; width: 100%; border-radius: 8px; margin-bottom: 24px; z-index: 1; border: 1px solid #ddd;"></div>
        
        <form method="post">
          <div class="ds-panel active">
            <div class="card" style="margin-bottom:24px;">
              <div class="card-body">
                <table class="table">
                  <tr><td>Ride</td><td><?php echo $r_data['r_from'];?> &rarr; <?php echo $r_data['r_to'];?></td></tr>
                  <tr><td>Via</td><td><?php echo $r_data['r_via'];?></td></tr>
                  <tr><td>Charge/Person</td><td style="font-weight:700;">₹<?php echo $r_data['ppc'];?></td></tr>
                  <tr><td>Ride Time</td><td><?php echo $r_data['start_time'];?> &rarr; <?php echo $r_data['end_time'];?></td></tr>
                  <tr><td>Ride Date</td><td><?php echo $r_data['date'];?></td></tr>
                </table>
              </div>
            </div>
          </div>
          <div class="ds-field">
            <label>Number of Persons</label>
            <select name="pers">
              <?php
              $avil_p = mysqli_query($con,"SELECT sum(`person`) AS total FROM ride_book WHERE ride_id='$r_data[r_id]' ");
              $avil_d = mysqli_fetch_assoc($avil_p);
              $avil = $r_data['seat'] - $avil_d['total'];
              for ($i=1; $i<=$avil; $i++) { echo '<option value="'.$i.'">'.$i.'</option>'; }
              ?>
            </select>
          </div>
          <div style="display:flex;gap:12px;">
            <button type="submit" name="save" class="ds-submit" style="flex:2;"><i class="material-icons" style="font-size:18px;">check_circle</i> Confirm Booking</button>
            <a href="ride.php" class="ds-submit" style="flex:1;background:rgba(242,141,91,.12);color:var(--orange);text-decoration:none;text-align:center;">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </main>
  <footer class="ds-footer"><p>&copy; <?php echo date('Y'); ?> <a href="../index.php">CarShare</a></p></footer>

  <!-- Leaflet Map JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
  <script>
    const fromCity = "<?php echo addslashes($r_data['r_from']); ?>";
    const toCity = "<?php echo addslashes($r_data['r_to']); ?>";
    
    // Initialize map centered roughly on India
    const map = L.map('route-map').setView([22.0, 79.0], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Free OpenStreetMap Geocoder (Nominatim)
    async function geocode(city) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(city)}`);
            const data = await response.json();
            if (data && data.length > 0) {
                return L.latLng(data[0].lat, data[0].lon);
            }
        } catch(e) { console.error("Geocoding failed for " + city); }
        return null;
    }

    async function drawRoute() {
        const start = await geocode(fromCity);
        const end = await geocode(toCity);

        if (start && end) {
            L.Routing.control({
                waypoints: [start, end],
                routeWhileDragging: false,
                addWaypoints: false,
                fitSelectedRoutes: true,
                show: false, // Hides the clunky turn-by-turn text box
                lineOptions: {
                    styles: [{color: '#1a73e8', opacity: 0.8, weight: 6}]
                }
            }).addTo(map);
        } else {
            document.getElementById('route-map').innerHTML = "<div style='display:flex;height:100%;align-items:center;justify-content:center;color:#666;'><p>Map route unavailable for these locations.</p></div>";
        }
    }

    drawRoute();
  </script>

<?php include 'chatbot.php'; ?>
</body>
</html>


