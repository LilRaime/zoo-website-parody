<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if (!isset($_SESSION["session_username"])) {
    header("Location: login.php");
    exit();
}

require_once("includes/connection.php");

$query_user = "SELECT admin_id FROM admin WHERE username = ?";
$stmt_user = mysqli_prepare($con, $query_user);
mysqli_stmt_bind_param($stmt_user, "s", $_SESSION["session_username"]);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);

$is_admin = ($result_user && mysqli_num_rows($result_user) > 0);
$admin_id = null;

if ($is_admin) {
    $row_admin = mysqli_fetch_assoc($result_user);
    $admin_id = $row_admin["admin_id"];
}

if ($is_admin) {
    if (isset($_POST['submit'])) {
        $name = htmlspecialchars($_POST['name']);
        $type = htmlspecialchars($_POST['type']);
        $date_of_birth = $_POST['date_of_birth'];
        $information = htmlspecialchars($_POST['information']);

        if (!empty($name) && !empty($type) && !empty($date_of_birth) && !empty($information)) {
            // Підготовлений запит для додавання тварини
            $insert_animal_query = "INSERT INTO animal (name, type, date_of_birth) VALUES (?, ?, ?)";
            $stmt_animal = mysqli_prepare($con, $insert_animal_query);
            mysqli_stmt_bind_param($stmt_animal, "sss", $name, $type, $date_of_birth);
            mysqli_stmt_execute($stmt_animal);

            if (mysqli_stmt_affected_rows($stmt_animal) > 0) {
                $animal_id = mysqli_insert_id($con);

                // Додаємо інформацію про тварину
                $insert_info_query = "INSERT INTO information_about_animal (animal_id, admin_id, information) VALUES (?, ?, ?)";
                $stmt_info = mysqli_prepare($con, $insert_info_query);
                mysqli_stmt_bind_param($stmt_info, "iis", $animal_id, $admin_id, $information);
                mysqli_stmt_execute($stmt_info);

                if (mysqli_stmt_affected_rows($stmt_info) > 0) {
                    header("Location: animal_info.php");
                    exit();
                } else {
                    die("Error adding information: " . mysqli_error($con));
                }
            } else {
                die("Error adding animal: " . mysqli_error($con));
            }
        } else {
            echo "<p>Please fill in all fields.</p>";
        }
    }

    // Обробка видалення тварини
    if (isset($_POST['delete'])) {
        $delete_animal_id = $_POST['delete_animal_id'];
    
        // Видалення інформації про тварину
        $delete_info_query = "DELETE FROM information_about_animal WHERE animal_id = ?";
        $stmt = $con->prepare($delete_info_query);
        $stmt->bind_param("i", $delete_animal_id);
        if (!$stmt->execute()) {
            die("Error deleting animal information: " . $stmt->error);
        }
        $stmt->close();
    
        // Видалення самої тварини
        $delete_animal_query = "DELETE FROM animal WHERE animal_id = ?";
        $stmt = $con->prepare($delete_animal_query);
        $stmt->bind_param("i", $delete_animal_id);
        if (!$stmt->execute()) {
            die("Error deleting animal: " . $stmt->error);
        }
        $stmt->close();
    
        // Отримання максимального значення animal_id
        $max_animal_id_query = "SELECT MAX(animal_id) AS max_id FROM animal";
        $stmt = $con->prepare($max_animal_id_query);
        if (!$stmt->execute()) {
            die("Error fetching max animal_id: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $max_animal_id_row = $result->fetch_assoc();
        $max_animal_id = $max_animal_id_row['max_id'] ?: 0;
        $stmt->close();
    
        // Скидання AUTO_INCREMENT для таблиці animal
        $reset_auto_increment_animal_query = "ALTER TABLE animal AUTO_INCREMENT = ?";
        $stmt = $con->prepare($reset_auto_increment_animal_query);
        $stmt->bind_param("i", $max_animal_id);
        if (!$stmt->execute()) {
            die("Error resetting AUTO_INCREMENT for animal: " . $stmt->error);
        }
        $stmt->close();
    
        // Отримання максимального значення information_about_animal_id
        $max_info_id_query = "SELECT MAX(information_about_animal_id) AS max_info_id FROM information_about_animal";
        $stmt = $con->prepare($max_info_id_query);
        if (!$stmt->execute()) {
            die("Error fetching max information_about_animal_id: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $max_info_id_row = $result->fetch_assoc();
        $max_info_id = $max_info_id_row['max_info_id'] ?: 0;
        $stmt->close();
    
        // Скидання AUTO_INCREMENT для таблиці information_about_animal
        $reset_auto_increment_info_query = "ALTER TABLE information_about_animal AUTO_INCREMENT = ?";
        $stmt = $con->prepare($reset_auto_increment_info_query);
        $stmt->bind_param("i", $max_info_id);
        if (!$stmt->execute()) {
            die("Error resetting AUTO_INCREMENT for information_about_animal: " . $stmt->error);
        }
        $stmt->close();
    
        // Перенаправлення на сторінку
        header("Location: animal_info.php");
        exit();
    }

    if (isset($_POST['update'])) {
        $animal_id = intval($_POST['update_animal_id']);
        $name = htmlspecialchars($_POST['update_name']);
        $type = htmlspecialchars($_POST['update_type']);
        $date_of_birth = $_POST['update_date_of_birth'];
        $information = htmlspecialchars($_POST['update_information']);

        if (!empty($name) && !empty($type) && !empty($date_of_birth) && !empty($information)) {
            // Оновлення тварини
            $update_animal_query = "UPDATE animal SET name = ?, type = ?, date_of_birth = ? WHERE animal_id = ?";
            $stmt_update_animal = mysqli_prepare($con, $update_animal_query);
            mysqli_stmt_bind_param($stmt_update_animal, "sssi", $name, $type, $date_of_birth, $animal_id);
            mysqli_stmt_execute($stmt_update_animal);

            if (mysqli_stmt_affected_rows($stmt_update_animal) > 0) {
                // Оновлення інформації
                $update_info_query = "UPDATE information_about_animal SET information = ? WHERE animal_id = ?";
                $stmt_update_info = mysqli_prepare($con, $update_info_query);
                mysqli_stmt_bind_param($stmt_update_info, "si", $information, $animal_id);
                mysqli_stmt_execute($stmt_update_info);

                if (mysqli_stmt_affected_rows($stmt_update_info) > 0) {
                    header("Location: animal_info.php");
                    exit();
                } else {
                    die("Error updating animal information: " . mysqli_error($con));
                }
            } else {
                die("Error updating animal: " . mysqli_error($con));
            }
        } else {
            echo "<p>Please fill in all fields to update the animal information.</p>";
        }
    }
}

$query = "SELECT i.information_about_animal_id, a.name AS animal_name, a.type AS animal_type, i.information FROM information_about_animal i JOIN animal a ON i.animal_id = a.animal_id";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Error accessing the database: " . mysqli_error($con));
}

$animal_query = "SELECT animal_id, name FROM animal";
$animal_result = mysqli_query($con, $animal_query);

if (!$animal_result) {
    die("Error fetching animals for delete: " . mysqli_error($con));
}
?>
<head>
    <meta charset="UTF-8">
    <title>Animal Information</title>
    <link rel="stylesheet" href="css\styles_info.css">
</head>
<body>
    <div class="username-display">
        <?php echo "Logged in as: " . htmlspecialchars($_SESSION["session_username"]); ?>
        <?php echo $is_admin ? " (Administrator)" : " (User)"; ?>
    </div>

    <h1 style="text-align: center;">Animal Information</h1>

    <center>
        <table border="1">
            <tr>
                <th>Animal Name</th>
                <th>Animal Type</th>
                <th>Information</th>
            </tr>

            <?php while ($row = mysqli_fetch_array($result)) { ?>
            <tr>
                <td><center><?php echo htmlspecialchars($row["animal_name"]); ?></center></td>
                <td><center><?php echo htmlspecialchars($row["animal_type"]); ?></center></td>
                <td><?php echo htmlspecialchars($row["information"]); ?></td>
            </tr>
            <?php } ?>
        </table>
    </center>

    <?php if ($is_admin): ?>
    <div class="form-container">
        <div class="form-left">
            <h2>Add New Animal</h2>
            <form action="animal_info.php" method="POST">
                <label for="name">Animal Name:</label><br>
                <input type="text" name="name" required><br><br>

                <label for="type">Animal Type:</label><br>
                <input type="text" name="type" required><br><br>

                <label for="date_of_birth">Date of Birth:</label><br>
                <input type="date" name="date_of_birth" required><br><br>

                <label for="information">Information:</label><br>
                <input type="text" name="information" required><br><br>

                <input type="submit" name="submit" value="Add Animal">
            </form>
        </div>

        <div class="form-right">
            <h2>Delete Animal</h2>
            <form action="animal_info.php" method="POST">
                <label for="delete_animal_id">Select Animal to Delete:</label><br>
                <select name="delete_animal_id" required>
                    <option value="">Select an animal</option>
                    <?php while ($row = mysqli_fetch_array($animal_result)) { ?>
                        <option value="<?php echo $row['animal_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                    <?php } ?>
                </select><br><br>

                <input type="submit" name="delete" value="Delete Animal">
            </form>
        </div>

        <div class="form-left">
            <h2>Update Animal Information</h2>
            <form action="animal_info.php" method="POST">
                <label for="update_animal_id">Select Animal to Update:</label><br>
                <select name="update_animal_id" required>
                    <option value="">Select an animal</option>
                    <?php
                    $animal_result = mysqli_query($con, "SELECT animal_id, name FROM animal");
                    while ($row = mysqli_fetch_array($animal_result)) { ?>
                        <option value="<?php echo $row['animal_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                    <?php } ?>
                </select><br><br>

                <label for="update_name">Animal Name:</label><br>
                <input type="text" name="update_name" required><br><br>

                <label for="update_type">Animal Type:</label><br>
                <input type="text" name="update_type" required><br><br>

                <label for="update_date_of_birth">Date of Birth:</label><br>
                <input type="date" name="update_date_of_birth" required><br><br>

                <label for="update_information">Information:</label><br>
                <input type="text" name="update_information" required><br><br>

                <input type="submit" name="update" value="Update Animal">
            </form>
        </div>
    </div>
    <?php endif; ?>

</body>
<div class="footer">
    <?php include("includes/footer.php"); ?>
</div>
</html>