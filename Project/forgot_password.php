<!DOCTYPE html>
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

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
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
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .success {
            color: green;
            margin-top: 10px;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .back-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container" id="password-reset-form">
        <h1>Reset Password</h1>
        <form action="reset_password_action.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" placeholder="Enter your registered email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your registered phone number" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
            </div>
            <button type="submit" class="btn">Update Password</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get("success") === "true") {
                document.getElementById("password-reset-form").style.display = "none";
                document.getElementById("success-message").style.display = "block";
            }
        });
    </script>
</body>
</html>
