<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Schedule</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .header {
            text-align: center;
        }
        .header h1 {
            color: #4c44b6ce;
        }
        .table-container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn-container {
            margin-top: auto;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4c44b6ce; /* Updated color */
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #3a318e;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <?php
        session_start();

        if (!isset($_SESSION['id'])) {
            header("Location: index.php");
            exit();
        }

        // Using PDO for better security
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "login";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $user_id = $_SESSION['id'];
            $stmt = $conn->prepare("SELECT * FROM medicines WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($medicines) > 0) {
                echo "<h1>Medicine Schedule</h1>";
                echo "<div class='table-container'>";
                echo "<table>";
                echo "<tr><th>Name</th><th>Dosage</th><th>Frequency</th><th>Morning</th><th>Afternoon</th><th>Night</th><th>Start Date</th><th>End Date</th></tr>";
                foreach($medicines as $medicine) {
                    echo "<tr>";
                    echo "<td>".$medicine["name"]."</td>";
                    echo "<td>".$medicine["dosage"]."</td>";
                    echo "<td>".$medicine["frequency"]."</td>";
                    echo "<td>".$medicine["morning_time"]."</td>";
                    echo "<td>".$medicine["afternoon_time"]."</td>";
                    echo "<td>".$medicine["night_time"]."</td>";
                    echo "<td>".$medicine["start_date"]."</td>";
                    echo "<td>".$medicine["end_date"]."</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "</div>";
            } else {
                echo "<p>No medicines found for this user</p>";
            }
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn = null;
        ?>
    </div>
    <div class="btn-container">
        <a href="home.php" class="btn">Go back</a>
    </div>
</div>
</body>
</html>
