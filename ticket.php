<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if (!isset($_SESSION["session_username"])) {
    header("Location: login.php");
    exit();
}

require_once("includes/connection.php");

$username = htmlspecialchars($_SESSION["session_username"], ENT_QUOTES, 'UTF-8'); 

// Підготовлений звіт для перевірки відвідувача
$query_visitor = "SELECT visitor_id FROM visitor WHERE username = ?";
$stmt_visitor = mysqli_prepare($con, $query_visitor);
mysqli_stmt_bind_param($stmt_visitor, "s", $username);
mysqli_stmt_execute($stmt_visitor);
$result_visitor = mysqli_stmt_get_result($stmt_visitor);

$is_visitor = (mysqli_num_rows($result_visitor) > 0);

if ($is_visitor) {
    header("Location: ticket_buy.php"); 
    exit(); 
}

// Перевірка на адміна
$query_user = "SELECT admin_id FROM admin WHERE username = ?";
$stmt_user = mysqli_prepare($con, $query_user);
mysqli_stmt_bind_param($stmt_user, "s", $username);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);

$is_admin = (mysqli_num_rows($result_user) > 0);
$admin_id = null;

if ($is_admin) {
    $row_admin = mysqli_fetch_assoc($result_user);
    $admin_id = $row_admin["admin_id"];
} else {
    // Перевірка на менеджера
    $query_manager = "SELECT manager_id FROM manager WHERE username = ?";
    $stmt_manager = mysqli_prepare($con, $query_manager);
    mysqli_stmt_bind_param($stmt_manager, "s", $username);
    mysqli_stmt_execute($stmt_manager);
    $result_manager = mysqli_stmt_get_result($stmt_manager);

    $is_manager = (mysqli_num_rows($result_manager) > 0);

    if ($is_manager) {
        $row_manager = mysqli_fetch_assoc($result_manager);
        $admin_id = $row_manager["manager_id"];
    }
}

