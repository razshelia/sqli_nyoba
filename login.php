<?php
require __DIR__ . '/db.php';

if (!in_array($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Halaman ini hanya bisa diakses dari localhost.');
}

session_start();
$db    = koneksi();
$error = null;

function proses_login(PDO $db, string $username, string $password): ?array
{
    // ==== VERSI RENTAN (aktif) ====
    $sql  = "SELECT * FROM users WHERE username = '$username' AND password_plain = '$password'";
    $user = $db->query($sql)->fetch();
    return $user ?: null;

    // ==== VERSI AMAN (nonaktif) ====
    // $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    // $stmt->execute([':username' => $username]);
    // $user = $stmt->fetch();
    // return ($user && password_verify($password, $user['password_hash'])) ? $user : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = (string)($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $user     = proses_login($db, $username, $password);

    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: produk.php');
        exit;
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Masuk - TokoKita</title>
</head>
<body>
    <h1>TokoKita</h1>
    <h2>Masuk ke Akun Anda</h2>

    <?php if ($error): ?>
        <p><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Username<br><input type="text" name="username"></label><br><br>
        <label>Password<br><input type="password" name="password"></label><br><br>
        <button type="submit">Masuk</button>
    </form>
</body>
</html>
