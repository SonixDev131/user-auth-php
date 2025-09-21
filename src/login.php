<?php
require_once("database_connection.php");
session_start();
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
$error_message = '';

// Handle form submission
if ($_POST) {
    if (!hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"] ?? '')) {
        $error_message = "CSRF token mismatch.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'];
        if (empty($username) || empty($password)) {
            $error_message = 'Username and password are required.';
        } else {
            try {
                // Database lookup
                $stmt = $db_handle->prepare("SELECT id, username, password_hash FROM users WHERE username = :username");
                $stmt->execute(['username' => $username]);
                $user = $stmt->fetch();
                // Verify credentials
                if ($user && password_verify($password, $user['password_hash'])) {
                    // Successful login
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    // Redirect
                    header("Location: dashboard.php");
                    exit();
                } else {
                    // Invalid credentials
                    $error_message = 'Invalid username or password.';
                }
            } catch (PDOException $e) {
                $error_message = 'Database error: ' . $e->getMessage();
            }
        }
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'];
    }

    // Basic validation

}
?>

<?php if ($error_message): ?>
    <div style="color: red;">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"]); ?>">
    <div>
        <label>Username:</label>
        <input type="text" name="username" required>
    </div>
    
    <div>
        <label>Password:</label>
        <input type="password" name="password" required>
    </div>
    
    <button type="submit">Login</button>
</form>
