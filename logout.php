<?php
// 1. Mulai sesi
session_start();

// 2. Hapus semua variabel sesi
$_SESSION = array();

// 3. Jika sesi menggunakan cookie, hapus cookie sesi tersebut
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Hancurkan sesi
session_destroy();

// 5. Alihkan user kembali ke halaman login
header("Location: login.php");
exit();
?>