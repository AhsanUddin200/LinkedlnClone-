<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user's ID

// Fetch all users except the logged-in user
$query = "SELECT * FROM linkedlin_user WHERE id != :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the form was submitted to connect
if (isset($_POST['connect']) && isset($_POST['connect_to_id'])) {
    $connect_to_id = $_POST['connect_to_id']; // User ID of the person to connect with

    // Check if the user is trying to connect to themselves
    if ($user_id == $connect_to_id) {
        echo "You cannot connect with yourself.";
        exit();
    }

    // Check if the connection already exists
    $query = "SELECT * FROM linkedlin_connections WHERE (user_id = :user_id AND connection_id = :connect_to_id) OR (user_id = :connect_to_id AND connection_id = :user_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':connect_to_id', $connect_to_id);
    $stmt->execute();
    
    // If no connection exists, insert into the connections table
    if ($stmt->rowCount() == 0) {
        $query = "INSERT INTO linkedlin_connections (user_id, connection_id) VALUES (:user_id, :connect_to_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':connect_to_id', $connect_to_id);
        $stmt->execute();
        echo "You are now connected!";
    } else {
        echo "You are already connected to this user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect with Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .user-list {
            margin-top: 20px;
        }
        .user {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .connect-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .connect-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h1>Connect with Users</h1>

    <div class="user-list">
        <?php foreach ($users as $user): ?>
            <div class="user">
                <h3><?= htmlspecialchars($user['name']) ?></h3>
                <p><strong>Job Title:</strong> <?= htmlspecialchars($user['job_title']) ?></p>
                <p><strong>Experience:</strong> <?= htmlspecialchars($user['experience']) ?></p>
                
                <form method="POST" action="connect.php">
                    <input type="hidden" name="connect_to_id" value="<?= $user['id'] ?>">
                    <button type="submit" name="connect" class="connect-btn">Connect</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
