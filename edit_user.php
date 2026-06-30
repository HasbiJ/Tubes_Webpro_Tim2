<?php
session_start();
include "config/koneksi.php";
$nama_admin = isset($_SESSION['nama_admin']) ? $_SESSION['nama_admin'] : 'Admin';
$foto_admin = isset($_SESSION['foto_admin']) ? $_SESSION['foto_admin'] : 'default.jpg';
// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Cek apakah ID ada di URL
if (!isset($_GET['id'])) {
    header("Location: ../Tubes_Webpro_Tim2/admin/crud_user_full.php");
    exit();
}

$id = $_GET['id'];

// Proses Update ketika tombol ditekan
if (isset($_POST['update'])) {
    $role = $_POST['role'];

    // Update role di tabel users
    $query = mysqli_query($conn, "UPDATE users SET role='$role' WHERE id_user='$id'");

    if ($query) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='../Tubes_Webpro_Tim2/admin/crud_user_full.php';</script>";
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
    <link rel="stylesheet" href="../Tubes_Webpro_Tim2/frontend_js/dashboard_admin.css">
    <link rel="stylesheet" href="edit_user.css">
</head>
<body>
    <header>
    <div class="brand">
      <img src="../Tubes_Webpro_Tim2/logo project website uid.png" alt="logo" class="logo">
      <div>
        <div>EduFlex</div>
        <div class="sub">Education Flexible</div>
      </div>
    </div>
    <nav>
      <ul>
        <li><a href="../Tubes_Webpro_Tim2/admin/dashboard_admin.php">Beranda</a></li>
        <li><a href="../Tubes_Webpro_Tim2/admin/crud_jadwal_full.php">Buat Jadwal</a></li>
        <li><a href="../Tubes_Webpro_Tim2/admin/crud_user_full.php" class="active">Buat User</a></li>
      </ul>
    </nav>
    <div class="right-section">
      <div class="language">
        <img src="../Tubes_Webpro_Tim2/language_24dp_000000_FILL0_wght400_GRAD0_opsz24.png" alt="bahasa" class="logo-bahasa">
        <select class="select">
          <option>ID</option>
          <option>ENG</option>
        </select>
      </div>
      <div class="account">
        <img src="../Tubes_Webpro_Tim2/avatars/<?= htmlspecialchars($foto_admin); ?>" class="avatar" alt="avatar">
        <div><?= htmlspecialchars($nama_admin); ?></div>
        <a href="../Tubes_Webpro_Tim2/logout.php" style="margin-left: 15px; color: #ff6b00; text-decoration: none; font-weight: bold;">
          Keluar
        </a>
      </div>
    </div>
  </header>

  <div class="container">

    <aside class="sidebar">
      <div class="profile">
        <img src="<?= htmlspecialchars($foto_admin); ?>" alt="foto admin">
        <h3><?= htmlspecialchars($nama_admin); ?></h3>
        <p class="role-badge">Administrator</p>
      </div>
    </aside>
        <main class="main">
            <divs tyle="padding-top: 50px;">
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
                        <a href="../Tubes_Webpro_Tim2/admin/crud_user_full.php"
                            style="display:block; text-align:center; margin-top:10px; color:#666;">Batal</a>
                    </form>
                </div>
            </divs>
        </main>
</body>

</html>