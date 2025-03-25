<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include('config/db_connect.php');

$user_id = $_SESSION['user_id']; // Logged-in user's ID

// Handle Add to Wishlist
if (isset($_POST['add_to_wishlist'])) {
    $pizza_id = mysqli_real_escape_string($conn, $_POST['pizza_id']);

    // Check if the pizza is already in the wishlist
    $check_sql = "SELECT * FROM wishlist WHERE user_id = '$user_id' AND pizza_id = '$pizza_id'";
    $result = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($result) == 0) {
        // Add pizza to wishlist
        $position_sql = "SELECT MAX(position) AS max_position FROM wishlist WHERE user_id = '$user_id'";
        $position_result = mysqli_query($conn, $position_sql);
        $max_position = mysqli_fetch_assoc($position_result)['max_position'];
        $new_position = $max_position + 1;

        $insert_sql = "INSERT INTO wishlist (user_id, pizza_id, position) VALUES ('$user_id', '$pizza_id', '$new_position')";
        if (mysqli_query($conn, $insert_sql)) {
            $message = "Pizza added to wishlist!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } else {
        $message = "Pizza is already in your wishlist.";
    }
}

// Handle Remove from Wishlist
if (isset($_POST['remove_from_wishlist'])) {
    $wishlist_id = mysqli_real_escape_string($conn, $_POST['wishlist_id']);
    $delete_sql = "DELETE FROM wishlist WHERE id = '$wishlist_id' AND user_id = '$user_id'";
    if (mysqli_query($conn, $delete_sql)) {
        $message = "Pizza removed from wishlist!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle Reorder Wishlist
if (isset($_POST['reorder_wishlist'])) {
    $wishlist_id = mysqli_real_escape_string($conn, $_POST['wishlist_id']);
    $new_position = mysqli_real_escape_string($conn, $_POST['new_position']);

    // Update the position
    $update_sql = "UPDATE wishlist SET position = '$new_position' WHERE id = '$wishlist_id' AND user_id = '$user_id'";
    if (mysqli_query($conn, $update_sql)) {
        $message = "Wishlist reordered successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Fetch Wishlist for the User
$sql = "SELECT w.id AS wishlist_id, w.position, p.id AS pizza_id, p.title, p.ingredients, p.price 
        FROM wishlist w
        JOIN pizzas p ON w.pizza_id = p.id
        WHERE w.user_id = '$user_id'
        ORDER BY w.position ASC";
$result = mysqli_query($conn, $sql);
$wishlist = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<?php include('templates/header.php'); ?>

<h4 class="center grey-text">Your Wishlist</h4>

<div class="container">
    <?php if (isset($message)) echo "<p style='color: green;'>$message</p>"; ?>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

    <?php if ($wishlist): ?>
        <table class="striped">
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Pizza</th>
                    <th>Ingredients</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($wishlist as $item): ?>
                <tr>
                    <td>
                        <form method="POST" action="" style="display: inline;">
                            <input type="number" name="new_position" value="<?php echo $item['position']; ?>" style="width: 50px;">
                            <input type="hidden" name="wishlist_id" value="<?php echo $item['wishlist_id']; ?>">
                            <button type="submit" name="reorder_wishlist" class="btn blue z-depth-0">Reorder</button>
                        </form>
                    </td>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td><?php echo htmlspecialchars($item['ingredients']); ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="wishlist_id" value="<?php echo $item['wishlist_id']; ?>">
                            <button type="submit" name="remove_from_wishlist" class="btn red z-depth-0">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="center">Your wishlist is empty. Start adding pizzas!</p>
    <?php endif; ?>
</div>

<?php include('templates/footer.php'); ?>
</html>
