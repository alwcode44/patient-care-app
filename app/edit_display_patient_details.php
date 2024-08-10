<?php
session_start();

$message = ""; // Initialize message variable

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

// Retrieve existing patient details
$userId = $_SESSION['id'];
$sql = "SELECT * FROM patient_details WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$patientDetails = $result->fetch_assoc();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve updated patient details from the form
    $updatedDetails = [
        $_POST['medical_record_number'],
        $_POST['blood_type'],
        $_POST['allergies'],
        $_POST['medical_alerts'],
        $_POST['current_medications'],
        $_POST['past_medical_history'],
        $_POST['family_medical_history'],
        $_POST['social_history'],
        $_POST['preferred_language'],
        $_POST['height'],
        $_POST['weight'],
        $_POST['insurance_provider'],
        $_POST['policy_number'],
        $_POST['primary_care_physician'],
        $_POST['emergency_contact_name'],
        $_POST['emergency_contact_relationship'],
        $_POST['emergency_contact_phone'],
        $_POST['marital_status'],
        $_POST['occupation'],
        $_POST['additional_info'],
        $_POST['gender']
    ];

    // Prepare SQL statement to update details
    $sql_update = "UPDATE patient_details SET 
        medical_record_number = ?, blood_type = ?, allergies = ?, medical_alerts = ?, current_medications = ?, past_medical_history = ?, 
        family_medical_history = ?, social_history = ?, preferred_language = ?, height = ?, weight = ?, insurance_provider = ?, 
        policy_number = ?, primary_care_physician = ?, emergency_contact_name = ?, emergency_contact_relationship = ?, 
        emergency_contact_phone = ?, marital_status = ?, occupation = ?, additional_info = ?, gender = ? 
        WHERE user_id = ?";

    $stmt_update = $conn->prepare($sql_update);
    
    // Add userId to the updatedDetails array
    $updatedDetails[] = $userId;

    // Bind parameters dynamically
    $types = str_repeat('s', count($updatedDetails) - 1) . 'i'; // Assuming all fields are strings except user_id which is an integer
    $stmt_update->bind_param($types, ...$updatedDetails);

    if ($stmt_update->execute()) {
        $message = "Details updated successfully";
        header("Location: home.php");
        exit();
    } else {
        $message = "Error updating details";
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
    <title>Edit Patient Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="tel"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4c44b6ce;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #4c44b6;
        }

        button {
            background-color: #4c44b6ce;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4c44b6;
        }
    </style>
</head>
<body>

<div class="container">
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php else: ?>
        <h2>Edit Patient Details:</h2>
        <form action="" method="post">
            <h3>Medical Information:</h3>
            <label for="medical_record_number">Medical Record Number (MRN):</label>
            <input type="text" id="medical_record_number" name="medical_record_number" value="<?php echo htmlspecialchars($patientDetails['medical_record_number']); ?>"><br><br>

            <label for="blood_type">Blood Type:</label>
            <input type="text" id="blood_type" name="blood_type" value="<?php echo htmlspecialchars($patientDetails['blood_type']); ?>"><br><br>

            <label for="allergies">Allergies:</label>
            <textarea id="allergies" name="allergies"><?php echo htmlspecialchars($patientDetails['allergies']); ?></textarea><br><br>

            <label for="medical_alerts">Medical Alerts:</label>
            <textarea id="medical_alerts" name="medical_alerts"><?php echo htmlspecialchars($patientDetails['medical_alerts']); ?></textarea><br><br>

            <label for="current_medications">Current Medications:</label>
            <textarea id="current_medications" name="current_medications"><?php echo htmlspecialchars($patientDetails['current_medications']); ?></textarea><br><br>

            <label for="past_medical_history">Past Medical History:</label>
            <textarea id="past_medical_history" name="past_medical_history"><?php echo htmlspecialchars($patientDetails['past_medical_history']); ?></textarea><br><br>

            <label for="family_medical_history">Family Medical History:</label>
            <textarea id="family_medical_history" name="family_medical_history"><?php echo htmlspecialchars($patientDetails['family_medical_history']); ?></textarea><br><br>

            <label for="social_history">Social History:</label>
            <textarea id="social_history" name="social_history"><?php echo htmlspecialchars($patientDetails['social_history']); ?></textarea><br><br>

            <label for="preferred_language">Preferred Language:</label>
            <input type="text" id="preferred_language" name="preferred_language" value="<?php echo htmlspecialchars($patientDetails['preferred_language']); ?>"><br><br>

            <label for="height">Height (in cm):</label>
            <input type="number" id="height" name="height" value="<?php echo htmlspecialchars($patientDetails['height']); ?>"><br><br>

            <label for="weight">Weight (in kg):</label>
            <input type="number" id="weight" name="weight" value="<?php echo htmlspecialchars($patientDetails['weight']); ?>"><br><br>

            <h3>Insurance Information:</h3>
            <label for="insurance_provider">Insurance Provider:</label>
            <input type="text" id="insurance_provider" name="insurance_provider" value="<?php echo htmlspecialchars($patientDetails['insurance_provider']); ?>"><br><br>

            <label for="policy_number">Policy Number:</label>
            <input type="text" id="policy_number" name="policy_number" value="<?php echo htmlspecialchars($patientDetails['policy_number']); ?>"><br><br>

            <label for="primary_care_physician">Primary Care Physician:</label>
            <input type="text" id="primary_care_physician" name="primary_care_physician" value="<?php echo htmlspecialchars($patientDetails['primary_care_physician']); ?>"><br><br>

            <h3>Emergency Contact:</h3>
            <label for="emergency_contact_name">Name:</label>
            <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="<?php echo htmlspecialchars($patientDetails['emergency_contact_name']); ?>"><br><br>

            <label for="emergency_contact_relationship">Relationship:</label>
            <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" value="<?php echo htmlspecialchars($patientDetails['emergency_contact_relationship']); ?>"><br><br>

            <label for="emergency_contact_phone">Phone Number:</label>
            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="<?php echo htmlspecialchars($patientDetails['emergency_contact_phone']); ?>"><br><br>

            <h3>Additional Patient Information:</h3>
            <label for="marital_status">Marital Status:</label>
            <input type="text" id="marital_status" name="marital_status" value="<?php echo htmlspecialchars($patientDetails['marital_status']); ?>"><br><br>

            <label for="occupation">Occupation:</label>
            <input type="text" id="occupation" name="occupation" value="<?php echo htmlspecialchars($patientDetails['occupation']); ?>"><br><br>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="male" <?php echo $patientDetails['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo $patientDetails['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                <option value="other" <?php echo $patientDetails['gender'] == 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
            <br><br>

            <label for="additional_info">Additional Information:</label>
            <textarea id="additional_info" name="additional_info"><?php echo htmlspecialchars($patientDetails['additional_info']); ?></textarea><br><br>

            <input type="submit" value="Update">
        </form>
    <?php endif; ?>
</div>

</body>
</html>
