<?php
require '../config.php';

$from = isset($_POST['from_city']) ? mysqli_real_escape_string($con, $_POST['from_city']) : '';
$to = isset($_POST['to_city']) ? mysqli_real_escape_string($con, $_POST['to_city']) : '';
$date = isset($_POST['date']) ? mysqli_real_escape_string($con, $_POST['date']) : '';
$time = isset($_POST['time']) ? mysqli_real_escape_string($con, $_POST['time']) : '';
$persons = isset($_POST['persons']) ? (int)$_POST['persons'] : 0;

$query = "SELECT * FROM car_ride WHERE 1=1";

if (!empty($from)) {
    $query .= " AND (r_from LIKE '%$from%' OR city LIKE '%$from%')";
}
if (!empty($to)) {
    $query .= " AND (r_to LIKE '%$to%' OR city LIKE '%$to%')";
}
if (!empty($date)) {
    $query .= " AND date = '$date'";
}
if (!empty($time)) {
    $query .= " AND start_time >= '$time'";
}

$query .= " ORDER BY add_time DESC";
$ride = mysqli_query($con, $query);

$ride_num = mysqli_num_rows($ride);

if($ride_num > 0) {
    $rides_list = [];

    while ($r_data = mysqli_fetch_assoc($ride)) {
        // Calculate available seats
        $avil_p = mysqli_query($con,"SELECT sum(`person`) AS total FROM ride_book WHERE ride_id='$r_data[r_id]' ");
        $avil_d = mysqli_fetch_assoc($avil_p);
        $booked_seats = ($avil_d['total'] == '') ? 0 : $avil_d['total'];
        $avail = $r_data['seat'] - $booked_seats;
        
        if ($avail > 0 && ($persons == 0 || $avail >= $persons)) {
            // Fetch driver info
            $driver_q = mysqli_query($con, "SELECT name FROM users WHERE id='".$r_data['owner']."'");
            $driver_row = mysqli_fetch_assoc($driver_q);
            $driver_name = $driver_row ? $driver_row['name'] : 'Unknown Driver';

            // Fetch rating
            $rev_q = mysqli_query($con, "SELECT AVG(rating) as avg_rating, COUNT(review_id) as total_rev FROM reviews WHERE reviewee_id='".$r_data['owner']."'");
            $rev_data = mysqli_fetch_assoc($rev_q);
            $avg_rating = $rev_data['total_rev'] > 0 ? round($rev_data['avg_rating'], 1) : 0;
            $total_rev = $rev_data['total_rev'];

            // SMART ALGORITHM: Calculate Match Score
            // Score = (Rating * 10) + (min(Reviews, 10)) - (Price/100)
            $score = ($avg_rating * 10) + min($total_rev, 10) - ($r_data['ppc'] / 100);

            $rides_list[] = [
                'data' => $r_data,
                'avail' => $avail,
                'driver_name' => $driver_name,
                'avg_rating' => $avg_rating,
                'total_rev' => $total_rev,
                'score' => $score
            ];
        }
    }

    // Sort by score (highest first)
    usort($rides_list, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    if (count($rides_list) > 0) {
        $first = true;
        foreach ($rides_list as $ride_item) {
            $r_data = $ride_item['data'];
            
            // Rating HTML
            $rating_html = '<span style="color:var(--muted); font-size:12px;">(No ratings yet)</span>';
            if($ride_item['total_rev'] > 0) {
                $rating_html = '<span style="color:#F28D5B; font-weight:700; font-size:14px; display:inline-flex; align-items:center; gap:2px;"><i class="material-icons" style="font-size:16px;">star</i> '.$ride_item['avg_rating'].' <span style="font-weight:400; font-size:12px; color:var(--muted);">('.$ride_item['total_rev'].' reviews)</span></span>';
            }

            // BEST MATCH Highlight
            $card_style = "margin-bottom:16px;";
            $best_match_html = "";
            if ($first) {
                $card_style = "margin-bottom:24px; border:2px solid var(--lime); box-shadow:0 8px 24px rgba(217, 243, 107, 0.2);";
                $best_match_html = '<div style="background:var(--green); color:var(--lime); text-align:center; padding:6px; font-weight:700; font-size:12px; letter-spacing:1px; text-transform:uppercase; border-radius: 6px 6px 0 0;"><i class="material-icons" style="font-size:14px; vertical-align:middle; margin-right:4px;">auto_awesome</i> SMART RIDE RECOMMENDATION &mdash; BEST MATCH</div>';
                $first = false;
            }

            echo '
            <div class="card" style="'.$card_style.'">
              '.$best_match_html.'
              <div class="card-body" style="padding:16px 20px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; border-bottom:1px solid #eee; padding-bottom:12px;">
                  <div>
                    <div style="font-weight:700; font-size:18px; color:var(--ink); margin-bottom:4px;"><i class="material-icons" style="font-size:20px; vertical-align:middle; color:var(--green); margin-right:4px;">account_circle</i> '.$ride_item['driver_name'].'</div>
                    <div>'.$rating_html.'</div>
                  </div>
                  <div style="text-align:right;">
                    <div style="font-size:12px; color:var(--muted); text-transform:uppercase; font-weight:600; margin-bottom:2px;">Fair Price</div>
                    <div style="font-weight:700; font-size:24px; color:#4A8A62; line-height:1;">&#8377;'.$r_data['ppc'].'</div>
                  </div>
                </div>
                
                <table class="table" style="margin-bottom:16px;">
                  <tr>
                    <td style="color:var(--muted); border:0; padding:8px 0; width:120px;"><i class="material-icons" style="font-size:16px; vertical-align:middle; margin-right:4px;">route</i> Route</td>
                    <td style="font-weight:600; border:0; padding:8px 0;">'.$r_data['r_from'].' &rarr; '.$r_data['r_to'].'</td>
                  </tr>
                  <tr>
                    <td style="color:var(--muted); border-bottom:1px solid #eee; padding:8px 0;"><i class="material-icons" style="font-size:16px; vertical-align:middle; margin-right:4px;">alt_route</i> Via</td>
                    <td style="border-bottom:1px solid #eee; padding:8px 0;">'.$r_data['r_via'].'</td>
                  </tr>
                  <tr>
                    <td style="color:var(--muted); border-bottom:1px solid #eee; padding:8px 0;"><i class="material-icons" style="font-size:16px; vertical-align:middle; margin-right:4px;">airline_seat_recline_normal</i> Available Seats</td>
                    <td style="border-bottom:1px solid #eee; padding:8px 0;"><strong>'.$ride_item['avail'].'</strong>/'.$r_data['seat'].'</td>
                  </tr>
                  <tr>
                    <td style="color:var(--muted); border:0; padding:8px 0;"><i class="material-icons" style="font-size:16px; vertical-align:middle; margin-right:4px;">schedule</i> Time & Date</td>
                    <td style="font-weight:600; border:0; padding:8px 0;">'.date('h:i A', strtotime($r_data['start_time'])).' &middot; '.date('M d, Y', strtotime($r_data['date'])).'</td>
                  </tr>
                </table>
              </div>
              <div style="padding:0 20px 20px; text-align:center;">
                <a href="confirm_ride.php?ride='.$r_data['r_id'].'" class="ds-cta" style="display:flex; justify-content:center; align-items:center; text-decoration:none; padding:12px; width:100%; border-radius:8px;">
                  REQUEST RIDE
                </a>
              </div>
            </div>';
        }
    } else {
        echo'<div class="ds-empty"><i class="material-icons">event_busy</i><p>No available rides found for this city.</p></div>';
    }
}
else
{
	echo'<div class="ds-empty"><i class="material-icons">location_off</i><p>There are no rides available from this city right now.</p></div>';
}
?>
