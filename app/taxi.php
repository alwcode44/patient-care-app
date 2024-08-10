<?php
// Database connection
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

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to add new taxi number to database
function add_taxi_number($conn, $company, $phone) {
    $company = sanitize_input($company);
    $phone = sanitize_input($phone);
    
    $sql = "INSERT INTO taxi_numbers (company, phone) VALUES ('$company', '$phone')";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data and add new taxi number
    $company = $_POST['company'];
    $phone = $_POST['phone'];
    
    if(add_taxi_number($conn, $company, $phone)) {
        echo "New taxi number added successfully.";
    } else {
        echo "Error adding taxi number: " . $conn->error;
    }
}

// Query to fetch existing taxi numbers from database
$sql = "SELECT company, phone FROM taxi_numbers";
$result = $conn->query($sql);

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Taxi Numbers</title>
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
            background-color: #4c44b6ce;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Local Taxi Numbers</h1>

    <table>
        <tr>
            <th>Company</th>
            <th>Phone Number</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["company"] . "</td><td>" . $row["phone"] . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No taxi numbers found</td></tr>";
        }
        ?>
    </table>

    <h2>Add New Taxi Number</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="company">Company:</label>
        <input type="text" id="company" name="company" required><br><br>
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" required><br><br>
        <input type="submit" value="Add Taxi Number">
    </form>
    <div><a href="home.php"><button class="btn">Go back</button></a></div>
</body>

</html>
