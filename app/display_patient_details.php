<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .details-container {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .detail-label {
            font-weight: bold;
            color: #555;
        }

        .detail-value {
            margin-bottom: 10px;
            color: #333;
        }

        .btn {
            background-color: #4c44b6ce;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .go-back-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>Patient Details</h2>

<div class="container">
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

    // Establish connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch patient details and user details from the database
    $user_id = $_SESSION['id'];
    $sql = "SELECT pd.*, u.Username, u.Email, u.Age FROM patient_details pd INNER JOIN users u ON pd.user_id = u.Id WHERE pd.user_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()): ?>
        <div class="details-container">
            <div class="detail-label">Full name:</div>
            <div class="detail-value"><?php echo $row['Username']; ?></div>
        <div class="detail-label">Email:</div>
        <div  class="detail-value"><?php echo $row['Email']; ?></div>
        <div class="detail-label">Age:</div>
        <div  class="detail-value"><?php echo $row['Age']; ?></div>
        <div class="detail-label">Medical Record Number:</div>
        <div  class="detail-value"><?php echo $row['medical_record_number']; ?></div>
        <div class="detail-label">Blood Type:</div>
        <div  class="detail-value"><?php echo $row['blood_type']; ?></div>
        <div class="detail-label">Allergies:</div>
        <div  class="detail-value"><?php echo $row['allergies']; ?></div>
        <div class="detail-label">Medical Alerts:</div>
        <div  class="detail-value"><?php echo $row['medical_alerts']; ?></div>
        <div class="detail-label">Current Medications:</div>
        <div  class="detail-value"><?php echo $row['current_medications']; ?></div>
        <div class="detail-label">Past Medical History:</div>
        <div  class="detail-value"><?php echo $row['past_medical_history']; ?></div>
        <div class="detail-label">Family Medical History:</div>
        <div  class="detail-value"><?php echo $row['family_medical_history']; ?></div>
        <div class="detail-label">Social History:</div>
        <div  class="detail-value"><?php echo $row['social_history']; ?></div>
        <div class="detail-label">Preferred Language:</div>
        <div  class="detail-value"><?php echo $row['preferred_language']; ?></div>
        <div class="detail-label">Height:</div>
        <div  class="detail-value"><?php echo $row['height']; ?></div>
        <div class="detail-label">Weight:</div>
        <div  class="detail-value"><?php echo $row['weight']; ?></div>
        <div class="detail-label">Insurance Provider:</div>
        <div  class="detail-value"><?php echo $row['insurance_provider']; ?></div>
        <div class="detail-label">Policy Number:</div>
        <div  class="detail-value"><?php echo $row['policy_number']; ?></div>
        <div class="detail-label">Primary Care Physician:</div>
        <div  class="detail-value"><?php echo $row['primary_care_physician']; ?></div>
        <div class="detail-label">Emergency Contact Name:</div>
        <div  class="detail-value"><?php echo $row['emergency_contact_name']; ?></div>
        <div class="detail-label">Emergency Contact Relationship:</div>
        <div  class="detail-value"><?php echo $row['emergency_contact_relationship']; ?></div>
        <div class="detail-label">Emergency Contact Phone:</div>
        <div  class="detail-value"><?php echo $row['emergency_contact_phone']; ?></div>
        <div class="detail-label">Marital Status:</div>
        <div  class="detail-value"><?php echo $row['marital_status']; ?></div>
        <div class="detail-label">Occupation:</div>
        <div  class="detail-value"><?php echo $row['occupation']; ?></div>
        <div class="detail-label">Additional Info:</div>
        <div  class="detail-value"><?php echo $row['additional_info']; ?></div>
        <div class="detail-label">Gender:</div>
        <div  class="detail-value"><?php echo $row['gender']; ?></div>
        </div>
    <?php endwhile; ?>
</div>

<div class="go-back-container">
    <a href="home.php" class="btn">Go back</a>
</div>

</body>
</html>

<?php
// Close database connection and statement
$stmt->close();
$conn->close();
?>
