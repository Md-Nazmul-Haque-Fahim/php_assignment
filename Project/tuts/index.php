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

$user_id = $_SESSION['user_id']; // Logged-in user's ID logout

// Get search and category filter values
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

// Build the WHERE clause for search and filters
$where_clauses = [];
if (!empty($search)) {
    $where_clauses[] = "p.title LIKE '%$search%' OR p.ingredients LIKE '%$search%'";
}
if (!empty($category)) {
    $where_clauses[] = "p.category = '$category'";
}
$where_sql = count($where_clauses) > 0 ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Fetch pizzas with the search and filter applied
$sql = "SELECT p.id, p.title, p.ingredients, p.price, p.stock, p.category, IFNULL(AVG(pr.rating), 0) as average_rating
        FROM pizzas p
        LEFT JOIN pizza_ratings pr ON p.id = pr.pizza_id
        $where_sql
        GROUP BY p.id, p.title, p.ingredients, p.price, p.stock, p.category
        ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $sql);
$pizzas = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);

// Fetch orders, favorites, and wishlist for the user
$orders_sql = "SELECT pizza_id FROM orders WHERE user_id = '$user_id'";
$favorites_sql = "SELECT pizza_id FROM favorites WHERE user_id = '$user_id'";
$wishlist_sql = "SELECT pizza_id FROM wishlist WHERE user_id = '$user_id'";//home

$orders_result = mysqli_query($conn, $orders_sql);
$favorites_result = mysqli_query($conn, $favorites_sql);
$wishlist_result = mysqli_query($conn, $wishlist_sql);

$orders = mysqli_fetch_all($orders_result, MYSQLI_ASSOC);
$favorites = mysqli_fetch_all($favorites_result, MYSQLI_ASSOC);
$wishlist = mysqli_fetch_all($wishlist_result, MYSQLI_ASSOC);

mysqli_free_result($orders_result);
mysqli_free_result($favorites_result);
mysqli_free_result($wishlist_result);

// Convert to arrays for quick lookup
$order_ids = array_column($orders, 'pizza_id');
$favorites_ids = array_column($favorites, 'pizza_id');
$wishlist_ids = array_column($wishlist, 'pizza_id');

