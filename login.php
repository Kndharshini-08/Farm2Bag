<?php
session_start();
include 'db_connection.php';

$error = ""; // Prevent "Undefined variable" warning

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Store user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['fullname'] = $user['fullname'];

        // Redirect to home.html
        header("Location: home.html");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm2Bag - Login</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            overflow-x: hidden;
        }

        header {
            background: #004d40;
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        nav ul li a:hover {
            color: #ff9800;
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .profile-icon img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }

        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 350px;
        }

        .login-box h2 {
            margin-bottom: 20px;
            color: #004d40;
        }

        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
        }

        .login-box .btn {
            width: 100%;
            padding: 12px;
            background: #ff9800;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            transition: 0.3s;
            font-size: 1em;
        }

        .login-box .btn:hover {
            background: #e65100;
        }

        .login-box p {
            margin-top: 15px;
            font-size: 0.9em;
        }

        .error-message {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        footer {
            background: #004d40;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">Farm2Bag</div>
        <nav>
            <ul>
                <li><a href="index.html">Back</a></li>
            </ul>
        </nav>
    </header>

    <div class="login-container">
        <div class="login-box">
            <h2>Login to Farm2Bag</h2>
            <?php if ($error): ?>
                <p class="error-message"><?= $error ?></p>
            <?php endif; ?>
            <form action="login.php" method="post">
                <input type="email" name="email" placeholder="✉️ Email" required>
                <input type="password" name="password" placeholder="🔑 Password" required>
                <button type="submit" class="btn">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Farm2Bag. All rights reserved.</p>
    </footer>

</body>

</html>