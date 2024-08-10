<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Register</title>
</head>
<body>
<div class="container">
    <div class="box form-box">
        <div class="logo">
            <p><a href="index.php" class="trucare2"></a></p>
        </div>
        <?php
        include("php/config.php");

        function validatePassword($password) {
            // Check if password contains at least one alphabet and one number
            return preg_match('/[A-Za-z]/', $password) && preg_match('/\d/', $password);
        }

        if (isset($_POST['submit'])) {
            $username = $_POST['username'];
            $email = $_POST['email'];
            if (substr($email, -4) !== ".com") {
                echo "<div class='message'>Invalid Email</div>";
                echo "<a href='register.php'><button class='btn'>Go back</button>";
                exit;
            }
            $age = $_POST['age'];
            if(strlen($age) > 3) {
                echo "<div class='message'>Age should be at most 3 digits.</div>";
                echo "<a href='register.php'><button class='btn'>Go back</button>";
                exit;
            }
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            if(strlen($password) < 8) {
                echo "<div class='message'>Password should be at least 8 characters long.</div>";
                echo "<a href='register.php'><button class='btn'>Go back</button>";
                exit;
            }

            if(!$password || !$password_confirm) {
                echo "<div class='message'>Please enter password and confirm password.</div>";
                echo "<a href='register.php'><button class='btn'>Go back</button>";
                exit;
            }

            if($password !== $password_confirm) {
                echo "<div class='message'>Passwords do not match.</div>";
                echo "<a href='register.php'><button class='btn'>Go back</button>";
                exit;
            }

            if(!validatePassword($password)) {
                echo "<div class='message'>Password should contain at least one alphabet and one number.</div>";
                echo "<a href='register.php'><button class='btn'>Go back</button>";
                exit;
            }

            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["photo"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["photo"]["tmp_name"]);
            if ($check !== false) {
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                    echo "Sorry, only JPG, JPEG, PNG files are allowed.";
                    $uploadOk = 0;
                }
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
            } else {
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {

                    mysqli_query($con, "INSERT INTO users(Username,Email,Age,Password,Photo) VALUES('$username','$email','$age','$password','$target_file')") or die("Error Occurred");

                    echo "<div class='message'>
                          <p>Registration successfully!</p>
                      </div> <br>";
                    echo "<a href='index.php'><button class='btn'>Login</button>";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }

        } else {
            ?>

            <header>Sign Up</header>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="field input">
                    <label for="username">Full Name</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="age">Age</label>
                    <input type="number" name="age" id="age" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password_confirm">Confirm Password</label>
                    <input type="password" name="password_confirm" id="password_confirm" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="photo">Photo</label>
                    <input type="file" name="photo" id="photo" accept="image/*" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Register" required>
                </div>
                <div class="links">
                    Already a member? <a href="index.php">Sign In</a>
                

                </div>
            </form>
            <?php } ?>
    </div>
</div>
</body>
</html>
