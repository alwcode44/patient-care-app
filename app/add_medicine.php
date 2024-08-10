<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (isset($_SESSION['id'])) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "login";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get form data
        $name = $_POST['name'];
        $dosage = $_POST['dosage'];
        $frequency = $_POST['frequency'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $morning_time = isset($_POST['morning_time']) ? $_POST['morning_time'] : '';
        $afternoon_time = isset($_POST['afternoon_time']) ? $_POST['afternoon_time'] : '';
        $night_time = isset($_POST['night_time']) ? $_POST['night_time'] : '';
        $user_id = $_SESSION['id'];

        // Prepare and execute SQL statement
        $sql = "INSERT INTO medicines (name, dosage, frequency, morning_time, afternoon_time, night_time, start_date, end_date, user_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $name, $dosage, $frequency, $morning_time, $afternoon_time, $night_time, $start_date, $end_date, $user_id);

        if ($stmt->execute()) {
            echo "<script>alert('Medicine added successfully');</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
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
    <title>Add Medicine</title>
</head>
<body>

<div class="container">
    <div class="box form-box">
        <header>Add medicine</header>
        <form action="add_medicine.php" method="post" id="add_medicine_form">
            <div class="field input">
                <label for="name">Medicine name</label>
                <input type="text" name="name" required>
            </div>

            <div class="field input">
                <label for="dosage">Dosage</label>
                <input type="text" name="dosage" required>
            </div>

            <div class="field input">
                <label for="frequency">Frequency (max: 3)</label>
                <input type="number" name="frequency" id="frequency" required min="1" max="3" onchange="showTimeFields()">
            </div>
            
            <div class="field input" id="morning_time_field">
                <label for="morning_time">Morning Time</label>
                <input type="time" name="morning_time" id="morning_time">
            </div>
            
            <div class="field input" id="afternoon_time_field">
                <label for="afternoon_time">Afternoon Time</label>
                <input type="time" name="afternoon_time" id="afternoon_time">
            </div>
            
            <div class="field input" id="night_time_field">
                <label for="night_time">Night Time</label>
                <input type="time" name="night_time" id="night_time">
            </div>
            
            <div class="field input">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" required>
            </div>
            <div class="field input">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" required>
            </div>
            <div class="field">
                <input type="submit" class="btn" name="submit" value="Add Medicine" required>
            </div>
        </form>
        <a href="home.php"> <button class="btn">Go back</button> </a>
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add_medicine_form').addEventListener('submit', function(e) {
        var frequency = document.getElementById('frequency').value;
        var morningTime = document.getElementById('morning_time').value;
        var afternoonTime = document.getElementById('afternoon_time').value;
        var nightTime = document.getElementById('night_time').value;

        if (frequency == 2) {
            var filledCount = 0;
            if (morningTime != '') filledCount++;
            if (afternoonTime != '') filledCount++;
            if (nightTime != '') filledCount++;

            if (filledCount != 2) {
                e.preventDefault();
                alert('Please select exactly two times when frequency is 2');
            }
        }

        if (frequency == 1 && (morningTime != '' && afternoonTime != '' && nightTime != '')) {
            e.preventDefault();
            alert('You can only select one time when frequency is 1');
        }
    });
});
</script>

</body>
</html>
