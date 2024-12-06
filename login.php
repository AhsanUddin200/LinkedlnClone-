<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM linkedlin_user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
    } else {
        echo "<p style='color:red; text-align:center;'>Invalid credentials!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #0a66c2;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .login-container {
            display: flex;
            justify-content: space-between;
            height: 100vh;
        }
        .left-section, .right-section {
            flex: 1;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }
        /* Left Section */
        .left-section {
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }
        .left-section h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .form-input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s ease;
        }
        .form-input:focus {
            border: 2px solid #0a66c2;
            outline: none;
        }
        .form-button {
            width: 100%;
            padding: 12px;
            background-color: #0a66c2;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .form-button:hover {
            background-color: #064a91;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
        /* Right Section */
        .right-section {
            background-color: #f2f2f2;
            background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR6ZjdfUYhfVAyHd-4UBym46Zcn6c6qCeOfEA&s'); /* Replace with your image URL */
            background-size: cover;
            background-position: center;
            position: relative;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        .right-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4); /* Dark overlay to make text more visible */
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        .welcome-text {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-size: 24px;
            text-align: center;
        }
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .right-section {
                height: 250px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Left Section: Login Form -->
    <div class="right-section">
        <div class="welcome-text">Welcome to Our Site!</div>
    </div>
    <div class="left-section">

        <div>
            <h2>Login to Your Account</h2>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" class="form-input" required />
                <input type="password" name="password" placeholder="Password" class="form-input" required />
                <button type="submit" class="form-button">Login</button>
            </form>
            <?php if (isset($_POST['email']) && isset($user) && !$user) { ?>
                <p class="error-message">Invalid credentials!</p>
            <?php } ?>
        </div>
    </div>

    <!-- Right Section: Background Image with Overlay -->
    
</div>

</body>
</html>
