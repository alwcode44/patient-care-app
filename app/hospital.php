<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    echo "User not logged in";
    exit(); // Stop further execution
}

// Include database connection
include("php/config.php");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $user_id = $_SESSION['id'];
    $hospital_name = $_POST['hospital_name'];
    $hospital_phone = $_POST['hospital_phone'];
    $ambulance_phone = $_POST['ambulance_phone'];

    // Insert hospital numbers into the database
    $sql = "INSERT INTO hospital_numbers (user_id, hospital_name, hospital_phone, ambulance_phone) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("isss", $user_id, $hospital_name, $hospital_phone, $ambulance_phone);
    $stmt->execute();

    // Close statement
    $stmt->close();
}

// Retrieve hospital numbers for the current user
$user_id = $_SESSION['id'];
$sql = "SELECT * FROM hospital_numbers WHERE user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Close statement
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital and Ambulance Numbers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            margin-bottom: 20px;
        }
        form input[type="text"] {
            padding: 8px;
            width: 200px;
        }
        form input[type="submit"] {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .emergency-btn {
            background-color: red;
            color: white;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
        }
        .emergency-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <h1>Hospital and Ambulance Numbers</h1>

    <table>
        <tr>
            <th>Hospital Name</th>
            <th>Hospital Phone Number</th>
            <th>Ambulance Phone Number</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['hospital_name']; ?></td>
                <td><?php echo $row['hospital_phone']; ?></td>
                <td><?php echo $row['ambulance_phone']; ?></td>
            </tr>
        <?php } ?>
    </table>

    <h2>Add New Hospital and Ambulance Number</h2>
    <form action="#" method="post">
        <label for="hospital_name">Hospital Name:</label>
        <input type="text" id="hospital_name" name="hospital_name" required><br><br>
        <label for="hospital_phone">Hospital Phone Number:</label>
        <input type="text" id="hospital_phone" name="hospital_phone" required><br><br>
        <label for="ambulance_phone">Ambulance Phone Number:</label>
        <input type="text" id="ambulance_phone" name="ambulance_phone" required><br><br>
        <input type="submit" value="Add Hospital and Ambulance Number">
    </form>
    <div><a href="home.php"><button class="btn">Go back</button></a></div>
</body>
</html>
