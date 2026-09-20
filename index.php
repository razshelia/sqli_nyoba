<?php
session_start();
header('Location: ' . (isset($_SESSION['user']) ? 'produk.php' : 'login.php'));
exit;
