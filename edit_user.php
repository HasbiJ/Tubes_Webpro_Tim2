<?php
session_start();
include "config/koneksi.php";

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Cek apakah ID ada di URL
if (!isset($_GET['id'])) {
    header("Location: buat_user.php");
    exit();
}

$id = $_GET['id'];

// Proses Update ketika tombol ditekan
if (isset($_POST['update'])) {
    $role = $_POST['role'];
    
    // Update role di tabel users
    $query = mysqli_query($conn, "UPDATE users SET role='$role' WHERE id_user='$id'");
    
    if ($query) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='buat_user.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
}

// Ambil data user yang akan diedit
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id_user='$id'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengguna - EduFlex</title>
    <link rel="stylesheet" href="buat_user.css"> 
</head>
<body>
    <div class="main" style="padding-top: 50px;">
        <div class="form-container" style="max-width: 400px;">
            <h2>Edit Pengguna: <?= htmlspecialchars($data['username']); ?></h2>
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?= htmlspecialchars($data['username']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role">
                        <option value="guru" <?= ($data['role'] == 'guru') ? 'selected' : ''; ?>>Guru</option>
                        <option value="siswa" <?= ($data['role'] == 'siswa') ? 'selected' : ''; ?>>Siswa</option>
                    </select>
                </div>
                <button type="submit" name="update" class="btn-submit">Simpan Perubahan</button>
                <a href="buat_user.php" style="display:block; text-align:center; margin-top:10px; color:#666;">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>