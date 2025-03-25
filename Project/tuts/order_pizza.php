<?php
session_start();
include('config/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['pizza_id'])) {
    $user_id = $_SESSION['user_id'];
    $pizza_id = $_POST['pizza_id'];

    // Check if the pizza is already in the user's order list
    $check_query = "SELECT * FROM orders WHERE user_id = $user_id AND pizza_id = $pizza_id";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // If the pizza is already in the order list, increase the quantity
        $update_query = "UPDATE orders SET quantity = quantity + 1 WHERE user_id = $user_id AND pizza_id = $pizza_id";
        mysqli_query($conn, $update_query);
    } else {
        // Otherwise, add the pizza to the order list
        $insert_query = "INSERT INTO orders (user_id, pizza_id) VALUES ($user_id, $pizza_id)";
        mysqli_query($conn, $insert_query);
    }

    header("Location: checkout.php");
    exit();
}

mysqli_close($conn);
?>
