<?php
session_start();
include "config/koneksi.php";

// Proteksi Akun
if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// 1. Ambil data siswa yang login
$query_user = mysqli_query($conn, "SELECT nama, foto FROM siswa WHERE id_user='$id_user'");
$data_user  = mysqli_fetch_assoc($query_user);

// 2. Query READ yang sudah disesuaikan tanpa filter id_kelas (Menampilkan semua jadwal)
$query_jadwal = mysqli_query($conn, "
    SELECT jadwal.*, mata_pelajaran.nama_mapel 
    FROM jadwal 
    LEFT JOIN mata_pelajaran ON jadwal.id_mapel = mata_pelajaran.id_mapel
    ORDER BY FIELD(jadwal.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), jadwal.jam_mulai ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlex - Jadwal Pelajaran</title>
    <link rel="stylesheet" href="jadwal.css">
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
              <li><a href="presensi.php">Presensi</a></li>
              <li><a href="jadwal.php" class="active">Jadwal</a></li>
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
              <?php if(empty($data_user['foto'])){ ?>
                  <img src="foto profile.jpg" class="avatar">
              <?php } else { ?>
                  <img src="uploads/<?= $data_user['foto']; ?>" class="avatar">
              <?php } ?>
          </a>
          <a href="profile.php"><div><?= $data_user['nama']; ?></div></a>
        </div>
      </section>
    </header>

    <main class="konten-utama">
        
        <?php 
        if(mysqli_num_rows($query_jadwal) > 0) {
            $hari_sekarang = "";
            
            while($row = mysqli_fetch_assoc($query_jadwal)) {
                
                if($hari_sekarang != $row['hari']) {
                    $hari_sekarang = $row['hari'];
                    
                    if($hari_sekarang != "" && isset($bukan_pertama)) {
                        echo '</div>'; 
                    }
                    $bukan_pertama = true;
                    ?>
                    
                    <div class="jadwal" style="margin-top: 30px;">
                        <h2>
                            <img src="calendar.png" alt="Mapel">   
                            Jadwal Pembelajaran | <?= $hari_sekarang; ?>
                        </h2>
                    </div>
                    <div class="utama">
                <?php 
                } 
                
                // Cek nama mata pelajaran untuk penentuan warna card CSS
                $nama_pelajaran = isset($row['nama_mapel']) ? $row['nama_mapel'] : 'Mata Pelajaran';
                $guru_pelajaran = "-"; // Set strip dulu sementara agar tidak memicu error kolum unknown
                $materi_pelajaran = isset($row['materi']) ? $row['materi'] : 'Materi Pembelajaran';
                
                $css_card = "card-matwa"; 
                if(strpos(strtolower($nama_pelajaran), 'seni') !== false) $css_card = "card-senbud";
                elseif(strpos(strtolower($nama_pelajaran), 'biologi') !== false) $css_card = "card-biologi";
                elseif(strpos(strtolower($nama_pelajaran), 'indonesia') !== false) $css_card = "card-bindo";
                elseif(strpos(strtolower($nama_pelajaran), 'lanjut') !== false) $css_card = "card-matmin";
                elseif(strpos(strtolower($nama_pelajaran), 'kimia') !== false) $css_card = "card-kimia";
                elseif(strpos(strtolower($nama_pelajaran), 'fisika') !== false) $css_card = "card-fisika";
                ?>
                
                <div class="<?= $css_card; ?>">
                    <h4><?= $nama_pelajaran; ?></h4>
                    <p><img src="schedule.png" alt="jam"> <?= substr($row['jam_mulai'], 0, 5); ?> - <?= substr($row['jam_selesai'], 0, 5); ?></p>
                    <p><img src="location.png" alt="lokasi"> <?= $row['ruang']; ?></p>
                    <p><img src="materi.png" alt="materi"> <?= $materi_pelajaran; ?></p>
                    <p><img src="guru.png" alt="guru"> Guru Mata Pelajaran : <?= $guru_pelajaran; ?></p>
                </div>

            <?php 
            }
            echo '</div>'; 
        } else { ?>
            <div class="jadwal" style="text-align:center; padding: 50px;">
                <h2>Belum ada jadwal pembelajaran yang terdaftar untuk kelas Anda.</h2>
            </div>
        <?php } ?>

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