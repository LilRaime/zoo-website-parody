<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require_once("includes/connection.php");
include("includes/header.php");

if (isset($_SESSION["session_username"])) {
    header("Location: intropage.php");
}

if (isset($_POST["login"])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Перевірка формату username
        if (preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            // Підготовлений запит для перевірки username і password
            $stmt = $con->prepare(" SELECT username, password FROM visitor WHERE username = ? AND password = ? UNION SELECT username, password FROM admin WHERE username = ? AND password = ? UNION SELECT username, password FROM manager WHERE username = ? AND password = ? ");
            $stmt->bind_param("ssssss", $username, $password, $username, $password, $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            // Якщо користувача знайдено
            if ($result->num_rows > 0) {
                $_SESSION['session_username'] = $username;
                header("Location: index.php");
                exit();
            } else {
                echo "Invalid username or password!";
            }
        } else {
            echo "Invalid username format.";
        }
    } else {
        echo "All fields are required!";
    }
}

?>
</head>
<body>
    <div class="container mlogin">
        <div id="login">
            <h1>Login</h1>
            <form action="" id="loginform" method="post" name="loginform">
                <p>
                    <label for="user_login">Username<br>
                        <input class="input" id="username" name="username" size="20" type="text" value="">
                    </label>
                </p>
                <p>
                    <label for="user_pass">Password<br>
                        <input class="input" id="password" name="password" size="20" type="password" value="">
                    </label>
                </p> 
                <p class="submit">
                    <input class="button" name="login" type="submit" value="Log In">
                </p>
                <p class="regtext">Not yet registered? <a href="register.php">Register</a>!</p>
            </form>
        </div>
    </div>
    <footer>
	<?php include("includes/footer.php"); ?>
    </footer>
</body>
</html>
