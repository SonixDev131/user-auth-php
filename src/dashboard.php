<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
<p>You are logged in.</p>
<a href="logout.php">Logout</a>
