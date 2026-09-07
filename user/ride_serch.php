<?php
json_encode($_POST);
require '../config.php';
$city = $_POST['city'];
$ride = mysqli_query($con,"SELECT * FROM car_ride WHERE city='$city' ORDER BY add_time DESC");
$ride_num = mysqli_num_rows($ride);
if($ride_num > 0)
{
while ($r_data = mysqli_fetch_assoc($ride)) {

  $avil_p = mysqli_query($con,"SELECT sum(`person`) AS total FROM ride_book WHERE ride_id='$r_data[r_id]' ");
  $avil_d = mysqli_fetch_assoc($avil_p);
if($ride_num > 0) {
  while ($r_data = mysqli_fetch_assoc($ride)) {

  if ($avil_d['total'] =='')
  {
    $avil = 0;
  }
  else
  {
    $avil = $r_data['seat']-$avil_d['total'];
  }
    $avil_p = mysqli_query($con,"SELECT sum(`person`) AS total FROM ride_book WHERE ride_id='$r_data[r_id]' ");
    $avil_d = mysqli_fetch_assoc($avil_p);

	echo '	 <div class="card">
                  <div class="card-body">
                    <table class="table table-sm">
                      <tr>
                        <td align="left">Ride</td>
                        <td>'.$r_data['r_from'].' to '.$r_data['r_to'].'</td>
                      </tr>
                      <tr>
                        <td align="left">Via</td>
                        <td>'.$r_data['r_via'].'</td>
                      </tr>
                       <tr>
                        <td align="left">Charge Per Person</td>
                        <td>'.$r_data['ppc'].'</td>
                      </tr>
                      <tr>
                        <td align="left">Avilable Seat</td>
                        <td><b>'.$avil.'</b>/'.$r_data['seat'].'</td>
                      </tr>
                      <tr>
                        <td align="left">Ride time</td>
                        <td>'.$r_data['start_time'].' to '.$r_data['end_time'].'</td>
                      </tr>
                       <tr>
                        <td align="left">Ride date</td>
                        <td>'.$r_data['date'].'</td>
                      </tr>
                    </table>
                  </div>
                  <center><a href="confirm_ride.php?ride='.$r_data['r_id'].'" class="btn btn-success" style="width:95% !important;">Book Now</a></center>
                </div>';
	echo '
  <div class="card">
    <div class="card-body">
      <table class="table">
        <tr>
          <td>Ride</td>
          <td>'.$r_data['r_from'].' → '.$r_data['r_to'].'</td>
        </tr>
        <tr>
          <td>Via</td>
          <td>'.$r_data['r_via'].'</td>
        </tr>
        <tr>
          <td>Charge/Person</td>
          <td style="font-weight:700;">₹'.$r_data['ppc'].'</td>
        </tr>
        <tr>
          <td>Available Seats</td>
          <td><strong>'.$avil.'</strong>/'.$r_data['seat'].'</td>
        </tr>
        <tr>
          <td>Ride Time</td>
          <td>'.$r_data['start_time'].' → '.$r_data['end_time'].'</td>
        </tr>
        <tr>
          <td>Ride Date</td>
          <td>'.$r_data['date'].'</td>
        </tr>
      </table>
    </div>
    <div style="padding:0 20px 18px; text-align:center;">
      <a href="confirm_ride.php?ride='.$r_data['r_id'].'" class="ds-cta" style="display:inline-flex; text-decoration:none;">
        <i class="material-icons" style="font-size:18px;">directions_car</i> Book Now
      </a>
    </div>
  </div>';
}
}
else
{
	echo'<div class="card"><div class="card-body">There is no any ride avilable on this city</div></div>';
    $avil = ($avil_d['total'] == '') ? 0 : ($r_data['seat'] - $avil_d['total']);
    
    // Fetch driver info and rating
    $driver_q = mysqli_query($con, "SELECT name FROM users WHERE id='".$r_data['owner']."'");
    $driver_row = mysqli_fetch_assoc($driver_q);
    $driver_name = $driver_row ? $driver_row['name'] : 'Unknown Driver';

	echo '
    $rev_q = mysqli_query($con, "SELECT AVG(rating) as avg_rating, COUNT(review_id) as total_rev FROM reviews WHERE reviewee_id='".$r_data['owner']."'");
    $rev_data = mysqli_fetch_assoc($rev_q);
    
    $rating_html = '<span style="color:var(--muted); font-size:12px;">(No ratings yet)</span>';
    if($rev_data['total_rev'] > 0) {
        $avg = round($rev_data['avg_rating'], 1);
        $rating_html = '<span style="color:#F28D5B; font-weight:600; font-size:14px; display:inline-flex; align-items:center; gap:2px;"><i class="material-icons" style="font-size:16px;">star</i> '.$avg.' ('.$rev_data['total_rev'].')</span>';
    }

    echo '
    <div class="card" style="margin-bottom:16px;">
      <div class="card-body">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; border-bottom:1px solid #eee; padding-bottom:12px;">
          <div style="font-weight:600; color:var(--ink);"><i class="material-icons" style="font-size:18px; vertical-align:middle; color:var(--green); margin-right:4px;">account_circle</i> '.$driver_name.'</div>
          <div>'.$rating_html.'</div>
        </div>
        <table class="table">
          <tr>
            <td>Ride</td>
            <td>'.$r_data['r_from'].' &rarr; '.$r_data['r_to'].'</td>
          </tr>
          <tr>
            <td>Via</td>
            <td>'.$r_data['r_via'].'</td>
          </tr>
          <tr>
            <td>Charge/Person</td>
            <td style="font-weight:700;">&#8377;'.$r_data['ppc'].'</td>
          </tr>
          <tr>
            <td>Available Seats</td>
            <td><strong>'.$avil.'</strong>/'.$r_data['seat'].'</td>
          </tr>
          <tr>
            <td>Ride Time</td>
            <td>'.$r_data['start_time'].' &rarr; '.$r_data['end_time'].'</td>
          </tr>
          <tr>
            <td>Ride Date</td>
            <td>'.$r_data['date'].'</td>
          </tr>
        </table>
      </div>
      <div style="padding:0 20px 18px; text-align:center;">
        <a href="confirm_ride.php?ride='.$r_data['r_id'].'" class="ds-cta" style="display:inline-flex; text-decoration:none;">
          <i class="material-icons" style="font-size:18px;">directions_car</i> Book Now
        </a>
      </div>
    </div>';
  }
} else {
  echo '
  <div class="ds-empty">
    <i class="material-icons" style="font-size:48px; color:#c9e8d8; margin-bottom:12px; display:block;">search_off</i>
    <p>No rides available in this city yet.</p>
  </div>';
}
 ?>
?>
