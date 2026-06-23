<?php
session_start();
include "../config/koneksi.php"; // Mengambil koneksi database global

// PROTEKSI HALAMAN: Jika user belum login ATAU role-nya bukan admin, tendang ke login.php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}

// 1. Ambil data profil admin dari tabel admin (relasi id_user)
$id_user_login = $_SESSION['id_user'];
$query_admin = mysqli_query($conn, "SELECT * FROM admin WHERE id_user = '$id_user_login'");
$data_admin = mysqli_fetch_assoc($query_admin);

// Jika nama admin di database belum diisi, gunakan nama default session
$nama_admin = (!empty($data_admin['nama'])) ? $data_admin['nama'] : "Hasbi Juwadi";
$foto_admin = (!empty($data_admin['foto'])) ? $data_admin['foto'] : "foto profile.jpg";

// 2. Query Hitung Data Dinamis Real-Time dari Mesin Database
// Hitung jumlah baris pada tabel users
// QUERY DIUBAH: Menghitung total user selain yang memiliki role 'admin'
$query_user = mysqli_query($conn, "SELECT COUNT(*) as total_user FROM users WHERE role != 'admin'");
$data_user = mysqli_fetch_assoc($query_user);
$total_user = $data_user['total_user'];

// Hitung jumlah baris pada tabel siswa
$query_siswa = mysqli_query($conn, "SELECT COUNT(*) as total_siswa FROM siswa");
$data_siswa = mysqli_fetch_assoc($query_siswa);
$total_siswa = $data_siswa['total_siswa'];

// Hitung jumlah baris pada tabel guru
$query_guru = mysqli_query($conn, "SELECT COUNT(*) as total_guru FROM guru");
$data_guru = mysqli_fetch_assoc($query_guru);
$total_guru = $data_guru['total_guru'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduFlex - Admin Panel Dashboard</title>
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
        <li><a href="../admin/dashboard_admin.php" class="active">Beranda</a></li>
        <li><a href="../admin/crud_jadwal_full.php">Buat Jadwal</a></li>
        <li><a href="../admin/crud_user_full.php">Buat User</a></li>
      </ul>
    </nav>
    <div class="right-section">
      <div class="language">
        <img src="../language_24dp_000000_FILL0_wght400_GRAD0_opsz24.png" alt="bahasa" class="logo-bahasa">
        <select class="select">
          <option>ID</option>
          <option>ENG</option>
        </select>
      </div>
      <div class="account">
        <img src="<?= htmlspecialchars($foto_admin); ?>" class="avatar" alt="avatar">
        <div><?= htmlspecialchars($nama_admin); ?></div>
        <a href="../logout.php" style="margin-left: 15px; color: #ff6b00; text-decoration: none; font-weight: bold;">
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
      <section class="welcome"
        style="background-image: linear-gradient(135deg, rgba(11,26,58,0.85), rgba(255,107,0,0.6)), url('https://i.pinimg.com/736x/da/cf/5f/dacf5f38f433acfc30499bde1b7f42e0.jpg'); background-size: cover; background-position: center;">
        <h2>Welcome, <?= htmlspecialchars($nama_admin); ?></h2>
      </section>

      <div class="content-grid">
        <div class="left">

          <section class="card">
            <h2>Fungsionalitas Utama Pengelola</h2>
            <p style="font-size: 14px; color: #555; margin-bottom: 20px;">Silakan pilih menu kendali di bawah ini untuk
              mengelola entitas data master pada database sistem EduFlex.</p>

            <div class="admin-actions-grid">
              <div class="action-box">
                <h3>Manajemen Pengguna</h3>
                <p>Kelola data kredensial login, pendaftaran data guru baru, serta manipulasi data akun siswa.</p>
                <a href="../admin/crud_user_full.php" class="btn-primary">Buka Fitur User</a>
              </div>
              <div class="action-box">
                <h3>Manajemen Jadwal</h3>
                <p>Atur pemetaan mata pelajaran, pembagian ruang kelas, dan jam operasional mengajar guru.</p>
                <a href="../admin/crud_jadwal_full.php" class="btn-primary">Buka Fitur Jadwal</a>
              </div>
            </div>
          </section>

          <section class="card">
            <h2>Ikhtisar Data Master Sekolah</h2>
            <p style="font-size: 14px; color: #555; margin-bottom: 15px;">Berikut adalah total rekaman aktif yang
              tersimpan di dalam engine database saat ini.</p>

            <div class="stats-overview-grid">
              <div class="stat-item-box">
                <span class="stat-title">Total Akun User</span>
                <span class="stat-number"><?= $total_user; ?> Akun</span>
              </div>
              <div class="stat-item-box">
                <span class="stat-title">Siswa Terdaftar</span>
                <span class="stat-number"><?= $total_siswa; ?> Siswa</span>
              </div>
              <div class="stat-item-box">
                <span class="stat-title">Guru Aktif</span>
                <span class="stat-number"><?= $total_guru; ?> Guru</span>
              </div>
            </div>
          </section>
        </div>

        <div class="right">
          <section class="card">
            <h2>Status Sistem</h2>
            <div class="berita-item">
              <strong>Koneksi Database:</strong>
              <span style="color: #2ecc71; font-weight: bold;"> Aktif</span>
              <p style="font-size: 12px; margin: 5px 0 0 0; color: #666;">Database Terhubung: eduflex (MySQL)</p>
            </div>
            <div class="berita-item">
              <strong>Hak Otorisasi Akun:</strong>
              <p style="font-size: 12px; margin: 5px 0 0 0; color: #666;">Akses penuh diizinkan untuk melakukan aksi
                eksekusi CREATE, READ, UPDATE, dan DELETE.</p>
            </div>
          </section>
        </div>
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
        <img src="../ instagram.png" alt="ig">
        <img src="../facebook (1).png" alt="fb">
        <img src="../youtube.png" alt="yt">
        <img src="../twitter.png" alt="x">
        <img src="../linkedin.png" alt="linkedin">
      </div>
    </div>
  </footer>

</body>

</html>