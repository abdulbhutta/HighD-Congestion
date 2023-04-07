<?php
// DMBS here means database management system, like Cloud SQL
define('HOSTSPEC', '34.118.155.224');
define('USERNAME', 'usr');
define('PASSWORD', 'sofe4630u');
define('DATABASE_INSTANCE_NAME', 'Readings'); // Or the name of a database instance within your Cloud SQL instance.
define('PORT', 3306);
#define('SOCKET', '/cloudsql/[GOOGLE_CLOUD_PROJECT_NAME]:[GOOGLE_CLOUD_REGION]:[CLOUD_SQL_DBMS_INSTANCE_NAME]');

// Option 1. Object-oriented style...
$mysqli = new mysqli(HOSTSPEC, USERNAME, PASSWORD, DATABASE_INSTANCE_NAME, PORT);

//Make a query to 
$result = $mysqli->query("SELECT * FROM processedData");

//Print the result of the query

for ($row_no = $result->num_rows - 1; $row_no >= 0; $row_no--) {
    $result->data_seek($row_no);
    $row = $result->fetch_assoc();
    
    $time = $row['time'];
    $no_lane_changes = $row['no_lane_changes'];
    $one_lane_changes = $row['one_lane_change'];
    $two_lane_changes = $row['two_lane_change'];
    $mean_value = $row['mean_value'];
    $car_direction1 = $row['car_direction1'];
    $car_direction2 = $row['car_direction2'];
    $truck_direction1 = $row['truck_direction1'];
    $truck_direction2 = $row['truck_direction2'];
}

$totalLaneChanges = $one_lane_changes + $two_lane_changes;

if ($totalLaneChanges < 100){
    $laneCongestionStatus = "Uncongested";
}

else {
    $laneCongestionStatus = "Congested";
}

//Average velocity 

if ($mean_value < 50){
    $traffic = "Slow";
}

else {
    $traffic = "Good";
}

$totalDirection1 = $car_direction1 + $truck_direction1;
$totalDirection2 = $car_direction2 + $truck_direction2;

if ($totalDirection1 > 450) { 
    $direction1Status = "Traffic Congested";
} 
else
{
    $direction1 = "Traffic Flowing";
}

if ($totalDirection2 > 450) { 
    $direction2Status = "Traffic Congested";
} 
else
{
    $direction2 = "Traffic Flowing";
}

#print ($no_lane_changes . " " . $one_lane_changes . " " . $two_lane_changes . " " . $mean_value . " " . $car_direction1 . " " . $car_direction2 . " " . $truck_direction1 . " " . $truck_direction2);

// Option 2. Procedural style...
#$DBMSresource = mysqli_connect(HOSTSPEC, USERNAME, PASSWORD, DATABASE_INSTANCE_NAME, PORT);
#if (!$DBMSresource)
    // log and handle error, maybe exit...
