# Penjelasan Lab SQL Injection

Dokumen ini menjelaskan **apa yang salah** di versi rentan tiap skenario dan
**bagaimana versi aman memperbaikinya**. Sengaja dipisah dari kode PHP
supaya UI web-nya tampil apa adanya seperti web sungguhan, tanpa petunjuk
apa pun di halamannya.

---

## 1. Login (`login.php`, fungsi `proses_login`)
**Salah:** `$username` dan `$password` ditempel langsung ke dalam string SQL
pakai kutip tunggal. Penyerang bisa "menutup" kutip lebih awal lalu menyisipkan
kondisi/perintah SQL sendiri sehingga proses pengecekan username/password
berubah maknanya secara logis.

**Perbaikan (aman):** query berparameter — input diperlakukan sebagai data,
bukan bagian dari perintah SQL. Password dicocokkan dengan `password_verify()`
terhadap hash, bukan dibandingkan langsung di dalam SQL.

---

## 2. Pencarian Produk (`produk.php`, fungsi `proses_cari`)
**Salah:** kata kunci ditempel di dalam `LIKE '%...%'`. Penyerang bisa keluar
dari klausa `LIKE` dan menambahkan klausa SQL lain untuk mengambil data dari
tabel lain, lalu menampilkannya seolah-olah hasil pencarian produk biasa.

**Perbaikan (aman):** wildcard `%...%` dibentuk di PHP, tapi nilainya tetap
dikirim sebagai satu parameter terikat, sehingga tidak bisa "membelah" query.

---

## Pola umum

| Rentan | Aman |
|---|---|
| Input digabung ke string SQL (`"...$var..."`) | Query berparameter (`prepare()` + `execute([...])`) |
| Password dicocokkan langsung di SQL | Dicocokkan lewat `password_verify()` terhadap hash |

## Cara pindah versi (rentan <-> aman)
Buka `login.php` atau `produk.php`, di dalam fungsi `proses_login` /
`proses_cari` ada dua blok:
1. `// ==== VERSI RENTAN (aktif) ====` — aktif secara default.
2. `/* ==== VERSI AMAN (nonaktif) ==== ... */` — komentar, di bawahnya.

Untuk memakai versi aman: beri komentar pada blok 1, lalu buka blok 2
(hapus `/*` dan `*/`-nya).
