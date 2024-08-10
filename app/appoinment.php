<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    echo "User not logged in";
    exit(); // Stop further execution
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['id'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (isset($_SESSION['id'])) {
        // Get form data
        $doctor_name = $_POST['doctor_name'];
        $hospital_name = $_POST['hospital_name'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];

        // Prepare and execute SQL statement
        $sql = "INSERT INTO appointments (user_id, doctor_name, hospital_name, appointment_date, appointment_time)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $user_id, $doctor_name, $hospital_name, $appointment_date, $appointment_time);

        if ($stmt->execute()) {
            echo "<script>alert('Appointment added successfully');</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "User not logged in";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Add Appointment</title>
</head>
<body>

<div class="container">
    <div class="box form-box">
        <header>Add Appointment</header>
        <form action="" method="post">
            <div class="field input">
                <label for="doctor_name">Doctor Name</label>
                <input type="text" name="doctor_name" required>
            </div>
            <div class="field input">
                <label for="hospital_name">Hospital Name</label>
                <input type="text" name="hospital_name" required>
            </div>
            <div class="field input">
                <label for="appointment_date">Appointment Date</label>
                <input type="date" name="appointment_date" required>
            </div>
            <div class="field input">
                <label for="appointment_time">Appointment Time</label>
                <input type="time" name="appointment_time" required>
            </div>
            <div class="field">
                <input type="submit" class="btn" name="submit" value="Add Appointment" required>
            </div>

            
        </form>
        <div><a href="home.php"><button class="btn">Go back</button></a></div>
    </div>
</div>

</body>
</html>
