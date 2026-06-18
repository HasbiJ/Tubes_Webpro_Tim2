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

// Ambil data siswa berdasarkan id_user dari session login
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

// Cek apakah siswa sudah pernah mengunggah berkas untuk tugas ini (Read data saat load awal)
$query_kumpul = mysqli_query($conn, "SELECT * FROM pengumpulan_tugas WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
$data_kumpul = mysqli_fetch_assoc($query_kumpul);
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
            <p><?= $tugas['deskripsi']; ?></p>
            <p style="margin-top: 10px; color: #e74c3c;">Jatuh tempo pengerjaan sampai tanggal: <strong><?= date('d F Y', strtotime($tugas['deadline'])); ?></strong></p>
          </div>
          
          <div class="unggah">
            <form id="formUploadTugas" style="display: flex; flex-direction: column; gap: 15px; max-width: 100%;">
                <input type="hidden" name="id_tugas" value="<?= $id_tugas; ?>">
                
                <label class="unggah-file">
                     <span>Pilih Berkas Tugas Anda</span>
                     <input type="file" name="file_resmi" required class="input-hidden">
                </label>
                
                <button type="submit" class="btn-kirim-hijau">
                    <?= $data_kumpul ? 'Perbarui File' : 'Kirim Tugas'; ?>
                </button>
            </form>
          </div>

          <?php if($data_kumpul){ ?>
              <div id="infoPengumpulan">
                  <div class="status-pengumpulan">
                    <p>Status : <?= $data_kumpul['nilai'] !== null ? 'Telah Dinilai' : 'Menunggu Penilaian'; ?></p>
                    <p>Dikumpulkan pada : <?= date('d-m-Y H:i', strtotime($data_kumpul['tgl_kumpul'])); ?> WIB</p>
                  </div>
                  <div class="pengumpulan">
                    <div class="file-document">
                      <div class="file">
                        <img src="folder_ikon.png" alt="ikon">
                        <p><a href="uploads_tugas/<?= $data_kumpul['file_tugas']; ?>" target="_blank"><?= $data_kumpul['file_tugas']; ?></a></p>
                      </div>
                    </div>
                    
                    <button type="button" id="btnBatalKumpul" class="btn-hapus-tugas">
                        Batalkan Pengumpulan
                    </button>
                  </div>
                  
                  <p>Nilai : <?= $data_kumpul['nilai'] !== null ? $data_kumpul['nilai'].'/100' : 'Belum dinilai'; ?></p>
                  <p>Komentar : <?= $data_kumpul['komentar'] !== null ? $data_kumpul['komentar'] : 'Belum ada tanggapan dari guru'; ?></p>
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

    <script>
    // 1. AJAK / FETCH PROSES UPLOAD & EDIT DATA
    document.getElementById('formUploadTugas').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        fetch('api_pengumpulan.php?action=upload', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if(data.status === 'success') {
                location.reload(); // Refresh halaman untuk memperbarui status UI berkas baru
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // 2. AJAX / FETCH PROSES DELETE DATA
    const btnBatal = document.getElementById('btnBatalKumpul');
    if(btnBatal) {
        btnBatal.addEventListener('click', function() {
            if(confirm('Apakah Anda yakin ingin membatalkan pengumpulan tugas ini?')) {
                let formData = new FormData();
                formData.append('id_tugas', '<?= $id_tugas; ?>');
                
                fetch('api_pengumpulan.php?action=delete', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if(data.status === 'success') {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    }
    </script>
</body>
</html>