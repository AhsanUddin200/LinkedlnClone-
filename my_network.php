<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch connected users
$query = "SELECT * FROM linkedlin_user u JOIN linkedlin_connections c ON u.id = c.connection_id WHERE c.user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$connected_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Network</title>
</head>
<body>

    <h1>My Network</h1>

    <div>
        <?php foreach ($connected_users as $user): ?>
            <div>
                <p><strong><?php echo htmlspecialchars($user['name']); ?></strong></p>
                <p><?php echo htmlspecialchars($user['job_title']); ?></p>
                <p><?php echo htmlspecialchars($user['experience']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
