<?php
session_start();
include('db.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $job_title = $_POST['job_title'];
    $experience = $_POST['experience'];
    $profile = $_POST['profile'];  // New profile field

    // Check if user wants to upload a picture
    if ($_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Creates folder if it doesn't exist
        }
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
        $profile_picture = $target_file;  // Save the file path
    } elseif (!empty($_POST['image_url'])) {
        // If user provides an image URL
        $profile_picture = $_POST['image_url'];  // Store the URL
    } else {
        // Default value if no image or URL is provided
        $profile_picture = '';  // Default profile image
    }

    // Insert data into the database
    $query = "INSERT INTO linkedlin_user (name, email, password, job_title, experience, profile, profile_picture) 
              VALUES (:name, :email, :password, :job_title, :experience, :profile, :profile_picture)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':job_title', $job_title);
    $stmt->bindParam(':experience', $experience);
    $stmt->bindParam(':profile', $profile);
    $stmt->bindParam(':profile_picture', $profile_picture);
    $stmt->execute();

    // Redirect to login.php after successful signup
    header("Location: login.php");
    exit(); // Ensure no further code is executed
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Form</title>
    <style>
         #splash-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #FFFFFF; /* Background color */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999; /* Ensure it is on top */
        }
        #splash-screen img {
            width: 30%; /* Adjust as needed */
            height: auto;
        }
        #loader-bar {
            position: fixed;
            top: 80%; /* Position it below the splash screen */
            left: 50%;
            transform: translateX(-50%);
            width: 80%; /* Width of the loading bar */
            height: 10px; /* Height of the loading bar */
            background-color: #ddd; /* Background color of the bar */
            border-radius: 5px; /* Rounded corners */
            overflow: hidden; /* Hide overflow */
            z-index: 9998; /* Just below the splash screen */
        }
        #loading {
            height: 100%; /* Full height of the loader bar */
            width: 0; /* Start with 0 width */
            background-color: #007BFF; /* Blue color for the loading bar */
            animation: loading 2s forwards; /* Animation for loading */
        }
        @keyframes loading {
            0% { width: 0; }
            100% { width: 100%; } /* Fill the bar */
        }#loader-bar {
            position: fixed;
            top: 80%; /* Position it below the splash screen */
            left: 50%;
            transform: translateX(-50%);
            width: 80%; /* Width of the loading bar */
            height: 10px; /* Height of the loading bar */
            background-color: #ddd; /* Background color of the bar */
            border-radius: 5px; /* Rounded corners */
            overflow: hidden; /* Hide overflow */
            z-index: 9998; /* Just below the splash screen */
        }
        #loading {
            height: 100%; /* Full height of the loader bar */
            width: 0; /* Start with 0 width */
            background-color: #3498db; /* Color of the loading bar */
            animation: loading 2s forwards; /* Animation for loading */
        }
        @keyframes loading {
            0% { width: 0; }
            100% { width: 100%; } /* Fill the bar */
        }
        


        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #0a66c2;
        }
        .container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            margin: auto;
            .left-side {
    width: 100%;
    padding: 20px;
    text-align: center;
    margin-top: 180px;  /* Correct syntax for margin-top */
}

        .left-side img {
            width: 80%;
            height: auto;
            border-radius: 10px;
        }
        .right-side {
            width: 50%;
            padding: 20px;
        }
        .right-side form {
            display: flex;
            flex-direction: column;
        }
        .right-side input, .right-side textarea {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .right-side button {
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .right-side button:hover {
            background-color: #0056b3;
        }
    </style>
      <script>
        // Function to hide the splash screen and show the main content
        function hideSplashScreen() {
            document.getElementById('splash-screen').style.display = 'none';
            document.getElementById('loader-bar').style.display = 'none'; // Hide loader bar
            document.body.style.display = 'flex'; // Show main content
        }

        // Show splash screen for 2 seconds
        window.onload = function() {
            setTimeout(hideSplashScreen, 2000); // 2000 milliseconds = 2 seconds
        };
    </script>
</head>
<body>

<div id="splash-screen">
        <img src="data:image/png;base64,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" alt="Splash Image"> <!-- Replace with your image path -->
    </div>
    <div id="loader-bar">
        <div id="loading"></div>
    </div>
    <div class="container">
        <!-- Left Side (Profile Image) -->
        <div class="left-side">
            <img src="data:image/jpeg;base64,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" alt="Profile Image">
        </div>
        
        <!-- Right Side (Form) -->
        <div class="right-side">
            <h2>Create Profile</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <label for="job_title">Job Title:</label>
                <input type="text" id="job_title" name="job_title" required>
                
                <label for="experience">Experience:</label>
                <textarea id="experience" name="experience" rows="4" required></textarea>
                
                <label for="profile">Profile Description:</label>
                <textarea id="profile" name="profile" rows="4" required></textarea>
                
                <label for="profile_picture">Upload Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture">
                
                <label for="image_url">Or provide an image URL:</label>
                <input type="text" id="image_url" name="image_url" placeholder="Image URL">

                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
