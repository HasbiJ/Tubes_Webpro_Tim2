<?php
session_start();
include "config/koneksi.php";

if(isset($_POST['username'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cari user berdasarkan username
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

    if(mysqli_num_rows($query) > 0){

        $user = mysqli_fetch_assoc($query);

        // Cek password
        if(password_verify($password, $user['password'])){

            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit;

        } else {

            echo "<script>alert('Password salah!');</script>";

        }

    } else {

        echo "<script>alert('Username tidak ditemukan!');</script>";

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