<!DOCTYPE html>
<html lang="en">
<?php
session_start();

// Перевірка на авторизацію
if (!isset($_SESSION["session_username"])) {
    header("Location: login.php");
    exit();
}

require_once("includes/connection.php");

$username = $_SESSION["session_username"];

// Отримуємо visitor_id з бази даних за ім'ям користувача
$query_visitor = "SELECT visitor_id FROM visitor WHERE username = '$username'";
$result_visitor = mysqli_query($con, $query_visitor);

if ($result_visitor && mysqli_num_rows($result_visitor) > 0) {
    $row_visitor = mysqli_fetch_assoc($result_visitor);
    $visitor_id = intval($row_visitor['visitor_id']);
} else {
    die("Error: Unable to find the visitor_id for the current user.");
}

// Обробка форми замовлення квитків
if (isset($_POST['order'])) {
    $ticket_type = $_POST['ticket_type'];
    $quantity = intval($_POST['quantity']);

    // Перевірка, чи існує вибраний тип квитка
    $stmt = $con->prepare("SELECT COUNT(*) AS count FROM ticket WHERE type = ?");
    $stmt->bind_param("s", $ticket_type);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['count'] > 0) {
        // Перевірка доступних квитків
        $stmt = $con->prepare("SELECT order_id FROM ticket_order WHERE ticket_type = ? AND status = 'pending' LIMIT ?");
        $stmt->bind_param("si", $ticket_type, $quantity);
        $stmt->execute();
        $result_tickets = $stmt->get_result();

        if ($result_tickets->num_rows >= $quantity) {
            $ticket_ids = [];
            while ($row = $result_tickets->fetch_assoc()) {
                $ticket_ids[] = $row['order_id'];
            }

            $ticket_ids_string = implode(',', $ticket_ids);

            $stmt = $con->prepare("UPDATE ticket_order SET visitor_id = ?, status = 'pending' WHERE order_id IN ($ticket_ids_string)");
            $stmt->bind_param("i", $visitor_id);
            if ($stmt->execute()) {
                echo "<p>Your order has been successfully placed! Number of tickets: $quantity.</p>";
            } else {
                die("Error when updating tickets: " . $stmt->error);
            }
        } else {
            echo "<p>Not enough tickets of the selected ticket type.</p>";
        }
    } else {
        echo "<p>Invalid ticket type selected.</p>";
    }
}


// Отримуємо дані про типи та ціни квитків
$query = "SELECT type, price FROM ticket GROUP BY type, price";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Database access error: " . mysqli_error($con));
}
?>

<head>
    <meta charset="UTF-8">
    <title>Ticket Information</title>
    <link rel="stylesheet" href="css/styles_info.css">
</head>
<body>
    <div class="username-display">
        <?php echo "Logged in as: " . htmlspecialchars($_SESSION["session_username"]); ?>
    </div>

    <h1 style="text-align: center;">Ticket Information</h1>

    <center>
        <table border="1">
            <tr>
                <th>Ticket Type</th>
                <th>Price</th>
            </tr>

            <?php while ($row = mysqli_fetch_array($result)) { ?>
            <tr>
                <td><center><?php echo htmlspecialchars($row["type"]); ?></center></td>
                <td><center><?php echo htmlspecialchars($row["price"]); ?></center></td>
            </tr>
            <?php } ?>
        </table>
    </center>

    <div class="form-container">
        <h2>Order Ticket</h2>
        <form action="ticket_buy.php" method="POST">
            <label for="ticket_type">Select Ticket Type:</label><br>
            <select name="ticket_type" required>
                <option value="">Select a ticket type</option>
                <?php
                $type_result = mysqli_query($con, "SELECT DISTINCT type FROM ticket");
                while ($row = mysqli_fetch_array($type_result)) { ?>
                    <option value="<?php echo htmlspecialchars($row['type']); ?>"><?php echo htmlspecialchars($row['type']); ?></option>
                <?php } ?>
            </select><br><br>

            <label for="quantity">Number of Tickets:</label><br>
            <input type="number" name="quantity" min="1" required><br><br>

            <input type="submit" name="order" value="Order Ticket">
        </form>
    </div>
</body>
<div class="footer">
    <?php include("includes/footer.php"); ?>
</div>
</html>
