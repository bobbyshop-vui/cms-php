<?php
try {
    $db = new PDO('sqlite:' . __DIR__ . '/../mycms.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tạo bảng người dùng
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL
    )");

    // Tạo bảng bài viết
    $db->exec("CREATE TABLE IF NOT EXISTS posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL
    )");

    // Tạo bảng cài đặt
    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT
    )");

    // Thêm một theme mặc định nếu chưa có
    $db->exec("INSERT OR IGNORE INTO settings (key, value) VALUES ('default_theme', 'default_theme_name')");

    echo "Database and tables created successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
