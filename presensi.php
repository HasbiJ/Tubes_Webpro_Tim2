<?php
session_start();
include "config/koneksi.php";

// Proteksi Akun Siswa
if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil data siswa asli hasil JOIN dari database (READ)
$query = mysqli_query($conn, "
    SELECT siswa.*, users.username, kelas.nama_kelas 
    FROM siswa 
    JOIN users ON siswa.id_user = users.id_user
    LEFT JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    WHERE siswa.id_user='$id_user'
");
$data = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlex - Presensi</title>
    <link rel="stylesheet" href="presensi.css">
</head>
<body>
  <header class="header">
    <div class="brand">
      <img src="logo project website uid.png" alt="logo" class="logo">
      <div>
        <div>EduFlex</div>
        <div class="sub">Education Flexible</div>
      </div>
    </div>
    <nav>
        <ul>
            <li><a href="dashboard.php">Beranda</a></li>
            <li><a href="presensi.php" class="active">Presensi</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="#">AI Helper</a></li>
            <li><a href="tampilanVideo.php">Video</a></li>
            <li><a href="tugas.php">Tugas</a></li>
        </ul>
    </nav>
    <section class="right-section">
      <div class="language">
        <img src="language_24dp_000000_FILL0_wght400_GRAD0_opsz24.png" alt="bahasa" class="logo-bahasa">
        <select class="select">
          <option>ID</option>
          <option>ENG</option>
        </select>
      </div>
      <div class="account">
        <a href="profile.php">
            <?php if(empty($data['foto'])){ ?>
                <img src="foto profile.jpg" class="avatar">
            <?php } else { ?>
                <img src="uploads/<?= $data['foto']; ?>" class="avatar">
            <?php } ?>
        </a>
        <a href="profile.php"><div><?= $data['nama']; ?></div></a>
      </div>
    </section>
  </header>

  <main class="main-container">
    <img src="presensi.png" alt="Scan Area">
    <div class="card">
        <img src="calendar.png" width="40" />
        <div class="card-title">Matematika Tingkat Lanjut</div>
        <div class="info">
            <div>08:00 - 09:30</div>
            <div>Ruang A101</div>
        </div>
    </div>
  </main>

  <footer class="gridfooter">
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
</body>
</html>