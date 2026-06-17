<?php
session_start();
include "config/koneksi.php";

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$query = mysqli_query($conn,"
SELECT users.username,
       siswa.nama,
       siswa.nisn,
       siswa.foto
FROM users
JOIN siswa ON users.id_user = siswa.id_user
WHERE users.id_user='$id_user'
");

$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Sekolah</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <header>
    <div class="brand">
      <img src="logo project website uid.png" alt="logo" class="logo">
      <div>
        <div>EduFlex</div>
        <div class="sub">Education Flexible</div>
      </div>
    </div>
    <nav>
        <ul>
            <li><a href="dashboard.php" class="active">Beranda</a></li>
            <li><a href="presensi.php">Presensi</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="#">AI Helper</a></li>
            <li><a href="tampilanVideo.php">Video</a></li>
            <li><a href="tugas.php">Tugas</a></li>
        </ul>
    </nav>
    <div class="right-section">
      <div class="language">
        <img src="language_24dp_000000_FILL0_wght400_GRAD0_opsz24.png" alt="bahasa" class="logo-bahasa">
        <select class="select">
          <option>ID</option>
          <option>ENG</option>
        </select>
      </div>
      <div class="account">
        <a href="profile.php">
          <?php if($data['foto']==""){ ?>
            <img src="foto profile.jpg" class="avatar">
          <?php }else{ ?>
            <img src="uploads/<?= $data['foto']; ?>" class="avatar">
          <?php } ?>
        </a>
        <a href="profile.php">
          <div><?= $data['nama']; ?></div>
        </a>
      </div>
    </div>
  </header>

<div class="container">
  
    <aside class="sidebar">
      <div class="profile"> 
        <a href="profile.php">
          <?php if($data['foto']==""){ ?>
            <img src="foto profile.jpg" alt="foto siswa">
          <?php }else{ ?>
            <img src="uploads/<?= $data['foto']; ?>" alt="foto siswa">
          <?php } ?>
          
          <h3><?= $data['nama']; ?></h3>
          <p>NISN : <?= !empty($data['nisn']) ? $data['nisn'] : '-'; ?></p>
          <p>Selamat Datang</p>
        </a>
      </div>
  </aside>

    <main class="main">
    <section class="welcome" style="background-image: url('https://i.pinimg.com/736x/da/cf/5f/dacf5f38f433acfc30499bde1b7f42e0.jpg'); background-size: cover; background-position: center;">
      <h2>Welcome, <?= $data['nama']; ?></h2>
    </section>
      <div class="content-grid">
        <div class="left">
          <section class="card">
              <h2>Grafik Kehadiran Siswa</h2>
              <div class="grafik-horiz">
                  <div class="row">
                      <span class="label">Hadir</span>
                      <div class="bar hadir"></div>
                      <span class="persen">80%</span>
                  </div>
                  <div class="row">
                      <span class="label">Izin</span>
                      <div class="bar izin"></div>
                      <span class="persen">10%</span>
                  </div>
                  <div class="row">
                      <span class="label">Sakit</span>
                      <div class="bar sakit"></div>
                      <span class="persen">7%</span>
                  </div>
                  <div class="row">
                      <span class="label">Alpa</span>
                      <div class="bar alpa"></div>
                      <span class="persen">3%</span>
                  </div>
              </div>
          </section>
          <section class="card">
            <h3>TUGAS</h3>
              <div class="progress-bar">
                <div class="progress"></div>
              </div>
              <p>100%</p>
          </section>
            <section class="card">
              <div class="daftarguru">
              <a href="listguru.html"><h2>Daftar Guru</h2></a>
                  <div class="guru-list">
                    <div class="guru">
                      <img src="profileguru.jpg">
                      <p><strong>Pak Anwar</strong></p>
                      <small>Matematika</small>
                    </div>
                    <div class="guru">
                      <img src="profileguru.jpg">
                      <p><strong>Bu Sari</strong></p>
                      <small>Fisika</small>
                    </div>
                    <div class="guru">
                      <img src="profileguru.jpg">
                      <p><strong>Bu Tika</strong></p>
                      <small>Bahasa Inggris</small>
                    </div>
                    <div class="guru">
                      <img src="profileguru.jpg">
                      <p><strong>Pak Budi</strong></p>
                      <small>Sejarah</small>
                    </div>
                  </div>
              </div>
            </section>
        </div>
          <div class="right">
              <section class="card">
                  <h2>Berita</h2>
                  <div class="berita-item">
                    <div class="imgberita"><img src="jadwalsnbp.jpeg" alt=""></div>
                      <a href="berita.html">Persiapan SNBT 2026 meliputi langkah-langkah akademis, pendaftaran, dan strategi belajar.</a>
                  </div>
                  <div class="berita-item">
                    <div class="imgberita"><img src="perisapansnbt.jpg" alt=""></div>
                      Tips dan trik mempersiapkan diri menghadapi SNBT tahun ini.
                  </div>
                  <div class="berita-item">
                    <div class="imgberita"><img src="TKA.jpg" alt=""></div>
                      Info TKA 2025: Apa Itu Tes Kemampuan Akademik (TKA) 2025? Kenapa Siswa Harus Siap Mulai Sekarang?
                  </div>
              </section>
          </div>
      </div>
  </main>
</div>
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