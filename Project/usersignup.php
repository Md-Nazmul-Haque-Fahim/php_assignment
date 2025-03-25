<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Form</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f2f2f2;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      width: 400px;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .container h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 5px;
      color: #333;
    }
    .form-group input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
    }
    .form-group input:focus {
      border-color: #16a085;
      outline: none;
    }
    .form-group .error {
      color: red;
      font-size: 14px;
    }
    .submit-btn {
      width: 100%;
      padding: 10px;
      background: #16a085;
      border: none;
      border-radius: 5px;
      color: white;
      font-size: 18px;
      cursor: pointer;
      margin-top: 20px;
    }
    .submit-btn:hover {
      background: #12876f;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Registration Form</h2>
  <form action="register.php" method="POST">
    <div class="form-group">
      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" placeholder="Enter your full name" required>
    </div>
    <div class="form-group">
      <label for="phone">Phone Number</label>
      <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
    </div>
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="Enter your email" required>
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter your password" required>
    </div>
    <button type="submit" class="submit-btn">Register</button>
  </form>
</div>

</body>
</html>
