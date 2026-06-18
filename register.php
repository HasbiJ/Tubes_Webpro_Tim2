<?php
include "config/koneksi.php";

if(isset($_POST['nama'])){

    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];
    $role = $_POST['role']; // Mengambil data role yang dipilih (siswa atau guru)

    // Cek password
    if($password != $konfirmasi){
        echo "<script>alert('Konfirmasi password tidak sama');</script>";
    }else{

        // Cek username sudah dipakai atau belum
        $cek = mysqli_query($conn,"SELECT * FROM users WHERE username='$username'");

        if(mysqli_num_rows($cek) > 0){
            echo "<script>alert('Username sudah digunakan');</script>";
        }else{

            // Enkripsi password
            $password_hash = password_hash($password,PASSWORD_DEFAULT);

            // 1. Simpan ke tabel users sesuai dengan role yang dipilih pendaftar
            mysqli_query($conn,"
                INSERT INTO users(username,password,role,status)
                VALUES('$username','$password_hash','$role','aktif')
            ");

            // Ambil id user yang baru saja dibuat
            $id_user = mysqli_insert_id($conn);

            // 2. Pengkondisian cabang penyimpanan berdasarkan kecocokan role
            if($role == 'guru') {
                // Jika mendaftar sebagai guru, simpan ke data entitas guru
                mysqli_query($conn,"
                    INSERT INTO guru(id_user,nama)
                    VALUES('$id_user','$nama')
                ");
            } else {
                // Jika mendaftar sebagai siswa, simpan ke data entitas siswa
                mysqli_query($conn,"
                    INSERT INTO siswa(id_user,nama)
                    VALUES('$id_user','$nama')
                ");
            }

            echo "<script>
            alert('Registrasi akun sebagai " . ucfirst($role) . " berhasil!');
            window.location='login.php';
            </script>";

        }

    }

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar EduFlex</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>

<div class="container">

    <div class="logo">
        <img src="logo_project_website_uid-removebg-preview.png" alt="EduFlex Logo">
        <h2>EduFlex</h2>
        <p class="subtitle">Buat Akun Baru Anda</p>
    </div>

    <form action="" method="POST" class="form-box">

        <div>Nama Lengkap</div>
        <input
        type="text"
        name="nama"
        placeholder="Masukkan nama lengkap Anda"
        required>

        <div>Email atau Nama Pengguna</div>
        <input
        type="text"
        name="username"
        placeholder="Masukkan email atau nama pengguna Baru"
        required>

        <div>Daftar Sebagai (Role)</div>
        <select name="role" class="select-role" required>
            <option value="" disabled selected>-- Pilih Peran Hak Akses --</option>
            <option value="siswa">Siswa</option>
            <option value="guru">Guru / Tenaga Pengajar</option>
        </select>

        <div>Kata Sandi</div>
        <input
        type="password"
        name="password"
        placeholder="Buat kata sandi Anda"
        required>

        <div>Konfirmasi Kata Sandi</div>
        <input
        type="password"
        name="konfirmasi"
        placeholder="Ulangi kata sandi Anda"
        required>

        <button class="btn-login" type="submit">Daftar</button>

        <div class="atau">ATAU</div>

        <button class="btn-google" type="button">
            Daftar dengan Google
        </button>

        <p class="register">
            Sudah punya akun? <a href="login.php">Masuk</a>
        </p>

    </form>

</div>

</body>
</html>