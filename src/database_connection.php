<?php

echo "Hello, Docker!";

$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$password_file_path = getenv('PASSWORD_FILE_PATH');
$db_pass = trim(file_get_contents($password_file_path));

$db_handle = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);

$db_handle->exec("
  Create TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL
  )
  ");
