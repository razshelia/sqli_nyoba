<?php
/**
 * db.php - Koneksi database & data awal saja.
 * Tidak ada logika lab / HTML di file ini.
 */

const DB_FILE = __DIR__ . '/sqli_lab.sqlite';

function koneksi(): PDO
{
    $baru = !file_exists(DB_FILE);

    $db = new PDO('sqlite:' . DB_FILE);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    if ($baru) {
        isi_data_awal($db);
    }
    return $db;
}

function isi_data_awal(PDO $db): void
{
    // password_plain (teks biasa) HANYA agar kode rentan bisa mengecek password di dalam SQL.
    // Jangan menyimpan password teks biasa di aplikasi sungguhan.
    $db->exec("CREATE TABLE users (
        id INTEGER PRIMARY KEY, username TEXT, password_plain TEXT,
        password_hash TEXT, role TEXT, email TEXT)");
    $stmt = $db->prepare("INSERT INTO users (username, password_plain, password_hash, role, email)
                          VALUES (?, ?, ?, ?, ?)");
    foreach ([['admin', 'Administrator', 'admin@kampus.id'],
              ['budi',  'Mahasiswa',     'budi@kampus.id'],
              ['sari',  'Mahasiswa',     'sari@kampus.id']] as [$user, $role, $email]) {
        $stmt->execute([$user, 'rahasia123', password_hash('rahasia123', PASSWORD_BCRYPT), $role, $email]);
    }

    $db->exec("CREATE TABLE items (id INTEGER PRIMARY KEY, item_name TEXT, category_id INTEGER, price INTEGER)");
    $stmt = $db->prepare("INSERT INTO items (item_name, category_id, price) VALUES (?, ?, ?)");
    foreach ([['Laptop ROG', 1, 15000000], ['Mouse Gaming', 1, 300000], ['Keyboard Mekanik', 1, 750000],
              ['Buku Pemrograman Web', 2, 85000], ['Buku Basis Data', 2, 92000]] as $item) {
        $stmt->execute($item);
    }

    $db->exec("CREATE TABLE visit_logs (
        id INTEGER PRIMARY KEY, browser_info TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)");
}

function log_terakhir(PDO $db): array
{
    return $db->query("SELECT id, browser_info, created_at FROM visit_logs ORDER BY id DESC LIMIT 5")->fetchAll();
}

function daftar_user(PDO $db): array
{
    return $db->query("SELECT id, username, role, email FROM users")->fetchAll();
}
