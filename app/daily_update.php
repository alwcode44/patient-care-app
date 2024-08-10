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

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id'];
    $date = date("Y-m-d");
    $sugar_level = $_POST['sugar_level'];
    $blood_pressure = $_POST['blood_pressure'];
    $water_consumption = $_POST['water_consumption'];

    // Check if there is already a record for the current date
    $existing_record_query = "SELECT * FROM health_data WHERE user_id = '$user_id' AND date = '$date'";
    $existing_record_result = $conn->query($existing_record_query);

    if ($existing_record_result->num_rows > 0) {
        // Update existing record
        $update_query = "UPDATE health_data SET sugar_level = '$sugar_level', blood_pressure = '$blood_pressure', water_consumption = '$water_consumption' WHERE user_id = '$user_id' AND date = '$date'";
        if ($conn->query($update_query) === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        // Insert new record
        $insert_query = "INSERT INTO health_data (user_id, date, sugar_level, blood_pressure, water_consumption) VALUES ('$user_id', '$date', '$sugar_level', '$blood_pressure', '$water_consumption')";
        if ($conn->query($insert_query) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $insert_query . "<br>" . $conn->error;
        }
    }
}

// Fetch health data for the current user (last 7 days)
$user_id = $_SESSION['id'];
$health_data_query = "SELECT * FROM health_data WHERE user_id = '$user_id' AND date >= CURDATE() - INTERVAL 7 DAY ORDER BY date DESC";
$result = $conn->query($health_data_query);

// Prepare data for daily statistics graph
$dates = [];
$sugar_levels = [];
$blood_pressures = [];
$water_consumptions = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['date'];
        $sugar_levels[] = $row['sugar_level'];
        $blood_pressures[] = $row['blood_pressure'];
        $water_consumptions[] = $row['water_consumption'];
    }
}

// Fetch all health data for the current user for the table
$all_health_data_query = "SELECT * FROM health_data WHERE user_id = '$user_id' ORDER BY date DESC";
$all_result = $conn->query($all_health_data_query);

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #4c44b6ce;
            text-align: center;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="number"],
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4c44b6ce;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4c44b6ce;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Health Tracker</h1>

        <h2>Update Health Data for Today</h2>
        <form action="#" method="post">
            <label for="sugar_level">Sugar Level:</label>
            <input type="number" id="sugar_level" name="sugar_level" required><br>
            <label for="blood_pressure">Blood Pressure:</label>
            <input type="text" id="blood_pressure" name="blood_pressure" required><br>
            <label for="water_consumption">Cholesterol:</label>
            <input type="number" id="water_consumption" name="water_consumption" required><br>
            <input type="submit" value="Submit">
        </form>

        <h2>Past Health Data</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Sugar Level</th>
                <th>Blood Pressure</th>
                <th>Cholesterol</th>
            </tr>
            <?php
            if ($all_result->num_rows > 0) {
                while ($row = $all_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["date"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["sugar_level"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["blood_pressure"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["water_consumption"]) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No health data available</td></tr>";
            }
            ?>
        </table>

        <!-- Daily Statistics Graph -->
        <h2>Daily Statistics Graph (Last 7 Days)</h2>
        <canvas id="dailyStatsChart" width="400" height="200"></canvas>
    </div>

    <script>
        var ctx = document.getElementById('dailyStatsChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Sugar Level',
                    data: <?php echo json_encode($sugar_levels); ?>,
                    backgroundColor: 'rgba(75, 99, 132, 0.2)',
                    borderColor: 'rgba(75, 99, 132, 1)',
                    borderWidth: 1
                }, {
                    label: 'Blood Pressure',
                    data: <?php echo json_encode($blood_pressures); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Cholesterol',
                    data: <?php echo json_encode($water_consumptions); ?>,
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
