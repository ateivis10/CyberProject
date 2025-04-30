<?php
session_start();
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $mysqli->real_escape_string($_POST['password']);
    $mysqli->query("INSERT INTO users (email, password) VALUES ('$email', '$password')");
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Signup</title>

  <style>
    /* Navigation bar */
    nav {
      background-color: #333;
      padding: 10px 20px;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
    }

    nav ul {
      list-style: none;
      margin: 0;
      padding-right: 30px; /* Added padding-right */
      display: flex;
      justify-content: flex-end;
      align-items: center;
    }

    nav ul li {
      margin-left: 20px;
    }

    nav ul li a {
      color: white;
      text-decoration: none;
      padding: 8px 15px;
      border-radius: 5px;
      transition: background-color 0.3s;
    }

    nav ul li a:hover {
      background-color: #575757;
    }

    /* Body styling */
    body {
      font-family: 'Arial', sans-serif;
      background: linear-gradient(to right, #ece9e6, #ffffff);
      margin: 0;
      padding: 0;
    }

    /* Form styling */
    .form-container {
      background: #fff;
      width: 350px;
      margin: 150px auto 0 auto;
      padding: 20px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
    }

    h2 {
      text-align: center;
      color: #333;
    }

    form input[type="email"],
    form input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    form button {
      width: 100%;
      padding: 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      margin-top: 15px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    form button:hover {
      background-color: #45a049;
    }
  </style>

</head>
<body>

<nav>
  <ul>
    <li><a href="signup.php">Signup</a></li>
    <li><a href="login.php">Login</a></li>
  </ul>
</nav>

<div class="form-container">
  <h2>Signup</h2>
  <form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Signup</button>
  </form>
</div>

</body>
</html>
