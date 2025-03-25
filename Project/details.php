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
$pizza_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : null;

// Fetch pizza details
$sql = "SELECT * FROM pizzas WHERE id = '$pizza_id'";
$result = mysqli_query($conn, $sql);
$pizza = mysqli_fetch_assoc($result);
mysqli_free_result($result);

if (!$pizza) {
    die("Pizza not found.");
}

// Fetch reviews for the pizza
$sql = "SELECT r.id, r.review, r.created_at, r.updated_at, u.username 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id
        WHERE r.pizza_id = '$pizza_id'
        ORDER BY r.created_at DESC";
$result = mysqli_query($conn, $sql);
$reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);

// Handle adding a review
if (isset($_POST['add_review'])) {
    $review_text = mysqli_real_escape_string($conn, $_POST['review']);
    $sql = "INSERT INTO reviews (user_id, pizza_id, review) VALUES ('$user_id', '$pizza_id', '$review_text')";
    if (mysqli_query($conn, $sql)) {
        header("Location: details.php?id=$pizza_id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle deleting a review
if (isset($_POST['delete_review'])) {
    $review_id = mysqli_real_escape_string($conn, $_POST['review_id']);
    $sql = "DELETE FROM reviews WHERE id = '$review_id' AND user_id = '$user_id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: details.php?id=$pizza_id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle editing a review
if (isset($_POST['edit_review'])) {
    $review_id = mysqli_real_escape_string($conn, $_POST['review_id']);
    $review_text = mysqli_real_escape_string($conn, $_POST['review']);
    $sql = "UPDATE reviews SET review = '$review_text' WHERE id = '$review_id' AND user_id = '$user_id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: details.php?id=$pizza_id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<?php include('templates/header.php'); ?>

<h4 class="center grey-text"><?php echo htmlspecialchars($pizza['title']); ?></h4>
<div class="container">
    <p><?php echo htmlspecialchars($pizza['ingredients']); ?></p>
    <p><strong>Price:</strong> $<?php echo number_format($pizza['price'], 2); ?></p>
</div>

<hr>

<!-- Reviews Section -->
<div class="container">
    <h5>Reviews</h5>
    <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $review): ?>
            <div class="card z-depth-0">
                <div class="card-content">
                    <p><strong><?php echo htmlspecialchars($review['username']); ?></strong> (<?php echo $review['created_at']; ?>)</p>
                    <p><?php echo htmlspecialchars($review['review']); ?></p>
                    <?php if ($review['updated_at'] != $review['created_at']): ?>
                        <small>(Edited)</small>
                    <?php endif; ?>
                </div>
                <?php if ($review['username'] === $_SESSION['username']): ?>
                    <div class="card-action">
                        <!-- Edit Review -->
                        <form action="" method="POST" style="display: inline;">
                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                            <textarea name="review" required><?php echo htmlspecialchars($review['review']); ?></textarea>
                            <button type="submit" name="edit_review" class="btn blue z-depth-0">Edit</button>
                        </form>
                        <!-- Delete Review -->
                        <form action="" method="POST" style="display: inline;">
                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                            <button type="submit" name="delete_review" class="btn red z-depth-0">Delete</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No reviews yet. Be the first to review this pizza!</p>
    <?php endif; ?>

    <!-- Add Review -->
    <form action="" method="POST">
        <textarea name="review" placeholder="Write a review..." required></textarea>
        <button type="submit" name="add_review" class="btn green z-depth-0">Submit Review</button>
    </form>
</div>

<?php include('templates/footer.php'); ?>
</html>
