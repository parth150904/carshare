<?php
// Test Case 1: Database Connection & Architecture Test
require_once __DIR__ . '/../config.php';

echo "<h3>Test Case 1: Database Connection</h3>";

if ($con) {
    echo "<p style='color:green;'>&#10004; PASS: Database connection successful.</p>";
    
    // Check if 'car_ride' table exists
    $result = mysqli_query($con, "SHOW TABLES LIKE 'car_ride'");
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color:green;'>&#10004; PASS: 'car_ride' table exists.</p>";
    } else {
        echo "<p style='color:red;'>&#10008; FAIL: 'car_ride' table does not exist.</p>";
    }

    // Check if 'users' table exists
    $result_users = mysqli_query($con, "SHOW TABLES LIKE 'users'");
    if (mysqli_num_rows($result_users) > 0) {
        echo "<p style='color:green;'>&#10004; PASS: 'users' table exists.</p>";
    } else {
        echo "<p style='color:red;'>&#10008; FAIL: 'users' table does not exist.</p>";
    }

} else {
    echo "<p style='color:red;'>&#10008; FAIL: Database connection failed. " . mysqli_connect_error() . "</p>";
}
?>

