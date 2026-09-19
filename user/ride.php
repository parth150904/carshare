<?php
require '../config.php';
session_start();
if (!isset($_SESSION['id'])) { header("location:login.php"); exit; }
$user = mysqli_query($con,"SELECT * from users WHERE id='$_SESSION[id]'");
$user_row = mysqli_fetch_assoc($user);
?>
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
  <style>
    @keyframes spin { 100% { transform: rotate(360deg); } }
  </style>
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
    <h2>Find a Ride</h2>
    <p class="ds-hero-email">Select your city to browse available rides</p>
  </section>
  <main class="ds-main">
    <div class="ds-form-card">
      <div class="ds-form-header"><i class="material-icons" style="vertical-align:middle;margin-right:8px;">search</i> Search Rides</div>
      <div class="ds-form-body">
        <div style="display:flex; gap:16px; flex-wrap:wrap; margin-bottom:16px;">
          <div class="ds-field" style="flex:1; min-width:200px;">
            <label>Leaving From</label>
            <input type="text" id="from_city" list="city-list" placeholder="Origin city..." autocomplete="off" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:6px; font-family:inherit; font-size:15px; outline:none; background:#f9fafa;">
          </div>
          <div class="ds-field" style="flex:1; min-width:200px;">
            <label>Going To</label>
            <input type="text" id="to_city" list="city-list" placeholder="Destination city..." autocomplete="off" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:6px; font-family:inherit; font-size:15px; outline:none; background:#f9fafa;">
          </div>
        </div>
        <div style="display:flex; gap:16px; flex-wrap:wrap;">
          <div class="ds-field" style="flex:1; min-width:150px;">
            <label>Date</label>
            <input type="date" id="search_date" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:6px; font-family:inherit; font-size:15px; outline:none; background:#f9fafa;">
          </div>
          <div class="ds-field" style="flex:1; min-width:150px;">
            <label>Time (After)</label>
            <input type="time" id="search_time" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:6px; font-family:inherit; font-size:15px; outline:none; background:#f9fafa;">
          </div>
          <div class="ds-field" style="flex:1; min-width:150px;">
            <label>Persons</label>
            <input type="number" id="search_persons" placeholder="1" min="1" max="10" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:6px; font-family:inherit; font-size:15px; outline:none; background:#f9fafa;">
          </div>
        </div>
        <datalist id="city-list">
          <?php 
          $city_q = mysqli_query($con,"SELECT * FROM city ORDER BY city ASC"); 
          while ($city = mysqli_fetch_assoc($city_q)) { 
              echo '<option value="'.htmlspecialchars($city['city']).'">'; 
          } 
          ?>
        </datalist>
      </div>
    </div>
    <div id="data" style="margin-top:24px;"></div>
  </main>
  <footer class="ds-footer"><p>&copy; <?php echo date('Y'); ?> <a href="../index.php">CarShare</a></p></footer>
  <script src="../assets/js/core/jquery.min.js"></script>
  <script>
    let timeout = null;
    
    function fetchRides() {
      var fromCity = $("#from_city").val().trim();
      var toCity = $("#to_city").val().trim();
      var date = $("#search_date").val();
      var time = $("#search_time").val();
      var persons = $("#search_persons").val();
      
      $("#data").show().html('<div style="text-align:center; padding:40px; color:#888;"><i class="material-icons" style="font-size:32px; animation:spin 1s linear infinite;">autorenew</i><p>Loading rides...</p></div>');
      $.ajax({
        url: 'ride_serch.php',
        method: 'POST',
        data: {
            'from_city': fromCity, 
            'to_city': toCity,
            'date': date,
            'time': time,
            'persons': persons
        },
        success: function(response) {
          $("#data").html(response);
        },
        error: function(xhr, status, error) {
          $("#data").html('<div class="ds-empty" style="color:red;"><i class="material-icons">error</i><p>Failed to load rides. Error: ' + error + '</p></div>');
        }
      });
    }

    // Load all rides by default
    $(document).ready(function() {
      fetchRides();
    });

    $("#from_city, #to_city, #search_date, #search_time, #search_persons").on("input change", function(){
      clearTimeout(timeout);
      timeout = setTimeout(function() {
        fetchRides();
      }, 300); // 300ms debounce
    });
  </script>
<?php include 'chatbot.php'; ?>
</body>
</html>