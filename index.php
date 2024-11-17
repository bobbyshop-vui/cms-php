<?php
// Start the session
session_start();

// Function to load all index.php files directly inside the /admin/web/ directory
function load_index_files_from_themes() {
    $theme_dir = __DIR__ . '/admin/web/';

    // Kiểm tra xem thư mục có tồn tại không
    if (!is_dir($theme_dir)) {
        // Nếu thư mục không tồn tại, thông báo lỗi
        echo "<p>Error: The directory '/admin/web/' does not exist.</p>";
        return; // Dừng việc thực thi hàm
    }

    // Lấy tất cả các file trong thư mục /admin/web/
    $files = array_diff(scandir($theme_dir), array('.', '..')); // Bỏ qua '.' và '..'

    $found_index = false; // Biến để kiểm tra nếu có file index.php

    foreach ($files as $file) {
        // Kiểm tra xem có file index.php trực tiếp trong thư mục không
        if ($file === 'index.php') {
            $index_file = $theme_dir . $file;
            if (file_exists($index_file)) {
                include_once $index_file; // Bao gồm file index.php
                $found_index = true;
            }
        }
    }

    if (!$found_index) {
        echo "<p>No index.php file found directly in the directory '/admin/web/'.</p>";
    }
}

// Gọi hàm để load index.php
load_index_files_from_themes();
?>
