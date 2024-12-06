<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile details
$query = "SELECT * FROM linkedlin_user WHERE id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch all users except the logged-in user
$query = "SELECT * FROM linkedlin_user WHERE id != :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch posts from the database
$query = "SELECT * FROM posts ORDER BY created_at DESC"; // Assuming you have a posts table
$stmt = $conn->prepare($query);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle new post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_content'])) {
    $content = $_POST['post_content'];
    $profile_picture = $user['profile_picture'];
    $user_name = $user['name'];
    $image_path = null;

    // Handle image upload
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["post_image"]["name"]);
        move_uploaded_file($_FILES["post_image"]["tmp_name"], $target_file);
        $image_path = $target_file;
    }

    // Insert the new post into the database
    $insertQuery = "INSERT INTO posts (user_id, content, user_profile_picture, user_name, image_path) VALUES (:user_id, :content, :profile_picture, :user_name, :image_path)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bindParam(':user_id', $user_id);
    $insertStmt->bindParam(':content', $content);
    $insertStmt->bindParam(':profile_picture', $profile_picture);
    $insertStmt->bindParam(':user_name', $user_name);
    $insertStmt->bindParam(':image_path', $image_path);
    $insertStmt->execute();

    // Redirect to the same page to see the new post
    header("Location: index.php");
    exit();
