<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "mydatabase"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$password = $_POST['password']; // Plain text password

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $phone, $email, $password);

// Execute the statement
if ($stmt->execute()) {
    // Redirect to a success page (or any other page)
    header("Location: success.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
