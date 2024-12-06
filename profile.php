<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user's ID

// Fetch user profile details from the database
$query = "SELECT * FROM linkedlin_user WHERE id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user posts from the database
$query_posts = "SELECT * FROM posts WHERE user_id = :user_id"; // Assuming 'posts' is the table name
$stmt_posts = $conn->prepare($query_posts);
$stmt_posts->bindParam(':user_id', $user_id);
$stmt_posts->execute();
$posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);

// Get profile picture path from database
$profile_picture = $user['profile_picture'];  // Assuming 'profile_picture' is the column name

// Default image if profile picture doesn't exist
if (empty($profile_picture)) {
    $profile_picture = 'uploads/default.jpg';  // You can use a default placeholder image
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
    <style>
        /* CSS for rounded image */
    
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4; /* Light background for contrast */
            margin: 0;
            padding: 20px;
        }

        .profile-container {
            max-width: 800px;
            margin: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-header img {
            width: 100px; /* Fixed size for profile picture */
            height: 100px;
            border-radius: 50%; /* Circular profile picture */
            margin-right: 20px;
            border: 2px solid #007BFF; /* Border color */
        }

        .profile-header h1 {
            margin: 0;
            font-size: 24px;
            color: #333; /* Darker text color */
        }

        .profile-header p {
            margin: 5px 0;
            color: #666; /* Lighter text color */
        }

        .profile-details {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9; /* Light background for details */
            border-radius: 5px;
        }

        .user-posts {
            margin-top: 20px;
        }

        .user-posts h2 {
            font-size: 20px;
            color: #007BFF; /* Blue color for headings */
            margin-bottom: 10px;
        }

        .user-posts ul {
            list-style-type: none; /* Remove default list styling */
            padding: 0;
        }

        .user-posts li {
            background: #f9f9f9; /* Light background for posts */
            border: 1px solid #ddd; /* Border for posts */
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            transition: box-shadow 0.3s; /* Smooth shadow transition */
        }

        .user-posts li:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Shadow on hover */
        }

        .user-posts img {
            max-width: 100%; /* Responsive image */
            height: auto;
            border-radius: 5px; /* Rounded corners for post images */
            margin-top: 10px; /* Space above images */
        }

        .verification-message {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd; /* Light yellow background */
            border: 1px solid #ffeeba; /* Border color */
            border-radius: 5px;
            color: #856404; /* Darker text color */
        }

        .verification-message button {
            background-color: #007BFF; /* Button color */
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.3s; /* Smooth transition */
        }

        .verification-message button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
    
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
            <div>
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <p><?php echo htmlspecialchars($user['job_title']); ?></p>
            </div>
        </div>
        <div class="profile-details">
            <p><strong>Experience:</strong> <?php echo htmlspecialchars($user['experience']); ?></p>
            <p><strong>Profile:</strong> <?php echo htmlspecialchars($user['profile']); ?></p>
        </div>
        <div class="verification-message">
            <p><?php echo htmlspecialchars($user['name']); ?>, you aren't verified yet</p>
            <p>Verification badge is a signal of trust, giving others more confidence to interact with you.</p>
            <button>Verify now</button> 
        </div>
        <div class="user-posts">
            <h2>User Posts</h2>
            <?php if (count($posts) > 0): ?>
                <ul>
                    <?php foreach ($posts as $post): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($post['title'] ?? 'Untitled'); ?></strong>
                            <p><?php echo htmlspecialchars($post['content'] ?? 'No content available.'); ?></p>
                            <?php if (!empty($post['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post Image" style="max-width: 100%; height: auto;">
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No posts available.</p>
            <?php endif; ?>
        </div>

       
    </div>
</body>
</html>
