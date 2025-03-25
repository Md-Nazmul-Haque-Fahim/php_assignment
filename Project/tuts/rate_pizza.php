<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include('config/db_connect.php');

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user input
    $pizza_id = $_POST['pizza_id'];
    $rating = $_POST['rating'];
    $user_id = $_SESSION['user_id'];

    // Validate the input
    if ($rating < 1 || $rating > 5) {
        echo "Invalid rating. Please provide a rating between 1 and 5.";
        exit();
    }

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO pizza_ratings (pizza_id, user_id, rating) 
                            VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE rating = ?");
    $stmt->bind_param("iiii", $pizza_id, $user_id, $rating, $rating);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to the index page or a confirmation page
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
