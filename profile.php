<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Farm2Bag</title>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, #00695c, #004d40);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .logo {
            font-size: 1.5em;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            padding: 0;
            margin: 0;
        }

        nav ul li {
            display: inline;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            padding: 8px 15px;
            border-radius: 5px;
        }

        nav ul li a:hover {
            background-color: #ff9800;
            color: white;
        }

        /* Search Bar */
        .search-bar {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 25px;
            padding: 5px 15px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .search-bar input {
            border: none;
            outline: none;
            padding: 8px;
            font-size: 14px;
            border-radius: 15px;
            width: 200px;
        }

        .search-bar button {
            background: #ff9800;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-bar button:hover {
            background: #e68900;
        }

        /* Main Container */
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        h2 {
            color: #004d40;
        }

        h3 {
            color: #00695c;
        }

        /* Order History */
        /* Order History Grid */
        .order-history {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        /* Order Card Styling */
        .order-item {
            background: #e0f2f1;
            padding: 15px;
            border-radius: 8px;
            border-left: 5px solid #004d40;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .order-item:hover {
            transform: scale(1.03);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.15);
        }

        /* Responsive */
        @media (max-width: 600px) {
            .order-history {
                grid-template-columns: 1fr;
                /* Single column on small screens */
            }
        }


        /* Footer */
        footer {
            background: #004d40;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 1em;
            margin-top: auto;
            box-shadow: 0px -4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">Farm2Bag</div>
        <nav>
            <ul>
                <li><a href="home.html">Home</a></li>
                <li><a href="home.html#categories">Categories</a></li>
                <li><a href="product.html">Products</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="cart.html">Cart</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="index.html">Logout</a></li>
            </ul>
        </nav>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search products..." onkeydown="handleKey(event)">
            <button onclick="searchProducts()">🔍</button>
        </div>
    </header>
    <div class="container">
        <h2>Welcome, <?= $_SESSION['fullname'] ?? 'User' ?>!</h2>
        <h3>Your Order History</h3>
        <div class="order-history" id="orderHistory"></div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("fetch_orders.php")
                .then(response => response.json())
                .then(data => {
                    const orderHistory = document.getElementById("orderHistory");

                    if (data.error) {
                        orderHistory.innerHTML = `<p style="color: red;">${data.error}</p>`;
                        return;
                    }

                    if (data.length === 0) {
                        orderHistory.innerHTML = `<p>No orders found.</p>`;
                        return;
                    }

                    data.forEach(order => {
                        const orderDiv = document.createElement("div");
                        orderDiv.classList.add("order-item");
                        orderDiv.innerHTML = `
                <p><strong>Customer:</strong> ${order.customer_name}</p>
                <p><strong>Date:</strong> ${order.created_at}</p>
                <p><strong>Items:</strong> ${order.items}</p>
            `;
                        orderHistory.appendChild(orderDiv);
                    });
                })
                .catch(error => console.error("Error fetching orders:", error));
        });
    </script>
    <footer>
        <p>&copy; 2025 Farm2Bag. All Rights Reserved.</p>
    </footer>
</body>

</html>