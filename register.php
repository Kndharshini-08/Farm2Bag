<?php
// Include database connection
include 'db_connection.php';

// Initialize message
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $phone = trim($_POST['phone']);

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $message = "Passwords do not match!";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long!";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if email or phone already exists using a prepared statement
        $checkQuery = "SELECT * FROM users WHERE email = ? OR phone = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Email or phone number is already registered!";
        } else {
            // Insert new user into the database using a prepared statement
            $query = "INSERT INTO users (fullname, email, password, phone) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $fullname, $email, $hashedPassword, $phone);

            if ($stmt->execute()) {
                $message = "Registration successful! <a href='login.php'>Login here</a>";
            } else {
                $message = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm2Bag - Register</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        html,
        body {
            overflow-x: hidden;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background: #004d40;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }

        .register-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 350px;
        }

        .register-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
        }

        .register-box .btn {
            width: 100%;
            padding: 12px;
            background: #ff9800;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            transition: 0.3s;
        }

        .register-box .btn:hover {
            background: #e65100;
        }

        .message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        footer {
            background: #004d40;
            color: white;
            text-align: center;
            padding: 8px;
            position: absolute;
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
                <li><a href="login.php">Signin</a></li>
            </ul>
        </nav>
    </header>

    <div class="register-container">
        <div class="register-box">
            <h2>Create Account</h2>
            <?php if (!empty($message)): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>
            <form action="" method="post">
                <input type="text" name="fullname" placeholder="🧍‍♂️ Full Name" required>
                <input type="email" name="email" placeholder="✉️ Email" required>
                <input type="password" name="password" placeholder="🔑 Password" required>
                <input type="password" name="confirmPassword" placeholder="🔑 Confirm Password" required>
                <input type="tel" name="phone" placeholder="📞 Phone Number" required>
                <button type="submit" class="btn">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Farm2Bag. All rights reserved.</p>
    </footer>

</body>

</html>