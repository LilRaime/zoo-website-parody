<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if (!isset($_SESSION["session_username"])) {
    header("Location: login.php");
    exit();
}

require_once("includes/connection.php");

// Перевірка на адміна
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
    // Додавання вольєра
    if (isset($_POST['submit'])) {
        $size = intval($_POST['size']);
        $location = htmlspecialchars($_POST['location']);
        $number = intval($_POST['number']);
        $animal_id = intval($_POST['animal_id']);
        $ticket_id = intval($_POST['ticket_id']);

        if (!empty($size) && !empty($location) && !empty($number)) {
            $insert_query = "INSERT INTO aviary (size, location, number, animal_id, admin_id, ticket_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $insert_query);
            mysqli_stmt_bind_param($stmt, "isiiii", $size, $location, $number, $animal_id, $admin_id, $ticket_id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                header("Location: aviary.php");
                exit();
            } else {
                die("Error adding aviary: " . mysqli_error($con));
            }
        } else {
            echo "<p>Please fill in all fields.</p>";
        }
    }

    // Видалення вольєра
    if (isset($_POST['delete'])) {
        $aviary_id = intval($_POST['delete_aviary_id']);

        // Видаляємо вольєр
        $delete_query = "DELETE FROM aviary WHERE aviary_id = ?";
        $stmt = mysqli_prepare($con, $delete_query);
        mysqli_stmt_bind_param($stmt, "i", $aviary_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Скидання AUTO_INCREMENT
            $query_max = "SELECT MAX(aviary_id) + 1 AS next_auto_increment FROM aviary";
            $result_max = mysqli_query($con, $query_max);
            $row_max = mysqli_fetch_assoc($result_max);
            $next_auto_increment = $row_max['next_auto_increment'];

            // Оновлення AUTO_INCREMENT
            $reset_query = "ALTER TABLE aviary AUTO_INCREMENT = $next_auto_increment";
            mysqli_query($con, $reset_query);

            header("Location: aviary.php");
            exit();
        } else {
            die("Error deleting aviary: " . mysqli_error($con));
        }
    }

    // Оновлення вольєра
    if (isset($_POST['update'])) {
        $aviary_id = intval($_POST['update_aviary_id']);
        $size = intval($_POST['update_size']);
        $location = htmlspecialchars($_POST['update_location']);
        $number = intval($_POST['update_number']);
        $animal_id = intval($_POST['update_animal_id']);
        $ticket_id = intval($_POST['update_ticket_id']);

        $update_query = "UPDATE aviary SET size = ?, location = ?, number = ?, animal_id = ?, ticket_id = ? WHERE aviary_id = ?";
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "isiiii", $size, $location, $number, $animal_id, $ticket_id, $aviary_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            header("Location: aviary.php");
            exit();
        } else {
            die("Error updating aviary: " . mysqli_error($con));
        }
    }
}

$query = "SELECT aviary_id, size, location, number, animal_id, admin_id, ticket_id FROM aviary";
$result = mysqli_query($con, $query);
?>
<head>
    <meta charset="UTF-8">
    <title>Aviary Information</title>
    <link rel="stylesheet" href="css/styles_aviary.css">
</head>
<body>
    <div class="username-display">
        <?php echo "Logged in as: " . htmlspecialchars($_SESSION["session_username"]); ?>
        <?php echo $is_admin ? " (Administrator)" : " (User)"; ?>
    </div>

    <h1 style="text-align: center;">Aviary Information</h1>

    <table border="1">
        <tr>
            <th>Aviary ID</th>
            <th>Size</th>
            <th>Location</th>
            <th>Number</th>
            <th>Animal ID</th>
            <th>Admin ID</th>
            <th>Ticket ID</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['aviary_id']; ?></td>
            <td><?php echo $row['size']; ?></td>
            <td><?php echo htmlspecialchars($row['location']); ?></td>
            <td><?php echo $row['number']; ?></td>
            <td><?php echo $row['animal_id']; ?></td>
            <td><?php echo $row['admin_id']; ?></td>
            <td><?php echo $row['ticket_id']; ?></td>
        </tr>
        <?php } ?>
    </table>

    <?php if ($is_admin): ?>
    <div class="form-container">
        <div class="form-left">
            <h2>Add New Aviary</h2>
            <form method="POST">
                <label>Size:</label><input type="number" name="size" required><br>
                <label>Location:</label><input type="text" name="location" required><br>
                <label>Number:</label><input type="number" name="number" required><br>
                <label>Animal ID:</label><input type="number" name="animal_id"><br>
                <label>Ticket ID:</label><input type="number" name="ticket_id"><br>
                <input type="submit" name="submit" value="Add Aviary">
            </form>
        </div>

        <div class="form-right">
            <h2>Delete Aviary</h2>
            <form method="POST">
                <label>Select Aviary:</label>
                <select name="delete_aviary_id">
                    <?php
                    $aviary_result = mysqli_query($con, "SELECT aviary_id FROM aviary");
                    while ($row = mysqli_fetch_assoc($aviary_result)) {
                        echo "<option value='{$row['aviary_id']}'>Aviary ID {$row['aviary_id']}</option>";
                    }
                    ?>
                </select>
                <input type="submit" name="delete" value="Delete Aviary">
            </form>
        </div>

        <div class="form-left">
            <h2>Update Aviary</h2>
            <form method="POST">
                <label>Select Aviary:</label>
                <select name="update_aviary_id">
                    <?php
                    $aviary_result = mysqli_query($con, "SELECT aviary_id FROM aviary");
                    while ($row = mysqli_fetch_assoc($aviary_result)) {
                        echo "<option value='{$row['aviary_id']}'>Aviary ID {$row['aviary_id']}</option>";
                    }
                    ?>
                </select><br>
                <label>Size:</label><input type="number" name="update_size" required><br>
                <label>Location:</label><input type="text" name="update_location" required><br>
                <label>Number:</label><input type="number" name="update_number" required><br>
                <label>Animal ID:</label><input type="number" name="update_animal_id"><br>
                <label>Ticket ID:</label><input type="number" name="update_ticket_id"><br>
                <input type="submit" name="update" value="Update Aviary">
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="footer">
        <?php include("includes/footer.php"); ?>
    </div>
</body>
</html>
