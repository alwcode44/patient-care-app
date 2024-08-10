<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <?php 
            include("php/config.php");
            
            if(isset($_POST['submit'])) {
                $email = mysqli_real_escape_string($con,$_POST['email']);
                $password = mysqli_real_escape_string($con,$_POST['password']);

                $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE Email=?");
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);

                if($row) {
                    // Compare passwords directly
                    if($password === $row['Password']) {
                        $_SESSION['valid'] = $row['Email'];
                        $_SESSION['username'] = $row['Username'];
                        $_SESSION['age'] = $row['Age'];
                        $_SESSION['id'] = $row['Id'];
                        header("Location: home.php");
                        exit();
                    } else {
                        echo "<div class='message'>
                                <p>Wrong Password</p>
                              </div> <br>";
                    }
                } else {
                    echo "<div class='message'>
                            <p>No user found with that email</p>
                          </div> <br>";
                }
            }
            ?>
            <div class="logo">
                <p><a href="index.php" class="trucare2"></a></p>
            </div>
            <header>Login</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Login">
                </div>
                <div class="links">
                    Don't have an account? <a href="register.php">Sign Up Now</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
