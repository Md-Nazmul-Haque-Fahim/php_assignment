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

// Fetch Order History for the User
$sql = "SELECT h.order_id, h.pizza_id, h.quantity, h.total_price, h.created_at, p.title 
        FROM order_history h
        JOIN pizzas p ON h.pizza_id = p.id
        WHERE h.user_id = '$user_id'
        ORDER BY h.created_at DESC";
$result = mysqli_query($conn, $sql);

// Check for SQL Errors
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Fetch the data
$order_history = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<?php include('templates/header.php'); ?>

<h4 class="center grey-text">Order History</h4>

<div class="container">
    <!-- If no orders exist -->
    <?php if (empty($order_history)): ?>
        <p class="red-text center">You have no orders yet.</p>
    <?php else: ?>
        <table class="striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Pizza</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_history as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['title']); ?></td>
                        <td><?php echo $order['quantity']; ?></td>
                        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                        <td><?php echo $order['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include('templates/footer.php'); ?>
</html>
