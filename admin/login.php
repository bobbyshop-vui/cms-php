<?php
// Start the session
session_start();

// Function to connect to the database and create tables if they do not exist
function getDB() {
    try {
        $db = new PDO('sqlite:' . __DIR__ . '/../mycms.db'); // Đường dẫn tới cơ sở dữ liệu SQLite
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Tạo bảng settings nếu chưa tồn tại
        $db->exec("CREATE TABLE IF NOT EXISTS settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            key TEXT NOT NULL UNIQUE,
            value TEXT NOT NULL
        )");

        // Tạo bảng users nếu chưa tồn tại
        $db->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL
        )");

        // Tạo bảng posts nếu chưa tồn tại
        $db->exec("CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            created_at TEXT NOT NULL
        )");
        $db->exec("CREATE TABLE IF NOT EXISTS extensions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            description TEXT NOT NULL,
            file_path TEXT,
            created_at TEXT NOT NULL
        )");
        return $db;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Check if the user is trying to log in
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();

    // Prepare the SQL query
    $stmt = $db->prepare('SELECT password FROM users WHERE username = ?');
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($_POST['password'], $user['password'])) {
        // Successful login
        $_SESSION['user_id'] = $_POST['username'];
        header('Location: index.php'); // Redirect to the admin dashboard
        exit();
    } else {
        // Failed login
        echo "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Login</h1>
        <form method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