if ($is_admin) {
    $is_manager = false;
    // Додавання нового ticket order
    if (isset($_POST['submit'])) {
        $type = isset($_POST['type']) && in_array($_POST['type'], ['Child', 'Adult', 'Senior']) 
            ? htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8') 
            : '';
        
            if (empty($type)) {
                echo "<p>Please select a valid ticket type.</p>";
            }
        
            // Валідація кількості квитків
            $quantity = isset($_POST['quantity']) ? filter_var($_POST['quantity'], FILTER_VALIDATE_INT) : 0;
            if ($quantity <= 0) {
                echo "<p>Please enter a valid quantity greater than 0.</p>";
            }

        $price = match($type) {
            'Child' => 75,
            'Adult' => 150,
            'Senior' => 95,
            default => 0
        };
        
        $quantity = isset($_POST['quantity']) ? filter_var($_POST['quantity'], FILTER_VALIDATE_INT) : 0;  
        $visitor_id = isset($_POST['visitor_id']) ? filter_var($_POST['visitor_id'], FILTER_VALIDATE_INT) : 0;
        $manager_id = isset($_POST['manager_id']) ? filter_var($_POST['manager_id'], FILTER_VALIDATE_INT) : 0;
        $status = 'pending';
    
        if (!empty($type) && !empty($price) && !empty($quantity)) {
            $insert_ticket_query = "INSERT INTO ticket_order (ticket_type, price, quantity, admin_id, visitor_id, manager_id, status) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $insert_ticket_query);
            mysqli_stmt_bind_param($stmt, "siiiiss", $type, $price, $quantity, $admin_id, $visitor_id, $manager_id, $status);
    
            if (mysqli_stmt_execute($stmt)) {
                header("Location: ticket.php");
                exit();
            } else {
                die("Error adding ticket order: " . mysqli_error($con));
            }
        } else {
            echo "<p>Please fill in all required fields.</p>";
        }
    }

    // Видалення квитка
    if (isset($_POST['delete'])) {
        $delete_ticket_id = isset($_POST['delete_ticket_id']) ? filter_var($_POST['delete_ticket_id'], FILTER_VALIDATE_INT) : 0;

        $delete_ticket_query = "DELETE FROM ticket WHERE ticket_id = ?";
        $stmt = mysqli_prepare($con, $delete_ticket_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_ticket_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $query_max_ticket_id = "SELECT MAX(ticket_id) AS max_id FROM ticket";
            $result_max_ticket_id = mysqli_query($con, $query_max_ticket_id);
            
            if ($result_max_ticket_id) {
                $row = mysqli_fetch_assoc($result_max_ticket_id);
                $max_ticket_id = intval($row['max_id']) + 1;

                // Скидаємо AUTO_INCREMENT до наступного ID
                $reset_auto_increment_ticket_query = "ALTER TABLE ticket AUTO_INCREMENT = ?";
                $stmt_reset = mysqli_prepare($con, $reset_auto_increment_ticket_query);
                mysqli_stmt_bind_param($stmt_reset, "i", $max_ticket_id);
                
                if (!mysqli_stmt_execute($stmt_reset)) {
                    die("Error resetting AUTO_INCREMENT: " . mysqli_error($con));
                }
            }

            header("Location: ticket.php");
            exit();
        } else {
            die("Error deleting ticket: " . mysqli_error($con));
        }
    }


    // Оновлення квитка
    if (isset($_POST['update'])) {
        $ticket_id = isset($_POST['update_ticket_id']) ? filter_var($_POST['update_ticket_id'], FILTER_VALIDATE_INT) : 0;
        $type = isset($_POST['update_type']) ? htmlspecialchars($_POST['update_type'], ENT_QUOTES, 'UTF-8') : '';
        $price = isset($_POST['update_price']) ? filter_var($_POST['update_price'], FILTER_VALIDATE_INT) : 0;
        $visitor_id = isset($_POST['update_visitor_id']) ? filter_var($_POST['update_visitor_id'], FILTER_VALIDATE_INT) : 0;
        $manager_id = isset($_POST['update_manager_id']) ? filter_var($_POST['update_manager_id'], FILTER_VALIDATE_INT) : 0;
    
        if (!empty($type) && !empty($price)) {
            $update_ticket_query = "UPDATE ticket SET type = ?, price = ?, visitor_id = ?, manager_id = ? WHERE ticket_id = ?";
            $stmt = mysqli_prepare($con, $update_ticket_query);
            mysqli_stmt_bind_param($stmt, "siiii", $type, $price, $visitor_id, $manager_id, $ticket_id);
    
            if (mysqli_stmt_execute($stmt)) {
                header("Location: ticket.php");
                exit();
            } else {
                die("Error updating ticket: " . mysqli_error($con));
            }
        } else {
            echo "<p>Please fill in all required fields to update the ticket information.</p>";
        }
    }
}

if ($is_manager) {
        if (isset($_POST['confirm_order'])) {
        $order_id = isset($_POST['confirm_order_id']) ? filter_var($_POST['confirm_order_id'], FILTER_VALIDATE_INT) : 0;

        // Отримуємо дані про замовлення
        $order_query = "SELECT * FROM ticket_order WHERE order_id = ?";
        $stmt_order = mysqli_prepare($con, $order_query);
        mysqli_stmt_bind_param($stmt_order, "i", $order_id);
        mysqli_stmt_execute($stmt_order);
        $order_result = mysqli_stmt_get_result($stmt_order);

        if (mysqli_num_rows($order_result) > 0) {
            $order = mysqli_fetch_assoc($order_result);
            $type = htmlspecialchars($order['ticket_type'], ENT_QUOTES, 'UTF-8');
            $price = intval($order['price']);
            $visitor_id = intval($order['visitor_id']);
            $admin_id = intval($order['admin_id']); 

            // Перевірка на менеджера
            $manager_query = "SELECT manager_id FROM manager WHERE username = ?";
            $stmt_manager = mysqli_prepare($con, $manager_query);
            mysqli_stmt_bind_param($stmt_manager, "s", $username);
            mysqli_stmt_execute($stmt_manager);
            $manager_result = mysqli_stmt_get_result($stmt_manager);

            if (mysqli_num_rows($manager_result) > 0) {
                $manager = mysqli_fetch_assoc($manager_result);
                $manager_id = intval($manager['manager_id']);

                // Вставка даних в таблицю ticket
                $insert_ticket_query = "INSERT INTO ticket (type, price, visitor_id, manager_id, admin_id) VALUES (?, ?, ?, ?, ?)";
                $stmt_ticket = mysqli_prepare($con, $insert_ticket_query);
                mysqli_stmt_bind_param($stmt_ticket, "siiii", $type, $price, $visitor_id, $manager_id, $admin_id);

                if (mysqli_stmt_execute($stmt_ticket)) {
                    // Видалення замовлення з ticket_order
                    $delete_order_query = "DELETE FROM ticket_order WHERE order_id = ?";
                    $stmt_delete = mysqli_prepare($con, $delete_order_query);
                    mysqli_stmt_bind_param($stmt_delete, "i", $order_id);
                    mysqli_stmt_execute($stmt_delete);
                    
                    header("Location: ticket.php");
                    exit();
                } else {
                    die("Error moving ticket to main table: " . mysqli_error($con));
                }
            } else {
                die("Error: Manager not found.");
            }
        } else {
            die("Order not found or already processed.");
        }
    }
}

