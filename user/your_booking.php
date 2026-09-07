<?php 
require '../config.php';
session_start();
 
$r_book = mysqli_query($con,"SELECT * FROM ride_book WHERE book_by='$_SESSION[id]' ORDER BY booking_time DESC ");
$found = false;

while ($booking = mysqli_fetch_assoc($r_book)) {
  $car_ride = mysqli_query($con,"SELECT * FROM car_ride WHERE r_id='$booking[ride_id]'");
  while ($car_Rdata = mysqli_fetch_assoc($car_ride)) {
    $found = true;
    $user = mysqli_query($con,"SELECT * FROM users WHERE id='$car_Rdata[owner]' ");
    $user_row = mysqli_fetch_assoc($user);

    $status_color = $booking['conform'] == 'Yes' ? '#4A8A62' : '#F28D5B';
    $status_text = $booking['conform'] == 'Yes' ? '&#10003; Confirmed' : 'Pending';

    echo '
    <div class="card" style="margin-bottom:16px;">
      <div class="card-body">
        <table class="table">
          <tr>
            <td>Ride</td>
            <td>'.$car_Rdata['r_from'].' &rarr; '.$car_Rdata['r_to'].'</td>
          </tr>
          <tr>
            <td>Date & Time</td>
            <td>'.$car_Rdata['date'].' ('.$car_Rdata['start_time'].' - '.$car_Rdata['end_time'].')</td>
          </tr>
          <tr>
            <td>Driver</td>
            <td>'.$user_row['name'].'</td>
          </tr>
          <tr>
            <td>Vehicle</td>
            <td>'.$car_Rdata['ride_type'].'</td>
          </tr>
          <tr>
            <td>Contact</td>
            <td>'.$user_row['mo_num'].'</td>
          </tr>
          <tr>
            <td>Status</td>
            <td style="color:'.$status_color.'; font-weight:700;">'.$status_text.'</td>
          </tr>
        </table>
      </div>
      <div style="padding:0 20px 20px; display:flex; gap:12px; align-items:stretch;">';
      
      if ($booking['conform'] == 'Yes') {
          echo '<a href="rate_user.php?ride_id='.$car_Rdata['r_id'].'&user_id='.$car_Rdata['owner'].'" class="ds-submit" style="flex:1; margin:0; padding:12px; font-size:14px; text-decoration:none;"><i class="material-icons" style="font-size:16px;">star_rate</i> Rate Driver</a>';
      }

      echo '
        <button class="ds-submit" style="flex:1; margin:0; padding:12px; background:rgba(242,141,91,.12); color:var(--orange); font-size:14px;" onclick="your_booking_cancel(`'.$booking['b_id'].'`)">Cancel Ride</button>
      </div>
    </div>';
  }
}

if (!$found) {
    echo '<div class="ds-empty"><i class="material-icons">event_busy</i><p>You have not booked any rides yet.</p></div>';
}

if (isset($_POST)) {
  if (isset($_POST['c_id'])) {
      mysqli_query($con,"DELETE FROM `ride_book` WHERE b_id=$_POST[c_id]");
  }
}
?>