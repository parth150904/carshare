<?php 
//session_start();
require '../config.php';
session_start();
$car_ride = mysqli_query($con,"SELECT * FROM car_ride WHERE owner='$_SESSION[id]'");
while ($car_Rdata = mysqli_fetch_assoc($car_ride)) 
{
  $r_book = mysqli_query($con,"SELECT * FROM ride_book WHERE ride_id='$car_Rdata[r_id]' AND conform='----' ORDER BY booking_time DESC");
  while ($booking = mysqli_fetch_assoc($r_book)) 
  {
    $user = mysqli_query($con,"SELECT * FROM users WHERE id='$booking[book_by]' ");
    $user_row = mysqli_fetch_assoc($user);

    echo '
    <div class="card">
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted" style="color: green !important;">RIDE BOOKED</h6>
                    

                    <table class="table table-sm">
                      <tr>
                        <td align="left">Ride</td>
                        <td>'.$car_Rdata['r_from'].' to '.$car_Rdata['r_to'].'</td>
                      </tr>
                      <tr>
                        <td align="left">Booking By</td>
                        <td>'.$user_row['name'].'</td>
                      </tr>
                       <tr>
                        <td align="left">Contact Num</td>
                        <td>'.$user_row['mo_num'].'</td>
                      </tr>
                      <tr>
                        <td align="left">Booking time</td>
                        <td>'.$booking['booking_time'].'</td>
                      </tr>
                    </table>
                  </div>
                  <center><button class="btn btn-success" style="" onclick="confirm(`'.$booking['b_id'].'`)">Confirm Booking</button><button class="btn btn-danger" onclick="cancel(`'.$booking['b_id'].'`)">Cancel Booking</button></center>
                </div>
    ';

  }
}


if (isset($_POST)) {
  json_encode($_POST);
  if (isset($_POST['c_id'])) {
      mysqli_query($con,"UPDATE ride_book SET conform='Yes' WHERE b_id=$_POST[c_id]");
  }
  if (isset($_POST['cn_id'])) {
      mysqli_query($con,"UPDATE ride_book SET conform='No' WHERE b_id=$_POST[cn_id]");
  }
}

?>
           
                