<?php
include 'config.php';
?>

<html>

<head>
  <title>Order Request</title>
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
  <!-- MDB -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet" />
</head>

<body style="background-color: #FBFBFB;" class="text-primary">
  <section class="mb-1">
    <div class="card">
      <div class="card-header py-5">
        <h1 class="mb-0 text-center"><strong>Germany Highway Traffic Status</strong></h1>
      </div>
    </div>
  </section>

  <main>

  
    <div class="container pt-4">
      <!-- Section-->
      <section class="mb-1">
        <div class="card">
          <div class="card-header py-4">
            <h5 class="mb-0 text-center"><strong>Highway Status</strong></h5>
          </div>
        </div>
        <!-- Section:  -->
        <section>
          <!-- Section: Row -->
          <div class="row mt-3" style="display: flex; justify-content: center;">
            <section style="text-align: center">
              <table class="table table-hover" style="text-align: center; align-self: center;">
                <thead>
                  <tr>
                    <th scope="col">Time Slot</th>
                    <th scope="col">No Lane Changes</th>
                    <th scope="col">One Total Lane Change</th>
                    <th scope="col">Two Total Lane Change</th>
                    <th scope="col">Average Speed of the Highway</th>
                    <th scope="col">Cars Direcion 1</th>
                    <th scope="col">Cars Direction 2</th>
                    <th scope="col">Trucks Direction 1</th>
                    <th scope="col">Trucks Direction 2</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><?php echo '8:38 AM - 8:53 AM' //$time 
                        ?></td>
                    <td><?php echo $no_lane_changes ?></td>
                    <td><?php echo $one_lane_changes ?></td>
                    <td><?php echo $two_lane_changes ?></td>
                    <td><?php echo $mean_value ?></td>
                    <td id="car_direction1"><?php echo $car_direction1 ?></td>
                    <td id="car_direction2"><?php echo $car_direction2 ?></td>
                    <td id="truck_direction1"><?php echo $truck_direction1 ?></td>
                    <td id="truck_direction2"><?php echo $truck_direction2 ?></td>
                  </tr>
                </tbody>
              </table>

              <!-- Section-->
              <section class="mb-1">
                <div class="card" style="margin-top: 50px;">
                  <div class="card-header py-4">

                    <h5 class="mb-0 text-center" style="color: red"><strong>Congestion Status</strong></h5>
                  </div>
                </div>
                <!-- Section:  -->
                <section class="mt-4">
                  <table class="table table-hover" style="text-align: center; align-self: center;">
                    <thead>
                      <tr>
                        <th scope="col">Time Slot</th>
                        <th scope="col">Total Lane Changes</th>
                        <th scope="col">Average Highway Speed</th>
                        <th scope="col">Direction 1 Traffic Status</th>
                        <th scope="col">Direction 2 Traffic Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><?php echo '8:38 AM - 8:53 AM' //$time 
                            ?></td>
                        <td id="laneChanges"><strong><?php echo $totalLaneChanges ?></strong></td>
                        <td id="mean"><strong><?php echo $mean_value ?></strong></td>
                        <td id="direction1"><strong><?php echo $direction1Status ?></strong></td>
                        <td id="direction2"><strong><?php echo $direction2Status  ?></strong></td>
                      </tr>
                    </tbody>
                  </table>
          </div>
        </section>
    </div>
  </main>
</body>
<script>
  //Change the color of the table row based on the congestion status
  var table = document.getElementById("laneChanges");
  var mean = document.getElementById("mean");
  var direction1 = document.getElementById("direction1");
  var direction2 = document.getElementById("direction2");

  var carDirection1 = document.getElementById("car_direction1");
  var carDirection2 = document.getElementById("car_direction2");
  var truckDirection1 = document.getElementById("truck_direction1");
  var truckDirection2 = document.getElementById("truck_direction2");

  var totalDirection1 = parseInt(carDirection1.innerHTML) + parseInt(truckDirection1.innerHTML);
  var totalDirection2 = parseInt(carDirection2.innerHTML) + parseInt(truckDirection2.innerHTML);
  
 //log the values
  console.log(direction1);

  if (totalDirection1 > 450) {
    direction1.style.color = "red";
  } else {
    direction1.style.color = "green";
  }

  if (totalDirection2 > 450) {
    direction2.style.color = "red";
  } else {
    direction2.style.color = "green";
  }

  if (mean.innerHTML > 60) {
    mean.style.color = "green";
  } else {
    mean.style.color = "red";
  }
  
</script>
</html>
<!-- MDB -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>