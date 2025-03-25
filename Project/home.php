<?php
// Start the session
session_start();

// Initialize error variable
$error = '';

// Database connection details
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mydatabase'; // Database name

// Create a new connection
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user input
    $email_or_phone = $_POST['email_or_phone'];
    $password = $_POST['password'];

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $email_or_phone, $email_or_phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password (plain text comparison)
        if ($password === $user['password']) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect to hello.php
            header("Location: tuts/index.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "No user found with this email or phone";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    body {
      background: #1abc9c;
      overflow: hidden;
    }
    ::selection {
      background: rgba(26, 188, 156, 0.3);
    }
    .container {
      max-width: 440px;
      padding: 0 20px;
      margin: 170px auto;
    }
    .wrapper {
      width: 100%;
      background: #fff;
      border-radius: 5px;
      box-shadow: 0px 4px 10px 1px rgba(0,0,0,0.1);
    }
    .wrapper .title {
      height: 90px;
      background: #16a085;
      border-radius: 5px 5px 0 0;
      color: #fff;
      font-size: 30px;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .wrapper form {
      padding: 30px 25px 25px 25px;
    }
    .wrapper form .row {
      height: 45px;
      margin-bottom: 15px;
      position: relative;
    }
    .wrapper form .row input {
      height: 100%;
      width: 100%;
      outline: none;
      padding-left: 60px;
      border-radius: 5px;
      border: 1px solid lightgrey;
      font-size: 16px;
      transition: all 0.3s ease;
    }
    form .row input:focus {
      border-color: #16a085;
      box-shadow: inset 0px 0px 2px 2px rgba(26,188,156,0.25);
    }
    form .row input::placeholder {
      color: #999;
    }
    .wrapper form .row i {
      position: absolute;
      width: 47px;
      height: 100%;
      color: #fff;
      font-size: 18px;
      background: #16a085;
      border: 1px solid #16a085;
      border-radius: 5px 0 0 5px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .wrapper form .pass {
      margin: -8px 0 20px 0;
    }
    .wrapper form .pass a {
      color: #16a085;
      font-size: 17px;
      text-decoration: none;
    }
    .wrapper form .pass a:hover {
      text-decoration: underline;
    }
    .wrapper form .button input {
      color: #fff;
      font-size: 20px;
      font-weight: 500;
      padding-left: 0px;
      background: #16a085;
      border: 1px solid #16a085;
      cursor: pointer;
    }
    form .button input:hover {
      background: #12876f;
    }
    .wrapper form .signup-link {
      text-align: center;
      margin-top: 20px;
      font-size: 17px;
    }
    .wrapper form .signup-link a {
      color: #16a085;
      text-decoration: none;
    }
    form .signup-link a:hover {
      text-decoration: underline;
    }
    .error {
      color: red;
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="wrapper">
      <div class="title"><span>Login Form</span></div>
      <form action="" method="POST">
        <div class="row">
          <i class="fas fa-user"></i>
          <input type="text" name="email_or_phone" placeholder="Email or Phone" required>
        </div>
        <div class="row">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="pass"><a href="forgot_password.php">Forgot password?</a></div>
        <div class="row button">
          <input type="submit" value="Login">
        </div>
        <div class="signup-link">Not a member? <a href="usersignup.php">Signup now</a></div>
      </form>
      <?php if (isset($error) && !empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>