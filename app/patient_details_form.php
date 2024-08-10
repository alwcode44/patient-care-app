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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve patient details from the form
    $patientDetails = [
        'user_id' => $_SESSION['id'],
        'medical_record_number' => $_POST['medical_record_number'],
        'blood_type' => $_POST['blood_type'],
        'allergies' => $_POST['allergies'],
        'medical_alerts' => $_POST['medical_alerts'],
        'current_medications' => $_POST['current_medications'],
        'past_medical_history' => $_POST['past_medical_history'],
        'family_medical_history' => $_POST['family_medical_history'],
        'social_history' => $_POST['social_history'],
        'preferred_language' => $_POST['preferred_language'],
        'height' => $_POST['height'],
        'weight' => $_POST['weight'],
        'insurance_provider' => $_POST['insurance_provider'],
        'policy_number' => $_POST['policy_number'],
        'primary_care_physician' => $_POST['primary_care_physician'],
        'emergency_contact_name' => $_POST['emergency_contact_name'],
        'emergency_contact_relationship' => $_POST['emergency_contact_relationship'],
        'emergency_contact_phone' => $_POST['emergency_contact_phone'],
        'marital_status' => $_POST['marital_status'],
        'occupation' => $_POST['occupation'],
        'additional_info' => $_POST['additional_info'],
        'gender' => $_POST['gender']
    ];

    // Prepare SQL statement
    $fields = implode(', ', array_keys($patientDetails));
    $placeholders = implode(', ', array_fill(0, count($patientDetails), '?'));
    $sql = "INSERT INTO patient_details ($fields) VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);

    // Bind parameters dynamically
    $types = str_repeat('s', count($patientDetails)); // Assuming all fields are strings
    $stmt->bind_param($types, ...array_values($patientDetails));

    if ($stmt->execute()) {
        $message = "Submitted successfully";

        // Update patient_details_submitted field for the user
        $id = $_SESSION['id'];
        $sql_update = "UPDATE users SET patient_details_submitted = TRUE WHERE Id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $id); // Assuming Id is an integer
        $stmt_update->execute();
        $conn->close();
        header("Location: home.php");
        exit();
    } else {
        $message = "Error submitting data";
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
    <title>Patient Details</title>
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
        <a href="index.php"><button>Go to Login Page</button></a>
    <?php else: ?>
        <h2>Please add the Patient Details before proceeding:</h2>
        <form action="" method="post">

            <h3>Medical Information:</h3>
            <label for="medical_record_number">Medical Record Number (MRN):</label>
            <input type="text" id="medical_record_number" name="medical_record_number"><br><br>

            <label for="blood_type">Blood Type:</label>
            <input type="text" id="blood_type" name="blood_type"><br><br>

            <label for="allergies">Allergies:</label>
            <textarea id="allergies" name="allergies"></textarea><br><br>

            <label for="medical_alerts">Medical Alerts:</label>
            <textarea id="medical_alerts" name="medical_alerts"></textarea><br><br>

            <label for="current_medications">Current Medications:</label>
            <textarea id="current_medications" name="current_medications"></textarea><br><br>

            <label for="past_medical_history">Past Medical History:</label>
            <textarea id="past_medical_history" name="past_medical_history"></textarea><br><br>

            <label for="family_medical_history">Family Medical History:</label>
            <textarea id="family_medical_history" name="family_medical_history"></textarea><br><br>

            <label for="social_history">Social History:</label>
            <textarea id="social_history" name="social_history"></textarea><br><br>

            <label for="preferred_language">Preferred Language:</label>
            <input type="text" id="preferred_language" name="preferred_language"><br><br>

            <label for="height">Height (in cm):</label>
            <input type="number" id="height" name="height"><br><br>

            <label for="weight">Weight (in kg):</label>
            <input type="number" id="weight" name="weight"><br><br>

            <h3>Insurance Information:</h3>
            <label for="insurance_provider">Insurance Provider:</label>
            <input type="text" id="insurance_provider" name="insurance_provider"><br><br>

            <label for="policy_number">Policy Number:</label>
            <input type="text" id="policy_number" name="policy_number"><br><br>

            <label for="primary_care_physician">Primary Care Physician:</label>
            <input type="text" id="primary_care_physician" name="primary_care_physician"><br><br>

            <h3>Emergency Contact:</h3>
            <label for="emergency_contact_name">Name:</label>
            <input type="text" id="emergency_contact_name" name="emergency_contact_name"><br><br>

            <label for="emergency_contact_relationship">Relationship:</label>
            <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship"><br><br>

            <label for="emergency_contact_phone">Phone Number:</label>
            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone"><br><br>

            <h3>Additional Patient Information:</h3>
            <label for="marital_status">Marital Status:</label>
            <input type="text" id="marital_status" name="marital_status"><br><br>

            <label for="occupation">Occupation:</label>
            <input type="text" id="occupation" name="occupation"><br><br>

            <label for="gender">Gender:</label> 
            <select id="gender" name="gender"> 
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option> 
            </select> 
            <br><br> 

            <label for="additional_info">Additional Information:</label>
            <textarea id="additional_info" name="additional_info"></textarea><br><br>

            <input type="submit" value="Submit">
        </form>
    <?php endif; ?>
</div>

</body>
</html>
