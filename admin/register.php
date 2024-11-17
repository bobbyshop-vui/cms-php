<?php
session_start();

function getDB() {
    try {
        $db = new PDO('sqlite:' . __DIR__ . '/../mycms.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Tạo bảng users nếu chưa tồn tại
        $db->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL
        )");

        return $db;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Xử lý đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $db = getDB();

        // Kiểm tra xem người dùng đã tồn tại chưa
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $error_message = "Username already taken.";
        } else {
            // Mã hóa mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Thêm người dùng vào cơ sở dữ liệu
            $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed_password]);
            $success_message = "Registration successful. You can now <a href='login.php'>login</a>.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 400px; margin: 0 auto; padding: 20px; }
        form { display: flex; flex-direction: column; }
        input { margin-bottom: 10px; padding: 10px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if (!empty($error_message)) : ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php elseif (!empty($success_message)) : ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
