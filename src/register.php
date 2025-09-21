<?php

require_once 'database_connection.php';

$success_message = '';
$error_message = '';

session_start();
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if ($_POST) {
    if (!hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"] ?? '')) {
        $error_message = "CSRF token mismatch.";
    } else {
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
            try {
                $stmt = $db_handle->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
                $stmt->execute(['username' => $username]);
                $count = $stmt->fetchColumn();
                if ($count > 0) {
                    $error_message = 'Username already exists.';
                } else {
                    $password_hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $db_handle->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
                    if ($stmt->execute(['username' => $username, 'password_hash' => $password_hash])) {
                        $success_message = 'Registration successful. You can now log in.';
                        header("Location: login.php");
                        exit();
                    } else {
                        $error_message = 'Registration failed. Please try again.';
                    }
                }
            } catch (PDOException $e) {
                $error_message = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
?>

<?php if ($error_message): ?>
    <div style="color: red;">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>
<?php if ($success_message): ?>
    <div style="color: green;">
        <?php echo htmlspecialchars($success_message); ?>
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

    <div>
        <label>Confirm password:</label>
        <input type="password" name="confirm_password" required>
    </div>

    <button type="submit">Register</button>
</form>
