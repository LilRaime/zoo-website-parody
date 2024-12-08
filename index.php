<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Home page?</title>
    <link rel="stylesheet" href="css/styles_homepage.css">
</head>
<body>
<div class="top-menu">
        <?php
        session_start(); 
        // Якщо користувач залогінений, відображаємо кнопку виходу
        if (isset($_SESSION["session_username"])) {
            echo '<div class="btn">
                    <a href="logout.php" class="btn">Logout</a>
                  </div>';
        } else {
            // Якщо користувач не залогінений, показуємо кнопки для входу і реєстрації
            echo '<div class="btn">
                    <a href="login.php" class="nav-button">Login</a>
                  </div>
                  <div class="btn">
                    <a href="register.php" class="nav-button">Register</a>
                  </div>';
        }
        ?>
    </div>
    <h1>Home page</h1>
    <div class="navigation">
        <div class="nav-item">
            <img src="src\jokerge.png" alt="Ticket" class="nav-image">
            <a href="ticket.php">Ticket</a>
        </div>
        <div class="nav-item">
            <img src="src\jokerge.png" alt="Animal Info" class="nav-image">
            <a href="animal_info.php">Animal Info</a>
        </div>
        <div class="nav-item">
            <img src="src\jokerge.png" alt="Aviary" class="nav-image">
            <a href="aviary.php">Aviary</a>
        </div>
    </div>
    <div class="footer">
        <?php include("includes/footer.php"); ?>
    </div>
</body>
</html>