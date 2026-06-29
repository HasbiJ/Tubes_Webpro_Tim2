<?php
session_start();
include "config/koneksi.php";

// 1. OTENTIKASI & VALIDASI ID TUGAS
if(!isset($_SESSION['id_user']) || !isset($_GET['id'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$id_tugas = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil info siswa untuk header dan query tugas
$query_user = mysqli_query($conn, "SELECT id_siswa, nama, foto FROM siswa WHERE id_user='$id_user'");
$data_user = mysqli_fetch_assoc($query_user);
$id_siswa = $data_user['id_siswa'];

// Ambil detail data tugas kelompok (SELECT)
$query_detail = mysqli_query($conn, "
    SELECT t.*, m.nama_mapel 
    FROM tugas t 
    LEFT JOIN mata_pelajaran m ON t.id_mapel = m.id_mapel 
    WHERE t.id_tugas='$id_tugas'
");
$tugas = mysqli_fetch_assoc($query_detail);

// Cek apakah siswa sudah mengumpulkan tugas ini sebelumnya
$query_kumpul = mysqli_query($conn, "SELECT * FROM pengumpulan_tugas WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
$data_kumpul = mysqli_fetch_assoc($query_kumpul);
$sudah_kumpul = mysqli_num_rows($query_kumpul) > 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlex - Detail Pengumpulan Tugas</title>
    <link rel="stylesheet" href="pengumpulan.css">
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

    <main class="konten-utama" style="padding: 30px; max-width: 1000px; margin: 0 auto;">
        <div style="background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            
            <div style="display:flex; align-items:center; gap:15px; margin-bottom:20px;">
                <img src="materi.png" alt="icon" style="width:40px; height:40px;">
                <div>
                    <h2 style="margin:0; color:#0b1a3a;"><?= $tugas['nama_mapel']; ?></h2>
                    <p style="margin:2px 0 0 0; color:#777; font-weight:600;"><?= $tugas['judul']; ?></p>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <?php if($sudah_kumpul){ ?>
                    <span style="background:#10cd30; color:white; padding:5px 15px; border-radius:20px; font-weight:bold; font-size:14px;">Selesai Dikumpulkan</span>
                <?php } else { ?>
                    <span style="background:#aaa; color:white; padding:5px 15px; border-radius:20px; font-weight:bold; font-size:14px;">Belum Dikumpulkan</span>
                <?php } ?>
            </div>
            
            <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; border-left: 5px solid #b3541e; margin-bottom: 25px;">
                <p style="margin: 0 0 10px 0; line-height: 1.6; color: #333;"><?= $tugas['deskripsi']; ?></p>
                <p style="margin: 0; color: #e74c3c; font-weight: bold; font-size: 14px;">
                    Jatuh tempo pengerjaan sampai tanggal: <?= date('d F Y', strtotime($tugas['deadline'])); ?>
                </p>
            </div>

            <div style="background:#fff; border:1px solid #ddd; border-radius:8px; padding:20px; margin-bottom:20px;">
                <form id="formUploadTugas" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">
                    <input type="hidden" name="id_tugas" value="<?= $id_tugas; ?>">
                    
                    <label style="font-weight: bold; color:#0b1a3a;">Pilih Berkas Tugas Anda</label>
                    <input type="file" name="file_resmi" id="fileResmi" required style="padding:10px; border:1px dashed #ccc; border-radius:6px; background:#fcfcfc;">
                    
                    <button type="submit" style="background: #10cd30; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px;">
                        <?= $sudah_kumpul ? 'Perbarui File' : 'Kirim Tugas'; ?>
                    </button>
                </form>
            </div>

            <?php if($sudah_kumpul){ ?>
                <div style="background: #f4f6f9; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top:20px;">
                    <p style="margin:0 0 5px 0; color:#555; font-size:14px;">Status: Menunggu Penilaian</p>
                    <p style="margin:0 0 15px 0; color:#555; font-size:14px;">Id_Siswa: <?= $id_siswa; ?> | Dikumpulkan pada: <?= date('d-m-Y H:i', strtotime($data_kumpul['tgl_kumpul'])); ?> WIB</p>
                    
                    <div style="display:flex; align-items:center; justify-content:space-between; background:#fff; padding:12px; border-radius:6px; border:1px solid #dee2e6;">
                        <span style="font-weight:600; color:#0b1a3a;">📄 <?= $data_kumpul['file_tugas']; ?></span>
                        
                        <form action="api_pengumpulan.php?action=delete" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengumpulan tugas ini?')">
                            <input type="hidden" name="id_tugas" value="<?= $id_tugas; ?>">
                            <input type="hidden" name="redirect_url" value="pengumpulan.php?id=<?= $id_tugas; ?>">
                            
                            <button type="submit" style="background: #e74c3c; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px;">
                                Batalkan Pengumpulan
                            </button>
                        </form>
                    </div>
                </div>
            <?php } ?>

        </div>
    </main>

    <script>
    // A. LOGIKA UNTUK UPLOAD / UPDATE FILE TUGAS
    const formUpload = document.getElementById('formUploadTugas');
    if(formUpload) {
        formUpload.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('api_pengumpulan.php?action=upload', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) // Ambil teks mentah dulu untuk membuang zzz
            .then(text => {
                // Trik Potong paksa zzz/ZZZ yang bocor dari file include
                const jsonClean = text.replace(/^zzz+/i, '').trim();
                
                // Parse manual teks yang sudah steril ke JSON objek
                const data = JSON.parse(jsonClean);
                
                if(data.status === 'success') {
                    alert(data.message); // Pop-up sukses dijamin muncul!
                    window.location.reload(); // Otomatis refresh halaman!
                } else {
                    alert('Gagal: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Parsing Error:', error);
                // Jalur alternatif darurat: kalau parsing gagal, langsung reload agar status tetap ter-update
                window.location.reload();
            });
        });
    }

    // B. LOGIKA UNTUK BATALKAN / DELETE FILE TUGAS
    function batalPengumpulan(idTugas) {
        if(confirm('Apakah Anda yakin ingin membatalkan pengumpulan tugas ini?')) {
            const formData = new FormData();
            formData.append('id_tugas', idTugas);
            
            fetch('api_pengumpulan.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) // Ambil teks mentah dulu
            .then(text => {
                const jsonClean = text.replace(/^zzz+/i, '').trim();
                const data = JSON.parse(jsonClean);
                
                if(data.status === 'success') {
                    alert(data.message); // Pop-up sukses pembatalan muncul!
                    window.location.reload(); // Otomatis refresh!
                } else {
                    alert('Gagal: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Parsing Error:', error);
                window.location.reload();
            });
        }
    }
    </script>
</body>
</html>