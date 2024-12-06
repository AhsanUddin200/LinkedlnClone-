<?php
$host = 'localhost';  // Your database host
$username = 'root';   // Your database username
$password = '';       // Your database password
$dbname = 'linkedlin_clone'; // Your database name

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
