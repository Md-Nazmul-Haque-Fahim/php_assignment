<?php
include('config/db_connect.php');

if (isset($_POST['delete'])) {
    $id_to_delete = mysqli_real_escape_string($conn, $_POST['id_to_delete']);

    // Delete from orders where pizza_id matches
    $delete_orders_sql = "DELETE FROM orders WHERE pizza_id = $id_to_delete";
    if (mysqli_query($conn, $delete_orders_sql)) {
        // Now delete the pizza
        $sql = "DELETE FROM pizzas WHERE id = $id_to_delete";
        if (mysqli_query($conn, $sql)) {
            // Success
            header('Location: index.php');
        } else {
            echo 'Query error while deleting pizza: ' . mysqli_error($conn);
        }
    } else {
        echo 'Query error while deleting orders: ' . mysqli_error($conn);
    }
}

// Check if the 'id' parameter is set
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Prepare the SQL statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM pizzas WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id); // Bind the parameter as an integer

    // Execute the query
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the pizza data
    $pizza = mysqli_fetch_assoc($result);

    // Close the statement
    mysqli_stmt_close($stmt);
    
    // Close the connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>

<?php include('templates/header.php'); ?>

<div class="container center">
    <?php if ($pizza) : ?>
        <h2><?php echo htmlspecialchars($pizza['title']); ?></h2>
        <p>Created by: <?php echo htmlspecialchars($pizza['email']); ?></p>
        <p>Created at: <?php echo date($pizza['created_at']); ?></p>
        <h5>Ingredients:</h5>
        <p><?php echo htmlspecialchars($pizza['ingredients']); ?></p>

        <!-- DELETE FORM -->
        <form action="details.php" method="POST">
            <input type="hidden" name="id_to_delete" value="<?php echo $pizza['id'] ?>">
            <input type="submit" name="delete" value="Delete" class="btn brand z-depth-0">
        </form>

    <?php else : ?>
        <p>Pizza not found.</p>
    <?php endif; ?>
</div>

<?php include('templates/footer.php'); ?>

</html>
