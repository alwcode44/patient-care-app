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
    // Check if any medicines are selected
    if (isset($_POST['medicine_taken']) && is_array($_POST['medicine_taken'])) {
        // Loop through each selected medicine
        foreach ($_POST['medicine_taken'] as $medicine_id => $times) {
            foreach ($times as $time) {
                // Insert a record into doses_taken table for each dose taken
                $sql = "INSERT INTO doses_taken (medicine_id, user_id, date, time_part) VALUES (?, ?, CURDATE(), ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iis", $medicine_id, $user_id, $time);
                $stmt->execute();
            }
        }
    } else {
        echo "No medicines selected";
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Medicines</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .medicine-card {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .medicine-card h3 {
            margin-top: 0;
        }

        .medicine-card input[type="checkbox"] {
            margin-right: 5px;
        }

        .medicine-card label {
            margin-right: 15px;
        }

        .btn-go-back {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    // Check if the user is logged in
    if (!isset($_SESSION['id'])) {
        echo "User not logged in";
        exit(); // Stop further execution
    }

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_id = $_SESSION['id'];

    // Get the current date
    $current_date = date("Y-m-d");

    // Use prepared statement to avoid SQL injection
    $sql = "SELECT * FROM medicines WHERE user_id = ? AND start_date <= ? AND end_date >= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $current_date, $current_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>Daily Medicines for " . date("F j, Y") . "</h2>";
        echo "<form id='medicine_form' action='' method='post'>";
        while ($row = $result->fetch_assoc()) {
            echo "<div class='medicine-card'>";
            echo "<h3>" . htmlspecialchars($row["name"]) . "</h3>";
            echo "<p>Dosage: " . htmlspecialchars($row["dosage"]) . "</p>";
            echo "<p>Frequency: " . htmlspecialchars($row["frequency"]) . "</p>";

            // Fetch doses taken for this medicine
            $medicine_id = $row["id"];
            $dose_query = "SELECT * FROM doses_taken WHERE medicine_id = ? AND user_id = ? AND date = ?";
            $dose_stmt = $conn->prepare($dose_query);
            $dose_stmt->bind_param("iis", $medicine_id, $user_id, $current_date);
            $dose_stmt->execute();
            $dose_result = $dose_stmt->get_result();

            // Initialize an array to keep track of checked times
            $checked_times = array();

            // Store checked times in the array
            while ($dose_row = $dose_result->fetch_assoc()) {
                $checked_times[] = $dose_row["time_part"];
            }

            // Display checkboxes for morning, afternoon, and night
            $times = array("Morning", "Afternoon", "Night");
            foreach ($times as $time) {
                $time_lower = strtolower($time);
                $time_value = $row[$time_lower . "_time"];
                if ($time_value != "00:00:00") {
                    $checked = in_array($time_lower, $checked_times) ? "checked" : "";
                    echo "<input type='checkbox' name='medicine_taken[$medicine_id][]' value='$time_lower' id='$time_lower' $checked>";
                    echo "<label for='$time_lower'>$time ($time_value)</label><br>";
                }
            }
            echo "</div>";
        }
        echo "<input type='submit' class='btn btn-primary' value='Mark as Taken'>";
        echo "</form>";
    } else {
        echo "No medicines found for today";
    }

    // Close prepared statements and database connection
    $stmt->close();
    $dose_stmt->close();
    $conn->close();
    ?>

    <div class="btn-go-back">
        <a href="home.php" class="btn btn-secondary">Go back</a>
    </div>
</div>

</body>
</html>
