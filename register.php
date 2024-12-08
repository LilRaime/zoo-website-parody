<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require_once("includes/connection.php");
include("includes/header.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); 

    // Перевірка на унікальність імені користувача
    $stmt = $con->prepare("SELECT COUNT(*) FROM visitor WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    // Перевірка на правильність імені та прізвища (тільки літери)
    if (!preg_match("/^[a-zA-Z]+$/", $name)) {
        echo "<p style='color: red;'>Name must contain only letters.</p>";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $surname)) {
        echo "<p style='color: red;'>Surname must contain only letters.</p>";
    }
    // Перевірка на правильність нікнейму (тільки літери та цифри)
    elseif (!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
        echo "<p style='color: red;'>Username must contain only letters and numbers.</p>";
    }
    // Якщо нікнейм унікальний
    elseif ($count > 0) {
        echo "<p style='color: red;'>Username already exists. Please choose another one.</p>";
    } else {
        // Вставка даних у таблицю visitor
        $stmt = $con->prepare("INSERT INTO visitor (name, surname, date_of_birth, username, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $surname, $date_of_birth, $username, $password);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Registration successful! <a href='login.php'>Login here</a>.</p>";
        } else {
            echo "<p style='color: red;'>Error during registration: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}
$con->close();

?>
<head>
    <title>Registration</title>
</head>
<body>
    <div class="container mregister">
        <div id="login">
            <h1>Registration</h1>
            <form action="register.php" id="registerform" method="post" name="registerform">
                <p>
                    <label for="name">Name<br>
                        <input class="input" id="name" name="name" size="32" type="text" required>
                    </label>
                </p>
                <p>
                    <label for="surname">Surname<br>
                        <input class="input" id="surname" name="surname" size="32" type="text" required>
                    </label>
                </p>
                <p>
                    <label for="date_of_birth">Date of Birth<br>
                        <input class="input" id="date_of_birth" name="date_of_birth" type="date" required>
                    </label>
                </p>
                <p>
                    <label for="username">Username<br>
                        <input class="input" id="username" name="username" size="20" type="text" required>
                    </label>
                </p>
                <p>
                    <label for="password">Password<br>
                        <input class="input" id="password" name="password" size="32" type="password" required>
                    </label>
                </p>
                <p class="submit">
                    <input class="button" id="register" name="register" type="submit" value="Register">
                </p>
                <p class="regtext">Already registered? <a href="login.php">Enter username</a>!</p>
            </form>
        </div>
    </div>
    <footer>
        <?php include("includes/footer.php"); ?>
    </footer>
</body>
</html>
