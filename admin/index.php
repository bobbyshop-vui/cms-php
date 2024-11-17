<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle extension upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['extension_file'])) {
    $file = $_FILES['extension_file'];
    $name = $_POST['name']; // Get the name of the extension from form

    // Allow .php files without restriction
    $uploadDir = __DIR__ . '/extensions/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($file['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        echo "<p>Extension uploaded successfully.</p>";
    } else {
        echo "<p>Error moving uploaded file.</p>";
    }
}

// Handle extension deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_extension_file'])) {
    $fileName = $_POST['delete_extension_file'];
    $filePath = __DIR__ . '/extensions/' . $fileName;

    if (file_exists($filePath)) {
        unlink($filePath);
        echo "<p>Extension deleted successfully.</p>";
    } else {
        echo "<p>Extension file not found.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .extension-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .extension-item p {
            margin: 0;
            margin-right: 10px;
        }
        .btn-delete {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>Admin Dashboard</h1>

        <!-- Include PHP files from extensions directory -->
        <?php
        $extensionsDir = __DIR__ . '/extensions/';
        $files = array_diff(scandir($extensionsDir), array('.', '..'));

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                include_once $extensionsDir . $file;
            }
        }
        ?>

        <!-- Form to upload extension -->
        <div class="mt-4">
            <h2>Upload New Extension</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Extension Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="extension_file">Choose File</label>
                    <input type="file" name="extension_file" id="extension_file" class="form-control-file" required>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>

        <!-- Display installed extensions -->
        <div class="mt-4">
            <h2>Installed Extensions</h2>
            <?php
            $files = array_diff(scandir($extensionsDir), array('.', '..'));

            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $fileNameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);
                    echo "<div class='extension-item'>";
                    echo "<p>{$fileNameWithoutExtension}</p>";
                    echo "<form action='' method='post' style='display:inline;'>";
                    echo "<input type='hidden' name='delete_extension_file' value='{$file}'>";
                    echo "<button type='submit' class='btn btn-danger btn-delete'>Delete</button>";
                    echo "</form>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
