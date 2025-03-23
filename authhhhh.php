<?php
$file = '/var/www/html/info.php'; // Pastikan path file sesuai

// Periksa apakah file ada sebelum menghapus
if (file_exists($file)) {
    if (unlink($file)) {
        echo "File '$file' berhasil dihapus.";
    } else {
        echo "Gagal menghapus file '$file'.";
    }
} else {
    echo "File '$file' tidak ditemukan.";
}
?>
