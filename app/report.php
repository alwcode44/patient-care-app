<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $file_name = $_FILES["file"]["name"];
    $file_type = $_FILES["file"]["type"];
    $file_temp = $_FILES["file"]["tmp_name"];
    $file_path = "uploads/" . $file_name;

    move_uploaded_file($file_temp, $file_path);

    $conn = new mysqli("localhost", "root", "", "login");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_id = $_SESSION['id'];

    $sql = "INSERT INTO files (file_name, file_type, file_path, user_id) VALUES ('$file_name', '$file_type', '$file_path', '$user_id')";
    if ($conn->query($sql) === TRUE) {
        echo "File uploaded successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

if (isset($_GET['delete'])) {
    $file_to_delete = $_GET['delete'];
    $conn = new mysqli("localhost", "root", "", "login");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $user_id = $_SESSION['id'];
    $sql = "SELECT * FROM files WHERE file_name='$file_to_delete' AND user_id='$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $file_path = $row['file_path'];
        unlink($file_path);
        $sql = "DELETE FROM files WHERE file_name='$file_to_delete' AND user_id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            echo "File deleted successfully";
        } else {
            echo "Error deleting file: " . $conn->error;
        }
    } else {
        echo "File not found or you don't have permission to delete";
    }
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="file"] {
            margin-bottom: 10px;
        }

        button {
            padding: 8px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .file-list {
            margin-top: 20px;
        }

        .file-item {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }

        .file-item a {
            color: #333;
            text-decoration: none;
        }

        .delete-btn {
            color: #ff0000;
            margin-left: 10px;
            cursor: pointer;
        }

        .delete-btn:hover {
            text-decoration: underline;
        }

        .go-back {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Doctor Reports</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <button type="submit" name="submit">Upload</button>
        </form>

        <h2>Submitted Doctor Reports</h2>
        <div class="file-list">
            <?php
            $conn = new mysqli("localhost", "root", "", "login");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $user_id = $_SESSION['id'];
            $sql = "SELECT * FROM files WHERE user_id = '$user_id'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='file-item'>";
                    echo "<a href='" . $row["file_path"] . "' target='_blank'>" . $row["file_name"] . "</a>";
                    echo "<span class='delete-btn' onclick='deleteFile(\"" . urlencode($row["file_name"]) . "\")'>Delete</span>";
                    echo "</div>";
                }
            } else {
                echo "<p>No files submitted yet</p>";
            }

            $conn->close();
            ?>
        </div>
        <div class="go-back"><a href="home.php"><button>Go back</button></a></div>
    </div>

    <script>
        function deleteFile(fileName) {
            if (confirm("Are you sure you want to delete this file?")) {
                window.location.href = "report.php?delete=" + fileName;
            }
        }
    </script>
</body>
</html>
