<?php
session_start();
include "../config/koneksi.php";

// Cek apakah admin sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php"); // Ganti dengan halaman login Anda
    exit();
}

// Ambil data admin yang sedang login
$id_login = $_SESSION['id_user'];
$query_admin = mysqli_query($conn, "SELECT * FROM admin WHERE id_user = '$id_login'");
$admin = mysqli_fetch_assoc($query_admin);

// Inisialisasi variabel agar tidak error
$nama_admin = $admin['nama'] ?? "Admin";
$foto_admin = $admin['foto'] ?? "default_avatar.png";

// 1. PROSES HAPUS (Tradisional - Reload Page)
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);

    // Matikan pengecekan relasi sementara agar tidak error saat hapus
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

    $query = mysqli_query($conn, "DELETE FROM users WHERE id_user = '$id'");

    // Nyalakan kembali
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

    if ($query) {
        echo "<script>alert('User dihapus!'); window.location='crud_user_full.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus: " . mysqli_error($conn) . "'); window.location='crud_user_full.php';</script>";
    }
}

// 2. PROSES TAMBAH (Tradisional - Form Post)
if (isset($_POST['submit'])) {
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$user', '$pass', '$role')");
    echo "<script>alert('User ditambah!'); window.location='crud_user_full.php';</script>";
}
$data_edit = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $q_edit = mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$id_edit'");
    $data_edit = mysqli_fetch_assoc($q_edit);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User - EduFlex</title>
    <link rel="stylesheet" href="../frontend_js/buat_user.css">
    <link rel="stylesheet" href="../frontend_js/dashboard_admin.css">
</head>
<body>
    <header>
    <div class="brand">
      <img src="../logo project website uid.png" alt="logo" class="logo">
      <div>
        <div>EduFlex</div>
        <div class="sub">Education Flexible</div>
      </div>
    </div>
    <nav>
        <ul>
            <li><a href="../admin/dashboard_admin.php" >Beranda</a></li>
            <li><a href="../admin/crud_jadwal_full.php">Buat Jadwal</a></li>
            <li><a href="../admin/crud_user_full.php" class="active">Buat User</a></li>
        </ul>
    </nav>
    <div class="right-section">
      <div class="language">
        <img src="../frontend_js/language_24dp_000000_FILL0_wght400_GRAD0_opsz24.png" alt="bahasa" class="logo-bahasa">
        <select class="select">
          <option>ID</option>
          <option>ENG</option>
        </select>
      </div>
      <div class="account">
         <img src="<?= htmlspecialchars($foto_admin); ?>" class="avatar" alt="avatar">
         <div><?= htmlspecialchars($nama_admin); ?></div>
         <a href="logout.php" style="margin-left: 15px; color: #ff6b00; text-decoration: none; font-weight: bold;">
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
            <div class="form-container" style="width:100%;">
                <h2>Daftar Pengguna</h2>
                <table style="width:100%; border-collapse:collapse; margin-bottom:40px;">
                    <tr style="background:#0b1a3a; color:white;">
                        <th style="padding:12px; text-align:left;">Username</th>
                        <th style="padding:12px; text-align:left;">Role</th>
                        <th style="padding:12px; text-align:left;">Aksi</th>
                    </tr>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM users");
                    while($u = mysqli_fetch_assoc($res)) {
                        echo "<tr>
                            <td style='padding:12px;'>{$u['username']}</td>
                                <td style='padding:12px;'>
                                    <span class='role-badge' style='background:#e8f4fd; color:#2196f3;'>{$u['role']}</span>
                                </td>
                                <td style='padding:12px;'>
                                    <a href='edit_user.php?id={$u['id_user']}' class='btn-action btn-edit'>Edit</a>
                                    <a href='?hapus={$u['id_user']}' class='btn-action btn-hapus' onclick='return confirm(\"Yakin ingin menghapus user ini?\")'>Hapus</a>
                                </td>
                            </tr>";
                    }
                    ?>
                </table>

                <hr style="margin-bottom:30px; border:0; border-top:1px solid #eee;">

                <h2>Tambah Pengguna Baru</h2>
                <form action="" id="formTambahuser" method="POST">
                    <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
                    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role">
                            <option value="guru">Guru</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>
                    <button type="submit" name="submit" class="btn-submit">Daftarkan Pengguna</button>
                </form>
            </div>
        </main>
    </div> 
    
    <footer>
    <div class="leftfooter">
      <div class="textfooter">
        <div>Navigasi</div>
        <div>Sumber Daya</div>
        <div>Hubungi Kami</div>
      </div>
    </div>
    <div class="rightfooter">
      <div class="logososmed">
        <img src="instagram.png" alt="ig">
        <img src="facebook (1).png" alt="fb">
        <img src="youtube.png" alt="yt">
        <img src="twitter.png" alt="x">
        <img src="linkedin.png" alt="linkedin">
      </div>
    </div>
  </footer>
  <script src="../js/user_api.js"></script>
</body>
</html>