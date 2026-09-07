<?php
require '../config.php';
session_start();

/*function avil($rid)
{
  GLOBAL $con; 
  $avil_p = mysqli_query($con,"SELECT sum(`person`) AS total FROM ride_book WHERE ride_id='$rid' ");
  $avil_d = mysqli_fetch_assoc($avil_p);

  if ($avil_d['total'] =='')
  {
    echo "0";
  }
  else
  {
  echo $avil_d['total'];
  }
}*/

$car_ride = mysqli_query($con,"SELECT * FROM car_ride WHERE owner='$_SESSION[id]' ORDER BY add_time DESC");
while ($car_Rdata = mysqli_fetch_assoc($car_ride)) 
{
  
  $avil_p = mysqli_query($con,"SELECT sum(`person`) AS total FROM ride_book WHERE ride_id='$car_Rdata[r_id]' ");
  $avil_d = mysqli_fetch_assoc($avil_p);

  if ($avil_d['total'] =='')
  {
    $avil = 0;
  }
  else
  {
    $avil = $avil_d['total'];
  }

	echo '
	 <div class="card">
                  <div class="card-body">
                    <table class="table table-sm">
                      <tr>
                        <td align="left">Ride</td>
                        <td>'.$car_Rdata['r_from'].' to '.$car_Rdata['r_to'].'</td>
                      </tr>
                      <tr>
                        <td align="left">Via</td>
                        <td>'.$car_Rdata['r_via'].'</td>
                      </tr>
                      <tr>
                        <td align="left">Ride</td>
                        <td>'.$car_Rdata['ride_type'].'</td>
                      </tr>
                      <tr>
                        <td align="left">Ride time</td>
                        <td>'.$car_Rdata['start_time'].' to '.$car_Rdata['end_time'].'</td>
                      </tr>
                       <tr>
                        <td align="left">Ride date</td>
                        <td>'.$car_Rdata['date'].'</td>
                      </tr>
                       <tr>
                        <td align="left">Charge Per Person</td>
                        <td>'.$car_Rdata['ppc'].'</td>
                      </tr>
                      <tr>
                        <td align="left">Total Booked Seat</td>
                        <td>'.$avil.'/'.$car_Rdata['seat'].'</td>
                      </tr>
                    </table>
                  </div>
                  <center><button class="btn btn-danger" style="width:95% !important;"  onclick="cancel_ride(`'.$car_Rdata['r_id'].'`)">Cancel ride</button></center>
                </div>
	';
}
if (isset($_POST)) {
  json_encode($_POST);
  if (isset($_POST['c_id'])) {
      mysqli_query($con,"DELETE FROM `car_ride` WHERE r_id=$_POST[c_id]");
  }
}
 ?>