// Handle Add to Cart
if (isset($_POST['add_to_order'])) {
    $pizza_id = mysqli_real_escape_string($conn, $_POST['pizza_id']);
    if (!in_array($pizza_id, $order_ids)) {
        $sql = "INSERT INTO orders (user_id, pizza_id, quantity) VALUES ('$user_id', '$pizza_id', 1)";
        mysqli_query($conn, $sql);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Add to Favorites
if (isset($_POST['add_favorite'])) {
    $pizza_id = mysqli_real_escape_string($conn, $_POST['pizza_id']);
    if (!in_array($pizza_id, $favorites_ids)) {
        $sql = "INSERT INTO favorites (user_id, pizza_id) VALUES ('$user_id', '$pizza_id')";
        mysqli_query($conn, $sql);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Remove from Favorites
if (isset($_POST['remove_favorite'])) {
    $pizza_id = mysqli_real_escape_string($conn, $_POST['pizza_id']);
    $sql = "DELETE FROM favorites WHERE user_id = '$user_id' AND pizza_id = '$pizza_id'";
    mysqli_query($conn, $sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Add to Wishlist
if (isset($_POST['add_to_wishlist'])) {
    $pizza_id = mysqli_real_escape_string($conn, $_POST['pizza_id']);
    if (!in_array($pizza_id, $wishlist_ids)) {
        $sql = "INSERT INTO wishlist (user_id, pizza_id) VALUES ('$user_id', '$pizza_id')";
        mysqli_query($conn, $sql);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Remove from Wishlist
if (isset($_POST['remove_from_wishlist'])) {
    $pizza_id = mysqli_real_escape_string($conn, $_POST['pizza_id']);
    $sql = "DELETE FROM wishlist WHERE user_id = '$user_id' AND pizza_id = '$pizza_id'";
    mysqli_query($conn, $sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Rating Submission
if (isset($_POST['rate_pizza'])) {
    $pizza_id = mysqli_real_escape_string($conn, $_POST['pizza_id']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $sql = "INSERT INTO pizza_ratings (user_id, pizza_id, rating) VALUES ('$user_id', '$pizza_id', '$rating')";
    mysqli_query($conn, $sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<?php include('templates/header.php'); ?>

<h4 class="center grey-text">Explore Our Pizzas</h4>

<!-- Navigation Buttons -->
<div class="container">
    <div class="row">
        <div class="col s12 m3">
            <a href="checkout.php" class="btn orange z-depth-0">Cart</a>
        </div>
        <div class="col s12 m3">
            <a href="order_history.php" class="btn green z-depth-0">Order History</a>
        </div>
        <div class="col s12 m3">
            <a href="wishlist.php" class="btn blue z-depth-0">Wishlist</a>
        </div>
        <div class="col s12 m3">
            <a href="../home.php" class="btn red z-depth-0">Logout</a>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="container">
    <form method="GET" action="" class="row">
        <!-- Search Bar -->
        <div class="input-field col s12 m6">
            <input type="text" name="search" id="search" placeholder="Search for pizzas..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </div>

        <!-- Filter Dropdown -->
        <div class="input-field col s12 m6">
            <select name="category" id="category">
                <option value="" <?php echo !isset($_GET['category']) ? 'selected' : ''; ?>>All Categories</option>
                <option value="Vegetarian" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Vegetarian') ? 'selected' : ''; ?>>Vegetarian</option>
                <option value="Non-Vegetarian" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Non-Vegetarian') ? 'selected' : ''; ?>>Non-Vegetarian</option>
                <option value="Vegan" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Vegan') ? 'selected' : ''; ?>>Vegan</option>
            </select>
        </div>

        <!-- Submit Button -->
        <div class="col s12 center">
            <button type="submit" class="btn blue z-depth-0">Search</button>
        </div>
    </form>
</div>

<!-- Pizza Listings -->
<div class="container">
    <div class="row">
        <?php if (empty($pizzas)): ?>
            <p class="red-text center">No pizzas found for your search and filter criteria.</p>
        <?php else: ?>
            <?php foreach ($pizzas as $pizza): ?>
            <div class="col s6 m3">
                <div class="card z-depth-0">
                    <div class="card-content center">
                        <h6><?php echo htmlspecialchars($pizza['title']); ?></h6>
                        <ul>
                            <?php foreach (explode(',', $pizza['ingredients']) as $ing): ?>
                            <li><?php echo htmlspecialchars($ing); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div>
                            <strong>Average Rating:</strong> <?php echo round($pizza['average_rating'], 1); ?>/5
                        </div>
                        <div>
                            <strong>Price:</strong> $<?php echo number_format($pizza['price'], 2); ?>
                        </div>
                        <div>
                            <strong>Stock:</strong>
                            <?php if ($pizza['stock'] > 0): ?>
                                <span class="green-text">Available</span>
                            <?php else: ?>
                                <span class="red-text">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-action center">
                        <!-- Add to Cart -->
                        <?php if (!in_array($pizza['id'], $order_ids) && $pizza['stock'] > 0): ?>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="pizza_id" value="<?php echo $pizza['id']; ?>">
                                <button type="submit" name="add_to_order" class="btn orange z-depth-0">Add to Cart</button>
                            </form>
                        <?php elseif ($pizza['stock'] <= 0): ?>
                            <button class="btn grey z-depth-0" disabled>Out of Stock</button>
                        <?php else: ?>
                            <button class="btn green z-depth-0" disabled>In Cart</button>
                        <?php endif; ?>

                        <!-- Add/Remove from Favorites -->
                        <?php if (in_array($pizza['id'], $favorites_ids)): ?>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="pizza_id" value="<?php echo $pizza['id']; ?>">
                                <button type="submit" name="remove_favorite" class="btn red z-depth-0">Unfavorite</button>
                            </form>
                        <?php else: ?>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="pizza_id" value="<?php echo $pizza['id']; ?>">
                                <button type="submit" name="add_favorite" class="btn green z-depth-0">Favorite</button>
                            </form>
                        <?php endif; ?>

                        <!-- Add/Remove from Wishlist -->
                        <?php if (in_array($pizza['id'], $wishlist_ids)): ?>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="pizza_id" value="<?php echo $pizza['id']; ?>">
                                <button type="submit" name="remove_from_wishlist" class="btn blue z-depth-0">Remove Wishlist</button>
                            </form>
                        <?php else: ?>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="pizza_id" value="<?php echo $pizza['id']; ?>">
                                <button type="submit" name="add_to_wishlist" class="btn yellow z-depth-0">Add to Wishlist</button>
                            </form>
                        <?php endif; ?>

                        <!-- Rating Form -->
                        <form action="" method="POST" style="margin-top: 10px;">
                            <label for="rating">Rate this pizza:</label>
                            <input type="number" name="rating" min="1" max="5" required>
                            <input type="hidden" name="pizza_id" value="<?php echo $pizza['id']; ?>">
                            <button type="submit" name="rate_pizza" class="btn brand z-depth-0">Submit Rating</button>
                        </form>

                        <!-- View Details -->
                        <a class="brand-text" href="details.php?id=<?php echo $pizza['id']; ?>">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include('templates/footer.php'); ?>
</html>