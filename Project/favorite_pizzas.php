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

// Handle Add to Favorites
if (isset($_POST['add_favorite'])) {
    $pizza_id = mysqli_real_escape_string($conn, $_POST['pizza_id']);
    $sql = "INSERT INTO favorites (user_id, pizza_id) VALUES ('$user_id', '$pizza_id')";
    if (mysqli_query($conn, $sql)) {
        $message = "Pizza added to favorites!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle Delete from Favorites
if (isset($_POST['delete_favorite'])) {
    $favorite_id = mysqli_real_escape_string($conn, $_POST['favorite_id']);
    $sql = "DELETE FROM favorites WHERE id = '$favorite_id'";
    if (mysqli_query($conn, $sql)) {
        $message = "Pizza removed from favorites!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Fetch Favorite Pizzas for the User
$sql = "SELECT f.id AS favorite_id, p.id AS pizza_id, p.title, p.ingredients 
        FROM favorites f
        JOIN pizzas p ON f.pizza_id = p.id
        WHERE f.user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$favorites = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Pizzas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .message {
            color: green;
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
        }
        .button {
            background-color: #5cb85c;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .button.delete {
            background-color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Favorite Pizzas</h1>

        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <!-- Favorite Pizzas List -->
        <?php if ($favorites): ?>
            <table>
                <thead>
                    <tr>
                        <th>Pizza Title</th>
                        <th>Ingredients</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($favorites as $favorite): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($favorite['title']); ?></td>
                            <td><?php echo htmlspecialchars($favorite['ingredients']); ?></td>
                            <td>
                                <!-- Remove from Favorites -->
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="favorite_id" value="<?php echo $favorite['favorite_id']; ?>">
                                    <button type="submit" name="delete_favorite" class="button delete">Remove</button>
                                </form>
                                <!-- Reorder (Placeholder for Future Update) -->
                                <button class="button">Reorder</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No favorite pizzas found. Start adding some!</p>
        <?php endif; ?>

        <!-- Add a Pizza to Favorites (Example) -->
        <h2>Add a Pizza to Favorites</h2>
        <form method="POST" action="">
            <label for="pizza_id">Pizza ID:</label>
            <input type="number" name="pizza_id" id="pizza_id" required>
            <button type="submit" name="add_favorite" class="button">Add to Favorites</button>
        </form>
    </div>
</body>
</html>
