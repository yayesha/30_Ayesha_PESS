<?php
// initialise variables to respective form data 
$callerName = $_POST['callerName'];
$contactNo = $_POST['contactNo'];
$locationofIncident = $_POST['locationofIncident'];
$typeofIncident = $_POST['typeofIncident'];
$descriptionofIncident = $_POST['descriptionofIncident'];

// start connection to database pessdb
require_once 'db.php';
//create a array var cars
$cars = [];
//create a new connection to db pessdb
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
//run sql query on db pessdb
$sql = "SELECT patrolcar.patrolcar_id, patrolcar_status.patrolcar_status_desc FROM patrolcar JOIN patrolcar_status ON patrolcar.patrolcar_status_id = patrolcar_status.patrolcar_status_id";
//create var result to contain result-set from SQL query
$result = $conn -> query($sql);
//use while loop to exract the column values from each row of the result-set3de
while ($row = $result -> fetch_assoc()) {
	//create var id to contain the column value patrolcar_id of a row
	$id = $row['patrolcar_id'];
	//create var status to contain the column value patrolcar_status_desc of a row
	$status = $row['patrolcar_status_desc'];
	//create array car to contain the column values of a row
	$car = ["id" => $id, "status" => $status];
	//using the array_push function to assign all rows of the result-set into array var cars
	array_push($cars, $car);
}

//create var btnDispatchedClicked to checked if btnDispatch button has been clicked
     $btnDispatchClicked = isset($_POST["btnDispatch"]);
//create var btnProcessCallClicked to checked if btnProcessCall button has been clicked
     $btnProcessCallClicked = isset($_POST["btnProcessCall"]);

//if both btnDispatch and btnProcessCall have not been clicked
  if($btnDispatchClicked == false && $btnProcessCallClicked == false) {
	//use header function to append error message in URL
	header("Location: logcall.php?message=error");
}

//if btnDispatch has been clicked
if($btnDispatchClicked == true) {
	//create var insertIncidentSuccess with inital value false
	$insertIncidentSuccess = false;
	//create var patrolcarDispatched to contain user input for cbCarSelection checkbox
	$patrolcarDispatched = $_POST["cbCarSelection"];
	//create var numPatrolcarDispatched and assign the no of values in patrolcarDispatched array using count function
	$numPatrolcarDispatched = count($patrolcarDispatched);
	//create var incidentStatus with inital value 0
	$incidentStatus = 0;

	if($numPatrolcarDispatched > 0) {
		$incidentStatus = '2'; //dispatched
	} else {
		$incidentStatus = '1'; //pending
	}

	// run SQL query on pessdb db
	$sql = "INSERT INTO incident (caller_name, phone_number,incident_type_id, incident_location, incident_desc, incident_status_id) VALUES ('".$callerName."', '".$contactNo."', '".$typeofIncident."', '".$locationofIncident."', '".$descriptionofIncident."', '".$incidentStatus."')";

		$insertIncidentSuccess = $conn -> query($sql);

		//display error message if unable to insert new row into incident table
		if($insertIncidentSuccess === false) {
			echo "Error: " .$sql. "<br>" .$conn -> error;
		}

		$incidentId = mysqli_insert_id($conn);
		//create var updateSuccess and var insertDispatchSuccess with initial value false
		$updateSuccess = false;
		$insertDispatchSuccess = false;

		//using for loop, update each patrolcar_id in patrolcar table found in patrolcarDispatched array
		for($i=0; $i<$numPatrolcarDispatched; $i++) {
			//for every elemetn of patrolcarDispatched array...
			$carId = $patrolcarDispatched[$i];

			//update patrolcar_status_id column in patrolcar table
			$sql = "UPDATE patrolcar SET patrolcar_status_id='1' WHERE patrolcar_id='" .$carId."'";
			$updateSuccess = $conn -> query($sql);
			//display error message if unable to update row in patrolcar table
			if ($updateSuccess === false) {
				echo "Error: " .$sql. "<br>" .$conn -> error;
			}

			$sql = "INSERT INTO dispatch (incident_id, patrolcar_id, time_dispatch) VALUES ('".$incidentId."', '".$carId."', NOW())";
				$insertDispatchSuccess = $conn -> query($sql);
				//display error message if unable to insert new row in dispatch table
				if($insertDispatchSuccess === false) {
					echo "Error: " .$sql. "<br>" .$conn -> error;
				}
		}

		$conn -> close();

		//if able to insert new row in incident and update patrolcar table nd insert new row in dispatch table
		if($insertIncidentSuccess === true && $updateSuccess === true && $insertDispatchSuccess === true) {
			header("Location: logcall.php?message=success&carId=".$carId);

		}
}


