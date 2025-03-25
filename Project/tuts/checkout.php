<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include('config/db_connect.php');

$user_id = $_SESSION['user_id']; // Logged-in user's ID

// Handle Remove Item
if (isset($_POST['remove_item'])) {
    $pizza_id = mysqli_real_escape_string($conn, $_POST['pizza_id']);
    $sql = "DELETE FROM orders WHERE user_id = '$user_id' AND pizza_id = '$pizza_id'";
    if (!mysqli_query($conn, $sql)) {
        die("Error Removing Item: " . mysqli_error($conn));
    }
}

// Handle Quantity Adjustment
if (isset($_POST['update_quantity'])) {
    $pizza_id = mysqli_real_escape_string($conn, $_POST['pizza_id']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    if ($quantity > 0) {
        $sql = "UPDATE orders SET quantity = '$quantity' WHERE user_id = '$user_id' AND pizza_id = '$pizza_id'";
        if (!mysqli_query($conn, $sql)) {
            die("Error Updating Quantity: " . mysqli_error($conn));
        }
    }
}

// Fetch Orders for the User
$sql = "SELECT o.pizza_id, o.quantity, p.title, p.price 
        FROM orders o
        JOIN pizzas p ON o.pizza_id = p.id
        WHERE o.user_id = '$user_id'";
$result = mysqli_query($conn, $sql);

// Check for SQL Errors
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Fetch the data
$order_items = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);

// Handle Order Confirmation
if (isset($_POST['confirm_order'])) {
    // Generate a unique order ID (e.g., timestamp)
    $order_id = time(); // Use the current timestamp as a unique order ID

    foreach ($order_items as $item) {
        $pizza_id = $item['pizza_id'];
        $quantity = $item['quantity'];
        $total_price = $item['quantity'] * $item['price'];

        // Insert into order_history
        $insert_sql = "INSERT INTO order_history (user_id, order_id, pizza_id, quantity, total_price)
                       VALUES ('$user_id', '$order_id', '$pizza_id', '$quantity', '$total_price')";
        if (!mysqli_query($conn, $insert_sql)) {
            die("Error Saving Order History: " . mysqli_error($conn));
        }
    }

    // Clear current orders
    $delete_sql = "DELETE FROM orders WHERE user_id = '$user_id'";
    if (!mysqli_query($conn, $delete_sql)) {
        die("Error Clearing Orders: " . mysqli_error($conn));
    }

    $order_confirmed = true; // This flag will display the confirmation message
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<?php include('templates/header.php'); ?>

<h4 class="center grey-text">Your Cart</h4>

<div class="container">
    <?php if (isset($order_confirmed) && $order_confirmed): ?>
        <p class="green-text center">Your order is confirmed!</p>
    <?php endif; ?>

    <table class="striped">
        <thead>
            <tr>
                <th>Pizza</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $grand_total = 0; ?>
            <?php foreach ($order_items as $item): ?>
                <?php $total_price = $item['quantity'] * $item['price']; ?>
                <?php $grand_total += $total_price; ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td>
                        <!-- Quantity Adjustment Form -->
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="pizza_id" value="<?php echo $item['pizza_id']; ?>">
                            <!-- Decrease Quantity -->
                            <input type="hidden" name="quantity" value="<?php echo $item['quantity'] - 1; ?>">
                            <button type="submit" name="update_quantity" class="btn-small" <?php if ($item['quantity'] <= 1) echo 'disabled'; ?>>-</button>
                        </form>
                        <span><?php echo $item['quantity']; ?></span>
                        <!-- Increase Quantity -->
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="pizza_id" value="<?php echo $item['pizza_id']; ?>">
                            <input type="hidden" name="quantity" value="<?php echo $item['quantity'] + 1; ?>">
                            <button type="submit" name="update_quantity" class="btn-small">+</button>
                        </form>
                    </td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>$<?php echo number_format($total_price, 2); ?></td>
                    <td>
                        <!-- Remove Item Form -->
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="pizza_id" value="<?php echo $item['pizza_id']; ?>">
                            <button type="submit" name="remove_item" class="btn red z-depth-0">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Grand Total</th>
                <th>$<?php echo number_format($grand_total, 2); ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <!-- Confirm Order Button -->
    <div class="center" style="margin-top: 20px;">
        <form action="" method="POST">
            <button type="submit" name="confirm_order" class="btn green z-depth-0">Confirm Order</button>
        </form>
    </div>
</div>

<?php include('templates/footer.php'); ?>
</html>
