<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('tuts/config/db_connect.php');

    // Fetch input values and sanitize them
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);

    // Validate email and phone in the database
    $query = "SELECT * FROM users WHERE email = '$email' AND phone = '$phone'";
    $result = mysqli_query($conn, $query);

    // Output the page header
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f9;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .container {
                background: white;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                padding: 30px;
                width: 100%;
                max-width: 400px;
                text-align: center;
            }
            .container h1 {
                font-size: 24px;
                margin-bottom: 20px;
                color: #333;
            }
            .container p {
                font-size: 16px;
                margin-bottom: 20px;
                color: #666;
            }
            .btn {
                display: inline-block;
                background-color: #007BFF;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                font-size: 16px;
                cursor: pointer;
                text-decoration: none;
                transition: background-color 0.3s, transform 0.2s;
            }
            .btn:hover {
                background-color: #0056b3;
                transform: translateY(-3px);
            }
        </style>
    </head>
    <body>';

    if (mysqli_num_rows($result) > 0) {
        // Update the password in the database without hashing
        $update_query = "UPDATE users SET password = '$new_password' WHERE email = '$email' AND phone = '$phone'";

        if (mysqli_query($conn, $update_query)) {
            echo '<div class="container">
                    <h1>Password Updated Successfully!</h1>
                    <p>Your password has been updated. Click the button below to go back to the homepage.</p>
                    <a href="home.php" class="btn">Back to Home</a>
                  </div>';
        } else {
            echo '<div class="container">
                    <h1>Error</h1>
                    <p>Error updating password: ' . mysqli_error($conn) . '</p>
                    <a href="home.php" class="btn">Back to Home</a>
                  </div>';
        }
    } else {
        echo '<div class="container">
                <h1>Error</h1>
                <p>Invalid email or phone number. Please try again.</p>
                <a href="home.php" class="btn">Back to Home</a>
              </div>';
    }

    // Output the page footer
    echo '</body></html>';

    // Close the database connection
    mysqli_close($conn);
}
?>
