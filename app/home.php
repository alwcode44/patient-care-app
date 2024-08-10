<?php 
session_start();

include("php/config.php");

// Redirect to login page if the user is not logged in
if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit(); // Stop further execution
}

$id = $_SESSION['id'];
$query = mysqli_query($con, "SELECT * FROM users WHERE Id=$id");
$user_details = mysqli_fetch_assoc($query);
$patient_details_submitted = $user_details['patient_details_submitted'];
$email = $user_details['Email'];
$username = $user_details['Username'];

if (!$patient_details_submitted) {
    // If patient details not submitted, redirect to patient details form
    header("Location: patient_details_form.php");
    exit(); // Stop further execution
}

$emergency_fetch =mysqli_query($con, "SELECT emergency_contact_name, emergency_contact_relationship, emergency_contact_phone FROM patient_details WHERE user_id = $id");
$em_details = mysqli_fetch_assoc($emergency_fetch);
$em_name = $em_details['emergency_contact_name'];
$em_rel = $em_details['emergency_contact_relationship'];
$em_ph = $em_details['emergency_contact_phone'];

// Fetch additional patient details from the database
$query_details = mysqli_query($con, "SELECT * FROM patient_details WHERE user_id=$id");
$details = mysqli_fetch_assoc($query_details);

// Extracting patient details
$gender = $details['gender'];
$height = $details['height'];
$weight = $details['weight'];
$blood_type = $details['blood_type'];
$additional_info = $details['additional_info'];

$current_date = date('Y-m-d');
$health_data_query = mysqli_query($con, "SELECT * FROM health_data WHERE user_id=$id AND date='$current_date'");
$health_data = mysqli_fetch_assoc($health_data_query);
$sugar_level = isset($health_data['sugar_level']) ? $health_data['sugar_level'] : "Not available";
$blood_pressure = isset($health_data['blood_pressure']) ? $health_data['blood_pressure'] : "Not available";
$water_consumption = isset($health_data['water_consumption']) ? $health_data['water_consumption'] : "Not available";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Home</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php" class="trucare"></a></p>
        </div>
        <div class="right-links">
            <?php 
            echo "<a href='edit.php?Id=$id' style='color: white;'>Edit Profile</a>";
            ?>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <main>
        <div class="main-box top">
            <div class="top">
                <div class="top">
                    <div class="box">
                        <?php
                        $photo_path = $user_details['Photo'];
                        ?>
                        <img src="<?php echo $photo_path; ?>" alt="User Photo" style="max-width: 100%; max-height: 200px;">
                        <div class="bottom">
                        <p><b><?php echo $username ?></b></p>
                        <button class="emergency-button" onclick="showEmergencyPopup()">Emergency Contact</button></div>

<!-- Popup for emergency contact information -->
<div id="emergencyPopup" class="popup">
    <h2>Emergency Contact Information</h2>
    <p><strong>Name:</strong> <?php echo $em_name ?></p>
    <p><strong>Relationship:</strong> <?php echo $em_rel ?></p>
    <p><strong>Phone Number:</strong> <?php echo $em_ph ?></p>
    <button onclick="hideEmergencyPopup()">Close</button>
</div>
                    </div>
                </div>
                <div class="top">
                    <div class="box">
                        <div class="box2">
                        <p><b>General Information:</b></p> 
                            <p>Name: <?php echo $username ?></p>
                            <p>Email: <?php echo $email ?></p>
                            <p>Gender: <?php echo $gender ?></p> 
                            <p>Height: <?php echo $height ?> cm</p> 
                            <p>Weight: <?php echo $weight ?> kg</p>
                            <p>Blood Type: <?php echo $blood_type ?></p> 
                            <p>Additional Info: <?php echo $additional_info ?></p>
                            <p><b>Today's Health update:</b></p> 
                            <p>Sugar Level: <?php echo $sugar_level ?></p> 
                            <p>Blood Pressure: <?php echo $blood_pressure ?></p>
                            <p>Cholestrol: <?php echo $water_consumption ?></p>
<!-- Button to open the emergency popup -->


<script>
    // Function to show the emergency popup
    function showEmergencyPopup() {
        document.getElementById("emergencyPopup").style.display = "block";
    }

    // Function to hide the emergency popup
    function hideEmergencyPopup() {
        document.getElementById("emergencyPopup").style.display = "none";
    }
</script>
       </div>
                    </div>
                </div>
            </div>
            <div class="bottom">
                <div class="box">
                    <p><b>Available Options are below:</b></p> 
                </div>
            </div>
            <div class="bottom">
    <div class="box">
    <div class="bottom">
        <p><b>Medicines</b></p>
        <a href="add_medicine.php"><button class="btn2">Add Medicines</button></a>
        <a href="view_medicine.php"><button class="btn2">View Medicines</button></a>
        <a href="daily_medicines.php"><button class="btn2">Daily Medicine</button></a>
    </div>
</div>
</div>
<div class="bottom">
    <div class="box">
    <div class="bottom">
        <p><b>Doctor</b></p>
        <a href="report.php"><button class="btn2">Doctor Reports</button></a>
        <a href="display_patient_details.php"><button class="btn2">Patient Details</button></a>
        <a href="edit_display_patient_details.php"><button class="btn2">Edit Patient Details</button></a>
        <a href="daily_update.php"><button class="btn2">Daily Health Tracker</button></a>
    </div>
</div>
</div>
<div class="bottom">
    <div class="box">   
    <div class="bottom">
        <p><b>Appointments</b></p>
        <a href="appoinment.php"><button class="btn2">Add Appointment Schedule</button></a>
        <a href="view_appoinments.php"><button class="btn2">View Appointment Schedule</button></a>
    </div>
</div>
</div>
<div class="bottom">
    <div class="box">
    <div class="bottom">
        <p><b>Others</b></p>
        <a href="tips.php"><button class="btn2">Medical Tips</button></a>
        <a href="taxi.php"><button class="btn2">Local Taxi's</button></a>
        <a href="hospital.php"><button class="btn2">Hospitals P.No</button></a>
        <a href="local_hospitals.php"><button class="btn2">Local Hospitals Map</button></a>
    </div>
</div>
</div>
    </main>
</body>
</html>
