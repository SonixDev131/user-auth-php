<?php

require_once 'database_connection.php';

$error_message = '';

if ($_POST) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error_message = 'Password must be at least 8 characters long.';
    } else {
        // Here you would typically hash the password and store the user in the database
        // For demonstration, we'll just print a success message
        echo "User registered successfully!";
    }

}
?>

<?php if ($error_message): ?>
    <div style="color: red;">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div>
        <label>Username:</label>
        <input type="text" name="username" required>
    </div>
    
    <div>
        <label>Password:</label>
        <input type="password" name="password" required>
    </div>

    <div>
        <label>Confirm password:</label>
        <input type="password" name="confirm_password" required>
    </div>
    
    <button type="submit">Register</button>
</form>