// Fetch user profile details from the database
$query = "SELECT * FROM linkedlin_user WHERE id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get profile picture path from database
 // Default image if not set


    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkedIn Clone - Homepage</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            align-items: center;
            background-color: #f3f3f3;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
        }
        .header img {
            height: 40px; /* LinkedIn logo size */
            margin-right: 20px;
        }
        .search-bar {
            flex-grow: 1;
            display: flex;
            align-items: center;
            background-color: #e6e6e6;
            border-radius: 20px;
            padding: 5px 10px;
        }
        .search-bar input {
            border: none;
            outline: none;
            background: transparent;
            padding: 10px;
            border-radius: 20px;
            width: 100%;
        }
        .nav-links {
            display: flex;
            align-items: center;
            margin-left: 20px;
        }
        .nav-links a {
            margin: 0 10px;
            text-decoration: none;
            color: #0073b1; /* LinkedIn blue */
        }
        .nav-links .profile {
            display: flex;
            align-items: center;
        }
        .nav-links .profile img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-left: 5px;
        }
        .container {
            display: flex;
        }
        .sidebar {
    width: 25%; /* Adjust width as needed */
    background-color: #f9f9f9; /* Light background */
    padding: 20px;
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

.profile-header {
    text-align: center; /* Center text */
    margin-bottom: 20px; /* Space below the header */
}

.profile-header .profile-picture {
    width: 80px; /* Adjust size */
    height: 80px; /* Adjust size */
    border-radius: 50%; /* Circular image */
    border: 2px solid #0073b1; /* LinkedIn blue border */
}

.job-title {
    font-weight: bold; /* Bold job title */
    margin: 5px 0; /* Space above and below */
}

.location, .company {
    color: #666; /* Gray text for location and company */
    margin: 2px 0; /* Space above and below */
}

.profile-stats {
    margin: 20px 0; /* Space above and below */
    text-align: center; /* Center text */
}

.view-count {
    font-weight: bold; /* Bold viewer count */
}

.view-analytics {
    color: #0073b1; /* LinkedIn blue */
    text-decoration: none; /* Remove underline */
}

.view-analytics:hover {
    text-decoration: underline; /* Underline on hover */
}

.premium-offer {
    background-color: #e6f7ff; /* Light blue background */
    padding: 10px;
    border-radius: 5px; /* Rounded corners */
    text-align: center; /* Center text */
    margin: 20px 0; /* Space above and below */
}

.premium-button {
    background-color: #0073b1; /* LinkedIn blue */
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none; /* Remove underline */
}

.premium-button:hover {
    background-color: #005582; /* Darker blue on hover */
}

.sidebar-links {
    margin-top: 20px; /* Space above */
}

.sidebar-links p {
    margin: 5px 0; /* Space above and below */
}

        .feed {
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center the posts */
        }
        .suggestions {
            width: 25%;
            background-color: #f3f3f3;
            padding: 20px;
            border-radius: 8px; /* Rounded corners */
        }
        .suggestion {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd; /* Border around each suggestion */
            border-radius: 8px; /* Rounded corners */
            background-color: #fff; /* White background for suggestions */
        }
        .suggestion img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .suggestion h3 {
            margin: 0;
            font-size: 16px;
        }
        .suggestion p {
            margin: 0;
            font-size: 12px;
            color: #666; /* Gray text for description */
        }
        .follow-button {
            background-color: #0073b1;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .follow-button:hover {
            background-color: #005582;
        }
        .profile-picture {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .new-post {
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 600px; /* Limit width */
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #fff; /* White background for new post */
        }
        .new-post textarea {
            flex-grow: 1;
            border: none;
            outline: none;
            padding: 10px;
            border-radius: 5px;
            margin-left: 10px;
            resize: none; /* Prevent resizing */
            height: 50px; /* Set height */
        }
        .media-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .media-buttons button {
            background-color: #e6e6e6;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            margin-right: 10px;
        }
        .uploaded-image {
            width: 600px; /* Set desired width */
            height: 450px; /* Set desired height */
            object-fit: cover; /* Ensures the image covers the specified dimensions */
            border-radius: 10px; /* Optional: adds rounded corners */
            margin-top: 10px; /* Adds space above the image */
        }
        .new-post {
    display: flex;
    align-items: center;
    width: 100%;
    max-width: 600px; /* Limit width */
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 10px;
    margin-bottom: 20px;
    background-color: #fff; /* White background for new post */
}

.new-post textarea {
    flex-grow: 1;
    border: none;
    outline: none;
    padding: 10px;
    border-radius: 20px; /* Rounded corners */
    margin-left: 10px;
    resize: none; /* Prevent resizing */
    height: 50px; /* Set height */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

.media-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.media-buttons button {
    background-color: #e6e6e6;
    border: none;
    border-radius: 5px;
    padding: 5px 10px;
    cursor: pointer;
    margin-right: 10px;
    display: flex;
    align-items: center;
    transition: background-color 0.3s;
}

.media-buttons button:hover {
    background-color: #d4d4d4; /* Darker gray on hover */
}

.media-buttons button img {
    margin-right: 5px; /* Space between icon and text */
}

.post-button {
    background-color: #0073b1; /* LinkedIn blue */
    color: white;
    border: none;
    padding: 5px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.post-button:hover {
    background-color: #005582; /* Darker blue on hover */
}
        .post {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            background-color: #fff; /* White background for posts */
            width: 100%; /* Full width of the feed */
            max-width: 600px; /* Maximum width for posts */
        }
        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .post-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .post-content {
            margin-bottom: 10px;
        }
        .post-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .post-actions button {
            background: none;
            border: none;
            color: #0073b1;
            cursor: pointer;
        }
        .comment-section {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .comment-section input {
            width: 80%;
            padding: 5px;
            margin-right: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .share-button {
            background-color: #0073b1;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .share-button:hover {
            background-color: #005582;
        }
        .suggestions {
    background-color: #f9f9f9; /* Light background */
    padding: 20px; /* Padding around the section */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    margin-bottom: 20px; /* Space below the section */
}

.suggestions h2 {
    margin-bottom: 15px; /* Space below the heading */
}

.suggestion {
    display: flex; /* Align items horizontally */
    align-items: center; /* Center items vertically */
    margin-bottom: 15px; /* Space between suggestions */
    padding: 10px; /* Padding around each suggestion */
    border-radius: 5px; /* Rounded corners */
    transition: background-color 0.3s; /* Smooth background transition */
}

.suggestion:hover {
    background-color: #e6f7ff; /* Light blue background on hover */
}

.suggestion-picture {
    width: 50px; /* Adjust size */
    height: 50px; /* Adjust size */
    border-radius: 50%; /* Circular image */
    margin-right: 10px; /* Space between image and text */
}

.suggestion-info {
    flex-grow: 1; /* Allow text to take available space */
}

.job-title {
    color: #666; /* Gray text for job title */
    margin: 5px 0; /* Space above and below */
}

.follow-form {
    display: flex; /* Align button */
}

.follow-button {
    background-color: #0073b1; /* LinkedIn blue */
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.follow-button:hover {
    background-color: #005582; /* Darker blue on hover */
}

.view-recommendations {
    display: block; /* Block display for the link */
    text-align: center; /* Center the link */
    margin-top: 15px; /* Space above the link */
    color: #0073b1; /* LinkedIn blue */
    text-decoration: none; /* Remove underline */
}

.view-recommendations:hover {
    text-decoration: underline; /* Underline on hover */
}
.messaging-section {
    background-color: #f9f9f9; /* Light background */
    padding: 20px; /* Padding around the section */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    margin-bottom: 20px; /* Space below the section */
}

.messaging-section {
    background-color: #fff; /* White background */
    padding: 20px; /* Padding around the section */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    margin-bottom: 20px; /* Space below the section */
}

.user-profile {
    display: flex; /* Align items horizontally */
    align-items: center; /* Center items vertically */
    margin-bottom: 15px; /* Space below the profile */
}

.profile-picture {
    width: 50px; /* Adjust size */
    height: 50px; /* Adjust size */
    border-radius: 50%; /* Circular image */
    margin-right: 10px; /* Space between image and text */
}

.user-info {
    flex-grow: 1; /* Allow text to take available space */
}

.user-info h3 {
    margin: 0; /* Remove default margin */
}

.status {
    color: #666; /* Gray text for status */
}

.message-button {
    background-color: #0073b1; /* LinkedIn blue */
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.message-button:hover {
    background-color: #005582; /* Darker blue on hover */
}

.message-content {
    margin-top: 10px; /* Space above the message content */
}

.message-content p {
    margin: 0 0 10px; /* Space below the paragraph */
}

.message-content textarea {
    width: 100%; /* Full width */
    height: 50px; /* Set height */
    padding: 10px; /* Padding inside textarea */
    border: 1px solid #ddd; /* Light border */
    border-radius: 5px; /* Rounded corners */
    resize: none; /* Prevent resizing */
}

.send-button {
    background-color: #0073b1; /* LinkedIn blue */
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
    margin-top: 10px; /* Space above the button */
}

.send-button:hover {
    background-color: #005582; /* Darker blue on hover */
}

.view-recommendations {
    display: block; /* Block display for the link */
    text-align: center; /* Center the link */
    margin-top: 15px; /* Space above the link */
    color: #0073b1; /* LinkedIn blue */
    text-decoration: none; /* Remove underline */
}

.view-recommendations:hover {
    text-decoration: underline; /* Underline on hover */
}
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <img src="data:image/png;base64,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" alt="LinkedIn Logo"> <!-- Replace with actual logo path -->
    <div class="search-bar">
        <input type="text" placeholder="Search">
    </div>
    <div class="nav-links">
        <a href="#">Home</a>
        <a href="#">My Network</a>
        <a href="#">Jobs</a>
        <a href="#">Messaging</a>
        <a href="#">Notifications</a>
        <div class="profile">
            <span>Me</span>
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?: 'uploads/default.jpg'); ?>" alt="Profile Picture">
        </div>
    </div>
</div>

<!-- Main Container -->
<div class="container">
    <!-- Left Sidebar -->
    <div class="sidebar">
    <div class="profile-header">
        <a href="profile.php">
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?: 'uploads/default.jpg'); ?>" alt="User Picture" class="profile-picture">
        </a>
        <h2><?php echo htmlspecialchars($user['name']); ?></h2>
      
        <p class="location">Karachi, Sindh</p>
       
    </div>
    <div class="profile-stats">
        <p><strong>Profile viewers:</strong> <span class="view-count">37</span></p>
        <a href="#" class="view-analytics">View all analytics</a>
    </div>
    <div class="premium-offer">
        <p>Grow your career with Premium</p>
        <a href="#" class="premium-button">Try 1 month for PKR0</a>
    </div>
    <div class="sidebar-links">
        <p><strong>Saved items</strong></p>
        <p><strong>Groups</strong></p>
        <p><strong>Newsletters</strong></p>
        <p><strong>Events</strong></p>
    </div>
</div>
    <!-- Center Feed -->
    <div class="feed">
        <!-- New Post Section -->
        <form method="POST" enctype="multipart/form-data">
            <div class="new-post">
                <img src="<?php echo htmlspecialchars($user['profile_picture'] ?: 'uploads/default.jpg'); ?>" alt="User Picture" class="profile-picture">
                <textarea name="post_content" placeholder="Start a post, try writing with AI" required></textarea>
            </div>
            <div class="media-buttons">
                <input type="file" id="post_image" name="post_image" style="display: none;" accept="image/*">
                <button type="button" class="media-button" onclick="document.getElementById('post_image').click();">
                    <img src="path/to/media-icon.png" alt="Media Icon"> Media
                </button>
                <button type="button" class="event-button">
                    <img src="path/to/event-icon.png" alt="Event Icon"> Event
                </button>
                <button type="button" class="article-button">
                    <img src="path/to/article-icon.png" alt="Article Icon"> Write article
                </button>
            </div>
            <button type="submit" class="post-button">Post</button>
        </form>

        <!-- Display Posts -->
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <div class="post-header">
                    <img src="<?php echo htmlspecialchars($post['user_profile_picture']); ?>" alt="User Picture">
                    <strong><?php echo htmlspecialchars($post['user_name']); ?></strong>
                </div>
                <div class="post-content">
                    <p><?php echo htmlspecialchars($post['content']); ?></p>
                    <?php if ($post['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Uploaded Image" class="uploaded-image">
                    <?php endif; ?>
                </div>
                <div class="post-actions">
                    <button class="share-button">Like</button>
                    <button class="share-button">Comment</button>
                    <button class="share-button">Repost</button>
                    <button class="share-button">Send</button>
                </div>
                <div class="comment-section">
                    <input type="text" placeholder="Add a comment...">
                    <button>Comment</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Right Suggestions -->
   <!-- Right Suggestions -->
   <div class="suggestions">
    <h2>Add to your feed</h2>
    <?php foreach ($users as $user): ?>
        <div class="suggestion">
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?: 'uploads/default.jpg'); ?>" alt="User Picture" class="suggestion-picture">
            <div class="suggestion-info">
                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                <p class="job-title"><?php echo htmlspecialchars($user['profile']); ?></p>
            </div>
            <form action="connect.php" method="POST" class="follow-form">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                <button type="submit" class="follow-button">+ Follow</button>
            </form>
        </div>
    <?php endforeach; ?>
    <a href="#" class="view-recommendations">View all recommendations →</a>
    <div class="messaging-section">
    <div class="user-profile">
        <a href="profile.php">
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?: 'uploads/default.jpg'); ?>" alt="User Picture" class="profile-picture">
        </a>
        <div class="user-info">
            <h3><?php echo htmlspecialchars($user['name']); ?></h3>
            <p class="status">Messaging</p>
        </div>
        <button class="message-button">Message</button>
    </div>
    <div class="message-content">
        <p>Start a conversation with <?php echo htmlspecialchars($user['name']); ?>...</p>
        <textarea placeholder="Type your message here..."></textarea>
        <button class="send-button">Send</button>
    </div>
    <a href="#" class="view-recommendations">View all recommendations →</a>
</div>
</div>

</div>


</body>
</html>