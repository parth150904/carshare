<?php
require '../config.php';
session_start();
if (!isset($_SESSION['id'])) { header("location:login.php"); } 
$user = mysqli_query($con,"SELECT * from users WHERE id='{$_SESSION['id']}'");
$user_row = mysqli_fetch_assoc($user);

if (isset($_POST['save'])) {
  $city = $_POST['city']; $r_start = $_POST['r_start']; $r_via = $_POST['r_via'];
  $r_end = $_POST['r_end']; $start = $_POST['start_time']; $end = $_POST['end_time'];
  $date = $_POST['date']; $cost = $_POST['cost']; $seat = $_POST['seat'];
  $ride_type = $_POST['ride_type'];
  mysqli_query($con,"INSERT INTO `car_ride` (`owner`, `city`, `r_from`, `r_via`, `r_to`,`ride_type`, `ppc`,`seat`,`start_time`, `end_time`, `date`) VALUES ('{$_SESSION['id']}','$city','$r_start','$r_via','$r_end','$ride_type','$cost','$seat','$start','$end','$date')");
  echo "<script>window.location.href='profile-page.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Ride - CarShare</title>
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="../assets/css/dashboard.css" rel="stylesheet">
  <!-- Leaflet Map CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
</head>
<body class="ds-body">
  <nav class="ds-topnav">
    <a class="ds-brand" href="../index.php"><span class="ds-brand-mark"><i></i><i></i><i></i></span><span>car<strong>share</strong></span></a>
    <button class="ds-menu-toggle" onclick="document.querySelector('.ds-nav-links').classList.toggle('is-open')" aria-label="Toggle menu"><span></span><span></span><span></span></button>
    <ul class="ds-nav-links">
      <li><a href="profile-page.php"><i class="material-icons">home</i> Home</a></li>
      <li><a class="active" href="add_ride.php"><i class="material-icons">directions_car</i> Add Ride</a></li>
      <li><a href="confirmed.php"><i class="material-icons">check_circle</i> Confirmed</a></li>
      <li><a href="support.php"><i class="material-icons">support_agent</i> Support</a></li>
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
          <div class="ds-field" style="display:none;">
            <label>Select Your City (Base City)</label>
            <select name="city" id="citySelect">
              <?php $city_q = mysqli_query($con,"SELECT * FROM city"); while ($city = mysqli_fetch_assoc($city_q)) { echo '<option value="'.$city['city'].'">'.$city['city'].'</option>'; } ?>
            </select>
          </div>
          <div class="ds-field"><label>Ride Start From</label><input type="text" id="r_start" name="r_start" required placeholder="Starting location"></div>
          <div class="ds-field"><label>Via (Optional waypoint)</label><input type="text" id="r_via" name="r_via" placeholder="Route waypoint"></div>
          <div class="ds-field"><label>Ride End To</label><input type="text" id="r_end" name="r_end" required placeholder="Destination"></div>
          
          <div id="map-error" style="color:#d32f2f; font-size:12px; margin-bottom:8px; font-weight:600;"></div>
          <div id="map" style="width:100%; height:300px; border-radius:8px; overflow:hidden; margin-bottom:22px; border:1px solid #ddd; z-index:1;"></div>
          
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
            <div class="ds-field" style="position:relative;">
              <label>Charge Per Person</label>
              <input type="number" name="cost" id="ride_cost" min="0" max="10000" step="any" placeholder="0.00" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:6px; font-family:inherit; font-size:15px; outline:none;">
              <button type="button" id="btn-estimate-fare" style="position:absolute; right:8px; top:32px; background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:6px 12px; border-radius:4px; font-size:12px; cursor:pointer; display:flex; align-items:center; gap:4px; font-weight:700; transition:0.2s;"><i class="material-icons" style="font-size:14px;">auto_awesome</i> AI Suggest</button>
            </div>
          </div>
          <div id="ai-fare-msg" style="margin-top:-10px; margin-bottom:16px; font-size:12px; color:var(--muted); display:none;"></div>
          <button type="submit" name="save" class="ds-submit"><i class="material-icons" style="font-size:18px;">add_circle</i> Save Ride</button>
        </form>
      </div>
    </div>
  </main>
  <footer class="ds-footer"><p>&copy; <?php echo date('Y'); ?> <a href="../index.php">CarShare</a></p></footer>
  <script src="../assets/js/core/jquery.min.js"></script>
  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
  <script>
    let map = L.map('map').setView([22.2587, 71.1924], 6); // Default center Gujarat
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: 'OpenStreetMap contributors'
    }).addTo(map);
    
    // Fix blank map issue
    setTimeout(function(){ map.invalidateSize(); }, 500);

    let routingControl = null;

    async function geocode(city) {
      if (!city || city.trim() === '') return null;
      try {
        // Appending 'India' helps Nominatim find local cities much more reliably and handles minor typos better.
        let query = city.toLowerCase().includes('india') ? city : city + ', India';
        const response = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(query));
        const data = await response.json();
        if (data && data.length > 0) {
          return L.latLng(data[0].lat, data[0].lon);
        }
      } catch(e) { console.error("Geocoding failed for " + city); }
      return null;
    }

    async function updateRoute() {
      const start = $("#r_start").val(); 
      const via = $("#r_via").val();
      const end = $("#r_end").val();

      $("#map-error").text(""); // Clear errors

      // Ensure hidden city select matches start
      if (start) {
          let matched = false;
          $("#citySelect option").each(function() {
              if ($(this).val().toLowerCase() === start.trim().toLowerCase()) {
                  $("#citySelect").val($(this).val());
                  matched = true;
              }
          });
      }

      if (!start) return;

      const startCoord = await geocode(start);
      if (!startCoord) {
          $("#map-error").text("Could not find starting location on map.");
          return;
      }

      if (!end) {
        if (routingControl) {
            map.removeControl(routingControl);
            routingControl = null;
        }
        map.setView(startCoord, 12);
        return;
      }

      const endCoord = await geocode(end);
      if (!endCoord) {
          $("#map-error").text("Could not find destination location on map.");
          return;
      }

      let viaCoord = null;
      if (via) {
          viaCoord = await geocode(via);
          if (!viaCoord) {
              $("#map-error").text("Could not find via location. Showing direct route.");
          }
      }

      let waypoints = [startCoord];
      if (viaCoord) waypoints.push(viaCoord);
      waypoints.push(endCoord);

      if (routingControl) {
          routingControl.setWaypoints(waypoints);
      } else {
          routingControl = L.Routing.control({
            waypoints: waypoints,
            routeWhileDragging: false,
            addWaypoints: false,
            fitSelectedRoutes: true,
            show: false,
            lineOptions: { styles: [{color: '#0ea5e9', weight: 6, opacity: 0.8}] },
            createMarker: function(i, wp, nWps) {
                // Keep default markers
                return L.marker(wp.latLng);
            }
          }).addTo(map);
      }
    }

    let mapTimeout = null;
    $("#r_start, #r_via, #r_end").on("input change", function() {
      clearTimeout(mapTimeout);
      mapTimeout = setTimeout(updateRoute, 1500); // 1.5s debounce to avoid rate limiting
    });

    $(document).ready(function() {
      updateRoute();
    });

    $("#btn-estimate-fare").click(function() {
      var origin = $("input[name='r_start']").val();
      var destination = $("input[name='r_end']").val();
      
      if (!origin || !destination) {
        alert("Please enter both 'Ride Start From' and 'Ride End To' before asking the AI for a price suggestion.");
        return;
      }
      
      var btn = $(this);
      btn.html('<i class="material-icons" style="font-size:14px; animation:spin 1s linear infinite;">autorenew</i> Calculating...');
      $("#ai-fare-msg").hide();
      
      $.ajax({
        url: 'api_fare_estimator.php',
        method: 'POST',
        data: { origin: origin, destination: destination },
        success: function(res) {
          btn.html('<i class="material-icons" style="font-size:14px;">auto_awesome</i> AI Suggest');
          if (res.estimated_price) {
            $("#ride_cost").val(res.estimated_price);
            $("#ai-fare-msg").show().html('<strong style="color:var(--green);">AI Suggestion Applied:</strong> ' + res.reason);
            $("#ride_cost").css("box-shadow", "0 0 8px rgba(46, 125, 50, 0.5)");
            setTimeout(() => $("#ride_cost").css("box-shadow", "none"), 1500);
          } else if (res.error) {
            alert("AI Error: " + res.error);
          }
        },
        error: function() {
          btn.html('<i class="material-icons" style="font-size:14px;">auto_awesome</i> AI Suggest');
          alert("Failed to connect to the AI estimator.");
        }
      });
    });
  </script>
  <style>
    @keyframes spin { 100% { transform: rotate(360deg); } }
    .leaflet-routing-container { display: none !important; }
  </style>
<?php include 'chatbot.php'; ?>
</body>
</html>

