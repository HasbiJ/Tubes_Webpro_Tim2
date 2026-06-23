<?php
session_start();
include "config/koneksi.php";

// Proteksi Otorisasi & Otentikasi (Siswa harus login)
if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil data siswa untuk Header & Sidebar
$query_user = mysqli_query($conn, "
    SELECT users.username, siswa.id_siswa, siswa.nama, siswa.nisn, siswa.foto 
    FROM users 
    JOIN siswa ON users.id_user = siswa.id_user 
    WHERE users.id_user='$id_user'
");
$data_user = mysqli_fetch_assoc($query_user);
$id_siswa = $data_user['id_siswa'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlex - Daftar Tugas</title>
    <link rel="stylesheet" href="tugas.css">
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
              <li><a href="jadwal.php">Jadwal</a></li>
              <li><a href="#">AI Helper</a></li>
              <li><a href="tampilanVideo.php">Video</a></li>
              <li><a href="tugas.php" class="active">Tugas</a></li>
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
            <?php if($data_user['foto'] == ""){ ?>
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
        <div class="jadwal">
            <h2>Pengumpulan Tugas Online</h2>
        </div>
        <div class="utama">
        
      <?php
        // Perbaikan Query JOIN agar nama_mapel keluar dengan benar
        $query_tugas = mysqli_query($conn, "
            SELECT tugas.id_tugas, tugas.judul, tugas.deskripsi, tugas.deadline, mata_pelajaran.nama_mapel 
            FROM tugas 
            LEFT JOIN mata_pelajaran ON tugas.id_mapel = mata_pelajaran.id_mapel 
            ORDER BY tugas.deadline ASC
        ");

        while($tugas = mysqli_fetch_assoc($query_tugas)){
            $id_tugas = $tugas['id_tugas'];
            
            // Cek status pengumpulan siswa
            $cek_kumpul = mysqli_query($conn, "SELECT * FROM pengumpulan_tugas WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
            $sudah_kumpul = mysqli_num_rows($cek_kumpul) > 0;

            // DETEKSI ABSOLUT MENGGUNAKAN KATA KUNCI JUDUL (Mengantisipasi bug tipe data ID)
            if (stripos($tugas['judul'], 'Quiz') !== false || stripos($tugas['judul'], 'Kuis') !== false || $id_tugas == 5) {
                $link_tujuan = "quiz.html";
            } else {
                $link_tujuan = "pengumpulan.php?id=" . $id_tugas;
            }
        ?>
            <div class="card">
                <h3 style="margin: 0 0 10px 0; color: #b3541e; font-size: 18px;"><?= $tugas['nama_mapel']; ?></h3>
                
                <p style="font-size: 16px; margin: 5px 0;">
                    <img src="materi.png" alt="materi"> 
                    <strong>
                        <a href="<?php echo $link_tujuan; ?>">
                            <?= $tugas['judul']; ?>
                        </a>
                    </strong>
                </p>
                
                <p style="margin: 8px 0; color: #555;"><?= $tugas['deskripsi']; ?></p>
                <p style="margin: 5px 0;"><img src="button merah.png" alt="lokasi"> Jatuh Tempo: <?= date('d F Y', strtotime($tugas['deadline'])); ?></p>
                
                <?php if($sudah_kumpul){ ?>
                    <p class="status-sudah">Sudah</p>
                <?php } else { ?>
                    <p class="status">Belum</p>
                <?php } ?>
            </div>
        <?php } ?>
        
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