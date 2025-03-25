<?php
session_start();
include('config/db_connect.php');

if (isset($_POST['rating']) && isset($_POST['pizza_id']) && isset($_SESSION['user_id'])) {
    $rating = intval($_POST['rating']);
    $pizza_id = intval($_POST['pizza_id']);
    $user_id = intval($_SESSION['user_id']);

    // Check if the user has already rated this pizza
    $stmt = $conn->prepare("SELECT * FROM pizza_ratings WHERE user_id = ? AND pizza_id = ?");
    $stmt->bind_param("ii", $user_id, $pizza_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User has already rated this pizza, update the rating
        $stmt = $conn->prepare("UPDATE pizza_ratings SET rating = ?, created_at = CURRENT_TIMESTAMP WHERE user_id = ? AND pizza_id = ?");
        $stmt->bind_param("iii", $rating, $user_id, $pizza_id);
    } else {
        // Insert new rating
        $stmt = $conn->prepare("INSERT INTO pizza_ratings (user_id, pizza_id, rating) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $pizza_id, $rating);
    }

    if ($stmt->execute()) {
        // Update the average rating in the pizzas table
        $stmt = $conn->prepare("UPDATE pizzas SET average_rating = (SELECT AVG(rating) FROM pizza_ratings WHERE pizza_id = ?) WHERE id = ?");
        $stmt->bind_param("ii", $pizza_id, $pizza_id);
        $stmt->execute();
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
}