$query = "SELECT * FROM ticket";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Error accessing the database: " . mysqli_error($con));
}

$ticket_query = "SELECT ticket_id FROM ticket";
$ticket_result = mysqli_query($con, $ticket_query);

if (!$ticket_result) {
    die("Error fetching tickets for delete: " . mysqli_error($con));
}
?>

<head>
    <meta charset="UTF-8">
    <title>Ticket Information</title>
    <link rel="stylesheet" href="css\styles_info.css">
</head>
<body>
    <div class="username-display">
        <?php echo "Logged in as: " . htmlspecialchars($_SESSION["session_username"]); ?>
        <?php echo $is_admin ? " (Administrator)" : " (Manager)"; ?>
    </div>

    <h1 style="text-align: center;">Ticket Information</h1>

    <center>
        <table border="1">
            <tr>
                <th>Number</th>
                <th>Type</th>
                <th>Price</th>
                <th>Visitor ID</th>
                <th>Manager ID</th>
                <th>Admin ID</th>
            </tr>

            <?php while ($row = mysqli_fetch_array($result)) { ?>
            <tr>
                <td><center><?php echo htmlspecialchars($row["ticket_id"]); ?></center></td>
                <td><center><?php echo htmlspecialchars($row["type"]); ?></center></td>
                <td><?php echo htmlspecialchars($row["price"]); ?></td>
                <td><?php echo htmlspecialchars($row["visitor_id"]); ?></td>
                <td><?php echo htmlspecialchars($row["manager_id"]); ?></td>
                <td><?php echo htmlspecialchars($row["admin_id"]); ?></td>
            </tr>
            <?php } ?>
        </table>
    </center>

    <?php if ($is_admin): ?>
    <div class="form-container">
    <div class="form-left">
        <h2>Add New Ticket Order</h2>
        <form action="ticket.php" method="POST">
            <label for="type">Ticket Type:</label><br>
            <select id="type" name="type" required>
                <option value="">Select a ticket type</option>
                <option value="Child" <?php if (isset($_POST['type']) && $_POST['type'] == 'Child') echo 'selected'; ?>>Child</option>
                <option value="Adult" <?php if (isset($_POST['type']) && $_POST['type'] == 'Adult') echo 'selected'; ?>>Adult</option>
                <option value="Senior" <?php if (isset($_POST['type']) && $_POST['type'] == 'Senior') echo 'selected'; ?>>Senior</option>
            </select><br><br>

            <label for="quantity">Quantity:</label><br>
            <input type="number" name="quantity" required><br><br>

            <label for="visitor_id">Visitor ID:</label><br>
            <input type="number" name="visitor_id"><br><br>
            
            <label for="manager_id">Manger ID:</label><br>
            <input type="number" name="manager_id"><br><br>

            <input type="submit" name="submit" value="Add Ticket Order">
        </form>
    </div>

        <div class="form-right">
            <h2>Delete Ticket</h2>
            <form action="ticket.php" method="POST">
                <label for="delete_ticket_id">Select Ticket to Delete:</label><br>
                <select name="delete_ticket_id" required>
                    <option value="">Select a ticket</option>
                    <?php while ($row = mysqli_fetch_array($ticket_result)) { ?>
                        <option value="<?php echo $row['ticket_id']; ?>"><?php echo htmlspecialchars($row['ticket_id']); ?></option>
                    <?php } ?>
                </select><br><br>

                <input type="submit" name="delete" value="Delete Ticket">
            </form>
        </div>

        <div class="form-left">
        <h2>Update Ticket Information</h2>
        <form action="ticket.php" method="POST">
            <label for="update_ticket_id">Select Ticket to Update:</label><br>
            <select name="update_ticket_id" required>
                <option value="">Select a ticket</option>
                <?php
                $ticket_query = "SELECT ticket_id, type, price, visitor_id, manager_id FROM ticket";
                $ticket_result = mysqli_query($con, $ticket_query);
                
                // Зберігати обрані дані квитка, якщо квиток було обрано раніше
                $selected_ticket = null;
                if (isset($_POST['update_ticket_id'])) {
                    while ($row = mysqli_fetch_assoc($ticket_result)) {
                        if ($row['ticket_id'] == $_POST['update_ticket_id']) {
                            $selected_ticket = $row;
                            break;
                        }
                    }
                    mysqli_data_seek($ticket_result, 0);
                }

                while ($row = mysqli_fetch_array($ticket_result)) {
                ?>
                    <option value="<?php echo $row['ticket_id']; ?>" 
                        <?php echo (isset($_POST['update_ticket_id']) && $row['ticket_id'] == $_POST['update_ticket_id']) ? 'selected' : ''; ?>>
                        <?php echo $row['ticket_id']; ?>
                    </option>
                <?php } ?>
            </select><br><br>

            <label for="update_type">Type:</label><br>
            <select name="update_type" required>
                <option value="">Select ticket type</option>
                <option value="Child" <?php echo ($selected_ticket && $selected_ticket['type'] == 'Child') ? 'selected' : ''; ?>>Child</option>
                <option value="Adult" <?php echo ($selected_ticket && $selected_ticket['type'] == 'Adult') ? 'selected' : ''; ?>>Adult</option>
                <option value="Senior" <?php echo ($selected_ticket && $selected_ticket['type'] == 'Senior') ? 'selected' : ''; ?>>Senior</option>
            </select><br><br>

            <label for="update_price">Price:</label><br>
            <input type="number" name="update_price" required 
                value="<?php echo $selected_ticket ? htmlspecialchars($selected_ticket['price']) : ''; ?>"><br><br>

            <label for="update_visitor_id">Visitor ID:</label><br>
            <input type="number" name="update_visitor_id" 
                value="<?php echo $selected_ticket ? htmlspecialchars($selected_ticket['visitor_id']) : ''; ?>"><br><br>

            <label for="update_manager_id">Manager ID:</label><br>
            <input type="number" name="update_manager_id" 
                value="<?php echo $selected_ticket ? htmlspecialchars($selected_ticket['manager_id']) : ''; ?>"><br><br>

            <input type="submit" name="update" value="Update Ticket">
            </form>
        </div>
    <?php endif; ?>

    <?php if ($is_manager): ?>
    <h2>Confirm Ticket Orders</h2>
    <form action="ticket.php" method="POST">
        <label for="confirm_order_id">Select Order to Confirm:</label>
        <select name="confirm_order_id" required>
            <option value="">Select an order</option>
        <?php
        // Отримання записів зі статусом 'pending' з таблиці ticket_order
        $order_query = "SELECT order_id, ticket_type, quantity, price FROM ticket_order WHERE status = 'pending'";
        $result_orders = mysqli_query($con, $order_query);

        if ($result_orders) {
            while ($row = mysqli_fetch_assoc($result_orders)) {
                echo "<option value='{$row['order_id']}'>
                        Order #{$row['order_id']} | Type: {$row['ticket_type']} | Qty: {$row['quantity']} | Price: {$row['price']}
                      </option>";
            }
        } else {
            echo "<option value=''>No pending orders</option>";
        }
        ?>
        </select>
        <br><br>
        <input type="submit" name="confirm_order" value="Confirm Order">
    </form>
    <?php endif; ?>
    <div class="footer">
        <?php include("includes/footer.php"); ?>
    </div>

</body>
</html>