?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Dispatch</title>
	<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
</head>
<body>
	<div class="containter" style="width:80%">
		<!-- links to header image and navigation bar from nav.php-->
		<?php require_once 'nav.php'; ?>

		<!-- create section container to place web form-->
		<section style="margin-top:20px">
			<form action="dispatch.php" method="post">

				<!-- row to display caller's name-->
				<div class="form-group row">
					<label for="callerName" class="col-sm col-form-label"> Caller's Name</label>
					<div class="col-sm-8">
						<?php echo $callerName; ?>
						<input type="hidden" name="callerName" id="callerName" value="<?php echo $callerName; ?>">
					</div>
				</div>

				<!-- row to display contact number-->
				<div class="form-group row">
					<label for="contactNo" class="col-sm col-form-label"> Contact Number</label>
					<div class="col-sm-8">
						<?php echo $contactNo; ?>
						<input type="hidden" name="contactNo" id="contactNo" value="<?php echo $contactNo; ?>">
					</div>
				</div>

				<!-- row to display location of Incident-->
				<div class="form-group row">
					<label for="locationofIncident" class="col-sm col-form-label"> Location of Incident</label>
					<div class="col-sm-8">
						<?php echo $locationofIncident; ?>
						<input type="hidden" name="locationofIncident" id="locationofIncident" value="<?php echo $locationofIncident; ?>">
					</div>
				</div>

				<!-- row to display type of Incident and drop-down input-->
				<div class="form-group row">
					<label for="typeofIncident" class="col-sm col-form-label"> Type of Incident</label>
					<div class="col-sm-8">
						<?php 
						// create new connection to db
						$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
						//run SQL query on databse pessdb
						$sql = "SELECT incident_type_desc FROM incident_type WHERE incident_type_id = '".$typeofIncident."'";
						// create var result to contain the result-set from SQL query
						$result = $conn -> query($sql);
						// using a while loop to extract incident_type_desc from incident_type table
						while($row = $result -> fetch_assoc()) {
							$desc = $row['incident_type_desc'];
							echo $desc;
						}
						$conn->close();
						?>
						<input type="hidden" name="typeofIncident" id="typeofIncident" value="<?php echo $typeofIncident; ?>">
					</div>
				</div>

				<!-- row to display description of Incident-->
				<div class="form-group row">
					<label for="descriptionofIncident" class="col-sm col-form-label"> Description of Incident</label>
					<div class="col-sm-8">
						<?php echo $descriptionofIncident; ?>
						<input type="hidden" name="descriptionofIncident" id="descriptionofIncident" value="<?php echo $descriptionofIncident; ?>">
					</div>
				</div>

				<!-- row to display patrol cars to dispatch-->
				<div class="form-group row">
					<label for="patrolCars" class="col-sm col-form-label"> Choose Patrol Car(s)</label>
					<div class="col-sm-8">
						<table class="table table-striped">
							<tbody>
								<tr>
									<th scope="col">Car's Number</th>
									<th scope="col">Car's Status</th>
									<th scope="col"></th>
								</tr>
								<?php
									//use for loop to populate the table row with patrolcar details retrieved from array var cars
									for($i=0; $i<count($cars); $i++) {
										$car = $cars[$i];
										echo "<tr>";
										echo "<td>" .$car['id']. "</td>";
										echo "<td>" .$car['status']. "</td>";
										echo "<td>";
										echo "<input name='cbCarSelection[]' type='checkbox' value='".$car['id']."'>";
										echo "</td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</div>
				</div>

				<!-- row to display dispatch button-->
				<div class="form-group row">
					<div class="col-sm-4"></div>
					<div class="col-sm-8" style="text-align:center">
						<input type="submit" name="btnDispatch" class="btn btn-primary" value="Dispatch">
					</div>
				</div>

				<!--End of web form-->
			</form>
			<!--End of section-->
		</section>
		<!--footer-->
		<footer class="page-footer font-small blue pt-4 footer-copyright text-center py-3">
			&copy;2021 Copyright
			<a href="www.ite.edu.sg">ITE</a>
		</footer>
		<script type="text/javascript" src="js/jquery-3.5.0.min.js"></script>
		<script type="text/javascript" src="js/popper.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.js"></script>
	</div>
</body>
</html>