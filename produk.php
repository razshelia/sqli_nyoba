<?php
require __DIR__ . '/db.php';

if (!in_array($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Halaman ini hanya bisa diakses dari localhost.');
}

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$db   = koneksi();
$kata = trim((string)($_GET['kata'] ?? ''));

function proses_cari(PDO $db, string $kata): array
{
    if ($kata === '') {
        return $db->query("SELECT item_name, price FROM items")->fetchAll();
    }

    // ==== VERSI RENTAN (aktif) ====
    $sql = "SELECT item_name, price FROM items WHERE item_name LIKE '%$kata%'";
    return $db->query($sql)->fetchAll();

    // ==== VERSI AMAN (nonaktif) ====
    // $stmt = $db->prepare("SELECT item_name, price FROM items WHERE item_name LIKE :kata");
    // $stmt->execute([':kata' => "%$kata%"]);
    // return $stmt->fetchAll();

}

$produk = proses_cari($db, $kata);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Produk - TokoKita</title>
</head>
<body>
    <h1>TokoKita</h1>
    <p>Masuk sebagai <?= htmlspecialchars($_SESSION['user']['username']) ?> | <a href="logout.php">Keluar</a></p>

    <form method="get">
        <input type="text" name="kata" value="<?= htmlspecialchars($kata) ?>" placeholder="Cari produk...">
        <button type="submit">Cari</button>
    </form>

    <table border="1" cellpadding="6">
        <tr><th>Nama Produk</th><th>Harga</th></tr>
        <?php foreach ($produk as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['item_name']) ?></td>
            <td>Rp<?= number_format((float)$p['price'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
