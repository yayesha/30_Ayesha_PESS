<?php 

// create var btnSearchClicked to chek if btnSearch button has been clicked
$btnSearchClicked = isset($_POST['btnSearch']);
$statuses = [];
$car = null;

if ($btnSearchClicked == true) {
	require_once 'db.php';

	$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

	if ($conn -> connect_error) {
		die("connection failed: " . $conn->connect_error);
	}

	$carId = $_POST['patrolCarId']

	$sql = "SELECT * FROM patrolcar WHERE patrolcar_id = '" .$carId . "'";
	$result = $conn -> query($sql);
    
    // if the patrolcar_id exists in db
	if ($row = $result -> fetch_assoc()) {
		$id = $row['patrolCar_Id'];
		$statusId = $row['patrolcar_status_id'];
		$car = ["id" => $id, "statusId" => $statusId];
	}

	$sql = "SELECT * FROM patrolcar_status " ;
	$result = $conn -> query($sql);

	while ($row = $result -> fetch_assoc()) {
		$id = $row['patrolCar_status_Id'];
		$desc = $row['patrolcar_status_desc'];
		$status = ["id" => $id, "desc" => $desc];
		array_push($statuses, $status);
	}

	$conn -> close();
}

?>

<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Update Patrol Car Status</title>
		<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="container" style="width: 80%">
			<!-- Use php require_once expression to include header image and navigation bar from nav.php -->
			<?php require_once 'nav.php'; ?>
			<aside>
				<h2>BONUS POINTS</h2>
				<ul>
					<li>To create a notification e.g alert or text, when status has been updated.</li>
				</ul>
				<p></p>
			</aside>
			<!-- Create section container to place web form -->
			<section style="margin-top: 20px">
			<!-- Create web form with Patrol Car Number input feilds -->
			<form action="update.php" method="POST">
               <?php 
                    if ($car != null) {\
                   // Row to display Patrol Car Number 
                   echo "<div class='form-group row'>";
                   echo '<label for="patrolCarId" class="col-sm-4 col-form-label">Patrol Car's Number</label>
                   echo ' <div class="col-sm-8">';
                   echo $car ['id'];
                   echo '   <input type="hidden" name="patrolCarId" id="patrolCarId" value="'.$car['id'].'">';
                   echo '  </div>';
                   echo ' </div>';
                   echo '<div class="form-group row">';
                   echo '   <label for="carNo" class="col-sm-4 col-form-label">Patrol Car status</label>';
                   echo '  <div class="col-sm-8">';
                   echo '      <select id="carStatus" class"form-control" name="carStatus">';
                          $totalStatus = count($statuses);
                          for ($i=0; $i <$totalStatus ; $i++) { 
                          	$status = $statuses[$i];
                          	$selected = "";
                          	if ($status['id'] == $car ['statusId']) {
                          		$selected = ' selected="selected"';
                          	}
                          	echo '<option value"'.$status['id'].'"'.$selected.">".$status['desc'].'</option>';
                          	$selected = "";
                          }

                   <input type="text" name="patrolCarId" class="form-control" id="patrolCarId">
               }
                </div> 
            </div>
            }
            ?>

            
             </form>

			   </section>
			<!-- Footer -->

                <footer class="page-footer font-small blue pt-4 footer-copyright text-center py-3">&copy; 2021 Copyright
                </footer>
