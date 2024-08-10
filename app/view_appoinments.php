<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .btn {
            background-color: #4c44b6ce;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #4c44b6ff;
        }

    </style>
</head>
<body>

<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    echo "<p>User not logged in</p>";
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

// Use prepared statement to avoid SQL injection
$sql = "SELECT * FROM appointments WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<div class='container'>";
    echo "<h1>Your Appointments</h1>";
    echo "<table>";
    echo "<tr><th>Doctor Name</th><th>Hospital Name</th><th>Date</th><th>Time</th></tr>";
    while($row = $result->fetch_assoc()) {
        // Check if appointment is due for reminder
        $appointmentDateTime = strtotime($row["appointment_date"] . ' ' . $row["appointment_time"]);
        $currentDateTime = time();
        $timeDiff = $appointmentDateTime - $currentDateTime;
        if ($timeDiff <= 24 * 60 * 60 && $timeDiff > 0) { // Reminder for appointments within 24 hours
            echo "<script>alert('Reminder: Your appointment with ".$row["doctor_name"]." at ".$row["hospital_name"]." is due on ".$row["appointment_date"]." at ".$row["appointment_time"]."');</script>";
        }
        echo "<tr>";
        echo "<td>".$row["doctor_name"]."</td>";
        echo "<td>".$row["hospital_name"]."</td>";
        echo "<td>".$row["appointment_date"]."</td>";
        echo "<td>".$row["appointment_time"]."</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<div><a href='home.php' class='btn'>Go back</a></div>";
    echo "</div>";
} else {
    echo "<p>No appointments found for this user</p>";
}

$conn->close();
?>

</body>
</html>
