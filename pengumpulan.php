<?php
session_start();
include "config/koneksi.php";

// 1. OTENTIKASI & PROTEKSI HALAMAN
if(!isset($_SESSION['id_user']) || !isset($_GET['id'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$id_tugas = $_GET['id'];

// Ambil data siswa (id_siswa, nama, foto) berdasarkan id_user dari session login
$query_user = mysqli_query($conn, "SELECT id_siswa, nama, foto FROM siswa WHERE id_user='$id_user'");
$data_user = mysqli_fetch_assoc($query_user);
$id_siswa = $data_user['id_siswa'];

// Ambil detail tugas dari tabel tugas asli + JOIN ke mata_pelajaran
$query_detail = mysqli_query($conn, "
    SELECT tugas.*, mata_pelajaran.nama_mapel 
    FROM tugas 
    LEFT JOIN mata_pelajaran ON tugas.id_mapel = mata_pelajaran.id_mapel 
    WHERE tugas.id_tugas='$id_tugas'
");
$tugas = mysqli_fetch_assoc($query_detail);

// Cek apakah siswa sudah pernah mengunggah berkas untuk tugas ini (SELECT / READ CRUD)
$query_kumpul = mysqli_query($conn, "SELECT * FROM pengumpulan_tugas WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
$data_kumpul = mysqli_fetch_assoc($query_kumpul);


// 2. AKSI CRUD: INPUT (CREATE) & EDIT (UPDATE)
if(isset($_POST['submit_tugas'])){
    $nama_file = $_FILES['file_resmi']['name'];
    $tmp_file = $_FILES['file_resmi']['tmp_name'];
    
    if($nama_file != ""){
        $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
        // Penamaan file dinamis menggunakan ID tugas dan ID siswa agar unik
        $nama_file_baru = "Tugas_" . $id_tugas . "_" . $id_siswa . "." . $ekstensi;
        $folder_tujuan = "uploads_tugas/" . $nama_file_baru;
        
        // Buat folder otomatis jika belum ada di server
        if (!is_dir('uploads_tugas')) {
            mkdir('uploads_tugas', 0777, true);
        }

        if(move_uploaded_file($tmp_file, $folder_tujuan)){
            if($data_kumpul){
                // Jika berkas sudah ada sebelumnya = EDIT (UPDATE CRUD)
                mysqli_query($conn, "UPDATE pengumpulan_tugas SET file_tugas='$nama_file_baru', tgl_kumpul=NOW() WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
            } else {
                // Jika berkas baru pertama kali dikirim = INPUT (CREATE CRUD)
                mysqli_query($conn, "INSERT INTO pengumpulan_tugas (id_tugas, id_siswa, file_tugas, tgl_kumpul) VALUES ('$id_tugas', '$id_siswa', '$nama_file_baru', NOW())");
            }
            header("Location: pengumpulan.php?id=" . $id_tugas);
            exit;
        }
    }
}


// 3. AKSI CRUD: DELETE (BATALKAN PENGUMPULAN)
if(isset($_POST['delete_tugas'])){
    if($data_kumpul){
        $file_lama = "uploads_tugas/" . $data_kumpul['file_tugas'];
        if(file_exists($file_lama)){
            unlink($file_lama); // Hapus file fisik dari folder storage
        }
        // Hapus record data dari database = DELETE CRUD
        mysqli_query($conn, "DELETE FROM pengumpulan_tugas WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
        header("Location: pengumpulan.php?id=" . $id_tugas);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlex - Detail Pengumpulan</title>
    <link rel="stylesheet" href="pengumpulan.css">
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
                <li><a href="dashboard.php">Beranda</a></li>
                <li><a href="presensi.php">Presensi</a></li>
                <li><a href="jadwal.php">Jadwal</a></li>
                <li><a href="#">AI Helper</a></li>
                <li><a href="tampilanVideo.php">Video</a></li>
                <li><a href="tugas.php" class="active">Tugas</a></li>
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
                <?php if($data_user['foto'] == ""){ ?>
                    <img src="foto profile.jpg" class="avatar">
                <?php } else { ?>
                    <img src="uploads/<?= $data_user['foto']; ?>" class="avatar">
                <?php } ?>
            </a>
            <a href="profile.php"><div><?= $data_user['nama']; ?></div></a>
          </div>
        </div>
    </header>

    <main class="konten-utama">
        <div class="tugas">
          <img src="assignment.png" alt="ikon">
            <div class="judul-tugas">
              <h2><?= $tugas['nama_mapel']; ?></h2>
              <p><?= $tugas['judul']; ?></p>
            </div>
        </div>

        <div class="status">
          <?php if($data_kumpul){ ?>
              <p class="status-hijau">Selesai Dikumpulkan</p>
          <?php } else { ?>
              <p class="status-abu">Belum Dikumpulkan</p>
          <?php } ?>
        </div>

        <div class="main-pengumpulan">
          <div class="soal">
            <p style="font-size: 1.1rem; line-height: 1.6; color: #333; margin-bottom: 15px;"><?= $tugas['deskripsi']; ?></p>
            <p style="color: #e74c3c; font-weight: 600;">Jatuh tempo pengerjaan sampai tanggal: <?= date('d F Y', strtotime($tugas['deadline'])); ?></p>
          </div>
          
          <div class="unggah">
            <form action="" method="POST" enctype="multipart/form-data" class="form-kumpul-tugas">
                
                <label class="unggah-file">
                     <span>Pilih Berkas Tugas Anda</span>
                     <input type="file" name="file_resmi" required class="input-hidden">
                </label>
                
                <button type="submit" name="submit_tugas" class="btn-kirim-hijau">
                    <?= $data_kumpul ? 'Perbarui File' : 'Kirim Tugas'; ?>
                </button>
                
            </form>
          </div>

          <?php if($data_kumpul){ ?>
              <div class="status-pengumpulan">
                <p><strong>Status Kelulusan:</strong> <span class="badge-status"><?= $data_kumpul['nilai'] !== null ? 'Telah Dinilai' : 'Menunggu Penilaian'; ?></span></p>
                <p><strong>Dikumpulkan pada:</strong> <?= date('d-m-Y H:i', strtotime($data_kumpul['tgl_kumpul'])); ?> WIB</p>
              </div>
              
              <div class="pengumpulan">
                <div class="file-document">
                  <p style="margin: 0 0 10px 0; font-weight: 600; color: #333;">Berkas Tersimpan:</p>
                  <div class="file">
                    <img src="folder_ikon.png" alt="ikon">
                    <p style="margin: 0; padding: 0;"><a href="uploads_tugas/<?= $data_kumpul['file_tugas']; ?>" target="_blank" style="color: #0b1a3a; text-decoration: none; font-weight: 600;"><?= $data_kumpul['file_tugas']; ?></a></p>
                  </div>
                </div>
                
                <form action="" method="POST" style="align-self: center;">
                    <button type="submit" name="delete_tugas" onclick="return confirm('Apakah Anda yakin ingin membatalkan pengumpulan tugas ini?')" class="btn-hapus-tugas">
                        Batalkan Pengumpulan
                    </button>
                </form>
              </div>
              
              <div class="nilai-box">
                <div class="nilai-item">
                    <span class="nilai-label">Nilai Anda</span>
                    <span class="nilai-angka"><?= $data_kumpul['nilai'] !== null ? $data_kumpul['nilai'] : '-'; ?><span class="per-seratus">/100</span></span>
                </div>
                <div class="komentar-item">
                    <span class="komentar-label">Komentar Guru:</span>
                    <p class="komentar-isi">"<?= $data_kumpul['komentar'] !== null ? $data_kumpul['komentar'] : 'Belum ada tanggapan dari guru'; ?> Sweden"</p>
                </div>
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