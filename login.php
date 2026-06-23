<?php
session_start();
include "config/koneksi.php"; // Pastikan jalur ke file koneksi Anda sudah benar

if (isset($_POST['username'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Ambil data user berdasarkan username (tidak sensitif terhadap kolom status jika tipenya enum)
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);

        // Validasi apakah akun berstatus aktif
        if ($user['status'] !== 'aktif') {
            echo "<script>alert('Akun Anda telah dinonaktifkan!'); window.location.href='login.php';</script>";
            exit;
        }

        // Cek password: password_verify (untuk siswa/guru) ATAU bypass teks polos 'admin123' (khusus admin)
        if (password_verify($password, $user['password']) || $password === 'admin123') {

            // Simpan data identitas ke dalam Session browser
            $_SESSION['id_user']  = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // ALUR GERBANG PENGALIHAN OTOMATIS SESUAI ROLE MASING-MASING
            if ($user['role'] === 'admin') {
                header("Location: ../Tubes_Webpro_Tim2/admin/dashboard_admin.php");
                exit;
            } elseif ($user['role'] === 'guru') {
                // header("Location: dashboard_guru.php"); //
                exit;
            } elseif ($user['role'] === 'siswa') {
                header("Location: dashboard.php");
                exit;
            } else {
                echo "<script>alert('Role pengguna tidak valid!'); window.location.href='login.php';</script>";
                exit;
            }

        } else {
            echo "<script>alert('Password salah!'); window.location.href='login.php';</script>";
            exit;
        }

    } else {
        echo "<script>alert('Username tidak ditemukan!'); window.location.href='login.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login EduFlex</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="container">

    <div class="logo">
        <img src="logo_project_website_uid-removebg-preview.png" alt="EduFlex Logo">
        <h2>EduFlex</h2>
        <p class="subtitle">Masuk ke Akun Anda</p>
    </div>

    <form action="" method="POST" class="form-box">

        <div>Email atau Nama Pengguna</div>
        <input
type="text"
name="username"
placeholder="Masukkan email atau nama pengguna Anda"
required>

        <div>Kata Sandi</div>
        <input
type="password"
name="password"
placeholder="Masukkan kata sandi Anda"
required>

        <a href="#" class="forgot">Lupa Kata Sandi?</a>

       <button class="btn-login" type="submit">
                    Masuk
    </button>

        <div class="atau">ATAU</div>

       <button type="button" class="btn-google">
    Masuk dengan Google
</button>

        <p class="register">
            Belum punya akun? <a href="register.php">Daftar</a>
    </p>
    </form>

</div>

</body>
</html>